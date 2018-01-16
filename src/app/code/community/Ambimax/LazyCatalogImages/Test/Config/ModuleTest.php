<?php

class Ambimax_LazyCatalogImages_Test_Config_ModuleTest extends EcomDev_PHPUnit_Test_Case_Config
{
    protected $_moduleName = 'Ambimax_LazyCatalogImages';

    public function testModuleIsActive()
    {
        $this->assertModuleIsActive(
            'Module is not active',
            $this->_moduleName
        );
    }

    public function testCodePool()
    {
        $this->assertModuleCodePool(
            'community',
            'Wrong module code pool',
            $this->_moduleName
        );
    }

    public function testVersion()
    {
        $this->assertModuleVersion(
            '1.0.0',
            'Module version is not valid',
            $this->_moduleName
        );
    }

    public function testModelsAreRegistered()
    {
        $this->assertModelAlias(
            'ambimax_lazycatalogimages/source',
            Ambimax_LazyCatalogImages_Model_Source::class,
            'Models are not defined in config.xml'
        );
    }

    public function testHelpersAreRegistered()
    {
        $this->assertHelperAlias(
            'ambimax_lazycatalogimages/data',
            Ambimax_LazyCatalogImages_Helper_Data::class,
            'Helpers are not defined in config.xml'
        );
    }
}