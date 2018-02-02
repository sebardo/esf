<?php

/**
 * Subclass for representing a row from the 'school_year' table.
 *
 * 
 *
 * @package lib.model.inscriptions
 */ 
class SchoolYear extends BaseSchoolYear
{
    public function __toString()
    {
        $user = sfContext::getInstance()->getUser();
        if (!$this->getCulture()) {
            $this->setCulture($user->getCulture());
        }

        return $this->getName();
    }
}
