<?php

/**
 * Subclass for representing a row from the 'kids_and_us_center' table.
 *
 * 
 *
 * @package lib.model.inscriptions
 */ 
class KidsAndUsCenter extends BaseKidsAndUsCenter
{
    public function __toString()
    {
        return (string) $this->getName();
    }
}