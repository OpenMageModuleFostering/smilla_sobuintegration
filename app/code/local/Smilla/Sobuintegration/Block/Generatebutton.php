<?php 
class Smilla_Sobuintegration_Block_Generatebutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('sobuadmin/admin/generatersa');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel(Mage::helper('sobuintegration')->__('Generate RSA Key'))
                    ->setOnClick("if(confirm('Neuen RSA Schlüssel jetzt generieren? (Der vorhandene wird ggf. überschrieben)')){ setLocation('$url')}")
                    ->toHtml();

        return $html;
    }
}