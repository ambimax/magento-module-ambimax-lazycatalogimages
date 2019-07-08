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
        if (!Mage::helper('ambimax_lazycatalogimages')->isEnabled()) {
            return;
        }

        /** @var Varien_Object $observerData */
        $observerData = $observer->getEvent()->getData('data');

        /** @var array $products */
        $products = $observerData->getProducts();

        foreach ($products as $productId => &$product) {

            /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image $image */
            $image = Mage::getModel('ambimax_lazycatalogimages/catalog_image');

            // set product for name
            $image->setProductAttributes(Mage::getModel('catalog/product')->setData($product));

            // reset image url
            if (key_exists('image', $product)) {
                $product['image'] = $image->setWidth(100)->setHeight(100)->getImageUrl($product['image']);
            }
        }

        $observerData->setProducts($products);
    }
}