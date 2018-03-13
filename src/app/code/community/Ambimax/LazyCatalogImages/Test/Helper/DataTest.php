<?php

class Ambimax_LazyCatalogImages_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    public $urlHelper;

    public function testImageHelperInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Helper_Abstract',
            Mage::helper('ambimax_lazycatalogimages')
        );
    }

    public function setUp()
    {
        $this->urlHelper = Mage::helper('ambimax_lazycatalogimages');
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testRemoteBaseUrl()
    {
        $this->assertSame(
            $this->expected('config')->getBaseUrl(),
            $this->urlHelper->getCdnBaseUrl()
        );
    }

}