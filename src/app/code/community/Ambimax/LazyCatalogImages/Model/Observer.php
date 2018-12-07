<?php

class Ambimax_LazyCatalogImages_Model_Observer
{
    /**
     * Observer for module Wyomind ElasticSearch setting image url into search index
     *
     * $indexer = $observer->getIndexer();
     * $store = $observer->getStore();
     * $products = $observer->getProducts();
     *
     * @param Varien_Event_Observer $observer
     */
    public function addImageUrlToElasticSearchIndex(Varien_Event_Observer $observer)
    {
        /** @var Ambimax_LazyCatalogImages_Helper_Data $lazyCatalogImages */
        $lazyCatalogImages = Mage::helper('ambimax_lazycatalogimages');

        if (!$lazyCatalogImages->isEnabled()) {
            return;
        }

        /** @var Varien_Object $observerData */
        $observerData = $observer->getEvent()->getData('data');

        /** @var array $products */
        $products = $observerData->getProducts();

        foreach ($products as $productId => &$product) {

            // set product for name
            $lazyCatalogImages->setProduct(Mage::getModel('catalog/product')->setData($product));

            // reset image url
            $product['image'] = $lazyCatalogImages->getImageUrl($product['image']);
        }

        $observerData->setProducts($products);
    }
}