<?php

class Yavva_AlsoViewed_Model_Resource_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/relation', 'relation_id');
    }

    public function updateRelations($relationsData, $bidirectional = true)
    {
        $data = $relationsData;
        if ($bidirectional) {
            foreach ($relationsData as $relation) {
                $data[] = array(
                    'product_id'         => $relation['related_product_id'],
                    'related_product_id' => $relation['product_id'],
                    'weight'             => $relation['weight'],
                );
            }
        }

        $adapter = $this->_getReadAdapter();
        $adapter->insertOnDuplicate($this->getMainTable(), $data, array(
            'weight' => new Zend_Db_Expr('weight + VALUES(weight)')
        ));
    }
}
