<?php
require_once PLUGIN_DIR.'/AlmaImport/helpers/alma_talker.php';
require_once PLUGIN_DIR.'/AlmaImport/helpers/transformer.php';
require_once PLUGIN_DIR.'/AlmaImport/helpers/importer.php';

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

            $type = isset($_POST['item-type']) ? $_POST['item-type'] : '';
            $collection = isset($_POST['collection']) ? $_POST['collection'] : '';

            //do lectio specific manipulation
            $transformer = new Transformer($records);
            $result = $transformer->get_array();

            $status = $this->_import($result,$type,$collection);
        endif;

        //get item types and collections
        $item_types = get_records('ItemType',array(),999);
        $collections = get_records('Collection',array(),999);

        //send to worker and predict result
        $this->view->assign(compact('ids','status','item_types','collections'));
    }

    protected function _import($data,$type,$collection){
        $mappingFilePath = PLUGIN_DIR."/AlmaImport/mappingrules.csv";

        if (!file_exists($mappingFilePath))
                die ("Mapping rules file '$mappingFilePath' does not exists.\n");

        $mapping_rules =  file_get_contents($mappingFilePath);

        $import = new Importer($data, $mapping_rules,$type,$collection);
        $status =  $import->go();
        return $status;
    }

}
