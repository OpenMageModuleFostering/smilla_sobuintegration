<?php


class Smilla_Sobuintegration_Block_Share extends Mage_Core_Block_Template
{

    public function __construct() {

        $this->setOrder(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
    }

    public function getApiKey(){
        return Mage::helper('sobuintegration/data')->getApiKey();

    }

    public function getOrderData(){
        $result = Mage::helper('sobuintegration/data')->getOrderData($this->getOrder());

        return json_encode($result);
    }

    public function getSignatureData($orderdata){
        return Mage::helper('sobuintegration/data')->getDigitalSignature($orderdata);
    }

}