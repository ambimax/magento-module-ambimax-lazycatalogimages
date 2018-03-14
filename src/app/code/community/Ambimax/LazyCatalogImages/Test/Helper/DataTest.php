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

    public function setUp()
    {
        $this->urlHelper = Mage::helper('ambimax_lazycatalogimages');
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

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testSrcSet()
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', 'tshirt-strong-red-xs');

        $this->assertSame(
            $this->expected('srcset')->getHtml(),
            $this->urlHelper->getSrcSet($product, $this->_dimensions)
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testSrcSetWithTags()
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', 'tshirt-strong-red-xs');

        $this->assertSame(
            $this->expected('srcset')->getHtmlWithTags(),
            $this->urlHelper->getSrcSet($product, $this->_dimensions, ['alt' => 'Alternative Text'], 'small_image')
        );
    }
}