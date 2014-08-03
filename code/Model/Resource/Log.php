<?php

class Yavva_AlsoViewed_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/log', 'entity_id');
    }

    /**
     * @param  int $id      Product Id
     * @param  array $ids   Related Product Ids
     * @return Yavva_AlsoViewed_Model_Resource_Log
     */
    public function insertRelations($id, $ids)
    {
        $insertData = array();
        foreach ($ids as $relatedId) {
            // All relations are bidirectional, so I can use the min and max to
            // prevent duplicated relations in grouped by product_id columns query
            // @see getGroupedRelations method
            $insertData[] = array(
                'product_id'         => min($id, $relatedId),
                'related_product_id' => max($id, $relatedId)
            );
        }
        $this->_getWriteAdapter()->insertMultiple(
            $this->getMainTable(), $insertData
        );

        return $this;
    }
}