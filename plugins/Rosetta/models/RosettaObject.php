<?php
require_once 'RosettaObjectTable.php';
/**
 * RosettaObject
 * @package: Omeka
 */
class RosettaObject extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $item_id;
    public $pid;
    public $label;
          
    protected function _validate()
    {
        if (empty($this->item_id)) {
            $this->addError('item_id', 'RosettaObject requires an item id.');
        }
        
        //check if item/pid combo already exists
        $db = get_db();
        
        //echo $url->pid;
	$select = $db->query("SELECT id
		FROM omeka_rosetta_objects
		WHERE pid = '".$this->pid."' AND item_id = '".$this->item_id."'		
	");

	$id = $select->fetchAll();
        if(!empty($id)):
            $this->addError('item_id', 'Item already has this pid.');
        endif;
       
    }
    
    public function get_thumb(){
        return get_option('rosetta_resolver')."/".$this->pid;        
    }
    
    public function get_representation(){
        return get_option('rosetta_resolver')."/".$this->pid."/representation";        
    }
    
    public function get_high(){
        return get_option('rosetta_resolver').$this->pid."/stream?quality=HIGH";        
    }
    
    public function get_viewer(){
        return "http://depot.lias.be/delivery/DeliveryManagerServlet?dps_pid=".$this->pid."&mirador=true"; 
    }
    
    /**
     * Required by Zend_Acl_Resource_Interface.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Rosetta_RosettaObjects';
    }
}
