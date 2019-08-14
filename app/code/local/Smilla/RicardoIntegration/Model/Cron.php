<?php
class Smilla_RicardoIntegration_Model_Cron{	
	public function SyncCatalog(){


	    $xmlhelper = Mage::helper('ricardointegration/xml');
	    $sftphelper = Mage::helper('ricardointegration/sftp');
	    $filename = 'products_'.time().'.cron.xml';
	    
	    $log = Mage::getModel("ricardointegration/log")
				->setExecutionTime(date('Y-m-d H:i:s'))
				->setType('Write Product Catalog ('.$filename.'). '.(string) $result)
				->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS) // Mage_Cron_Model_Schedule::STATUS_ERROR
				->save();

	    $result = $sftphelper->writeRemoteFile('/kunden/179933_49716/rp-hosting/2/2/ricardotest/'.$filename, $xmlhelper->generateProductXml());


	} 	
	public function SyncSales(){
		//do something
/*
	    $log = Mage::getModel("ricardointegration/log")
				->setExecutionTime(date('Y-m-d H:i:s'))
				->setType('Read Sale File XXXX.xml')
				->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS) // Mage_Cron_Model_Schedule::STATUS_ERROR
				->save();
			*/	

	} 
}