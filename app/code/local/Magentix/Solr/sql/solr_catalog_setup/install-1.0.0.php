<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('catalogsearch/search_query'),
    'suggestion',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'after'     => 'synonym_for',
        'comment'   => 'Suggested query due to a misspell'
    )
);

$installer->endSetup();
