<?php
class Smilla_RicardoIntegration_Adminhtml_RicardointegrationbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
    
	    $xmlhelper = Mage::helper('ricardointegration/xml');
	    $sftphelper = Mage::helper('ricardointegration/sftp');
	    $xmlcontent = $xmlhelper->generateProductXml();
	    #$sftphelper->writeRemoteFile('/kunden/179933_49716/rp-hosting/2/2/ricardotest/products_'.time().'.xml', $xmlhelper->generateProductXml());


	    $orderxml = simplexml_load_file('orders_example.xml');

	    $transaction = Mage::getModel('core/resource_transaction');
		$storeId = 1;
		$groupId = 1;
		$currency = 'CHF';
		
		

		
    
    
		$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
		
		$order = Mage::getModel('sales/order')
			->setIncrementId($reservedOrderId)
			->setStoreId($storeId)
			->setQuoteId(0)
			->setGlobal_currency_code($currency)
			->setBase_currency_code($currency)
			->setStore_currency_code($currency)
			->setRicardo_customerid($orderxml->order->customerId)
			->setRicardo_ordernumber($orderxml->order->orderNumber)
			->setOrder_currency_code($currency);
			//Set your store currency USD or any other
		
		// set Customer data
		$order->setCustomerPrefix($orderxml->order->paymentAddress->title)
			->setCustomerFirstname($orderxml->order->paymentAddress->firstName)
			#->setCustomer_email('hu@smilla.com')
			->setCustomerLastname($orderxml->order->paymentAddress->lastName)
			->setCustomerGroupId($groupId)
			->setCustomer_is_guest(1);
		
		// set Billing Address
		$billingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
			->setPrefix($orderxml->order->paymentAddress->title)
			->setFirstname($orderxml->order->paymentAddress->firstName)
			#->setMiddlename('')
			->setLastname($orderxml->order->paymentAddress->lastName)
			#->setCompany('smilla')
			->setStreet($orderxml->order->paymentAddress->streetName.' '.$orderxml->order->paymentAddress->streetNumber)
			->setPostcode($orderxml->order->paymentAddress->zip)
			->setCity($orderxml->order->paymentAddress->city)
			->setCountry_id($orderxml->order->paymentAddress->country)
			#->setRegion($billing->getRegion())
			#->setRegion_id($billing->getRegionId())
			#->setTelephone('1234435')
			->setFax('');
		$order->setBillingAddress($billingAddress);
		
		$shippingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
			->setPrefix($orderxml->order->deliveryAddress->title)
			->setFirstname($orderxml->order->deliveryAddress->firstName)
			#->setMiddlename('')
			->setLastname($orderxml->order->deliveryAddress->lastName)
			#->setCompany('smilla')
			->setStreet($orderxml->order->deliveryAddress->streetName.' '.$orderxml->order->deliveryAddress->streetNumber)
			->setPostcode($orderxml->order->deliveryAddress->zip)
			->setCity($orderxml->order->deliveryAddress->city)
			->setCountry_id($orderxml->order->deliveryAddress->country)
			#->setRegion($shipping->getRegion())
			#->setRegion_id($shipping->getRegionId())
			#->setTelephone('1234435')
			->setFax('');
		
		$order->setShippingAddress($shippingAddress)
			->setShipping_method('flatrate_flatrate')
			->setCollectShippingRates(false);
			/*->setShippingDescription($this->getCarrierName('flatrate'));*/
			/*some error i am getting here need to solve further*/
		
		//you can set your payment method name here as per your need
		$orderPayment = Mage::getModel('sales/order_payment')
			->setStoreId($storeId)
			->setCustomerPaymentId(0)
			->setMethod('purchaseorder')
			->setPo_number($orderxml->order->paymentOrderNumber);
		$order->setPayment($orderPayment);
		
		// let say, we have 2 products
		//check that your products exists
		//need to add code for configurable products if any
		$subTotal = 0;
		$products = array(
		    '5' => array(
		    'qty' => 2
		    ),
		    '6' => array(
		    'qty' => 1
		    )
		);
		
		foreach ($products as $productId=>$product) {
			$_product = Mage::getModel('catalog/product')->load($productId);
			$rowTotal = $_product->getPrice() * $product['qty'];
			$orderItem = Mage::getModel('sales/order_item')
			->setStoreId($storeId)
			->setQuoteItemId(0)
			->setQuoteParentItemId(NULL)
			->setProductId($productId)
			->setProductType($_product->getTypeId())
			->setQtyBackordered(NULL)
			#->setTotalQtyOrdered($product['rqty'])
			->setTaxPercent(8)
			->setTaxAmount(5.67)
			->setQtyOrdered($product['qty'])
			->setName($_product->getName())
			->setSku($_product->getSku())
			->setPrice($_product->getPrice())
			->setBasePrice($_product->getPrice() / 1.19)
			->setOriginalPrice($_product->getPrice())
			->setRowTotal($rowTotal * $product['qty'])
			->setBaseRowTotal($rowTotal * $product['qty'] / 1.19)#
			
			;
			

			$subTotal += $rowTotal;
			$order->addItem($orderItem);
		}
		
		$order->setSubtotal($orderxml->order->totalPrice / 1.08)
			->setBaseSubtotal($orderxml->order->totalPrice / 1.08)
			->setTaxAmount($orderxml->order->totalPrice * 0.08)
			->setBaseTaxAmount($orderxml->order->totalPrice * 0.08)
			->setShippingAmount((double) $orderxml->order->deliveryCosts)
			->setBaseShippingAmount((double) $orderxml->order->deliveryCosts)
			->setGrandTotal((double) $orderxml->order->totalPrice + (double) $orderxml->order->deliveryCosts)
			->setBaseGrandTotal((double) $orderxml->order->totalPrice + (double) $orderxml->order->deliveryCosts);
		
		#$order->setCustomerNote('Comment duummy');
		
		#$order->collectShippingRates();
		
		$transaction->addObject($order);
		$transaction->addCommitCallback(array($order, 'place'));
		$transaction->addCommitCallback(array($order, 'save'));
		$transaction->save();
echo "Order created";
	    #$this->loadLayout();
		#$this->_title($this->__("Ricardo Integration"));
		#$this->renderLayout();
    }
    
 	public function productsAction()
    {
    
	    $xmlhelper = Mage::helper('ricardointegration/xml');
	    $xmlcontent = $xmlhelper->generateProductXml();

	    $this->getResponse()->setHeader('Content-type', 'application/xml');
	    $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename=products_'.time().'.xml');
	    $this->getResponse()->setBody($xmlcontent);

	}

}