<?php
require_once("Mage/Adminhtml/controllers/Sales/ShipmentController.php"); 
class Smilla_PostChShippingLabel_PostchshippinglabelController extends Mage_Adminhtml_Controller_Sales_Shipment
{
		
	public function singleShippingLabelAction()
    {
        $request = $this->getRequest();
		
        $ids = $request->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($ids);
		
		$labelBinaryData = $this->generateLabel($order);
		$pdf = Zend_Pdf::parse($labelBinaryData);
		
		$this->_prepareDownloadResponse('ShippingLabel-'.$ids.'.pdf', $pdf->render(), 'application/pdf');
		
    }	
	
	public function multipleShippingLabelAction()
    {
        $request = $this->getRequest();
		
        $ids = $request->getParam('order_ids');
		
		if(!$ids){
			$ids = array($request->getParam('order_id'));
		}
		
		$pdf = new Zend_Pdf();  // Initializing the merged PDF
        
		for($i=0;$i<count($ids);$i++){
		
			$order = Mage::getModel('sales/order')->load($ids[$i]);
			
			// Generate Label
			$labelBinaryData = $this->generateLabel($order);
			
			// Copy Page to multipage PDF
			$tmp_pdf = Zend_Pdf::parse($labelBinaryData);
			$tmp_page = clone $tmp_pdf->pages[0];
			$pdf->pages[] = $tmp_page;

		}
		
		$this->_prepareDownloadResponse('ShippingLabels-'.implode('-', $ids).'.pdf', $pdf->render(), 'application/pdf');
		
    }
    
    
  /**
   * Get PDF Label From Post.CH Webservice
   */
  protected function generateLabel($order){
		$tarif = 'ECO';
		
		// SOAP Config
		/* TODO: Datei auslesen */
		$SOAP_wsdl_file_path = 'post/barcode_v2_1.wsdl';
		$SOAP_config = array(
		
			 // Webservice Endpoint URL
		  	 'location' => Mage::getStoreConfig('postchshippinglabel/settings/endpointurl'),
		
			 // Webservice Barcode Login
		  	 'login' => Mage::getStoreConfig('postchshippinglabel/settings/username'),
		     //'password' => Mage::getStoreConfig('postchshippinglabel/settings/password'),
		     'password' => 'SktNuAM2u8'
		);
		
		$SOAP_Client = new SoapClient($SOAP_wsdl_file_path, $SOAP_config);
		
		// Franking License Configuration
		$generateLabelRequest = array(
		    'Language' => 'de',
		    'Envelope' => array(
		       	'LabelDefinition' => array(
			       	'LabelLayout' => Mage::getStoreConfig('postchshippinglabel/advanced_settings/format'),
			        'PrintAddresses' => 'RecipientAndCustomer',
			        'ImageFileType' => 'PDF',
			        'ImageResolution' => 300,
					'PrintPreview' => false
		        ),
		        'FileInfos' => array(
			         'FrankingLicense' => Mage::getStoreConfig('postchshippinglabel/settings/licenseno'),
					 'PpFranking' => false,
			         'Customer' => array(
				         'Name1' => Mage::getStoreConfig('postchshippinglabel/customer/company'),
				         'Street' => Mage::getStoreConfig('postchshippinglabel/customer/street'),
				         'ZIP' => Mage::getStoreConfig('postchshippinglabel/customer/postcode'),
				         'City' => Mage::getStoreConfig('postchshippinglabel/customer/city'),
		      		 ),
					 'CustomerSystem' => 'Magento Labeling Extension by Smilla AG'
		   		),
			    'Data' => array(
			        'Provider' => array(
			            'Sending' => array(
							'Item' => array(
								array(
									'ItemID' => $order->getRealOrderId(),
									'Recipient' => array(
										'Title' => $order->getShippingAddress()->getTitle(),
										'Name1' => $order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname(),
										'Name2' => $order->getShippingAddress()->getCompany(),
										'Street' => implode(", ", $order->getShippingAddress()->getStreet()),
										'ZIP' => substr($order->getShippingAddress()->getPostcode(), 0, 4),
										'City' => $order->getShippingAddress()->getCity(),
										'EMail' =>  $order->getShippingAddress()->getEmail()
									),
									'Attributes' => array(
										'PRZL' => array(
											Mage::getStoreConfig('postchshippinglabel/advanced_settings/code')
										),
										'ProClima' => (Mage::getStoreConfig('postchshippinglabel/advanced_settings/proclima') == 1)
									)
								)
							)
						)
					)
				)
			)
		);
		
		
		// Web service call
		$response = null;
		$response = $SOAP_Client->GenerateLabel($generateLabelRequest);

		$item = $response->Envelope->Data->Provider->Sending->Item;
		if (isset($item->Errors)) {
	      	$errorMessages = "";
	      	$delimiter="";
	      	foreach ($item->Errors as $error) {
	      		$errorMessages .= $delimiter.$error->Message;
	      		$delimiter=",";
	      	}
	      	echo '<p>ERROR for item with itemID='.$item->ItemID.": ".$errorMessages.'.<br/></p>';
	      	die();
	
		}

		// Save Tracking Code (Swiss Post)		
		$identCode = $item->IdentCode;
		$shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
		$shipment_collection->addAttributeToFilter('order_id', $order->getId());
		
		foreach($shipment_collection as $sc) {
		    $shipment = Mage::getModel('sales/order_shipment');
		    $shipment->load($sc->getId());
		 
		    if($shipment->getId() != '') { 
		       $track = Mage::getModel('sales/order_shipment_track')
		                 ->setShipment($shipment)
		                 ->setData('title', 'Bestellung '.$order->getRealOrderId())
		                 ->setData('number', $identCode)
		                 ->setData('carrier_code', 'swisspost')
		                 ->setData('order_id', $shipment->getData('order_id'))
		                 ->save();
		    }
		}

		// Get successfully generated label as binary data:
		return $item->Label;
		

    }

	
	
}