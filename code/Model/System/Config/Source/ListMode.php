<?php

class Yavva_Alsoviewed_Model_System_Config_Source_ListMode
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'grid', 'label' => Mage::helper('catalog')->__('Grid')),
            array('value' => 'list', 'label' => Mage::helper('catalog')->__('List'))
        );
    }
}
