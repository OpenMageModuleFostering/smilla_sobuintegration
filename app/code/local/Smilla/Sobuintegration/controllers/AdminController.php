<?php

class Smilla_Sobuintegration_AdminController extends Mage_Adminhtml_Controller_Sales_Shipment
{
		
	public function generatersaAction()
    {
        // generate 2048-bit RSA key
		$pkGenerate = openssl_pkey_new(array(
		    'private_key_bits' => 2048,
		    'private_key_type' => OPENSSL_KEYTYPE_RSA
		));
		 
		// get the private key
		openssl_pkey_export($pkGenerate,$pkGeneratePrivate); // NOTE: second argument is passed by reference
		 
		// get the public key
		$pkGenerateDetails = openssl_pkey_get_details($pkGenerate);
		$pkGeneratePublic = $pkGenerateDetails['key'];
		 
		// free resources
		openssl_pkey_free($pkGenerate);
		
		// Save keys to Config
		$config = new Mage_Core_Model_Config();
		$config->saveConfig('sobuintegration/settings/publickey', $pkGeneratePublic, 'default', 0);
		$config->saveConfig('sobuintegration/settings/privatekey', $pkGeneratePrivate, 'default', 0);
		#echo Mage::getStoreConfig('sobuintegration/settings/privatekey');
		
		// Redirect
		$this->_redirect('adminhtml/system_config/edit/section/sobuintegration');

		 
    }	
	

	
	
}