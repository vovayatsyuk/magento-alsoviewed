<?php

class Yavva_Alsoviewed_Model_Basis extends Varien_Object
{
    const MODE_CURRENT_PRODUCT   = 'current';
    const MODE_RECENTLY_VIEWED   = 'viewed';
    const MODE_RECENTLY_COMPARED = 'compared';
    const MODE_SHOPPING_CART     = 'cart';

    /**
     * Retreive mode to use to recieve product ids basis
     *
     * @return array
     */
    public function getMode()
    {
        $mode = $this->_getData('mode');
        if (null === $mode || !is_array($mode)) {
            return $mode ? array($mode) : array(self::MODE_CURRENT_PRODUCT);
        }
        return $mode;
    }

    /**
     * Retrieve product ids that where recently viewed, compared, added to cart, etc.
     *
     * @return mixed array|null
     */
    public function getProductIds()
    {
        $ids = array();
        foreach ($this->getMode() as $mode) {
            $method = 'get' . ucfirst($mode) . 'Ids';
            if (!method_exists($this, $method)) {
                continue;
            }
            // @TODO make mode much more flexibe (create logic rules):
            //  current|compared|viewed,cart - use current|if none was found - use compared|and so on...
            //  compared:cart|cart|viewed - items from cart that where compared recently|cart (if none was found)| viewed (if none was found)
            //  compared,cart|viewed - items from cart and compared items|viewed if none was found

            // @TODO collect ids by groups and then slice each group proportionally
            $ids = array_merge($ids, $this->{$method}());
        }
        $ids = array_unique($ids);

        if (!count($ids)) {
            return null;
        }

        if (($limit = $this->getLimit()) && (count($ids) > $limit)) {
            $ids = array_slice($ids, -$limit);
        }
        return $ids;
    }

    /**
     * Retrieve currently viewed product id
     *
     * @return array
     */
    public function getCurrentIds()
    {
        $product = Mage::registry('current_product');
        if ($product) {
            return array($product->getId());
        }

        // FPC compatibility
        $request = Mage::app()->getRequest();
        $fullActionName = implode('_', array(
            $request->getModuleName(),
            $request->getControllerName(),
            $request->getActionName()
        ));
        if ('catalog_product_view' === $fullActionName) {
            return array($request->getParam('id'));
        }

        // third party modules
        if ($id = $request->getParam('product_id')) {
            return array($id);
        }

        return array();
    }

    /**
     * Retrieve recently compared product ids
     *
     * @return array
     */
    public function getComparedIds()
    {
        $collection = Mage::getModel('reports/product_index_compared')->getCollection()
            ->addPriceData()
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize(5);

        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSiteFilterToCollection($collection);

        return $collection->getColumnValues('entity_id');
    }

    /**
     * Retrieve recently viewed product ids
     *
     * @return array
     */
    public function getViewedIds()
    {
        $collection = Mage::getModel('reports/product_index_viewed')->getCollection()
            ->addPriceData()
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize(5);

        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSiteFilterToCollection($collection);

        return $collection->getColumnValues('entity_id');
    }

    /**
     * Retrieve product ids from shopping cart
     *
     * @return array
     */
    public function getCartIds()
    {
        return Mage::getSingleton('checkout/cart')->getProductIds();
    }
}
