<?php
class Smilla_RicardoIntegration_Helper_Sftp extends Mage_Core_Helper_Abstract
{
    public function writeRemoteFile($remoteFilename, $content){
		require_once('lib'.DS .'phpseclib'.DS .'Net'.DS .'SFTP.php');
		
		// SFTP
		$sftp = new Net_SFTP('www.ncn.eu');
		if (!$sftp->login('ssh-649954-ncn', 'q2w3e4r')) {
		    exit('Login Failed');
		}
		#$attributesString = Mage::getStoreConfig('ricardosettings/catalog/attributes', Mage::app()->getStore());

		return $sftp->put($remoteFilename, $content); 
	
	}
}
	 