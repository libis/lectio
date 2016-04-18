<?php
require_once PLUGIN_DIR.'/AlmaImport/helpers/alma_talker.php';
require_once PLUGIN_DIR.'/AlmaImport/helpers/transformer.php';
require_once PLUGIN_DIR."/AlmaImport/helpers/libisinworkerclient/helpers/integrationQueue.php";

/**
 * @copyright Libis
 * @package AlmaImport
 */

/**
 * Controller for Alma Import.
 *
 * @package AlmaImport
 */
class AlmaImport_IndexController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        $records="";
        $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
        $status='';
        //get ids
        if($ids):
            $ids_array = explode("|",$ids);   
            
            //make json
            foreach($ids_array as $record):
                $talk = new AlmaTalker($record,get_option('alma_import_api_key'));
                $marc_json = $talk->make_marc_json();
                
                $records[] = $marc_json;
                
            endforeach;
            
            //var_dump($records);
            
            //do lectio specific manipulation
            $transformer = new Transformer($records);
            //var_dump($transformer);
            $json = $transformer->array_to_json();            
            $status = $this->_sendToWorker($json);
        endif;
        
        //send to worker and predict result

        $this->view->assign(compact('ids','status'));
    }    
    
    protected function _sendToWorker($data){
        $queuing_server = new integrationQueue();
        $queuing_server->loadLibisInConfigurations();

        $mappingFilePath = PLUGIN_DIR."/AlmaImport/helpers/libisinworkerclient/helpers/mappings/mappingrules.csv";

        if (!file_exists($mappingFilePath))
                die ("Mapping rules file '$mappingFilePath' does not exists.\n");

        $mapping_rules =  file_get_contents($mappingFilePath);
        
        $set_info[] = array(
                'set_name'	=> 'myset',
                'set_id'    	=> 100,
                'record_type'	=> 'objects',
                'bundle'    	=> null,
                'mapping'   	=> $mapping_rules,
                'data'		=> $data,
                'collective_access_call' => false
        );
        $msg_body = array(
                'set_info'  => $set_info,
                'user_info' => array('name' => 'Admin', 'email' => get_option('administrator_email'))
        );

        $queuing_server->queuingRequest($msg_body);

        return "Ids sent to queue!";
    }
}
