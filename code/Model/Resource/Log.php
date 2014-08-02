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
            $insertData[] = array(
                'product_id' => $id,
                'related_product_id' => $relatedId
            );
            // bi-directional relations will be generated during log processing
            // @see Yavva_AlsoViewed_Model_Observer::processLog
        }
        $this->_getWriteAdapter()->insertMultiple(
            $this->getMainTable(), $insertData
        );

        return $this;
    }
}