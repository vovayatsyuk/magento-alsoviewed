<?php

class Yavva_Alsoviewed_Model_Resource_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/relation', 'relation_id');
    }

    /**
     * Insert new and update existing relations according to $relationsData
     * Unlike the updateMultiple, this method adds saved relation weight
     * with the weight from $relationsData.
     *
     * @param  array   $relationsData   Array of product to product relations:
     *                                  array(
     *                                      array(
     *                                          product_id          => int
     *                                          related_product_id  => int
     *                                          weight              => int
     *                                      )
     *                                      ...
     *                                  )
     * @param  boolean $bidirectional
     * @return int     Number of affected rows
     */
    public function updateRelations($relationsData, $bidirectional = true)
    {
        $data = $relationsData;
        if ($bidirectional) {
            foreach ($relationsData as $relation) {
                $data[] = array(
                    'product_id'         => $relation['related_product_id'],
                    'related_product_id' => $relation['product_id'],
                    'weight'             => $relation['weight']
                );
            }
        }

        $adapter = $this->_getWriteAdapter();
        return $adapter->insertOnDuplicate($this->getMainTable(), $data, array(
            'weight' => new Zend_Db_Expr('weight + VALUES(weight)')
        ));
    }

    /**
     * Update multiple relations at once
     *
     * @param  array $ids
     * @param  array $data  Column => Value pairs
     * @return int          Number of affected rows
     */
    public function updateMultiple($ids, $data)
    {
        $adapter = $this->_getWriteAdapter();
        return $adapter->update($this->getMainTable(), $data, array(
            'relation_id IN (?)' => $ids
        ));
    }

    /**
     * Delete relations by ids
     *
     * @param  array $ids   Relations ids
     * @return int          Number of affected rows
     */
    public function deleteMultiple($ids)
    {
        $adapter = $this->_getWriteAdapter();
        return $adapter->delete($this->getMainTable(), array(
            'relation_id IN (?)' => $ids
        ));
    }
}
