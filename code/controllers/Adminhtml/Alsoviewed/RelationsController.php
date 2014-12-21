<?php

class Yavva_Alsoviewed_Adminhtml_Alsoviewed_RelationsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/alsoviewed_relations/index')
            ->_addBreadcrumb(
                Mage::helper('alsoviewed')->__('Also Viewed Products'),
                Mage::helper('alsoviewed')->__('Also Viewed Products')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Also Viewed Relations'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('relation_id');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select relation(s).'));
        } else {
            if (!empty($ids)) {
                try {
                    $count = Mage::getResourceModel('alsoviewed/relation')->deleteMultiple($ids);
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', $count)
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ids    = (array) $this->getRequest()->getParam('relation_id');
        $status = (int) $this->getRequest()->getParam('status');

        try {
            Mage::getResourceModel('alsoviewed/relation')->updateMultiple($ids, array(
                'status' => $status
            ));
            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($ids))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the relation(s) status.'));
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
            case 'delete':
            case 'massDelete':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations/delete');
                break;
            case 'save':
            case 'massStatus':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations/save');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations');
                break;
        }
    }
}
