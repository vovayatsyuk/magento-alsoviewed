<?php

class Yavva_Alsoviewed_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('alsoviewedLogGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('alsoviewed/log_collection')
            ->addProductNamesToSelect();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'index'  => 'entity_id',
            'width'  => 50,
            'type'   => 'number'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('alsoviewed')->__('Product'),
            'index'  => 'product_name'
        ));

        $this->addColumn('related_product_name', array(
            'header' => Mage::helper('adminhtml')->__('Related Product'),
            'index'  => 'related_product_name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
