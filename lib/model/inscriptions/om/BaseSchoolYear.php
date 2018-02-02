<?php


abstract class BaseSchoolYear extends BaseObject  implements Persistent {


	
	protected static $peer;


	
	protected $id;


	
	protected $orden;

	
	protected $collInscriptions;

	
	protected $lastInscriptionCriteria = null;

	
	protected $collSchoolYearI18ns;

	
	protected $lastSchoolYearI18nCriteria = null;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

  
  protected $culture;

	
	public function getId()
	{

		return $this->id;
	}

	
	public function getOrden()
	{

		return $this->orden;
	}

	
	public function setId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = SchoolYearPeer::ID;
		}

	} 
	
	public function setOrden($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->orden !== $v) {
			$this->orden = $v;
			$this->modifiedColumns[] = SchoolYearPeer::ORDEN;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getInt($startcol + 0);

			$this->orden = $rs->getInt($startcol + 1);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 2; 
		} catch (Exception $e) {
			throw new PropelException("Error populating SchoolYear object", $e);
		}
	}

	
	public function delete($con = null)
	{

    foreach (sfMixer::getCallables('BaseSchoolYear:delete:pre') as $callable)
    {
      $ret = call_user_func($callable, $this, $con);
      if ($ret)
      {
        return;
      }
    }


		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(SchoolYearPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SchoolYearPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	

    foreach (sfMixer::getCallables('BaseSchoolYear:delete:post') as $callable)
    {
      call_user_func($callable, $this, $con);
    }

  }
	
	public function save($con = null)
	{

    foreach (sfMixer::getCallables('BaseSchoolYear:save:pre') as $callable)
    {
      $affectedRows = call_user_func($callable, $this, $con);
      if (is_int($affectedRows))
      {
        return $affectedRows;
      }
    }


		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(SchoolYearPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
    foreach (sfMixer::getCallables('BaseSchoolYear:save:post') as $callable)
    {
      call_user_func($callable, $this, $con, $affectedRows);
    }

			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	protected function doSave($con)
	{
		$affectedRows = 0; 		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SchoolYearPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setId($pk);  
					$this->setNew(false);
				} else {
					$affectedRows += SchoolYearPeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

			if ($this->collInscriptions !== null) {
				foreach($this->collInscriptions as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collSchoolYearI18ns !== null) {
				foreach($this->collSchoolYearI18ns as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} 
	
	protected $validationFailures = array();

	
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = SchoolYearPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collInscriptions !== null) {
					foreach($this->collInscriptions as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collSchoolYearI18ns !== null) {
					foreach($this->collSchoolYearI18ns as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = SchoolYearPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getOrden();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = SchoolYearPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getOrden(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = SchoolYearPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setOrden($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = SchoolYearPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setOrden($arr[$keys[1]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(SchoolYearPeer::DATABASE_NAME);

		if ($this->isColumnModified(SchoolYearPeer::ID)) $criteria->add(SchoolYearPeer::ID, $this->id);
		if ($this->isColumnModified(SchoolYearPeer::ORDEN)) $criteria->add(SchoolYearPeer::ORDEN, $this->orden);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(SchoolYearPeer::DATABASE_NAME);

		$criteria->add(SchoolYearPeer::ID, $this->id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setOrden($this->orden);


		if ($deepCopy) {
									$copyObj->setNew(false);

			foreach($this->getInscriptions() as $relObj) {
				$copyObj->addInscription($relObj->copy($deepCopy));
			}

			foreach($this->getSchoolYearI18ns() as $relObj) {
				$copyObj->addSchoolYearI18n($relObj->copy($deepCopy));
			}

		} 

		$copyObj->setNew(true);

		$copyObj->setId(NULL); 
	}

	
	public function copy($deepCopy = false)
	{
				$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SchoolYearPeer();
		}
		return self::$peer;
	}

	
	public function initInscriptions()
	{
		if ($this->collInscriptions === null) {
			$this->collInscriptions = array();
		}
	}

	
	public function getInscriptions($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
			   $this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				InscriptionPeer::addSelectColumns($criteria);
				$this->collInscriptions = InscriptionPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				InscriptionPeer::addSelectColumns($criteria);
				if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
					$this->collInscriptions = InscriptionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastInscriptionCriteria = $criteria;
		return $this->collInscriptions;
	}

	
	public function countInscriptions($criteria = null, $distinct = false, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

		return InscriptionPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addInscription(Inscription $l)
	{
		$this->collInscriptions[] = $l;
		$l->setSchoolYear($this);
	}


	
	public function getInscriptionsJoinCourse($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
				$this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				$this->collInscriptions = InscriptionPeer::doSelectJoinCourse($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

			if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
				$this->collInscriptions = InscriptionPeer::doSelectJoinCourse($criteria, $con);
			}
		}
		$this->lastInscriptionCriteria = $criteria;

		return $this->collInscriptions;
	}


	
	public function getInscriptionsJoinProvincia($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
				$this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				$this->collInscriptions = InscriptionPeer::doSelectJoinProvincia($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

			if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
				$this->collInscriptions = InscriptionPeer::doSelectJoinProvincia($criteria, $con);
			}
		}
		$this->lastInscriptionCriteria = $criteria;

		return $this->collInscriptions;
	}


	
	public function getInscriptionsJoinGrupo($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
				$this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				$this->collInscriptions = InscriptionPeer::doSelectJoinGrupo($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

			if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
				$this->collInscriptions = InscriptionPeer::doSelectJoinGrupo($criteria, $con);
			}
		}
		$this->lastInscriptionCriteria = $criteria;

		return $this->collInscriptions;
	}


	
	public function getInscriptionsJoinKidsAndUsCenter($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
				$this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				$this->collInscriptions = InscriptionPeer::doSelectJoinKidsAndUsCenter($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

			if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
				$this->collInscriptions = InscriptionPeer::doSelectJoinKidsAndUsCenter($criteria, $con);
			}
		}
		$this->lastInscriptionCriteria = $criteria;

		return $this->collInscriptions;
	}


	
	public function getInscriptionsJoinSummerFunCenter($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptions === null) {
			if ($this->isNew()) {
				$this->collInscriptions = array();
			} else {

				$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

				$this->collInscriptions = InscriptionPeer::doSelectJoinSummerFunCenter($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->getId());

			if (!isset($this->lastInscriptionCriteria) || !$this->lastInscriptionCriteria->equals($criteria)) {
				$this->collInscriptions = InscriptionPeer::doSelectJoinSummerFunCenter($criteria, $con);
			}
		}
		$this->lastInscriptionCriteria = $criteria;

		return $this->collInscriptions;
	}

	
	public function initSchoolYearI18ns()
	{
		if ($this->collSchoolYearI18ns === null) {
			$this->collSchoolYearI18ns = array();
		}
	}

	
	public function getSchoolYearI18ns($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseSchoolYearI18nPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSchoolYearI18ns === null) {
			if ($this->isNew()) {
			   $this->collSchoolYearI18ns = array();
			} else {

				$criteria->add(SchoolYearI18nPeer::ID, $this->getId());

				SchoolYearI18nPeer::addSelectColumns($criteria);
				$this->collSchoolYearI18ns = SchoolYearI18nPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(SchoolYearI18nPeer::ID, $this->getId());

				SchoolYearI18nPeer::addSelectColumns($criteria);
				if (!isset($this->lastSchoolYearI18nCriteria) || !$this->lastSchoolYearI18nCriteria->equals($criteria)) {
					$this->collSchoolYearI18ns = SchoolYearI18nPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSchoolYearI18nCriteria = $criteria;
		return $this->collSchoolYearI18ns;
	}

	
	public function countSchoolYearI18ns($criteria = null, $distinct = false, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseSchoolYearI18nPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(SchoolYearI18nPeer::ID, $this->getId());

		return SchoolYearI18nPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addSchoolYearI18n(SchoolYearI18n $l)
	{
		$this->collSchoolYearI18ns[] = $l;
		$l->setSchoolYear($this);
	}

  public function getCulture()
  {
    return $this->culture;
  }

  public function setCulture($culture)
  {
    $this->culture = $culture;
  }

  public function getName()
  {
    $obj = $this->getCurrentSchoolYearI18n();

    return ($obj ? $obj->getName() : null);
  }

  public function setName($value)
  {
    $this->getCurrentSchoolYearI18n()->setName($value);
  }

  protected $current_i18n = array();

  public function getCurrentSchoolYearI18n()
  {
    if (!isset($this->current_i18n[$this->culture]))
    {
      $obj = SchoolYearI18nPeer::retrieveByPK($this->getId(), $this->culture);
      if ($obj)
      {
        $this->setSchoolYearI18nForCulture($obj, $this->culture);
      }
      else
      {
        $this->setSchoolYearI18nForCulture(new SchoolYearI18n(), $this->culture);
        $this->current_i18n[$this->culture]->setCulture($this->culture);
      }
    }

    return $this->current_i18n[$this->culture];
  }

  public function setSchoolYearI18nForCulture($object, $culture)
  {
    $this->current_i18n[$culture] = $object;
    $this->addSchoolYearI18n($object);
  }


  public function __call($method, $arguments)
  {
    if (!$callable = sfMixer::getCallable('BaseSchoolYear:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method BaseSchoolYear::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }


} 