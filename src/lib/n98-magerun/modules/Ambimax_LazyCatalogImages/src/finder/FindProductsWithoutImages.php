<?php

namespace finder;

use Mage;
use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ambimax_LazyCatalogImages_Helper_Data;
use Ambimax_Iterator_Model_Event_Observer;

class FindProductsWithoutImages extends AbstractMagentoCommand
{
    private $_placeholderEtag = 'ETag: a38802daa986bee515f36d5e3e138c25';

    protected function configure()
    {
        $this
            ->setName('products:find-without-image')
            ->setDescription('Find products  without images');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->initMagento();

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('image');

        // iterate every product by callback
        Mage::getModel('ambimax_iterator/data_collection')
            ->registerCallback('iterate_data_collection_item',[$this, 'findProductWithoutImage'])
            ->iterateDataCollection($collection)
        ;
    }

    /**
     * @param $currentEtag
     * @return bool
     */
    protected function compareEtag($currentEtag){
        if ( $currentEtag == $this->_placeholderEtag ) {
            return true;
        }
        return false;
    }

    /**
     * @param Ambimax_Iterator_Model_Event_Observer $observer
     */
    public function findProductWithoutImage(Ambimax_Iterator_Model_Event_Observer $observer)
    {
        $imagePath = $observer->getItem()->getImage();

        /* generate product url*/
        $url = Mage::helper('ambimax_lazycatalogimages')->getImageUrl($imagePath);

        if ( $this->compareEtag(get_headers($url)[8]) ) {
            $sku = $observer->getItem()->getSku();
            echo $sku . ' has no picture' . PHP_EOL;
        }
    }


}