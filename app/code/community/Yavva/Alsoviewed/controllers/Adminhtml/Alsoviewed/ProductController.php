<?php

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Yavva_Alsoviewed_Adminhtml_Alsoviewed_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function relationsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.alsoviewed')
            ->setProductsAlsoviewed($this->getRequest()->getPost('products_alsoviewed', null));
        $this->renderLayout();
    }

    public function relationsGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.alsoviewed')
            ->setProductsAlsoviewed($this->getRequest()->getPost('products_alsoviewed', null));
        $this->renderLayout();
    }
}
