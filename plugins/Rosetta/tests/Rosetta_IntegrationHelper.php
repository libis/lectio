<?php
/**
 * @version $Id$
 * @copyright Libis,2015
 * @license 
 * @package Rosetta
 */
class Rosetta_IntegrationHelper
{
    const PLUGIN_NAME = 'Rosetta';
    
    public function setUpPlugin()
    {        
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp(self::PLUGIN_NAME);
    }
        
   
}