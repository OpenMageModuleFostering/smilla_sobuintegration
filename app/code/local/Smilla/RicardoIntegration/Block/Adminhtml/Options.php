<?php


class Smilla_RicardoIntegration_Block_Adminhtml_Options extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
{
    public function __construct()
    {
    	
        parent::__construct();
        $this->setTemplate('ricardointegration/attribute/options.phtml');
    }
}