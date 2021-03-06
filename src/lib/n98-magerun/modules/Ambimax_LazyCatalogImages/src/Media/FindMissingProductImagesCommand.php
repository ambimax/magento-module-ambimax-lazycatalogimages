<?php

namespace Media;

use Mage;
use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ambimax_LazyCatalogImages_Helper_Data;
use Ambimax_Iterator_Model_Event_Observer;

class FindMissingProductImagesCommand extends AbstractMagentoCommand
{
    protected $_placeholderEtag = 'ETag: a38802daa986bee515f36d5e3e138c25';

    protected function configure()
    {
        $this
            ->setName('media:find-missing-product-images')
            ->setDescription('Returns evaluation from product images | Return csv format');
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

        $iterator = Mage::getModel('ambimax_iterator/data_collection');

        $iterator->getObserver()
            ->setInput($input)
            ->setOutput($output);

        // iterate every product by callback
        $iterator
            ->registerCallback('iterate_data_collection_item', [$this, 'findProductWithoutImage'])
            ->iterateDataCollection($collection);
    }

    /**
     * @param $imageUrl
     * @return bool
     */
    public function isPlaceholderImage($imageUrl)
    {
        // @codingStandardsIgnoreLine
        $headers = get_headers($imageUrl);
        return in_array($this->_placeholderEtag, $headers);
    }

    /**
     * @param Ambimax_Iterator_Model_Event_Observer $observer
     */
    public function findProductWithoutImage(Ambimax_Iterator_Model_Event_Observer $observer)
    {
        /** @var \Mage_Catalog_Model_Product $item */
        $item = $observer->getItem();

        /** @var \Symfony\Component\Console\Output\OutputInterface $output */
        $output = $observer->getOutput();

        $productSku = $item->getSku();

        if ( empty($item->getImage()) ) {
            $output->writeln(sprintf('%s,missing image path/ product isn\'t avaiable', $productSku));
            return;
        }

        $imageUrl = Mage::helper('ambimax_lazycatalogimages/rewrite_enhancedgrid')->getImageUrl($item->getImage());

        if ( $this->isPlaceholderImage($imageUrl) ) {
            $output->writeln(sprintf('%s,has placeholder image', $productSku));
            return;
        }

        $output->writeln(sprintf('%s,has own image', $productSku));
    }
}