<?php

class Ambimax_LazyCatalogImages_Model_Rewrite_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    public function getMediaUrl($file)
    {
        /** @var Ambimax_LazyCatalogImages_Helper_Data $helper */
        $helper = Mage::helper('ambimax_lazycatalogimages');
        if ( $helper->isEnabled() ) {
            return $helper->getImageUrl($file);
        }

        return parent::getMediaUrl($file);
    }
}