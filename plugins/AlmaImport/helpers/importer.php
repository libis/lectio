<?php
class Importer{

    protected $records;
    protected $mapping;
    protected $type;
    protected $collection;

    public function __construct($alma_array, $mapping,$type,$collection) {
        $this->records = $alma_array['results'];

        /*echo '<pre>';
        var_dump($this->records);
        echo '</pre>';*/

        $this->mapping = $this->process_mapping($mapping);
        $this->type = $type;
        $this->collection = $collection;
    }

    protected function process_mapping($mapping){
        $mapping = explode(PHP_EOL,$mapping);

        foreach($mapping as $rule):
            $mapping_rule = explode("|",$rule);
            if(isset($mapping_rule[1])):
              $new_mapping[$mapping_rule[0]] = array('name'=>$mapping_rule[2],'set'=>$mapping_rule[1]);
            endif;
        endforeach;

        return $new_mapping;
    }

    public function go(){
        $new_records = 0;
        $updated_records = 0;
        foreach($this->records as $record):
            //create item if not exist
            $check = false;
            $item = $this->get_existing_item($record);
            if($item):
                //create new element texts
                if($this->map($record,$item)):
                    $updated_records++;
                endif;
            else:
                //create new element texts
                if($this->map($record)):
                    $new_records++;
                endif;
            endif;
        endforeach;

        return "<p style='font-weight:bold;color:green;'>
                  Records imported ".$new_records."<br>
                  Records updated ".$updated_records."
                </p>";
    }

    protected function map($record_metadata,$item = null){
        //create new item if none exist
        if(!$item):
            $new_item = true;
            $item = new Item();
            $item->item_type_id = $this->type;
            $item->collection_id = $this->collection;
            $item->featured = 0;
            $item->public = 1;
            $item->owner_id = 1;
            $item->save();
        else:
            $new_item = false;
            //delete old files
            $files = $item->getFiles();
            foreach($files as $file):
                $file->delete();
            endforeach;
        endif;

        //add files (needs rosetta plugin)
        $pids = explode('$$',$record_metadata['pid']);
        if($pids):
            $this->add_files($item,$pids);
        endif;

        //handle metadata
        foreach($record_metadata as $key=>$metadata):
            $element_name = $this->mapping[$key]['name'];
            $element_set = $this->mapping[$key]['set'];
            $element_texts = explode('$$',$metadata);

            $element = get_db()->getTable('Element')->findByElementSetNameAndElementName($element_set, $element_name);

            //delete if exists
            if(!$new_item):
                $existing_texts = get_db()->getTable('ElementText')->findBy(array('record_id' => $item->id, 'element_id' => $element->id));
                foreach($existing_texts as $existing_text):
                    $existing_text->delete();
                endforeach;
            endif;

            foreach($element_texts as $text):
                $element_text = new ElementText();
                $element_text->record_id = $item->id;
                $element_text->record_type = 'Item';
                $element_text->element_id = $element->id;
                $element_text->html = 0;
                $element_text->text = $text;
                $element_text->save();
            endforeach;
        endforeach;

        return true;
    }

    protected function add_files($item,$pids){

        foreach($pids as $pid):
            //download the file, start with the highest quality (to get more accurate metadata)
            $obj = rosetta_download_image(get_option('rosetta_resolver').'/'.$pid);

            file_put_contents('/tmp/'.$pid.'_resolver',$obj);

            $file = new File();
            $file->item_id = $item->id;
            $file->filename = $pid.'_resolver';
            $file->has_derivative_image = 1;
            $file->mime_type = rosetta_get_mime_type($obj);
            $file->original_filename = $pid;
            $file->metadata = "";
            $file->save();

            //delete the tmp file
            unlink('/tmp/'.$pid.'_resolver');
        endforeach;
    }

    protected function get_existing_item($record){
        $objectid = get_db()->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata', 'Object id');

        if(!$objectid):
          die("element Object id does not exist");
        endif;

        //item exists?
        $item = get_records('Item', array('advanced' =>
            array(
                array(
                    'element_id' => $objectid->id,
                    'type' => 'is exactly',
                    'terms' => $record['object_id']
                )
            )
        ));

        if(!$item):
            return false;
        endif;

        return $item[0];
    }
}
?>
