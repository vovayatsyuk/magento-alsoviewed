<?php

class Yavva_Alsoviewed_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alsoviewed';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = Mage::helper('alsoviewed')->__('Log');

        parent::__construct();

        $this->_removeButton('add');

        if ($this->_isAllowedAction('process')) {
            $this->_addButton('process', array(
                'label'   => Mage::helper('alsoviewed')->__('Process Log'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/process') .'\')'
            ));
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('yavva/alsoviewed/alsoviewed_log/' . $action);
    }
}
