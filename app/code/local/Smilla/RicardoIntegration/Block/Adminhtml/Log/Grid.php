<?php

class Smilla_RicardoIntegration_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("logGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("ricardointegration/log")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
					"header" => Mage::helper("ricardointegration")->__("ID"),
					"align" =>"right",
					"width" => "50px",
				    "type" => "number",
					"index" => "id",
				));
				
				$this->addColumn("execution_time", array(
					"header" => Mage::helper("ricardointegration")->__("Execution Time"),
					"index" => "execution_time",
					"type" => "datetime",
				));
                
				$this->addColumn('type', array(
						'header'    => Mage::helper('ricardointegration')->__('Type'),
						'index'     => 'type',
				));
				
				$this->addColumn("status", array(
					"header" => Mage::helper("ricardointegration")->__("Status"),
					"index" => "status",
					"type" => "options",
					"frame_callback" => array(Mage::helper('ricardointegration'), 'decorateStatus'),
					"options" => array(
						Mage_Cron_Model_Schedule::STATUS_PENDING => Mage_Cron_Model_Schedule::STATUS_PENDING,
						Mage_Cron_Model_Schedule::STATUS_SUCCESS => Mage_Cron_Model_Schedule::STATUS_SUCCESS,
						Mage_Cron_Model_Schedule::STATUS_ERROR => Mage_Cron_Model_Schedule::STATUS_ERROR,
						Mage_Cron_Model_Schedule::STATUS_MISSED => Mage_Cron_Model_Schedule::STATUS_MISSED,
						Mage_Cron_Model_Schedule::STATUS_RUNNING => Mage_Cron_Model_Schedule::STATUS_RUNNING,
					)
				));
				
				//$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		

}