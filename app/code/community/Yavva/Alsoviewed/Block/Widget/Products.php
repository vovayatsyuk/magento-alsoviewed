<?php

class Yavva_Alsoviewed_Block_Widget_Products extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{
    protected function _beforeToHtml()
    {
        $list = $this->getLayout()->createBlock('alsoviewed/products')
            ->setTemplate('yavva/alsoviewed/products.phtml');

        if ($template = $this->_getData('template')) {
            $list->setTemplate($template);
        }

        $data = $this->getData();
        unset($data['type']);
        unset($data['module_name']);
        $list->addData($data);

        if (!$this->getIsWrapperDisabled()) {
            $this->mainBlock = $this->getLayout()->createBlock('core/template')
                ->setTemplate('yavva/alsoviewed/wrapper/block.phtml');

            if ($template = $this->_getData('wrapper_template')) {
                $this->mainBlock->setTemplate($template);
            }

            $this->mainBlock->setChild('alsoviewed.list', $list);
        } else {
            $this->mainBlock = $list;
        }
    }

    protected function _toHtml()
    {
        return $this->mainBlock->toHtml();
    }
}