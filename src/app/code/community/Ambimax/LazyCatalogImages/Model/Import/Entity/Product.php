<?php

class Ambimax_LazyCatalogImages_Model_Import_Entity_Product extends AvS_FastSimpleImport_Model_Import_Entity_Product
{
    /**
     * Uploading files into the "catalog/product" media folder.
     * Return a new file name if the same file is already exists.
     *
     * @see https://github.com/avstudnitz/AvS_FastSimpleImport/issues/109
     * In some cases the moving of files doesn't work because it is already
     * moved in a previous entity. We try and find the product in the destination folder.
     *
     * @param  string $fileName ex: /abc.jpg
     * @return string           ex: /a/b/abc.jpg
     */
    protected function _uploadMediaFiles($fileName)
    {
        if ( $this->mediaUploadPreventionIsEnabled() ) {
            return $fileName;
        }

        return parent::_uploadMediaFiles($fileName);
    }

    /**
     * @return bool
     */
    public function mediaUploadPreventionIsEnabled()
    {
        return Mage::getStoreConfigFlag('web/lazycatalogimages/prevent_media_upload');
    }

}