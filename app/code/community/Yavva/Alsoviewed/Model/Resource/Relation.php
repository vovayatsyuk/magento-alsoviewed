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
     * Insert new and update existing relations according to $relationsData
     * Unlike updateRelations, this method does not respect previous value
     * of the weight column of the duplicate relation.
     *
     * @param  array  $relationsData
     * @param  boolean $bidirectional
     * @return int     Number of affected rows
     */
    public function saveRelations($relationsData, $bidirectional = true)
    {
        $data = $relationsData;

        if ($bidirectional) {
            foreach ($relationsData as $relation) {
                $_tmp = $relation;
                $_tmp['product_id']         = $relation['related_product_id'];
                $_tmp['related_product_id'] = $relation['product_id'];
                $data[] = $_tmp;
            }
        }

        $adapter = $this->_getWriteAdapter();
        return $adapter->insertOnDuplicate($this->getMainTable(), $data, array(
            'weight'   => new Zend_Db_Expr('VALUES(weight)'),
            'position' => new Zend_Db_Expr('VALUES(position)'),
            'status'   => new Zend_Db_Expr('VALUES(status)')
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
