<?php

class Yavva_Alsoviewed_Block_Adminhtml_Relations_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alsoviewed';
        $this->_controller = 'adminhtml_relations';

        parent::__construct();

        if ($this->_isAllowedAction('delete')) {
            $this->_addButton('delete_with_inverse', array(
                'label' => Mage::helper('alsoviewed')->__('Delete with inverse relation'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getDeleteWithInverseUrl() . '\')'
            ), 0);
        }
    }

    public function getDeleteWithInverseUrl()
    {
        return $this->getUrl('*/*/delete', array(
            $this->_objectId   => $this->getRequest()->getParam($this->_objectId),
            'inverse_relation' => 1
        ));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true
        ));
    }

    public function getRelation()
    {
        return Mage::registry('alsoviewed_relation');
    }

    public function getHeaderText()
    {
        if ($this->getRelation()->getId()) {
            return Mage::helper('cms')->__(
                "Edit Relation '%s'",
                $this->escapeHtml($this->getRelation()->getName())
            );
        }
        return Mage::helper('alsoviewed')->__('New Relation');
    }

    /**
    * Check permission for passed action
    *
    * @param string $action
    * @return bool
    */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_relations/' . $action);
    }
}
