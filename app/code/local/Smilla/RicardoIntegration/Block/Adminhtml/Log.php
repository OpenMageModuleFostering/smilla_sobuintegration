<?php


class Smilla_RicardoIntegration_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_log";
	$this->_blockGroup = "ricardointegration";
	$this->_headerText = Mage::helper("ricardointegration")->__("Log Manager");
	$this->_addButtonLabel = Mage::helper("ricardointegration")->__("Add New Item");
	

   $data = array(
           'label' =>  'Download Product XML',
           'onclick'   => "window.open('".$this->getUrl('ricardointegration/adminhtml_ricardointegrationbackend/products')."')"
           );
   Mage_Adminhtml_Block_Widget_Container::addButton('download_to_mas', $data, 0, 100,  'header', 'header');

       
       
       
	parent::__construct();
	$this->_removeButton('add');
	}

}