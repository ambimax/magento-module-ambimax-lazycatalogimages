<?php

class Ambimax_LazyCatalogImages_Test_Helper_Catalog_ImageTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ambimax_LazyCatalogImages_Helper_Catalog_Image
     */
    protected $_imageHelper;

    /**
     * @param $sku
     * @return Ambimax_LazyCatalogImages_Helper_Catalog_Image
     */
    public function getInitializedImage($sku, $attributeCode = 'small_image')
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        return $this->_imageHelper->init($product, $attributeCode);
    }

    public function setUp()
    {
        /** @var Ambimax_LazyCatalogImages_Helper_Catalog_Image $image */
        $this->_imageHelper = Mage::helper('ambimax_lazycatalogimages/catalog_image');
    }

    public function testImageHelperInstance()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Helper_Image',
            Mage::helper('ambimax_lazycatalogimages/catalog_image')
        );

        $this->assertInstanceOf(
            'Ambimax_LazyCatalogImages_Helper_Catalog_Image',
            Mage::helper('ambimax_lazycatalogimages/catalog_image')
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
            $this->_imageHelper->getRemoteBaseUrl()
        );
    }

    /**
     * @param null $width
     * @param null $height
     * @param string $sku
     *
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @dataProvider dataProvider
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testGetImageUrlWithWidthAndHeight($width = null, $height = null, $sku = 'tshirt-strong-red-xs')
    {
        $image = $this->getInitializedImage($sku);

        $image->resize($width, $height);

        $this->assertSame(
            $this->expected('image_urls')->getData(sprintf('%s-%d-%d', $sku, $width, $height)),
            $image->__toString(),
            sprintf(sprintf('Expected %s-%d-%d url is not identical', $sku, $width, $height))
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testNotSupportedFunctionsDoNotLeadToErrors()
    {
        $image = $this->getInitializedImage('tshirt-strong-red-xs');

        $image->backgroundColor('#fff')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepFrame(true)
            ->keepTransparency(true)
            ->rotate('90')
            ->setQuality(80)
            ->setWatermarkImageOpacity(0.4)
            ->setWatermarkSize(100)
            ->watermark('filename.jpg', 0);
    }

    public function testCatalogImageHelperIsReplaced()
    {
        $this->assertInstanceOf(
            'Ambimax_LazyCatalogImages_Helper_Catalog_Image',
            Mage::helper('catalog/image')
        );
    }

    /**
     * @param null $width
     * @param string $sku
     *
     * @loadFixture ~Ambimax_LazyCatalogImages/square
     * @dataProvider dataProvider
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testGetImageUrlWithWidthAndSquareHeight($width = null, $sku = 'tshirt-strong-red-xs')
    {
        $image = $this->getInitializedImage($sku);

        $image->resize($width);

        $this->assertSame(
            $this->expected('square_image_urls')->getData(sprintf('%s-%d', $sku, $width)),
            $image->__toString()
        );
    }

}