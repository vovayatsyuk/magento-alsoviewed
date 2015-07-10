<?php

class Yavva_Alsoviewed_Adminhtml_Alsoviewed_LogController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/alsoviewed_log/index')
            ->_addBreadcrumb(
                Mage::helper('alsoviewed')->__('Also Viewed Products'),
                Mage::helper('alsoviewed')->__('Also Viewed Products')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Also Viewed Log'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function processAction()
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
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) have been updated.', count($data))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'process':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_log/process');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_log');
                break;
        }
    }
}
