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
            array('label' => Mage::helper('sobuintegration')->__('Sobu Benefit'), 'value' => 'sobuintegration/condition_benefit'),
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
        $clickId = (int)Mage::getSingleton("checkout/session")->getData("sobu_clickid");

        // Sobu ClickId exists?
        if ($clickId > 0) {
            // Register Sale (Server-2-Server=
            $api_url = 'https://www.sobu.ch/register';
            $postdata = array(
                'apiKey' => Mage::helper('sobuintegration/data')->getApiKey(),
                'orderId' => $order->getRealOrderId(),
                'total' => Mage::helper('sobuintegration/data')->getTotal($order),
                'signature' => Mage::helper('sobuintegration/data')->getDigitalSignature($order->getRealOrderId().'#'.Mage::helper('sobuintegration/data')->getTotal($order)),
                'clickId' => $clickId,
            );

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = json_decode(curl_exec($ch));
            curl_close($ch);

            if($result->isError){
                // Error sending Request
            }

            // Remove Session Data
            Mage::getSingleton("checkout/session")->unsetData("sobu_vouchercode");
            Mage::getSingleton("checkout/session")->unsetData("sobu_clickid");

        }

        return true;
    }
}