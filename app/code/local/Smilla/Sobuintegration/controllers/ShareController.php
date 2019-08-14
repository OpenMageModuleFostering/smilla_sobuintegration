<?php
class Smilla_Sobuintegration_ShareController extends Mage_Core_Controller_Front_Action
{

    public function orderAction()
    {

        $this->loadLayout();
        $this->order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));

        $this->renderLayout();

    }

    public function linkAction()
    {
        $voucherCode = trim($this->getRequest()->getParam('code'));
        $this->session = Mage::getSingleton('checkout/session');

        if(strlen($voucherCode) > 0) {

            // Save Vouchercode to Session
            Mage::getSingleton('core/session')->setSobuVoucherCode($voucherCode);
            // Save ClickId to Session
            Mage::getSingleton('core/session')->setSobuClickId($this->getRequest()->getParam('sobuClickId'));

            if(strlen(Mage::getStoreConfig('sobuintegration/settings/promotion_redirect')) > 0){
                $this->_redirect(Mage::getStoreConfig('sobuintegration/settings/promotion_redirect'));
            } else {
                $this->_redirect('sobu/share/link');
            }
        } else {
            // Show Info Page
            $this->loadLayout();
            $this->renderLayout();
        }

    }

    public function infoAction()
    {
        $this->loadLayout();
        $this->renderLayout();


    }

    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            return true;
        }
        return false;
    }
}