<?php  

class Smilla_RicardoIntegration_Block_Adminhtml_Productxml extends Mage_Adminhtml_Block_Template {
	public function getRicardoCategories($product){
		if(count($product->getCategoryIds()) == 0){
			return array();
		}
	
		 $result = array();
		 $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                     ->addAttributeToSelect('name')
                     ->addAttributeToSelect('ricardo_category')
                     ->addAttributeToFilter('entity_id', $product->getCategoryIds())
                     ->addIsActiveFilter();
                     
         foreach($categoryCollection as $category){
         	if(strlen(trim($category->getRicardoCategory())) > 0){
         		$result[] = $category;
         	}
         }         
      
         
         return $result;
         
	}
	
	public function getAdditionalAttributes($product){
	
		// Read Attributemapping from Configuration
		$attributesString = Mage::getStoreConfig('ricardosettings/catalog/attributes', Mage::app()->getStore());
		
		$mappedAttributes = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $attributesString) as $attributesMapping){
			list($magentoAttribute, $ricardoAttribute) = explode('>', $attributesMapping);
			$mappedAttributes[trim($magentoAttribute)] = trim($ricardoAttribute);
    	} 
    	
    	$result = array();
    	
    	// Read Magento Product attributes
    	foreach($product->getAttributes() as $attribute){
    		if(array_key_exists($attribute->getAttributeCode(), $mappedAttributes)){
	    		$ricardoAttribute = $mappedAttributes[$attribute->getAttributeCode()];
	    		$result[$ricardoAttribute] = $attribute;
    		}
    	}
    	
    	

		return $result;
	}
	
	public function getParentProduct($_product){
		$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
		if(isset($parentIds[0])){
				return $_configurable_product = Mage::getModel('catalog/product')->load($parentIds[0]);
		} else {
				return false;
		}
	}
	
	public function getAttributeValue($_product, $attribute_code){
		if(strlen($ricardocode = $_product->getAttributeRicardoCode($attribute_code)) > 0){
			// Ricardo Code Value
			$result = $ricardocode;
		} else {
			// Dropdown text
			if(strlen($dropdownvalue = $_product->getAttributeText($attribute_code)) > 0){
				 $result = $dropdownvalue; 
			} else {
				if(strlen($value = $_product->getData($attribute_code)) > 0 || !$this->getParentProduct($_product)){
					 $result = $value;
				} else {
					 $result = $this->getParentProduct()->getData($attribute_code);
				}				
			}
		}
		return $result;
	}
	
	/*
	<?php $ricardocode = $_product->getAttributeRicardoCode($attribute->getAttributeCode()); ?>
	   		
	    		(strlen($ricardocode) > 0) ? $ricardocode : $_product->getAttributeText($attribute->getAttributeCode()); ?>
	    		<?php $value = (strlen($value) == 0 && $_configurable_product) ? $_configurable_product->getData($attribute->getAttributeCode()) : $value; ?>
	*/
	
	public function getBundlingId($product){

		$attributeCode = Mage::getStoreConfig('ricardosettings/catalog/bundleattribute', Mage::app()->getStore());	
		return $product->getData($attributeCode);
	}
	
	public function getLocalizedProduct($product){
		$storeViews = Mage::getModel('core/store')
            ->getCollection()
            ->setLoadDefault(true);
        $result = array();
        foreach($storeViews as $storeId => $store) {
        	$locale = substr(Mage::getStoreConfig('general/locale/code', $storeId), 0, 2);
        	$result[$locale] = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
        }
        return $result;
	}
}