<?php

class Yavva_Alsoviewed_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Initialize session namespace
     */
    public function __construct()
    {
        $this->init('alsoviewed');
    }

    public function addViewedProductId($id)
    {
        $ids   = (array) $this->getViewedProductIds();
        $limit = $this->_getViewedLimit();

        while (count($ids) >= $limit) {
            unset($ids[0]);
            $ids = array_values($ids);
        }
        $ids[] = $id;

        $this->setViewedProductIds($ids);
        return $this;
    }

    protected function _getViewedLimit()
    {
        return 10;
    }
}
