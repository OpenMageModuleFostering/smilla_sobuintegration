<?php
class Smilla_Sobuintegration_Model_Condition_Benefit extends Mage_Rule_Model_Condition_Abstract
{

    /**
     *
     * @return Smilla_Sobuintegration_Model_Condition_Benefit
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
            'sobu_code' => Mage::helper('sobuintegration')->__('Sobu Benefit Code')
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
      *
     * @return mixed
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     *
     * @return string
     */
    public function getInputType()
    {

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Validate Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        if($this->getAttribute() == 'sobu_code' && $this->getValue() == Mage::getSingleton("checkout/session")->getData("sobu_vouchercode")){
            return true;
        } else {
            return false;
        }

    }
}