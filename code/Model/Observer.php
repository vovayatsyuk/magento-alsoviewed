<?php

class Yavva_AlsoViewed_Model_Observer
{
    protected function _getSession()
    {
        return Mage::getSingleton('alsoviewed/session');
    }

    /**
     * Add product relations to alsoviewed_log table
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductView(Varien_Event_Observer $observer)
    {
        $session   = $this->_getSession();
        $productId = $observer->getEvent()->getProduct()->getId();
        $viewedIds = $session->getViewedProductIds();

        if (!$viewedIds) {
            $viewedIds = array();
        }

        if (!in_array($productId, $viewedIds)) {
            if (count($viewedIds)) {
                Mage::getResourceModel('alsoviewed/log')->insertRelations(
                    $productId, $viewedIds
                );
            }
            $session->addViewedProductId($productId);
        }
    }

    /**
     * Move generated log records to alsoviewed_relation table
     */
    public function processLog()
    {
        // Take the records from alsoviewed_log and update/insert
        // @todo create bi-directional relations: id1 => id2 and id2 => id1
        // Mage::getResourceModel('alsoviewed/relation')
    }
}
