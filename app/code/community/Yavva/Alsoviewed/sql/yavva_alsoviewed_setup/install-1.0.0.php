<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('alsoviewed/relation'))
    ->addColumn('relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Relation ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product ID')
    ->addColumn('related_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Related Product ID')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Relation Weight')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '50',
        ), 'Custom Sort Order Parameter')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Status')
    ->addIndex(
        $installer->getIdxName(
            'alsoviewed/relation',
            array('product_id', 'related_product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('product_id', 'related_product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName('alsoviewed/relation', array('product_id')),
        array('product_id'))
    ->addIndex(
        $installer->getIdxName('alsoviewed/relation', array('weight', 'position')),
        array('weight', 'position'))
    ->addIndex(
        $installer->getIdxName('alsoviewed/relation', array('status')),
        array('status'))
    ->addForeignKey($installer->getFkName('alsoviewed/relation', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('alsoviewed/relation', 'related_product_id', 'catalog/product', 'entity_id'),
        'related_product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Alsoviewed Relations Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('alsoviewed/log'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product ID')
    ->addColumn('related_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Related Product ID')
    ->setComment('Alsoviewed Relations Log Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
