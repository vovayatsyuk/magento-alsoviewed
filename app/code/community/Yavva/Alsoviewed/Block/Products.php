<?php

class Yavva_Alsoviewed_Block_Products extends Mage_Catalog_Block_Product_Abstract
{
    const DEFAULT_PRODUCTS_COUNT = 4;
    const DEFAULT_IMAGE_WIDTH    = 170;
    const DEFAULT_IMAGE_HEIGHT   = 170;
    const DEFAULT_BASIS_LIMIT    = 10;

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection $_productCollection
     */
    protected $_productCollection = null;

    /**
     * Retrieve alsoviewed products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (null === $this->_productCollection) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            $collection->setVisibility(
                Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()
            );

            $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

            $productIds = $this->_getBasisProductIds();
            $collection
                ->joinTable(
                    array('alsoviewed' => 'alsoviewed/relation'),
                    'related_product_id=entity_id',
                    array(
                        'alsoviewed_weight'   => 'weight',
                        'alsoviewed_position' => 'position',
                    ),
                    array(
                        'product_id' => array('in' => $productIds),
                        'status'     => 1
                    ),
                    'inner'
                )
                ->addAttributeToSort('alsoviewed_position', 'ASC')
                ->addAttributeToSort('alsoviewed_weight', 'DESC');

            if (count($productIds) > 1) {
                $collection->addAttributeToFilter('entity_id', array('nin' => $productIds));
                // prevent "Item with the same id already exist" error
                $collection->getSelect()->group('entity_id');
            }

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve basis product ids to suggest alternative products.
     *
     * @return array
     */
    protected function _getBasisProductIds()
    {
        $ids = $this->_getData('product_id');
        if (null === $ids) {
            $ids = $this->_getBasisModel()->getProductIds();
        } elseif (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve basis model to get product ids that the customer was interested in.
     *
     * @return Yavva_Alsoviewed_Model_Basis
     */
    protected function _getBasisModel()
    {
        $model = Mage::getSingleton('alsoviewed/basis');

        $mode = $this->_getData('basis_mode');
        if (null !== $mode) {
            if (!is_array($mode)) {
                $mode = explode(',', $mode);
            }
            $model->setMode($mode);
        }

        $limit = $this->_getData('basis_limit');
        if (null === $limit) {
            $limit = self::DEFAULT_BASIS_LIMIT;
        }
        $model->setLimit($limit);

        return $model;
    }

    /**
     * Retrieve products count to show
     *
     * @return integer
     */
    public function getProductsCount()
    {
        $count = $this->_getData('products_count');
        if (null === $count) {
            return self::DEFAULT_PRODUCTS_COUNT;
        }
        return $count;
    }

    /**
     * Retrieve image width
     *
     * @return integer
     */
    public function getImageWidth()
    {
        $width = $this->_getData('image_width');
        if (null === $width) {
            return self::DEFAULT_IMAGE_WIDTH;
        }
        return $width;
    }

    /**
     * Retreive image height. This variable is nullable.
     *
     * @return mixed
     */
    public function getImageHeight()
    {
        $height = $this->_getData('image_height');
        if (null === $height) {
            return self::DEFAULT_IMAGE_HEIGHT;
        }
        return $height;
    }

    /**
     * Used to setup the block from the layout file
     *
     * @param [type] $path [description]
     */
    public function addDataFromConfig($path)
    {
        $config = Mage::getStoreConfig($path);
        if (is_array($config)) {
            $this->addData($config);
        }
        return $this;
    }
}
