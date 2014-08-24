<?php

class Yavva_AlsoViewed_Model_Resource_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/relation', 'relation_id');
    }

    public function updateRelations($relationsData, $bidirectional = true)
    {
        $adapter = $this->_getReadAdapter();
        $adapter->insertOnDuplicate($this->getMainTable(), $relationsData, array(
            'weight' => new Zend_Db_Expr('weight + VALUES(weight)')
        ));

        if ($bidirectional) {
            // swap product_id and related_product_id
        }
    }
}
