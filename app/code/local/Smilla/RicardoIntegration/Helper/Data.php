<?php
class Smilla_RicardoIntegration_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * Decorate status values
	 *
	 * @param $status
	 * @return string
	 */
	public function decorateStatus($status) {
		switch ($status) {
			case Mage_Cron_Model_Schedule::STATUS_SUCCESS:
				$result = '<span class="grid-severity-notice"><span>'.$status.'</span></span>';
				break;
			case Mage_Cron_Model_Schedule::STATUS_PENDING:
				$result = '<span class="bar-lightgray"><span>'.$status.'</span></span>';
				break;
			case Mage_Cron_Model_Schedule::STATUS_RUNNING:
				$result = '<span class="bar-yellow"><span>'.$status.'</span></span>';
				break;
			case Mage_Cron_Model_Schedule::STATUS_MISSED:
				$result = '<span class="bar-orange"><span>'.$status.'</span></span>';
				break;
			case Mage_Cron_Model_Schedule::STATUS_ERROR:
				$result = '<span class="grid-severity-critical"><span>'.$status.'</span></span>';
				break;
			default:
				$result = $status;
				break;
		}
		return $result;
	}
}
	 