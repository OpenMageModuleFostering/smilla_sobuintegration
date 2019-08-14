<?php
class Smilla_RicardoIntegration_Model_Entity_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    

    /**
     *  Save attribute options
     *
     * @param Mage_Eav_Model_Entity_Attribute $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $adapter            = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('eav/attribute_option');
            $optionValueTable   = $this->getTable('eav/attribute_option_value');

            $stores = Mage::app()->getStores(true);
            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $adapter->delete($optionTable, array('option_id = ?' => $intOptionId));
                        }
                        continue;
                    }
/* changes start */
                    $sortOrder = !empty($option['order'][$optionId]) ? $option['order'][$optionId] : 0;
                    $ricardo_code = !empty($option['ricardo_code'][$optionId]) ? $option['ricardo_code'][$optionId] : '';
                    
                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'  => $object->getId(),
                           'sort_order'    => $sortOrder,
                           'ricardo_code'    => $ricardo_code
                        );
                        $adapter->insert($optionTable, $data);
                        $intOptionId = $adapter->lastInsertId($optionTable);
                    } else {
                        $data  = array('sort_order'    => $sortOrder, 'ricardo_code'    => $ricardo_code);
                        $where = array('option_id =?' => $intOptionId);
                        $adapter->update($optionTable, $data, $where);
                    }
/* changes end */
                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } elseif ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }

                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                    }

                    $adapter->delete($optionValueTable, array('option_id =?' => $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()])
                            && (!empty($values[$store->getId()])
                            || $values[$store->getId()] == "0")
                        ) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            $adapter->insert($optionValueTable, $data);
                        }
                    }
                }
                $bind  = array('default_value' => implode(',', $attributeDefaultValue));
                $where = array('attribute_id =?' => $object->getId());
                $adapter->update($this->getMainTable(), $bind, $where);
            }
        }

        return $this;
    }


}
