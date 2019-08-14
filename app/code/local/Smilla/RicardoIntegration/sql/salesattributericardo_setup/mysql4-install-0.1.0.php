<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("quote", "ricardo_ordernumber", array("type"=>"varchar", 'visible' => true));
$installer->addAttribute("quote", "ricardo_customerid", array("type"=>"varchar", 'visible' => true));

# add to order ?

$installer->endSetup();
	 