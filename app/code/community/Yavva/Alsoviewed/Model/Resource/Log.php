<?php

class Yavva_Alsoviewed_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/log', 'entity_id');
    }

    /**
     * @param  int $id      Product Id
     * @param  array $ids   Related Product Ids
     * @return Yavva_Alsoviewed_Model_Resource_Log
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

    /**
     * Retrieve product relations with weight
     *
     * @return array
     */
    public function getGroupedRelations()
    {
        $this->cleanOrphans();

        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable(), array(
                'product_id',
                'related_product_id',
                'weight' => 'COUNT(entity_id)'
            ))
            ->group(array('product_id', 'related_product_id'));

        return $adapter->fetchAll($select);
    }

    /**
     * Remove records from table
     *
     * @param array $where
     * @return Number of affected rows
     */
    public function clean($where = '')
    {
        return $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
    }

    /**
     * Remove orphans records from table
     *
     * @return int Number of deleted records
     */
    public function cleanOrphans()
    {
        $tableName = Mage::getModel('core/resource')
            ->getTableName('catalog_product_entity');

        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select();

        $select->from($this->getMainTable(), 'entity_id')
            ->joinLeft(
                array('e' => $tableName),
                'product_id = e.entity_id',
                array()
            )
            ->orWhere('e.entity_id IS NULL');

        $select->joinLeft(
                array('e2' => $tableName),
                'related_product_id = e2.entity_id',
                array()
            )
            ->orWhere('e2.entity_id IS NULL');

        $ids = $adapter->fetchCol($select);
        if (!$ids) {
            return 0;
        }
        return $this->clean(array('entity_id IN (?)' => $ids));
    }
}
