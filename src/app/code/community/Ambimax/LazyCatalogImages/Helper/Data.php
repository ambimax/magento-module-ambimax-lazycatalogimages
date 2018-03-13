<?php

class Ambimax_LazyCatalogImages_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mage_Catalog_Model_Product|null
     */
    protected $_product;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('web/lazycatalogimages/enabled');
    }

    /**
     * @param $filename
     * @param array $customParams
     * @return string
     */
    public function getImageUrl($filename, $customParams = [])
    {
        if ( !$this->isEnabled() ) {
            return Mage::getSingleton('catalog/product_media_config')
                ->getMediaUrl($filename);
        }

        $params = array_merge(
            [
                'host'       => $this->getCdnBaseUrl(),
                'path'       => null,
                'identifier' => null,
                'dimension'  => null,
                'name'       => $this->getImageName(),
                'extension'  => null,
                'width'      => null,
                'height'     => null,
            ],
            $customParams
        );

        if ( preg_match('/(.*)\/(.*)\.(jpg|jpeg|png|gif)$/i', $filename, $matches) ) {
            list($empty, $params['path'], $params['identifier'], $params['extension']) = $matches;
        }

        // add dimensions
        if ( $params['width'] > 0 || $params['height'] > 0 ) {
            $params['dimension'] = sprintf(
                '%sx%s',
                $this->_parseDimensionValue($params['width']),
                $this->_parseDimensionValue($params['height'])
            );
        }

        // handle placeholder
        if ( !$params['identifier'] ) {
            // set placeholder image
            $params['identifier'] = 'placeholder';
            $params['name'] = 'default';
            $params['extension'] = 'jpg';
        }


        $url = [
            'host'       => rtrim($params['host'], '/'),
            'identifier' => $params['identifier'],
            'dimension'  => $params['dimension'],
            'path'       => trim($params['path'], '/'),
            'filename'   => $this->formatUrlKey(sprintf('%s.%s', $params['name'], $params['extension'])),
        ];

        return implode('/', array_filter($url));
    }

    /**
     * @return string|null
     */
    public function getCdnBaseUrl()
    {
        return Mage::getStoreConfig('web/lazycatalogimages/base_url');
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        if ( $this->getProduct() ) {
            return $this->getProduct()->getName();
        }

        return 'unknown';
    }

    /**
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
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
     * @param $params
     * @return int|null
     */
    protected function _parseDimensionValue($size)
    {
        return $size > 0 ? (int)$size : null;
    }

}