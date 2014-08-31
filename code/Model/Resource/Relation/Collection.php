<?php

class Yavva_AlsoViewed_Model_Resource_Relation_Collection extends Yavva_AlsoViewed_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('alsoviewed/relation');
        $this->setItemObjectClass('Varien_Object');
    }
}
