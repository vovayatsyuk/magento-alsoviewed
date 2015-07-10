<?php

class Yavva_Alsoviewed_Adminhtml_Alsoviewed_RelationsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/alsoviewed_relations/index')
            ->_addBreadcrumb(
                $this->__('Also Viewed Products'),
                $this->__('Also Viewed Products')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Also Viewed Relations'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Also Viewed Relations'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('alsoviewed/relation');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('This relation no longer exists')
                );
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Adding new relation from backend is not supported')
            );
            $this->_redirect('*/*/');
            return;
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_title($id ? $this->__('Edit Relation') : $this->__('New Relation'));

        Mage::register('alsoviewed_relation', $model);
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? $this->__('Edit Relation') : $this->__('New Relation'),
                $id ? $this->__('Edit Relation') : $this->__('New Relation')
            );
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $inverseRelation = isset($data['inverse_relation']);
            unset($data['inverse_relation']);

            $model = Mage::getModel('alsoviewed/relation');
            $model->addData($data);
            try {
                $model->save();
                if ($inverseRelation) {
                    $model->getInverseRelation()
                        ->addData(array(
                            'weight'   => $data['weight'],
                            'position' => $data['position'],
                            'status'   => $data['status']
                        ))
                        ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('alsoviewed')->__('Relation was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('relation_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('alsoviewed/relation');
                $model->load($id);
                if ($model->getId() && $this->getRequest()->getParam('inverse_relation')) {
                    $inverse = $model->getInverseRelation();
                    if ($inverse->getId()) {
                        $inverse->delete();
                    }
                }
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('alsoviewed')->__('Relation was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('alsoviewed')->__('Unable to find a relation to delete'));
        $this->_redirect('*/*/');
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
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'delete':
            case 'massdelete':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations/delete');
                break;
            case 'save':
            case 'massstatus':
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations/save');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations');
                break;
        }
    }
}
