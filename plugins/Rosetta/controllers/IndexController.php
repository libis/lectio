<?php

/**
* @package omeka
* @subpackage digitool plugin
* @copyright 2014 Libis.be
*/

//require_once 'Omeka/Controller/Action.php';

class Rosetta_IndexController extends Omeka_Controller_AbstractActionController
{
	public function init() 
        {
    
            $this->_helper->db->setDefaultModelName('RosettaObject');
    
        }
    
        public function indexAction()
	{
            // Always go to browse.
            $this->_helper->redirector(url(get_current_record('item')));
            return;
	}
	public function cgiAction()
	{
		
	}
	
	public function childcgiAction()
	{
	
	}
        
        protected function  _getDeleteSuccessMessage($record)
        {
            return __('The rosetta object was successfully deleted!');
        }

        protected function _getDeleteConfirmMessage($record)
        {
            return __('This will delete the link to a rosetta object.');
        }
        
}

?>