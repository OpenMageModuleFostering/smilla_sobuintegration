<?php

class Smilla_PostChShippingLabel_Block_View_Postchshippinglabel extends Mage_Adminhtml_Block_Sales_Order_View
{
	
	public function __construct()
	{		
		parent::__construct();
		$this->_addButton('postch_shippinglabel', array(
			'label'     => Mage::helper('sales')->__('Post.CH Label drucken'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('smillaadmin/postchshippinglabel/singleShippingLabel') . '\')',
		));
	}
	
}