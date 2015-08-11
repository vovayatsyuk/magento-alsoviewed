<?php

class Yavva_Alsoviewed_Block_Adminhtml_Relations extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alsoviewed';
        $this->_controller = 'adminhtml_relations';
        $this->_headerText = Mage::helper('alsoviewed')->__('Relations');

        parent::__construct();

        $this->_removeButton('add');
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
