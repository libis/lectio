<?php
/**
 * Tests for rosetta plugin
 */
class RosettaTestTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $helper = new Rosetta_IntegrationHelper();
         $this->item = insert_item(array('public' => true));
        $helper->setUpPlugin();
    }    
    
    public function assertPreConditions()
    {
        $this->assertThat($this->item, $this->isInstanceOf('Item'));
        $this->assertTrue($this->item->exists());
    }
     
    public function testResolverMetadata()
    {
        $data = rosetta_get_metadata('http://resolver.libis.be/FL136995/metadata');
        $this->assertTrue(is_array($data));
        
        //returns false when item does not exist or has nog metadata
        $data = rosetta_get_metadata('http://resolver.libis.be/99999/metadata');
        $this->assertFalse($data);
    }
    
    public function testResolverDownloadImage()
    {
        $res = rosetta_download_image('http://resolver.libis.be/9999');                
        $this->assertFalse($res);
        
        $result = rosetta_download_image('http://resolver.libis.be/IE4923148/');   
        if($result):
            $result = true;
        endif;
        $this->assertTrue($result);
    }  
    
    public function testResolverList()
    {
        $list = rosetta_get_list('http://resolver.libis.be/IE4923148/list');        
        $this->assertTrue(is_array($list));
    }   
}