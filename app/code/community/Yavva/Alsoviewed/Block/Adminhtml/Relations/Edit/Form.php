<?php

class Yavva_Alsoviewed_Block_Adminhtml_Relations_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('relation_form');
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('alsoviewed_relation');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('relation_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('cms')->__('General Information'),
            'class'  => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('relation_id', 'hidden', array(
                'name' => 'relation_id'
            ));
            $fieldset->addField('product_id', 'hidden', array(
                'name' => 'product_id'
            ));
            $fieldset->addField('related_product_id', 'hidden', array(
                'name' => 'related_product_id'
            ));
        }

        $product = $model->getProduct();
        $relatedProduct = $model->getRelatedProduct();
        $fieldset->addField('relation', 'note', array(
            'label' => Mage::helper('alsoviewed')->__('Relation'),
            'text' => sprintf(
                '<a href="%s" onclick="this.target=\'blank\'">%s</a> - <a href="%s" onclick="this.target=\'blank\'">%s</a>',
                $this->getUrl('*/catalog_product/edit', array('id' => $product->getId())),
                $product->getName(),
                $this->getUrl('*/catalog_product/edit', array('id' => $relatedProduct->getId())),
                $relatedProduct->getName()
            )
        ));

        $fieldset->addField('position', 'text', array(
            'label' => Mage::helper('catalog')->__('Position'),
            'title' => Mage::helper('catalog')->__('Position'),
            'name'  => 'position',
            'note'  => Mage::helper('alsoviewed')->__(
                'Position is used to sort products manually'
            ),
            'required' => true
        ));

        $fieldset->addField('weight', 'text', array(
            'label' => Mage::helper('sales')->__('Weight'),
            'title' => Mage::helper('sales')->__('Weight'),
            'name'  => 'weight',
            'note'  => Mage::helper('alsoviewed')->__(
                'Weight is automatically increased by module. It is highly recommended to not to change it, to see the actual popular relations'
            ),
            'required' => true
        ));

        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('cms')->__('Status'),
            'title'    => Mage::helper('cms')->__('Status'),
            'name'     => 'status',
            'required' => true,
            'options'  => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '0' => Mage::helper('cms')->__('Disabled')
            )
        ));
        if (!$model->getId()) {
            $model->setData('status', '1');
        }

        $fieldset->addField('inverse_relation', 'checkbox', array(
            'label' => Mage::helper('alsoviewed')->__('Apply the same values to the inverse relation'),
            'title' => Mage::helper('alsoviewed')->__('Apply the same values to the inverse relation'),
            'name'  => 'inverse_relation',
            'value' => 1
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
