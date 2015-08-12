<?php

class Yavva_Alsoviewed_Block_Adminhtml_Catalog_Product_Edit_Tab_Relations extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('alsoviewed_product_grid');
        $this->setDefaultSort('weight');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }

        $this->setAdditionalJavaScript("
// added click on selectbox support
serializerController.prototype.rowClick = serializerController.prototype.rowClick.wrap(function(o, grid, event) {
    var tagName = Event.element(event).tagName
        isSelect = (tagName == 'SELECT' || tagName == 'OPTION');

    if (!isSelect) {
        o(grid, event);
    }
});
        ");
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $value = $column->getFilter()->getValue();
            if ($value == 1) {
                $this->getCollection()->addFieldToFilter('alsoviewed_relation', array('notnull' => true));
            } elseif ($value == 0) {
                $this->getCollection()->addFieldToFilter('alsoviewed_relation', array('null' => true));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('*');
        $collection->joinTable(
                array('alsoviewed' => 'alsoviewed/relation'),
                'related_product_id=entity_id',
                array(
                    'alsoviewed_relation' => 'relation_id',
                    'alsoviewed_weight'   => 'weight',
                    'alsoviewed_position' => 'position',
                    'alsoviewed_status'   => 'status'
                ),
                array(
                    'product_id' => $this->_getProduct()->getId()
                ),
                'left'
            );

        if ($this->getIsReadonly() === true) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('weight', array(
            'header'    => Mage::helper('sales')->__('Weight'),
            'name'      => 'weight',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'alsoviewed_weight',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId()
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'alsoviewed_position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId()
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'name'      => 'status',
            'type'      => 'select',
            'options'    => array(
                1 => Mage::helper('catalog')->__('Enabled'),
                0 => Mage::helper('catalog')->__('Disabled')
            ),
            'index'     => 'alsoviewed_status',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId()
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/alsoviewed_product/relationsgrid', array('_current'=>true));
    }

    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/alsoviewed_product/relations', array('_current'=>true));
    }

    /**
     * Retrieve selected alsoviewed products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsAlsoviewed();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedAlsoviewedProducts());
        }
        return $products;
    }

    /**
     * Retrieve alsoviewed products
     *
     * @return array
     */
    public function getSelectedAlsoviewedProducts()
    {
        $relations = Mage::getResourceModel('alsoviewed/relation_collection')
            ->addFieldToFilter('product_id', $this->_getProduct()->getId());
        $products = array();
        foreach ($relations as $relation) {
            $products[$relation->getRelatedProductId()] = array(
                'weight'   => $relation->getWeight(),
                'position' => $relation->getPosition(),
                'status'   => $relation->getStatus()
            );
        }
        return $products;
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    public function getTabLabel()
    {
        return Mage::helper('alsoviewed')->__('Also Viewed Products');
    }

    public function getTabTitle()
    {
        return Mage::helper('alsoviewed')->__('Also Viewed Products');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
