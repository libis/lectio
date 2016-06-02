<?php
require 'File/MARC.php';
require 'File/MARCXML.php';
class AlmaTalker{
    
    protected $object_id;
    protected $key;
    protected $alma_url = "https://api-eu.hosted.exlibrisgroup.com/almaws/v1/bibs/";    

    public function __construct($id,$key) {
        $this->object_id = $id;
        $this->key = $key;
    }

    public function get_id(){
        return $id;
    }

    public function alma_curl($url){        
        $ch = curl_init($url);
        
        $options = array(
            CURLOPT_HEADER => "Content-Type:application/xml",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_PROXY => $proxy
        );
        curl_setopt_array($ch, $options);

        $alma_xml = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // this would be your first hint that something went wrong
            die('Couldn\'t send request: ' . curl_error($ch));
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200) {
                // everything went better than expected
            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)

                die('Request failed: HTTP status code: ' . $resultStatus);
            }
        }
        
        curl_close($ch);
        return $alma_xml;
    }

    public function get_bibrecord(){        
        $bibrecord = $this->alma_curl($this->alma_url.$this->object_id."?apikey=".$this->key);   
        //$record = json_encode($bibrecord);     
        $record = new File_MARCXML($bibrecord,File_MARC::SOURCE_STRING);        
        return $record;
    }    

    public function get_holdings_links(){
        $hold_links = array();
        $holdings = $this->alma_curl($this->alma_url.$this->object_id."/holdings?apikey=".$this->key);
        $holdings = new SimpleXMLElement($holdings);
        foreach($holdings as $hold):
            if($hold->holding_id):
                $hold_links[]=$hold->attributes()['link'];
            endif;    
        endforeach;
        return $hold_links;
    }

    public function get_holdings(){
        $links = $this->get_holdings_links();
        
        foreach($links as $link):            
            $holding = $this->alma_curl($link."?apikey=".$this->key);
            $record = new File_MARCXML($holding,File_MARC::SOURCE_STRING);            
            $records[] = $record;
        endforeach;
        return $records;
    }

    public function make_marc_json(){
        $bibrecord = $this->get_bibrecord();        
        $holdings = $this->get_holdings();
        //var_dump($bibrecord);
        $json="";

        while ($record = $bibrecord->next()) {
            //this is the bibrecord
            foreach($holdings as $holding):
                while ($record_hold = $holding->next()) {   
                    //these are the holding records 
                    $fields = $record_hold->getFields();
                    foreach($fields as $field):
                        if($field->isDataField()):
                            $record->appendField($field);
                        endif;        
                    endforeach;
                }
            endforeach;
                        
            $json .= $record->toJSON();
        }
        return $json;            
    }

}
?>
