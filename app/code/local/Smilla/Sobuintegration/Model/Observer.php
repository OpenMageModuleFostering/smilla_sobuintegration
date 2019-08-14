<?php
class Smilla_Sobuintegration_Model_Observer
{

    public function __construct()
    {

    }

    /**
     * Event: salesrule_rule_condition_combine
     *
     * @param $observer
     */
    public function addConditionToSalesRule($observer)
    {

        $additional = $observer->getAdditional();
        $conditions = (array)$additional->getConditions();

        $conditions = array_merge_recursive($conditions, array(
            array('label' => Mage::helper('sobuintegration')->__('sobu Benefit'), 'value' => 'sobuintegration/condition_benefit'),
        ));

        $additional->setConditions($conditions);
        $observer->setAdditional($additional);

        return $observer;
    }

    /**
     * Register Sale to Sobu
     *
     * @param $observer
     * @return boolean
     */
    public function registerSobuSale(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $clickId = (int) Mage::getSingleton('core/session')->getSobuClickId();

        // Sobu ClickId exists?
        if ($clickId > 0) {
            // Register Sale (Server-2-Server)
            $api_url = 'https://www.sobu.ch/register';
            $postdata = array(
                'apiKey' => Mage::helper('sobuintegration/data')->getApiKey(),
                'orderId' => $order->getRealOrderId(),
                'total' => Mage::helper('sobuintegration/data')->getTotal($order),
                'signature' => Mage::helper('sobuintegration/data')->getDigitalSignature(($order->getRealOrderId()).'#'.Mage::helper('sobuintegration/data')->getTotal($order)),
                'clickId' => $clickId,
            );

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = json_decode(curl_exec($ch));
            curl_close($ch);
            if($result->isError){
                // Error sending Requ est
            } else {
	            // Remove Session Data
	            Mage::getSingleton('core/session')->unsSobuClickId();
	            Mage::getSingleton('core/session')->unsSobuVoucherCode();
            }

        }

        return true;
    }
}