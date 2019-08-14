<?php

class Smilla_Sobuintegration_Helper_Data extends Mage_Core_Helper_Abstract
{   
	public function getOrderData($order){

        $storeViews = Mage::getModel('core/store')
            ->getCollection()
            ->setLoadDefault(true);

        $result = array(
            "id" => $order->getRealOrderId(),
            "items" => array()
        );

        // Items
        foreach($order->getItemsCollection() as $item){


            if(!$item->getParentItem()){
	            $product = Mage::getModel('catalog/product')->load(($item->getParentItem()) ? $item->getParentItem()->getProductId() : $item->getProductId());

                $itemarray = array(
                    "quantity" => (int) $item->getQtyOrdered(),
                    "product" => array(
                        "id" => $product->getSku(),
                        "editable" => true,
                        "languageVersions" => array( ),
                        "image" => $product->getImageUrl()
                    )
                );

                // Get all Labels from Store views
                foreach($storeViews as $storeId => $store) {
                    $locale = substr(Mage::getStoreConfig('general/locale/code', $storeId), 0, 2);

                    // Only supported localizations
                    if(in_array($locale, array('de', 'en', 'fr', 'it'))){
                        Mage::app()->setCurrentStore($storeId);

                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $itemarray["product"]["languageVersions"][$locale] = array(
                            "name" => $product->getName(),
                            "message" => sprintf(Mage::helper('sobuintegration')->__('Ich habe gerade das Produkt "%s" gekauft.'), $product->getName()),
                        );
                    }
                }

                $result['items'][] = $itemarray;

            }

        }

        return $result;
    }

    public function getApiKey(){
        if(Mage::getStoreConfig('sobuintegration/settings/testmode') == 1){
            return Mage::getStoreConfig('sobuintegration/settings/testapikey');
        } else {
            return Mage::getStoreConfig('sobuintegration/settings/apikey');
        }

    }

    public function getTotal($order){
        $currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getShortName();
        return number_format($order->getGrandTotal(), 2).$currency;
    }

    /**
     * Returns the digital signature for the passed string
     * @param string $sData
     * @return string hexadezimal coded RSA signature of the SHA1 hash of the string
     */
    public function getDigitalSignature($sData)
    {

        Mage::getStoreConfig('sobuintegration/settings/privatekey');
        $sPKeyID = openssl_get_privatekey(Mage::getStoreConfig('sobuintegration/settings/privatekey'));
        // calculate the signature
        openssl_sign($sData, $sSignature, $sPKeyID, OPENSSL_ALGO_SHA1);
        // remove key from memory
        openssl_free_key($sPKeyID);

        return $this->getHEXConvertedSignature($sSignature);
    }

    /**
     * Returns a hex encoded string
     * @param string $sSignature
     * @return string
     */
    private function getHEXConvertedSignature($sSignature)
    {
        $sFinalSingature = '';
        for($i=0,$ii=strlen($sSignature); $i<$ii; $i++)
        {
            $sChar = $sSignature[$i];
            $sFinalSingature .= str_pad(dechex(ord($sChar)), 2, 0, STR_PAD_LEFT);
        }


        return $sFinalSingature;
    }

}