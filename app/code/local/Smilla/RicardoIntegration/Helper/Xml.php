<?php
class Smilla_RicardoIntegration_Helper_Xml extends Mage_Core_Helper_Abstract
{
    public function generateProductXml(){
	        	
        $collection = Mage::getModel('catalog/product')
                        ->getCollection()
                        #->addAttributeToFilter('export_ricardo', 1)
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToSelect('*');


        $layout  = Mage::getSingleton('core/layout');
		$block_header = Mage::getSingleton('core/layout')->createBlock(
			'Smilla_RicardoIntegration_Block_Adminhtml_Productxml',
			'productxml',
			array('template' => 'ricardointegration/productxml.phtml')
		);
		$block_header->setCollection($collection);
		
		$xmlresult = $block_header->toHtml();

         
         // Check against XSD File
		 if(!@$this->checkXsd($xmlresult, Mage::getModuleDir('', 'Smilla_RicardoIntegration').DS.'xsd'.DS .'productImport.xsd')){
			 echo "Fehler bei XSD Validierung!";
		 }
		
		return $xmlresult;



    }
    
    private function checkXsd($content, $xsdfilepath){
         $xml= new DOMDocument();
		 $xml->loadXML($content, LIBXML_NOBLANKS); 
	     return $xml->schemaValidate($xsdfilepath);
    }
    
    
}
	 