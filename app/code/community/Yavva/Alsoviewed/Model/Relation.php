<?php

class Yavva_Alsoviewed_Model_Relation extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/relation');
    }

    /**
     * Retrieve reverse relation model
     *
     * @return Yavva_Alsoviewed_Model_Relation
     */
    public function getInverseRelation()
    {
        $relation = $this->getData('inverse_relation');
        if (null === $relation) {
            $relation = $this->getCollection()
                ->addFieldToFilter('product_id', $this->getRelatedProductId())
                ->addFieldToFilter('related_product_id', $this->getProductId())
                ->getFirstItem();

            if (!$relation->getId()) {
                $relation->setProductId($this->getRelatedProductId())
                    ->setRelatedProductId($this->getProductId());
            }
            $this->setData('inverse_relation', $relation);
        }
        return $relation;
    }

    /**
     * Retrieve product collection with 2 related products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        $products = $this->getData('products');
        if (null === $products) {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array(
                    'in' => array(
                        $this->getProductId(),
                        $this->getRelatedProductId()
                    )
                ));
            $this->setData('products', $products);
        }
        return $products;
    }

    public function getProduct()
    {
        return $this->getProducts()->getItemById($this->getProductId());
    }

    public function getRelatedProduct()
    {
        return $this->getProducts()->getItemById($this->getRelatedProductId());
    }

    public function getName()
    {
        return $this->getProduct()->getName() . ' - ' . $this->getRelatedProduct()->getName();
    }
}
