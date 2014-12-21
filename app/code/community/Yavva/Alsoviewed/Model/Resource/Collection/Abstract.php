<?php

abstract class Yavva_Alsoviewed_Model_Resource_Collection_Abstract
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_map = array('fields' => array(
        'product_name'         => 'product_name.value',
        'related_product_name' => 'related_product_name.value'
    ));

    public function addProductNamesToSelect()
    {
        $attribute = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('attribute_code', 'name')
            ->getFirstItem();
        $tableName = Mage::getModel('core/resource')
            ->getTableName('catalog_product_entity_varchar');

        $this->getSelect()->join(
                array('product_name' => $tableName),
                'product_id = product_name.entity_id',
                array('product_name' => 'value')
            )
            ->where('product_name.store_id = ?', 0)
            ->where('product_name.attribute_id = ?', $attribute->getId());

        $this->getSelect()->join(
                array('related_product_name' => $tableName),
                'related_product_id = related_product_name.entity_id',
                array('related_product_name' => 'value')
            )
            ->where('related_product_name.store_id = ?', 0)
            ->where('related_product_name.attribute_id = ?', $attribute->getId());

        return $this;
    }
}
