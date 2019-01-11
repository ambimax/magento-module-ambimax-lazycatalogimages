<?php

class Ambimax_LazyCatalogImages_Model_Picture
{
    /**
     * @var array
     */
    protected $_sources = [];

    /**
     * @var Ambimax_LazyCatalogImages_Model_Catalog_Image|null
     */
    protected $_defaultImage;

    /**
     * @param Ambimax_LazyCatalogImages_Model_Catalog_Image $source
     * @return $this
     */
    public function addSource(Ambimax_LazyCatalogImages_Model_Catalog_Image $source)
    {
        if (!$this->_defaultImage) {
            $this->setDefaultImage($source);
        }

        $this->_sources[] = $source;
        return $this;
    }

    /**
     * @return array
     */
    public function getSources()
    {
        return $this->_sources;
    }

    /**
     * @param Ambimax_LazyCatalogImages_Model_Catalog_Image $image
     * @return $this
     */
    public function setDefaultImage(Ambimax_LazyCatalogImages_Model_Catalog_Image $image)
    {
        $this->_defaultImage = $image;
        return $this;
    }

    /**
     * @return Ambimax_LazyCatalogImages_Model_Catalog_Image|null
     */
    public function getDefaultImage()
    {
        return $this->_defaultImage;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $generateWebp = Mage::getStoreConfigFlag('web/lazycatalogimages/generate_webp');

        $output = [];
        foreach ($this->getSources() as $source) {

            if ($generateWebp && preg_match('/(\.jpg)/i', $source->getImageUrl())) {
                $webp = clone $source;
                $webp->setExtension('webp');
                $webp->addHtmlAttribute('type', 'image/webp');
                $output[] = $webp->getPictureSourceHtml();
            }

            $output[] = $source->getPictureSourceHtml();
        }

        $output[] = $this->getDefaultImage()->getImageHtml();

        $output = array_unique($output);

        return sprintf("<picture>\n%s\n</picture>", implode(PHP_EOL, $output));
    }
}