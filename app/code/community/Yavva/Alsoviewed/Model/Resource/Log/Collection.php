<?php

class Yavva_Alsoviewed_Model_Resource_Log_Collection extends Yavva_Alsoviewed_Model_Resource_Collection_Abstract
{
    protected $_map = array('fields' => array(
        'entity_id'            => 'main_table.entity_id',
        'product_name'         => 'product_name.value',
        'related_product_name' => 'related_product_name.value'
    ));

    protected function _construct()
    {
        $this->_init('alsoviewed/log');
        $this->setItemObjectClass('Varien_Object');
    }
}
