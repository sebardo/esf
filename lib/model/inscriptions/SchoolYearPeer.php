<?php

/**
 * Subclass for performing query and update operations on the 'school_year' table.
 *
 * 
 *
 * @package lib.model.inscriptions
 */ 
class SchoolYearPeer extends BaseSchoolYearPeer
{
    /**
     * @return array
     */
    public static function doSelectAllByI18n()
    {
        $c = new Criteria();
        $c->addAscendingOrderByColumn(SchoolYearPeer::ORDEN);
        $result = parent::doSelectWithI18n($c);

        return $result;
    }

    /**
     * @param $id
     * @return null
     */
    public static function getTitleById($id)
    {
        $entity = SchoolYearPeer::doSelectOneById($id);

        return $entity ? $entity->getName() : null;
    }

    /**
     * @param $id
     * @return null
     */
    public static function doSelectOneById($id)
    {
        $c = new Criteria();
        $c->add(self::ID, $id);
        $c->setLimit(1);
        $items = parent::doSelectWithI18n($c);

        return ($items ? $items[0] : null);
    }

    /**
     * @return array
     */
    public static function getArrayCenters()
    {
        $entities = self::doSelectAllByI18n();
        $c = array();

        foreach ($entities as $entity)
        {
            $c[$entity->getId()] = $entity->getName();
        }

        return $c;
    }
}
