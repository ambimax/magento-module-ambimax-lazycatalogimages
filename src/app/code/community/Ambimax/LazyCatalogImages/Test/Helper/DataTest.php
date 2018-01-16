<?php

class Ambimax_LazyCatalogImages_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    public function testImageHelperInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Helper_Abstract',
            Mage::helper('ambimax_lazycatalogimages')
        );
    }
}