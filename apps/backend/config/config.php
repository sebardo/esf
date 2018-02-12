<?php

// include project configuration
include(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// symfony bootstraping
require_once($sf_symfony_lib_dir.'/util/sfCore.class.php');
sfCore::bootstrap($sf_symfony_lib_dir, $sf_symfony_data_dir);


// add an alternative PDO wrapper to use instead (next to) Propel Hell
require_once $sf_symfony_lib_dir.'/util/sfYaml.class.php';
$dbDsn = sfYaml::load(SF_ROOT_DIR . '/config/databases.yml');
require_once SF_ROOT_DIR . '/apps/backend/lib/mysql.class.php';
mysql::connect($dbDsn['all']['propel']['param']['dsn']);
