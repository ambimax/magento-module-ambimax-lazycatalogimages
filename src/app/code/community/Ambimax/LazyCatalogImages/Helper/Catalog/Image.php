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
        return Mage::getStoreConfigFlag('web/lazycatalogimages/enabled');
    }

    /**
     * @return string
     */
    public function getRemoteBaseUrl()
    {
        return Mage::getStoreConfig('web/lazycatalogimages/base_url');
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

        $this->setImagePath($product->getData($attributeName));

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

        $url = $this->getRemoteBaseUrl();

        // add dimensions
        if ( $this->getWidth() || $this->getHeight() ) {
            $url .= sprintf('%sx%s/', $this->getWidth(), $this->getHeight());
        }

        if ( $this->getImagePath() ) {
            return $url . $this->getImagePath();
        }

        return $url . $this->getPlaceholder();
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
     * Reset all previous data
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _reset()
    {
        $this->_width = null;
        $this->_height = null;

        return parent::_reset();
    }

}