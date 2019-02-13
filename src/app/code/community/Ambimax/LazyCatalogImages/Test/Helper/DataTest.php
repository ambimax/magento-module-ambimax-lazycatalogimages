<?php

class Ambimax_LazyCatalogImages_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var array
     */
    protected $_dimensions = [210, 420, 840];

    /**
     * @var Ambimax_LazyCatalogImages_Helper_Data
     */
    public $urlHelper;
    /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image */
    protected $_image;

    public function setUp()
    {
        $this->urlHelper = Mage::helper('ambimax_lazycatalogimages');
        $this->_image = Mage::getModel('ambimax_lazycatalogimages/catalog_image');
    }

    public function testImageHelperInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Helper_Abstract',
            Mage::helper('ambimax_lazycatalogimages')
        );
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