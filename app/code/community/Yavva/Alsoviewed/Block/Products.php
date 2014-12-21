<?php

class Yavva_Alsoviewed_Block_Products extends Mage_Catalog_Block_Product_Abstract
{
    const DEFAULT_PRODUCTS_COUNT = 4;
    const DEFAULT_IMAGE_WIDTH    = 170;
    const DEFAULT_IMAGE_HEIGHT   = 170;

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

            $collection
                ->joinTable(
                    array('alsoviewed' => 'alsoviewed/relation'),
                    'related_product_id=entity_id',
                    array(
                        'alsoviewed_weight'   => 'weight',
                        'alsoviewed_position' => 'position',
                    ),
                    array(
                        'product_id' => $this->getProductId(),
                        'status'     => 1
                    ),
                    'inner'
                )
                ->addAttributeToSort('alsoviewed_position', 'ASC')
                ->addAttributeToSort('alsoviewed_weight', 'DESC');

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve product id
     *
     * @return integer
     */
    public function getProductId()
    {
        $id = $this->_getData('product_id');
        if (null === $id) {
            $product = Mage::registry('current_product');
            if ($product) {
                $id = $product->getId();
            }
        }
        return $id;
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
