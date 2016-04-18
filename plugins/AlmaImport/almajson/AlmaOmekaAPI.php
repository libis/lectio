<?php
require_once 'API.class.php';
require_once 'helpers/alma_talker.php';
require_once 'helpers/transformer.php';

class AlmaOmekaAPI extends API
{
    protected $key;
    protected $object_id;

    public function __construct($request, $origin) {
        
        parent::__construct($request);
        
        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        }else{
            $key = $this->request['apiKey'];
            $this->key= $key; 
        }

        if (!array_key_exists('id', $this->request)) {
            throw new Exception('No object id provided');
        }else{
            $this->object_id = $this->request['id'];
        }           
    }
   
    protected function alma(){
        $talk = new AlmaTalker($this->object_id,$this->key);
        $alma_json = $talk->make_marc_json();

        $transformer = new Transformer($alma_json);
        return $transformer->get_record();  
    }   
}
?>
