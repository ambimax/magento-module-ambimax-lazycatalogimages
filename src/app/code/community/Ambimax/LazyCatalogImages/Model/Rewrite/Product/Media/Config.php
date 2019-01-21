<?php

class Ambimax_LazyCatalogImages_Model_Rewrite_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    public function getMediaUrl($file)
    {
        if (!Mage::helper('ambimax_lazycatalogimages')->isEnabled()) {
            return parent::getMediaUrl($file);
        }

        /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image $image */
        $image = Mage::getModel('ambimax_lazycatalogimages/catalog_image');

        if ($product = Mage::registry('current_product')) {
            $image->setProductAttributes($product);
        }

        return $image->getImageUrl($file);
    }
}