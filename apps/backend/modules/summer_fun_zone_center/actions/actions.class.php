<?php

/**
 * summer_fun_zone_center actions.
 *
 * @package    kids
 * @subpackage summer_fun_zone_center
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class summer_fun_zone_centerActions extends autosummer_fun_zone_centerActions
{
	private $backend = null;

	public function validateEdit()
	{
		$result = true;
		if ($this->getRequest()->getMethod() == sfRequest::POST) {
			$result = ThairaUploadsValidator::validate();
		}

		$values = $this->getRequestParameter('summer_fun_center');


		$isDiscountPercent = $values['weeks_percent_discount'] > 0 || $values['brothers_percent_discount'] || $values['kids_and_us_student_percent_discount'] > 0;
		$isDiscountAmount = $values['weeks_amount_discount'] > 0 || $values['brothers_amount_discount'] || $values['kids_and_us_student_amount_discount'] > 0;

		if ($isDiscountAmount && $isDiscountPercent) {
			$this->getRequest()->setError('discounts', $this->getContext()->getI18N()->__('wrong_discounts'));
			$result = false;
		}

		return $result;
	}

	protected function getLabels()
	{
		$labels = parent::getLabels();
		$labels['thairaUploads{docs}'] = 'Documents';
		return $labels;
	}

	public function preExecute()
	{
		$user = $this->getUser();

		if (!$user->hasCredential('administrador')) {
            $this->backend = new myBackendSummerFun($this);
        }
	}

	protected function addFiltersCriteria($c)
	{
		$user = $this->getUser();
		if (!$user->hasCredential('administrador')) {
            // JOIN
            $c->addJoin(SummerFunCenterHasProfilePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
            // WHERE
            $c->add(SummerFunCenterHasProfilePeer::PROFILE_ID, $user->getProfile()->getId());
		}

		parent::addFiltersCriteria($c);
	}

}
