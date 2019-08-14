<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();
             
$setup->addAttribute('catalog_product', 'ricardo_enabled', array(
         'group'    => 'General',
         'label'    => 'Sync to Ricardo?',          
         'type' => 'int',
         'input'    => 'boolean',                  
         'visible'  => true,
         'required' => false,
         'position' => 10000,
         'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
         'note'     => '',
         'default' => true,
         'default_value_yesno' => '1',
));


$installer->endSetup();
	 