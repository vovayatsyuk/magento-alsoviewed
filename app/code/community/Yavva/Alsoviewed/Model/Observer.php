<?php

class Yavva_Alsoviewed_Model_Observer
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
        $helper = Mage::helper('alsoviewed');
        if ($helper->isIpAddressIgnored() || $helper->isUserAgentIgnored()) {
            return;
        }

        $session   = $this->_getSession();
        $productId = $observer->getControllerAction()->getRequest()->getParam('id');
        $viewedIds = $session->getViewedProductIds();

        if (!$viewedIds) {
            $viewedIds = array();
        }

        if ($productId && !in_array($productId, $viewedIds)) {
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
        $log  = Mage::getResourceModel('alsoviewed/log');
        $data = $log->getGroupedRelations();
        if ($data) {
            try {
                $size  = Mage::getStoreConfig('alsoviewed/perfomance/chunk_size');
                $model = Mage::getResourceModel('alsoviewed/relation');
                foreach (array_chunk($data, $size) as $_data) {
                    $model->updateRelations($_data);
                }
                $log->clean();
            } catch (Zend_Db_Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($alsoviewed = $request->getPost('alsoviewed')) {
            $relations = Mage::helper('adminhtml/js')->decodeGridSerializedInput($alsoviewed['relations']);
            foreach ($relations as $key => $relation) {
                if (!is_numeric($relation['weight'])) {
                    $relations[$key]['weight'] = 1;
                }
                if (!is_numeric($relation['position'])) {
                    $relations[$key]['position'] = 50;
                }
            }
            $product->setAlsoviewedData($relations);
        }
    }

    public function catalogProductSaveAfter($observer)
    {
        $product    = $observer->getEvent()->getProduct();
        $relations  = $product->getAlsoviewedData();
        if (!is_array($relations)) {
            return;
        }

        $collection = Mage::getResourceModel('alsoviewed/relation_collection')
            ->addFieldToFilter('product_id', $product->getId());

        if (!$relations && !$collection->getSize()) {
            return;
        }

        $model = Mage::getResourceModel('alsoviewed/relation');

        // remove relations
        if (!$relations) {
            $relationsToRemove = $collection->getAllIds();
        } else {
            $relationsToRemove = array();
            $relatedProductIds = array_keys($relations);
            foreach ($collection as $relation) {
                if (!in_array($relation->getRelatedProductId(), $relatedProductIds)) {
                    $relationsToRemove[] = $relation->getId();
                }
            }
        }
        if ($relationsToRemove) {
            $model->deleteMultiple($relationsToRemove);
        }

        if (!$relations) {
            return;
        }

        // update relations
        foreach ($relations as $relatedProductId => $values) {
            $relations[$relatedProductId]['product_id'] = $product->getId();
            $relations[$relatedProductId]['related_product_id'] = $relatedProductId;
        }
        $model->saveRelations($relations, true);
    }
}
