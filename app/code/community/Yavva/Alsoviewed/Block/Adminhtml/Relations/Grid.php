<?php

class Yavva_Alsoviewed_Block_Adminhtml_Relations_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('alsoviewedRelationsGrid');
        $this->setDefaultSort('weight');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('alsoviewed/relation_collection')
            ->addProductNamesToSelect();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('relation_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'index'  => 'relation_id',
            'width'  => 50,
            'type'   => 'number'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Product'),
            'index'  => 'product_name'
        ));

        $this->addColumn('related_product_name', array(
            'header' => Mage::helper('adminhtml')->__('Related Product'),
            'index'  => 'related_product_name'
        ));

        $this->addColumn('weight', array(
            'header' => Mage::helper('sales')->__('Weight'),
            'index'  => 'weight',
            'width'  => 50,
            'type'   => 'number'
        ));

        $this->addColumn('position', array(
            'header' => Mage::helper('adminhtml')->__('Position'),
            'index'  => 'position',
            'width'  => 50,
            'type'   => 'number'
        ));

        $this->addColumn('status', array(
            'header'  => Mage::helper('cms')->__('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'width'   => 150,
            'options' => array(
                0 => Mage::helper('cms')->__('Disabled'),
                1 => Mage::helper('cms')->__('Enabled')
            )
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
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('relation_id');
        $this->getMassactionBlock()->setFormFieldName('relation_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'   => Mage::helper('adminhtml')->__('Delete'),
             'url'     => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url'   => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('catalog')->__('Status'),
                    'values' => array(
                        0 => Mage::helper('cms')->__('Disabled'),
                        1 => Mage::helper('cms')->__('Enabled')
                    )
                )
            )
        ));

        return $this;
    }
}
