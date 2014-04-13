<?php

class Yavva_AlsoViewed_Block_Products extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection $_productCollection
     */
    protected $_productCollection = null;

    /**
     * @todo replace with AlsoViewed collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (null === $this->_productCollection) {
            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $todayEndOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $collection = Mage::getResourceModel('catalog/product_collection');
            $collection->setVisibility(
                Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()
            );

            $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->addAttributeToFilter('news_from_date', array('or'=> array(
                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter('news_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                        array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
                )
                ->addAttributeToSort('news_from_date', 'desc')
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    public function addDataFromConfig($path)
    {
        $config = Mage::getStoreConfig($path);
        if (is_array($config)) {
            $this->addData($config);
        }
        return $this;
    }
}
