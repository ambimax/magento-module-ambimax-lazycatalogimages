<?php

/**
 * Class Ambimax_LazyCatalogImages_Model_Catalog_Image
 *
 * @method $this setHeight($height)
 * @method $this setWidth($width)
 * @method $this setImageName($imageName)
 */
class Ambimax_LazyCatalogImages_Model_Catalog_Image extends Varien_Object
{
    /** @var array */
    protected $_allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    /** @var array */
    protected $_dimensions = [];

    /** @var array */
    protected $_sizes = [];

    /** @var array */
    protected $_htmlAttributes = [];

    /** @var bool */
    protected $_transparency = false;

    /**
     * @return array
     */
    public function getAllowedImageExtensions()
    {
        return $this->_allowedImageExtensions;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setImagePath($path)
    {
        $path = trim($path, '/');
        $pattern = '/(.*)\/(.*)\.(' . implode('|', $this->getAllowedImageExtensions()) . ')$/i';
        if (preg_match($pattern, $path, $matches)) {
            list($empty, $path, $identifier, $extension) = $matches;
            $this->setPath($path);
            $this->setIdentifier($identifier);
            $this->setExtension($extension);
        }

        $pattern = '/(.*)\.(' . implode('|', $this->getAllowedImageExtensions()) . ')$/i';
        if (preg_match($pattern, $path, $matches)) {
            list($empty, $identifier, $extension) = $matches;
            $this->setIdentifier($identifier);
            $this->setExtension($extension);
        }

        return $this;
    }

    /**
     * @param null $path
     * @return string
     */
    public function getImageUrl($path = null)
    {
        if (!is_null($path)) {
            $this->setImagePath($path);
        }

        if (!$this->hasIdentifier()) {
            return $this->getPlaceholderUrl();
        }

        $url = [
            'host' => $this->getCdnBaseUrl(),
            'identifier' => $this->getIdentifier(),
            'options' => $this->getImageOptions(),
            'path' => $this->getPath(),
            'filename' => $this->getFilename(),
        ];

        return $this->_parseArrayToUrl($url);
    }

    /**
     * @return string
     */
    public function getPlaceholderUrl()
    {
        $url = [
            'host' => $this->getCdnBaseUrl(),
            'identifier' => 'placeholder',
            'options' => $this->getImageOptions(),
            'path' => '',
            'filename' => 'default.jpg',
        ];

        return $this->_parseArrayToUrl($url);
    }

    protected function _parseArrayToUrl($urlArray)
    {
        foreach ($urlArray as &$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
        }

        return implode('/', array_filter($urlArray));
    }

    /**
     * @return array|string|null
     */
    public function getImageOptions()
    {
        if (!Mage::getStoreConfigFlag('web/lazycatalogimages/support_image_options')) {
            return $this->getDefaultDimensions();
        }

        $options = [
            $this->getDefaultDimensions(),
            $this->getTransparency() ? 't' : null,
        ];

        foreach ($options as $k => $value) {
            if (is_null($value) || empty($value)) {
                unset($options[$k]);
            }
        }

        return $options;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        $path = trim($this->_getData('path'), '/');
        return !$this->hasData('path') || empty($path) ? null : $path;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->formatUrlKey(sprintf('%s.%s', $this->getImageName(), $this->getExtension()));
    }

    /**
     * @return string|null
     */
    public function getDefaultDimensions()
    {
        $width = $this->getWidth() > 0 ? $this->getWidth() : '';
        $height = $this->getHeight() > 0 ? $this->getHeight() : '';

        if (empty($width) && empty($height)) {
            return null;
        }

        return sprintf('%sx%s', $width, $height);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->hasData('width') ? (int)$this->_getData('width') : null;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->hasData('height') ? (int)$this->_getData('height') : null;
    }

    /**
     * @return string|null
     */
    public function getCdnBaseUrl()
    {
        return rtrim(Mage::getStoreConfig('web/lazycatalogimages/base_url'), '/');
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        if ($name = $this->_getData('image_name')) {
            return $name;
        }

        return 'unknown';
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-zA-Z\.]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * @return $this
     */
    public function resetDimensions()
    {
        $this->_dimensions = [];
        return $this;
    }

    /**
     * @param array $dimensions
     * @return $this
     */
    public function setDimensions(array $dimensions)
    {
        $this->resetDimensions();

        foreach ($dimensions as $dimension) {
            $this->addDimension($dimension);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        return $this->_dimensions;
    }

    /**
     * @param mixed $width
     * @param int|null $height
     *
     * @return $this
     */
    public function addDimension($width = null, $height = null)
    {
        if (isset($width['height'])) {
            $height = $width['height'];
        }

        if (isset($width['width'])) {
            $width = $width['width'];
        }

        if (is_array($width)) {
            $width = null;
        }

        // support 200x230
        if (false !== strpos($width, 'x')) {
            list($width, $height) = explode('x', $width);
        }

        $width = $width > 0 ? (int)$width : null;
        $height = $height > 0 ? (int)$height : null;

        $this->_dimensions[$width . 'x' . $height] = [
            'width' => $width,
            'height' => $height
        ];

        return $this;
    }

    /**
     * @param $dimensionKey
     * @return $this
     */
    public function removeDimension($dimensionKey)
    {
        if (isset($this->_dimensions[$dimensionKey])) {
            unset($this->_dimensions[$dimensionKey]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function resetSizes()
    {
        $this->_sizes = [];
        return $this;
    }

    /**
     * @param array $sizes
     * @return $this
     */
    public function setSizes(array $sizes)
    {
        $this->resetDimensions();

        foreach ($sizes as $size) {
            $this->addSize($size);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return array_values($this->_sizes);
    }

    /**
     * @param null $size
     * @return $this
     */
    public function addSize($size = null)
    {
        if (!in_array($size, $this->getSizes())) {
            $this->_sizes[] = $size;
        }
        return $this;
    }

    /**
     * @param $sizeKey
     * @return $this
     */
    public function removeSize($sizeKey)
    {
        foreach ($this->getSizes() as $key => $size) {
            if (0 === strcmp($size, $sizeKey)) {
                unset($this->_sizes[$key]);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSrcSet()
    {
        $srcSet = [];
        foreach ($this->getDimensions() as $dimension) {

            $image = clone $this;
            $image->setWidth($dimension['width']);
            $image->setHeight($dimension['height']);

            $srcSet[] = sprintf('%s %dw', $image->getImageUrl(), $dimension['width']);
        }

        return $srcSet;
    }

    /**
     * @param array $htmlTags
     * @return string
     */
    public function getSrcSetHtml(array $htmlTags = [])
    {
        $htmlTags = array_merge(
            [
                'src' => $this->getImageUrl(),
                'srcset' => $this->getSrcSet(),
                'sizes' => $this->getSizes(),
            ],
            $this->getHtmlAttributes(),
            $htmlTags
        );

        $attributes = [];
        foreach ($htmlTags as $attribute => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $attributes[] = sprintf('%s="%s"', $attribute, $value);
        }
        return sprintf('<img %s />', implode(' ', $attributes));
    }

    /**
     * @param array $htmlTags
     * @return string
     */
    public function getPictureSourceHtml(array $htmlTags = [])
    {
        $htmlTags = array_merge(
            [
                'srcset' => $this->getSrcSet(),
                'sizes' => $this->getSizes(),
            ],
            $this->getHtmlAttributes(),
            $htmlTags
        );

        $allowedHtmlAttributes = ['srcset', 'type', 'media'];

        $attributes = [];
        foreach ($htmlTags as $attribute => $value) {
            if (empty($value) || !in_array($attribute, $allowedHtmlAttributes)) {
                continue;
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $attributes[] = sprintf('%s="%s"', $attribute, $value);
        }
        return sprintf('<source %s />', implode(' ', $attributes));
    }

    /**
     * @param array $htmlTags
     * @return string
     */
    public function getImageHtml(array $htmlTags = [])
    {
        $htmlTags = array_merge(
            [
                'src' => $this->getImageUrl(),
                'srcset' => $this->getSrcSet(),
            ],
            $this->getHtmlAttributes(),
            $htmlTags
        );

        $attributes = [];
        foreach ($htmlTags as $attribute => $value) {
            if (empty($value)) {
                continue;
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $attributes[] = sprintf('%s="%s"', $attribute, $value);
        }

        return sprintf('<img %s />', implode(' ', $attributes));
    }

    /**
     * @param $name
     * @param null $value
     * @return $this
     */
    public function addHtmlAttribute($name, $value = null)
    {
        $this->_htmlAttributes[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeHtmlAttribute($name)
    {
        unset($this->_htmlAttributes[$name]);
        return $this;
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return $this->_htmlAttributes;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getHtmlAttributeValue($name)
    {
        return isset($this->_htmlAttributes[$name]) ? $this->_htmlAttributes[$name] : null;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Ambimax_LazyCatalogImages_Model_Catalog_Image
     */
    public function setProductAttributes(Mage_Catalog_Model_Product $product)
    {
        $this->addHtmlAttribute('alt', $product->getName());
        $this->setImageName($product->getName());
        $this->setImagePath($product->getSmallImage());
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return $this
     */
    public function setCategoryAttributes(Mage_Catalog_Model_Category $category)
    {
        $this->addHtmlAttribute('alt', $category->getName());
        $this->setImageName($category->getName());
        $this->setImagePath($category->getThumbnail());
        return $this;
    }

    /**
     * @param bool $flag
     * @return Ambimax_LazyCatalogImages_Model_Catalog_Image
     */
    public function setTransparency($flag = true)
    {
        $this->_transparency = (bool)$flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTransparency()
    {
        return $this->_transparency;
    }
}