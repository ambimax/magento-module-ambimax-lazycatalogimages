<?php

class Ambimax_LazyCatalogImages_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('web/lazycatalogimages/enabled');
    }

}