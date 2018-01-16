<?php

class Ambimax_LazyCatalogImages_Test_Model_Import_Entity_Product extends EcomDev_PHPUnit_Test_Case
{
    public function testEnsureOriginalFileIsOverloaded()
    {
        $this->assertInstanceOf(
            Ambimax_LazyCatalogImages_Model_Import_Entity_Product::class,
            Mage::getModel('fastsimpleimport/import_entity_product')
        );
    }
}