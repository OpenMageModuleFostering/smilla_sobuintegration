<?php

class Smilla_PostChShippingLabel_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	
	protected function _prepareMassaction()
    {
        parent::_prepareMassaction();       
        $this->getMassactionBlock()->addItem('postch_shippinglabel', array(
            'label'=> Mage::helper('sales')->__('Post.CH Labels drucken'),
            'url'  => $this->getUrl('smillaadmin/postchshippinglabel/multipleShippingLabel'),
        ));
 

        return $this;
    }
}