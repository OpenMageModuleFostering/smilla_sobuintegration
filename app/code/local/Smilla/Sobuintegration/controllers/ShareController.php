<?php
class Smilla_Sobuintegration_ShareController extends Mage_Core_Controller_Front_Action
{

    public function orderAction()
    {

        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        $this->loadLayout();
        $this->order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));

        if (!$this->_canViewOrder($this->order)) {
            // Not authorized for this order
            $this->_redirect('customer/account');
        }


        $this->renderLayout();

    }

    public function linkAction()
    {
        $voucherCode = trim($this->getRequest()->getParam('code'));
        $this->session = Mage::getSingleton('checkout/session');

        if(strlen($voucherCode) > 0) {

            // Save Vouchercode to Session
            Mage::getSingleton("checkout/session")->setData("sobu_vouchercode", $voucherCode);
            // Save ClickId to Session
            Mage::getSingleton("checkout/session")->setData("sobu_clickid", $this->getRequest()->getParam('sobuClickId'));

            $this->session->addSuccess(
                Mage::helper('sobuintegration')->__('Sobu Benefit was successfully applied')
            );

        } else {
            $this->session->addError(
                Mage::helper('sobuintegration')->__('Link not valid')
            );
        }
        $this->_redirect("/");

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