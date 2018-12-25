<?php

class Ambimax_LazyCatalogImages_Helper_Catalog_Image extends Mage_Catalog_Helper_Image
{
    /**
     * @var string
     */
    protected $_imagePath;

    /**
     * @var int|null
     */
    protected $_width;

    /**
     * @var int|null
     */
    protected $_height;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getUrlHelper()->isEnabled();
    }

    /**
     * @return bool
     */
    public function useSquareImages()
    {
        return Mage::getStoreConfigFlag('web/lazycatalogimages/use_square_images');
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Mage_Catalog_Helper_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ( !$this->isEnabled() ) {
            return parent::init($product, $attributeName, $imageFile);
        }

        $this->_reset();
        // set dummy model
        $this->_setModel(new Varien_Object());

        $this->setProduct($product);

        $this->setImagePath($imageFile ? $imageFile : $product->getData($attributeName));

        return $this;
    }

    /**
     * @param int $width
     * @param null $height
     * @return $this|Mage_Catalog_Helper_Image
     */
    public function resize($width, $height = null)
    {
        if ( !$this->isEnabled() ) {
            return parent::resize($width, $height);
        }

        $this->setWidth($width);
        $this->setHeight($height);

        if ( !$height && $this->useSquareImages() ) {
            $this->setHeight($width);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ( !$this->isEnabled() ) {
            return parent::__toString();
        }

        /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image $image */
        $image = Mage::getModel('ambimax_lazycatalogimages/catalog_image');

        return $image
            ->setProductAttributes($this->getProduct())
            ->setImagePath($this->getImagePath())
            ->setWidth($this->getWidth())
            ->setHeight($this->getHeight())
            ->getImageUrl();
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        return $this->_imagePath;
    }

    /**
     * @param string $imagePath
     */
    public function setImagePath($imagePath)
    {
        if ( $imagePath && strlen($imagePath) ) {
            $this->_imagePath = ltrim($imagePath, '/');
        }
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @param int $width
     * @return Ambimax_LazyCatalogImages_Helper_Catalog_Image
     */
    public function setWidth($width)
    {
        if ( $width > 0 ) {
            $this->_width = (int)$width;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @param int $height
     * @return Ambimax_LazyCatalogImages_Helper_Catalog_Image
     */
    public function setHeight($height)
    {
        if ( $height > 0 ) {
            $this->_height = (int)$height;
        }

        return $this;
    }

    /**
     * @return Ambimax_LazyCatalogImages_Helper_Data|Mage_Core_Helper_Abstract
     */
    public function getUrlHelper()
    {
        return Mage::helper('ambimax_lazycatalogimages');
    }

    /**
     * Reset all previous data
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _reset()
    {
        $this->_width = null;
        $this->_height = null;
        $this->_imagePath = null;

        return parent::_reset();
    }

}