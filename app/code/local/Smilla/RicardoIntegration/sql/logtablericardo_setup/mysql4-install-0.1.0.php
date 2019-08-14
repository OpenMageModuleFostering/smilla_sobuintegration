<?php
$installer = $this;
$installer->startSetup();


$installer->run("
DROP TABLE IF EXISTS ".$this->getTable('ricardolog').";
CREATE TABLE ".$this->getTable('ricardolog')." (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL default '',
  `execution_time` datetime NULL,
  `status` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    ");

        
$installer->endSetup();
