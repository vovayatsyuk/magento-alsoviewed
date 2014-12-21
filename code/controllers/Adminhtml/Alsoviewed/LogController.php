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
                $result = Mage::getResourceModel('alsoviewed/relation')->updateRelations($data);
                $log->clean();
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) have been updated.', $result)
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
        switch ($this->getRequest()->getActionName()) {
            case 'process':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_log/process');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_log');
                break;
        }
    }
}
