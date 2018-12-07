
# ambimax® LazyCatalogImages

[![Build Status](https://travis-ci.org/ambimax/magento-module-ambimax-lazycatalogimages.svg?branch=master)](https://travis-ci.org/ambimax/magento-module-ambimax-lazycatalogimages)

Image urls are replaced by CDN url.

## Install

For installation use composer, modman or copy files manually.

### Composer

```
"require": {
    "ambimax/magento-module-ambimax-lazycatalogimages": "~1.0"
}
```

### Image format

Image urls will be replaced with the following scheme:

```
/path/to/imagename.extension => /imagename/path/to/product-name.extension
```

Full CDN Url will therefore look like this:
```
/path/to/imagename.extension => https://cdn.com/imagename/path/to/product-name.extension
```

This format is used to ease invalidation of images. 
A lambda function must be used to serve the right image using Lambda@Edge.

## Supported Modules

### Wyomind ElasticSearch

Please disable media handling `elasticsearch/product/disable_media_handling`

## License

[MIT License](http://choosealicense.com/licenses/mit/)

## Author Information

 - [Tobias Schifftner](https://twitter.com/tschifftner), [ambimax® GmbH](https://www.ambimax.de)