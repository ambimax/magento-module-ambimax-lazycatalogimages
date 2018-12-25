<?php

class Ambimax_LazyCatalogImages_Test_Model_Catalog_ImageTest extends EcomDev_PHPUnit_Test_Case
{
    /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image */
    protected $_image;

    public function setUp()
    {
        $this->_image = Mage::getModel('ambimax_lazycatalogimages/catalog_image');
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     */
    public function testImageUrlWithOriginalSize()
    {
        $path = 'Kategorien/Bilder/Katze.png';

        $this->assertSame(
            'https://xxx.cdn-server.com/Katze/Kategorien/Bilder/unknown.png',
            $this->_image->getImageUrl($path)
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     *
     * @dataProvider dataProvider
     */
    public function testImageUrlWithDimensions($path, $width, $height, $expectation)
    {
        $this->_image
            ->setImagePath($path)
            ->setWidth($width)
            ->setHeight($height);

        $this->assertSame(
            $expectation,
            $this->_image->getImageUrl()
        );
    }

    public function testGetImageNameNotSet()
    {
        $this->assertSame(
            'unknown',
            $this->_image->getImageName()
        );
    }

    public function testGetImageNameSet()
    {
        $this->_image->setImageName('red-car');

        $this->assertSame(
            'red-car',
            $this->_image->getImageName()
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     */
    public function testGetCdnBaseUrl()
    {
        $this->assertSame(
            'https://xxx.cdn-server.com',
            $this->_image->getCdnBaseUrl()
        );
    }

    public function testGetAllowedImageExtensions()
    {
        $this->assertSame(
            ['jpg', 'jpeg', 'png', 'gif'],
            $this->_image->getAllowedImageExtensions()
        );
    }

    /**
     * @param $imagePath
     * @param $expectation
     *
     * @dataProvider dataProvider
     */
    public function testSetImagePath($imagePath, $expectation)
    {
        $this->_image->setImagePath($imagePath);

        $this->assertSame(
            $expectation['path'],
            $this->_image->getPath(),
            $imagePath . ' has no valid path'
        );

        $this->assertSame(
            $expectation['identifier'],
            $this->_image->getIdentifier(),
            $imagePath . ' has no valid identifier'
        );

        $this->assertSame(
            $expectation['extension'],
            $this->_image->getExtension()
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDimensions($width, $height, $expectation)
    {
        $this->_image
            ->setWidth($width)
            ->setHeight($height);

        $this->assertSame(
            $expectation,
            $this->_image->getDefaultDimensions()
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     */
    public function testGetPlaceholderUrl()
    {
        $this->assertSame(
            'https://xxx.cdn-server.com/placeholder/default.jpg',
            $this->_image->getPlaceholderUrl()
        );
    }

    /**
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testSetDimensions()
    {
        $dimensions = [
            ['width' => 200, 'height' => 200],
            ['width' => 250, 'height' => 250],
            '300x400',
        ];

        $this->_image->setDimensions($dimensions);

        $this->assertSame(
            $this->expected('catalog_image')->getSetDimensions(),
            $this->_image->getDimensions()
        );
    }

    /**
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testAddDimension()
    {
        $this->_image
            ->addDimension(['width' => 100, 'height' => 100])
            ->addDimension(200, 200)
            ->addDimension(250)
            ->addDimension(null, 280)
            ->addDimension(['height' => 300])
            ->addDimension(['width' => 320])
            ->addDimension('400x400')
            ->addDimension('420x')
            ->addDimension('x440');

        $this->assertSame(
            $this->expected('catalog_image')->getAddDimensions(),
            $this->_image->getDimensions()
        );
    }

    public function testResetDimensions()
    {
        $this->_image
            ->addDimension('200x200')
            ->resetDimensions()
            ->addDimension('300x300');

        $this->assertSame(
            ['300x300' => ['width' => 300, 'height' => 300]],
            $this->_image->getDimensions()
        );
    }

    public function testRemoveDimension()
    {
        $this->_image
            ->addDimension('200x200')
            ->addDimension('300x300')
            ->removeDimension('200x200');

        $this->assertSame(
            ['300x300' => ['width' => 300, 'height' => 300]],
            $this->_image->getDimensions()
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testGetSrcSet()
    {
        $this->_image
            ->setImagePath('Kategorien/Bilder/Katze.png')
            ->setImageName('test')
            ->setWidth(120)
            ->setHeight(120)
            ->addDimension('200x200')
            ->addDimension('400x400');

        $this->assertSame(
            $this->expected('catalog_image')->getSrcSet(),
            $this->_image->getSrcSet()
        );

        $this->assertSame(
            'https://xxx.cdn-server.com/Katze/120x120/Kategorien/Bilder/test.png',
            $this->_image->getImageUrl()
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testGetSrcSetHtml()
    {
        $this->_image
            ->setImagePath('Kategorien/Bilder/Katze.png')
            ->setImageName('test')
            ->setWidth(120)
            ->setHeight(120)
            ->addSize('(min-width: 650px) 50vw')
            ->addSize('100vw')
            ->resetDimensions()
            ->addDimension('200x200')
            ->addDimension('400x400')
            ->addHtmlAttribute('alt', 'Katzen & mehr');

        $tags = [
            'class' => 'srcset',
        ];

        $this->assertSame(
            $this->expected('catalog_image')->getSrcSetHtml(),
            $this->_image->getSrcSetHtml($tags)
        );
    }

    /**
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testAddSize()
    {
        $this->_image
            ->addSize('(min-width: 650px) 50vw')
            ->addSize('100vw');

        $this->assertSame(
            $this->expected('catalog_image')->getAddSize(),
            $this->_image->getSizes()
        );
    }

    /**
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testResetSizes()
    {
        $this->_image
            ->addSize('200vw')
            ->resetSizes()
            ->addSize('(min-width: 650px) 50vw')
            ->addSize('100vw');

        $this->assertSame(
            $this->expected('catalog_image')->getAddSize(),
            $this->_image->getSizes()
        );
    }

    /**
     * @loadExpectation ~Ambimax_LazyCatalogImages/default
     */
    public function testRemoveSize()
    {
        $this->_image
            ->addSize('200vw')
            ->addSize('(min-width: 650px) 50vw')
            ->addSize('100vw')
            ->removeSize('200vw');

        $this->assertEquals(
            $this->expected('catalog_image')->getAddSize(),
            $this->_image->getSizes()
        );
    }

    public function testAddHtmlTags()
    {
        $this->_image
            ->addHtmlAttribute('class', 'text')
            ->addHtmlAttribute('alt', 'Test')
            ->removeHtmlAttribute('class');

        $this->assertSame(
            ['alt' => 'Test'],
            $this->_image->getHtmlAttributes()
        );
    }

    public function testSetProductAttributes()
    {
        $product = Mage::getModel('catalog/product');

        $product->addData([
            'name' => 'A unique product name',
            'small_image' => 'Dog/snacks.jpg',
        ]);

        $this->_image->setProductAttributes($product);

        $this->assertSame(
            'A unique product name',
            $this->_image->getHtmlAttributeValue('alt')
        );

        $this->assertSame(
            'A unique product name',
            $this->_image->getImageName()
        );

        $this->assertSame(
            'snacks/Dog/A-unique-product-name.jpg',
            $this->_image->getImageUrl()
        );
    }

    public function testSetCategoryAttributes()
    {
        $category = Mage::getModel('catalog/category');
        $category->addData([
            'name' => 'A unique category name',
            'thumbnail' => 'Bilder/Katze.png'
        ]);

        $this->_image->setCategoryAttributes($category);

        $this->assertSame(
            'A unique category name',
            $this->_image->getHtmlAttributeValue('alt')
        );

        $this->assertSame(
            'A unique category name',
            $this->_image->getImageName()
        );

        $this->assertSame(
            'Katze/Bilder/A-unique-category-name.png',
            $this->_image->getImageUrl()
        );
    }

    /**
     * @loadFixture ~Ambimax_LazyCatalogImages/default
     */
    public function testImageOptions()
    {
        $this->_image
            ->setImagePath('Kategorien/Bilder/Katze.png')
            ->setImageName('test')
            ->setWidth(120)
            ->setHeight(120)
            ->setTransparency();

        $this->assertSame(
            'https://xxx.cdn-server.com/Katze/120x120,t/Kategorien/Bilder/test.png',
            $this->_image->getImageUrl()
        );
    }

    public function testImageOptionsWhenDisabled()
    {
        $this->_image
            ->setImagePath('Kategorien/Bilder/Katze.png')
            ->setImageName('test')
            ->setWidth(120)
            ->setHeight(120)
            ->setTransparency();

        $this->assertSame(
            'Katze/120x120/Kategorien/Bilder/test.png',
            $this->_image->getImageUrl()
        );
    }
}