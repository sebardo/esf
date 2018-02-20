<?php
define('PG_NO_BOUND', "\x00no-param\x00");

/**
 * @desc MySQL abstraction layer
 * @throws PDOException
 */
class mysql
{
    protected static $_connected = false;

    /** @var PDO */
    protected static $_pdo;

    private static $_addRowsContainer = array();

    private static $_intransaction = false;

    private static $_handler;

    const IGNORE = 1;
    const REPLACE = 2;

    private $_value;

    /**
     * @desc connect to a mysql db
     * @static
     *
     * @param  $host
     * @param  $database
     * @param  $username
     * @param  $password
     *
     * @return bool
     */

    public static function connect($dsn = null)
    {

        if (self::$_connected === FALSE) {

            preg_match('[mysql://([^@]+):([^@]+)@([^@]+)/([^@]+)]', $dsn, $matches);

            list(, $username, $password, $host, $database) = $matches;

            try {
                self::$_pdo = new PDO(
                    "mysql:host={$host};dbname={$database}",
                    $username,
                    $password,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
            } catch (PDOException $e) {
                throw new Exception("CAN'T CONNECT TO DB: " . $e->getMessage());
            }

            static::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            static::$_connected = true;
        }

        return static::$_connected;
    }

    /**
     * @param callable $handler
     *
     * @return bool
     */
    public static function setErrorHandler($handler)
    {
        static::$_handler = $handler;
    }

    /**
     * Close connection to database
     */
    public static function close()
    {
        if (static::$_connected === true) {

            static::$_pdo = null;
            static::$_connected = false;
            static::$_addRowsContainer = array();

        }
    }

    private function __construct($value, $param = null)
    {
        is_array($param) or $param = array($param);

        // replace each subsequent ? with an element from the array
        while (strpos($value, '?') !== false) {
            if (empty($param)) {
                throw new errException('Param has more members than query params');
            }

            // escape the needed value and replace the first
            $replace = static::$_pdo->quote(array_shift($param));
            $value = preg_replace('/\?/', $replace, $value, 1);
        }

        $this->_value = $value;
    }

    public static function literal($value, $param = null)
    {
        static::$_connected or static::connect();
        return new static ($value, $param);
    }

    private function _get()
    {
        return $this->_value;
    }

    public function __toString()
    {
        return $this->_get();
    }

    /**
     * @desc handles pdoexceptions raised due to mysql errors
     * @static
     * @throws PDOException
     *
     * @param string $query
     * @param array $boundValues
     * @param PDOException $e
     */


    private static function _hadleError($query = null, $boundValues = null, PDOException $e = null)
    {
        $boundQuery = static::_bindQuery(
            $query,
            $boundValues,
            isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage()
        );

        d($e, $query, $boundValues, $boundQuery);
        die;
        call_user_func(static::$_handler, $e, $query, $boundValues, $boundQuery);
    }


    private static function _bindQuery($query, $boundValues, $errorMessage)
    {
        if ($boundValues === PG_NO_BOUND || $boundValues === array()) {
            return static::_highlightErrors($query, $errorMessage);
        }


        $replacements = array();
        if (!is_array($boundValues)) {
            $boundValues = array($boundValues);
        }

        # build a regular expression for each parameter
        foreach ($boundValues as $key => $value) {

            if (is_string($key)) {
                $key = ':' . $key;
                $strlen = strlen($key);

                preg_match_all("/\\W({$key})\\W/", $query, $matches, PREG_OFFSET_CAPTURE);
                if (isset($matches[1])) {
                    $offset = 0;
                    foreach ($matches[1] as $ps) {
                        $pos = $ps[1] + $offset;
                        $offset -= $strlen - 2;

                        $query = substr_replace($query, "##", $pos, $strlen);

                        $v = (is_numeric($value))
                            ? $value
                            : "'{$value}'";

                        $n = array();
                        foreach ($replacements as $k => $r) {
                            if ($k > $pos) {
                                $k -= $strlen - 2;
                            }
                            $n[$k] = $r;
                        }
                        $replacements = $n;


                        $replacements[$pos] = "<abbr title=\"{$key}\">$v</abbr>";
                    }
                }
            } else {
                $key = '?';
                $pos = 0;

                $pos = strpos($query, $key, $pos);
                if ($pos !== false) {
                    $query = substr_replace($query, "##", $pos, strlen($key));
                    $pos += 2;
                    $v = (is_numeric($value))
                        ? $value
                        : "'{$value}'";

                    $replacements[$pos] = "<abbr title=\"{$key}\">$v</abbr>";
                }
            }
        }

        $query = static::_highlightErrors($query, $errorMessage);

        if (!empty($replacements)) {
            ksort($replacements);
            $query = preg_replace(array_fill(0, sizeof($replacements), '/##/'), $replacements, $query, 1);
        }

        return $query;
    }

    private static function _highlightErrors($query, $errorMessage)
    {
        if (preg_match("#'([^']+)'#", $errorMessage, $matches)) {
            $query = preg_replace(
                '#([\W])(' . preg_quote($matches[1]) . ')#',
                '\1<span class="sql-error">\2</span>',
                $query,
                1
            );
        }

        return $query;
    }

    /**
     * @desc Execute SQL query that returns no rows
     * @static
     *
     * @param string $query
     * @param mixed $boundValues
     *
     * @throws PDOException
     * @return bool|int affected number of rows by this query|false on failure
     */

    public static function exec($query, $boundValues = PG_NO_BOUND)
    {
        if (empty($query)) {

            throw new PDOException('mysql::exec empty query');

        } elseif ($boundValues !== PG_NO_BOUND) {

            return static::query($query, $boundValues)->rowCount();

        } else {

            try {
                static::$_connected or static::connect();

                return static::$_pdo->exec($query);

            } catch (PDOException $e) {

                static::_hadleError($query, $boundValues, $e);

            }
        }
    }

    protected static function isAssoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /**
     * @desc decides whether to prepare a statement (based on amount of parameters) and executes a query
     *
     * @param string $query SQL query to execute
     * @param mixed $boundValues values to bind: accepts array or single value
     *
     * @throws PDOException
     * @throws errException
     * @return PDOstatement
     */

    public static function query($query, $boundValues = PG_NO_BOUND)
    {
        static::$_connected or static::connect();
        $statement = false;
        if (empty($query)) {

            throw new PDOException('mysql::query empty query');

        } elseif ($boundValues !== PG_NO_BOUND) {
            is_array($boundValues) or $boundValues = array($boundValues);
            $isAssoc = static::isAssoc($boundValues);

            $i = 0;
            $processedValues = $boundValues; # php (should) do this on its own, but lets avoid confusion
            foreach ($boundValues as $key => &$val) {

                // prevent column cannot be null error
                if ($val === NULL) {
                    $val = '';
                } elseif (is_array($val)) {
                    $bug = true;

                    if ($isAssoc) {

                        $regex = "/\\sin\\s*\\(\\s*:{$key}\\s*\\)/i";

                        if (preg_match_all($regex, $query, $matches, PREG_OFFSET_CAPTURE)) {

                            $offset = 0;
                            foreach ($matches[0] as $match) {
                                $replacement = '';
                                $newVal = array();
                                foreach ($val as $k => $v) {
                                    $newKey = "{$key}__{$k}";
                                    $replacement .= ':' . $newKey . ',';
                                    $newVal[$newKey] = $v;
                                }
                                $replacement = " IN (" . substr($replacement, 0, -1) . ")";


                                $query = substr_replace($query, $replacement, $match[1] + $offset, strlen($match[0]));
                                $offset += strlen($replacement) - strlen($match[0]);
                                $processedValues = array_slice($processedValues, 0, $i, true) +
                                    $newVal +
                                    array_slice($processedValues, $i + 1, null, true);
                            }
                            $bug = false;
                        }

                    } else {

                        $regex = "/\\sin\\s*\\(\\s*\\?\\s*\\)/i";

                        if (preg_match($regex, $query, $matches, PREG_OFFSET_CAPTURE)) {
                            $replacement = substr(str_repeat('?,', count($val)), 0, -1);
                            $query = substr_replace($query, " IN ($replacement)", $matches[0][1], strlen($matches[0][0]));
                            array_splice($processedValues, $i, 1, $val);
                            $i += count($val) - 1;

                            $bug = false;
                        }

                    }

                    if ($bug) throw new errException('Nested array provided to bind to a query.', get_defined_vars());

                }
                $i++;

            }

            try {

                /** @var $statement PDOStatement */
                $statement = static::$_pdo->prepare($query);

                $statement->execute($processedValues);

            } catch (PDOException $e) {
                static::_hadleError($query, $processedValues, $e);
            }

        } else {

            try {

                $statement = static::$_pdo->query($query);

            } catch (PDOException $e) {

                static::_hadleError($query, $boundValues, $e);

            }
        }

        return $statement;
    }


    /**
     * Adds/replaces one row. Action depends on the
     *
     * @param string $table
     * @param array|mysql[] $data single level associative array of `column=>value` pairs
     * @param array|int $extra possible values:
     *  + NULL - insert as usual
     *  + mysql::IGNORE - INSERT IGNORE clause
     *  + mysql::REPLACE - REPLACE clause
     *  + indexed array - uses ON DUPLICATE KEY UPDATE with the array of column names passed
     *  + associative array - uses ON DUPLICATE KEY UPDATE with the array of column => value passed
     *
     * @throws errException
     * @return int new primary key value
     */
    public static function addRow($table, $data = array(), $extra = NULL)
    {
        $keyQuery = '';
        $valQuery = '';
        $onduplicateQuery = '';

        // alter clause of query if an apropriate $extra param is passed
        if ($extra && !is_array($extra)) {

            if ($extra === self::REPLACE) {
                $queryClause = 'REPLACE';
            } elseif ($extra === self::IGNORE) {
                $queryClause = 'INSERT IGNORE';
            } else {
                throw new errException('Unsupported $extra parameter', get_defined_vars());
            }

        } else {
            $queryClause = 'INSERT';
        }


        // build key and value clauses
        foreach ($data as $key => $value) {
            $keyQuery .= "`{$key}`,";

            if (isset($value) && !is_scalar($value)) {
                if ($value instanceof self) {
                    $valQuery .= $value->_get() . ',';

                    unset($data[$key]);
                } else {
                    // it's an unknown object or an array - most definitely a bug
                    throw new errException('Passed value to updateTable is not string or numeric', $key, $value);
                }
            } else {
                $valQuery .= ":{$key},";
            }
        }
        $valQuery = substr($valQuery, 0, -1);
        $keyQuery = substr($keyQuery, 0, -1);

        // do the ON DUPLICATE KEY UPDATE magic
        if (is_array($extra)) {
            $onduplicateQuery = " ON DUPLICATE KEY UPDATE ";

            if (static::isAssoc($extra)) {
                $alias = 'a';
                foreach ($extra as $key => $val) {

                    if (isset($val) && !is_scalar($val)) {
                        if ($val instanceof self) {
                            $onduplicateQuery .= "`{$key}` = " . $val->_get() . ",";
                        } else {
                            // it's an unknown object or an array - most definitely a bug
                            throw new errException('Passed value to updateTable $extra is not string or numeric', $extra);
                        }
                    } else {
                        $onduplicateQuery .= "`{$key}` = :{$key}{$alias},";
                        $data[$key . $alias] = $val;
                        $alias++;
                    }
                }
            } else {
                foreach ($extra as $key) {
                    $onduplicateQuery .= "`{$key}` = VALUES(`{$key}`),";
                }
            }
            $onduplicateQuery = substr($onduplicateQuery, 0, -1);
        }

        // build the final query and execute it
        $query = "{$queryClause}`{$table}`({$keyQuery})VALUES({$valQuery}){$onduplicateQuery}";

        self::query($query, $data);

        return self::$_pdo->lastInsertId();
    }

    /**
     * @static
     *
     * @param string $table
     * @param string|array $whereClause - no 'WHERE' keyword, just condition, accepts pairs of values too
     * @param mixed $boundValues - these are for whereClause
     *
     * @throws ErrException
     * @throws errException
     * @throws errException
     * @return int number of deleted rows
     */
    public static function deleteRows($table, $whereClause = null, $boundValues = PG_NO_BOUND)
    {
        if (empty($table)) {
            throw new ErrException('provide a table name', get_defined_vars());
        }

        if (is_array($whereClause)) {
            $columns = $whereClause;
            $whereClause = '';
            if ($boundValues !== PG_NO_BOUND) {
                throw new errException('specify bound values in where clause if you pass it as array');
            }
            $boundValues = $columns;


            $assoc = static::isAssoc($columns);

            foreach ($columns as $key => $value) {

                if (isset($value) && !is_scalar($value)) {
                    if ($value instanceof self) {
                        $whereClause[] = "`{$key}`=" . $value->_get();

                        unset($boundValues[$key]);
                    } else {
                        // it's an unknown object or an array - most definitely a bug
                        throw new errException('Passed value to deleteRows is not string or numeric', $key, $value);
                    }
                } else {
                    $param = $assoc ? ":{$key}" : "?";

                    $whereClause[] = "`{$key}`={$param}";
                }

            }

            $whereClause = implode(' AND ', $whereClause);
        }

        $whereClause and $whereClause = 'WHERE ' . $whereClause;

        $query = "DELETE FROM {$table} {$whereClause}";


        return static::exec($query, $boundValues);
    }

    /**
     * @static
     *
     * @param string $table
     * @param array|mysql[] $data
     * @param array|string $whereClause note that this only supports question marks as sql parameters. I.e. no :id ones
     * @param mixed $boundValues
     *
     * @throws ErrException
     * @throws errException
     * @return bool|int number of rows affected
     */
    public static function updateTable($table, $data = array(), $whereClause = null, $boundValues = PG_NO_BOUND)
    {
        if (empty($data)) {
            throw new ErrException('no data to update', get_defined_vars());
        }

        $query = "UPDATE {$table} SET ";
        $updateClause = array();
        foreach ($data as $key => &$value) {


            if (isset($value) && !is_scalar($value)) { // if not null and not scalar
                if ($value instanceof self) {
                    $updateClause[] = "`{$key}`=" . $value->_get();

                    unset($data[$key]);
                } else {
                    // it's an unknown object or an array - most definitely a bug
                    throw new errException('Passed value to updateTable is not string or numeric', $key, $value);
                }
            } else {
                if (is_bool($value)) $value = (int)$value;
                $updateClause[] = "`{$key}`=?";
            }
        }
        unset($value);

        $query .= implode(',', $updateClause);
        $data = array_values($data);

        if (isset($whereClause)) {

            if ($boundValues !== PG_NO_BOUND) {
                if (is_array($boundValues)) {
                    if (static::isAssoc($boundValues)) {
                        throw new errException('Named parameters are not supported in updateTable, sorry');
                    }
                    $data = array_merge($data, $boundValues);
                } else {
                    array_push($data, $boundValues);
                }
            }

            if (is_array($whereClause)) {
                if ($boundValues !== PG_NO_BOUND) {
                    throw new errException('specify bound values in where clause if you pass it as array');
                }
                if (!static::isAssoc($whereClause)) {
                    throw new errException('if where clause is array it must be associative');
                }

                $columns = $whereClause;
                $whereClause = array();

                foreach ($columns as $key => $value) {

                    if (isset($value) && !is_scalar($value)) {
                        if ($value instanceof self) {
                            $whereClause[] = "`{$key}`=" . $value->_get();
                            unset($columns[$key]);
                        } else {
                            // it's an unknown object or an array - most definitely a bug
                            throw new errException('Passed value to deleteRows is not string or numeric', $key, $value);
                        }
                    } else {
                        $whereClause[] = "`{$key}`=?";
                    }

                }

                $whereClause = implode(' AND ', $whereClause);
                $data = array_merge($data, array_values($columns));
            }

            $query .= " WHERE {$whereClause}";
        }

        return static::exec($query, $data);
    }

    /**
     * Create lots of new rows, prioritise performance;
     *
     * foreach ($data as $row) {
     *     mysql::addRows('table_name', array('id'=>$row['id']));
     * }
     * mysql::addRows('table_name');
     *
     * @param string $table
     * @param mixed $data
     *     array:
     *         data to be inserted, may be indexed or numeric. The latter only if you are passing all present
     *         table fields *AND* in the correct order. (For eg. quite safe in many-to-many relationship tables).
     *     NULL:
     *         the operation on passed table is cancelled. Otherwise it will be performed on shutdown
     *  void:
     *         the operation is performed explicitly
     * @param int|array $extra mysql::REPLACE or mysql::IGNORE
     *
     * @throws errException
     * @return int|false rows inserted/false on fail
     */
    public static function addRows($table, $data = array(), $extra = null)
    {
        $container = &static::$_addRowsContainer[$table];

        if (func_num_args() === 1) {
            // check if it's the table name and commit if so (the last step)
            if (isset($container)) {

                $valQuery = '';
                $keyQuery = '';
                $keysGenerated = false;
                foreach ($container['values'] as $row) {
                    foreach ($row as $key => $value) {
                        $keysGenerated or $keyQuery .= "`{$key}`,";

                        if (isset($value) && !is_scalar($value)) {
                            if ($value instanceof self) {
                                $valQuery .= $value->_get() . ',';
                            } else {
                                // it's an unknown object or an array - most definitely a bug
                                throw new errException('Passed value to updateTable is not string or numeric', $key, $value);
                            }
                        } else {
                            if (is_bool($value)) $value = (int)$value;
                            $valQuery .= static::$_pdo->quote($value) . ',';
                        }
                    }
                    $valQuery = substr($valQuery, 0, -1) . '),(';
                    $keysGenerated = true;
                }
                $valQuery = substr($valQuery, 0, -3);
                $keyQuery = substr($keyQuery, 0, -1);


                $query = "{$container['action']}`{$table}`({$keyQuery})VALUES({$valQuery})";

                $affected = static::exec($query);
                unset(static::$_addRowsContainer[$table]);
                return $affected;
            } else {
                // todo this is a probable bug
                return false;
            }

        } elseif ($data === null) {
            // if NULL is passed, reset the values to be inserted

            unset(static::$_addRowsContainer[$table]);

            return;
        } elseif (isset($container)) {
            // add more values to insert to a started operation (means the same table is called a second or later time)

            $container['values'][] = $data;

        } else {
            // it's the first time this table is called; initialize

            switch ($extra) {
                case static::REPLACE:
                    $container['action'] = 'REPLACE';
                    break;
                case static::IGNORE:
                    $container['action'] = 'INSERT IGNORE';
                    break;
                default:
                    $container['action'] = 'INSERT INTO ';
                    break;
            }
            $container['values'][] = $data;

            // add the rows at the end of execution unless recalled
            // shutdown::getInstance()->register(array('mysql', 'addRows'), $table);
        }
    }

    public static function exists($table, $whereClause = null, $boundValues = PG_NO_BOUND)
    {
        $whereClause and $whereClause = ' WHERE ' . $whereClause;
        return (bool)static::query("SELECT 1 FROM {$table}{$whereClause} LIMIT 1", $boundValues)->fetchColumn();
    }

    public static function getRow($query, $boundValues = PG_NO_BOUND)
    {
        return static::query($query, $boundValues)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @desc Get single value from query as string
     *
     * @param string $query
     * @param mixed $boundValues if bound values is given, the query will be prepared and executed with these values
     *
     * @return string|bool
     */

    public static function getOne($query, $boundValues = PG_NO_BOUND)
    {
        return static::query($query, $boundValues)->fetchColumn(0);
    }

    /**
     * @desc Get last executed query results as two-dimensional array
     *
     * @param string $query
     * @param mixed $boundValues if bound values is given, the query will be prepared and executed with these values
     *
     * @return array array of fetched results
     */
    public static function getAll($query, $boundValues = PG_NO_BOUND)
    {
        return static::query($query, $boundValues)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @static Get query results as two-dimensional array as defined by format
     *
     *
     * example:
     *  format: "id=>value"
     *  query result:
     *  __________
     *  |id|value|
     *  ----------
     *  | 5|apple|
     *  ----------
     *
     *  returns array( '5' => 'apple' )
     *
     *
     * example2:
     *  format: "id=>value;type"
     * !!! OR
     *  format: "id=>*"
     *
     *  query result:
     *  ________________
     *  |id|value|type |
     *  ----------------
     *  | 5|apple|fruit|
     *  ----------------
     *
     *  returns array( '5' => array( 'value' => 'apple', 'type' => 'fruit' ) )
     *
     * example3:
     *  format: "id[]=>value;type"
     * !!! OR
     *  format: "id[]=>*"
     *  query result:
     *  _________________
     *  |id|value|type  |
     *  -----------------
     *  | 5|apple|fruit |
     *  | 5|cat  |mammal|
     *  | 6|pear |fruit |
     *  -----------------
     *
     *  returns array( '5' => array(
     *                              0 => array( 'value' => 'apple', 'type' => 'fruit'  ),
     *                              1 => array( 'value' => 'cat'  , 'type' => 'mammal' ),
     *                          ),
     *                 '6' => array(
     *                              0 => array( 'value' => 'pear' , 'type' => 'fruit'  ),
     *                          ),
     *              )
     * example4:
     *  format: "id[type]=>value" // you can also nest braces like this: key1[key2][key3][]
     * !!! OR
     *  format: "id[type]=>*"
     *  query result:
     *  _________________
     *  |id|value|type  |
     *  -----------------
     *  | 5|apple|fruit |
     *  | 5|cat  |mammal|
     *  | 5|rhino|mammal|
     *  | 6|pear |fruit |
     *  -----------------
     *
     *  returns array( '5' => array(
     *                              'fruit' => 'pear',
     *                              'mammal' => 'rhino', // if more than one value is found with same key[key2]=>... ,
     *                                                   // the last value is returned without warning, to prevent this
     *                                                   // use key[key2][]=>..
     *                          ),
     *                 '6' => array(
     *                              'fruit' => 'pear'
     *                          ),
     *              )
     *
     *
     * @param string $query
     * @param string $format
     * @param mixed $boundValues
     * @param bool $keepKey
     *
     * @throws ErrException
     * @return array|false
     */

    public static function getAsAssoc($query, $format, $boundValues = PG_NO_BOUND, $keepKey = false)
    {
        preg_match('#([^=\[]+)((?:\[(?:[^\]]*)\])*)=>(.+)#', $format, $matches);

        try {
            list(, $key, $braces, $columns) = $matches;
        } catch (Exception $e) {
            throw new ErrException('Invalid format pattern', get_defined_vars());
        }

        if ($braces) {
            preg_match_all('#\[([^\]]*)\]#', $braces, $matches);
            $nestedKeys = $matches[1];
        }

        if ($PdoStatement = static::query($query, $boundValues)) {

            $values = $columns === '*' ? $columns : explode(';', $columns);

            $hasMultipleValues = $values === '*' || isset($values[1]);

            $rows = array();
            while ($result = $PdoStatement->fetch(PDO::FETCH_ASSOC)) {
                if ($hasMultipleValues) {

                    if ($values === '*') {
                        $row = $result;
                        if (!$keepKey) {
                            unset($row[$key]);
                        }
                    } else {
                        foreach ($values as $v) {
                            $row[$v] = $result[$v];
                        }
                    }

                } else {
                    $row = $result[$columns];
                }

                if ($braces) {
                    isset($rows[$result[$key]]) or $rows[$result[$key]] = array();

                    $cont = &$rows[$result[$key]];

                    foreach ($nestedKeys as $nestedKey) {

                        if ($values === '*') {
                            if (!$keepKey) {
                                unset($row[$nestedKey]);
                            }
                        }

                        if ($nestedKey) {
                            isset($cont[$result[$nestedKey]]) or $cont[$result[$nestedKey]] = array();
                            $cont = &$cont[$result[$nestedKey]];
                        } else {
                            $cont[] = array();
                            $cont = &$cont[static::last($cont, true)];
                        }
                    }


                    $cont = $row;
                } else {
                    $rows[$result[$key]] = $row;

                }


            }
            return $rows;
        }

        return false;
    }

    protected static function last($array, $getKey = false)
    {
        if ($getKey) {
            $array = array_keys($array);
        }

        return is_array($array) ? end($array) : null;
    }

    /**
     * Get a single dimension array, is usefull only for single column select
     *
     * @static
     *
     * @param string $query
     * @param array|string $boundValues
     *
     * @return array|false fetched results or false on error
     */

    public static function getSingleColumn($query, $boundValues = PG_NO_BOUND)
    {
        if ($resource = static::query($query, $boundValues)) {
            $result = array();
            while (($record = $resource->fetchColumn(0)) !== false) {
                $result[] = $record;
            }
            return $result;
        }
        return false;
    }

    /**
     * @static returns number of rows returned in query
     *
     * @param  $query
     * @param  $boundValues
     *
     * @return int|NULL
     */
    public static function countRows($query, $boundValues = PG_NO_BOUND)
    {
        return static::query($query, $boundValues)->rowCount();
    }


    /* ************************
     * DIRECT WRAPPERS TO PDO *
     ************************ */

    public static function beginTransaction()
    {
        static::$_connected or static::connect();

        if (static::$_intransaction) {
            return;
        }

        static::$_intransaction = true;

        static::$_pdo->beginTransaction();
    }

    public static function commit()
    {
        if (!static::$_intransaction) {
            return;
        }

        static::$_intransaction = false;

        static::$_pdo->commit();
    }

    public static function rollBack()
    {
        static::$_pdo->rollBack();
    }

    public static function lastInsertId()
    {
        return static::$_pdo->lastInsertId();
    }

    /**
     * replaces empty strings with DEFAULT
     *
     * @static
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function defaultify($value)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v === '' and $v = static::literal('DEFAULT');
            }

            return $value;
        }
        return $value === '' ? static::literal('DEFAULT') : $value;
    }
}

