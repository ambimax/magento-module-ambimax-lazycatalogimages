<?php

class Ambimax_LazyCatalogImages_Helper_Rewrite_Enhancedgrid extends TBT_Enhancedgrid_Helper_Data
{
    const DEFAULT_WIDTH = 75;
    const DEFAULT_HEIGHT = 75;

    /**
     * @param $filename
     * @return bool|string
     */
    public function getImageUrl($filename)
    {
        /** @var Ambimax_LazyCatalogImages_Model_Catalog_Image $responsiveImage */
        $responsiveImage = Mage::getModel('ambimax_lazycatalogimages/catalog_image');

        return $responsiveImage
            ->setWidth($this->getImageWidth())
            ->setHeight($this->getImageHeight())
            ->getImageUrl($filename);
    }

    /**
     * @param null $filename
     * @return bool
     */
    public function getFileExists($filename = null)
    {
        return true;
    }

    /**
     * @return int|mixed
     */
    public function getImageWidth()
    {
        return $this->_getDimensionValue(
            Mage::getStoreConfig('enhancedgrid/images/width'),
            self::DEFAULT_WIDTH
        );
    }

    /**
     * @return int|mixed
     */
    public function getImageHeight()
    {
        return $this->_getDimensionValue(
            Mage::getStoreConfig('enhancedgrid/images/height'),
            self::DEFAULT_HEIGHT
        );
    }

    /**
     * @param $size
     * @return int
     */
    protected function _getDimensionValue($size, $default)
    {
        return $size > 0 && $size < 200 ? $size : $default;
    }
}