<?php
$installer = $this;
$installer->startSetup();


$table = $installer->getTable('eav_attribute_option');
$installer->getConnection()->addColumn(
    $table,
    'ricardo_code',
    "VARCHAR( 255 ) NULL"
);

$installer->endSetup();
