<?php
class Transformer{

    protected $records;

    public function __construct($alma_json) {
        $this->records = $alma_json;
    }

    public function array_to_json(){
        $final['ok']='true';
        foreach($this->records as $record):
            $record = json_decode($record,true);
            //simplify the array
            $fields = $this->parse_fields($record['fields']);
            $result = $this->transform($fields);
            $final['results'][]= $result;
        endforeach;
        
        return json_encode($final);
    }

    public function get_array(){
        $final['ok']='true';
        foreach($this->records as $record):
            $record = json_decode($record,true);
            //simplify the array
            $fields = $this->parse_fields($record['fields']);
            $result = $this->transform($fields);
            $final['results'][]= $result;
        endforeach;

        return $final;
    }

    public function parse_fields($fields){
        $result='';
        foreach($fields as $field):
            if(isset($field[key($field)]['subfields'])):
                $subfields = $field[key($field)]['subfields'];
                $temp='';
                foreach($subfields as $code):
                    $temp[key($code)]=$code[key($code)];
                endforeach;
                $field[key($field)]['subfields']=$temp;
            endif;
            $result[][key($field)]=$field[key($field)];

        endforeach;
        return $result;
    }

    public function get_record(){
        return $this->array_to_json();
    }

    public function transform($fields){
        $result="";
        foreach($fields as $field):

            if(isset($field["001"])):
                $result["object_id"][]=$field["001"];
                $result["LIMO"][]="https://services.libis.be/query?institution=KUL&view=KULeuven&query=any:".$field["001"];
            endif;

            if(isset($field["245"])):
                $result["title"][]=$field["245"]['subfields']['a'];
            endif;

            if(isset($field["246"])):
                $result["other titles"][]=$field["246"]['subfields']['a'];
            endif;

            if(isset($field["830"])):
                $result["content"][]=$field["830"]['subfields']['a'];
            endif;

            if(isset($field["546"])):
                $result["language"][]=$field["546"]['subfields']['a'];
            endif;

            if(isset($field["653"])):
                if($field["653"]["ind1"]=' '&&$field["653"]["ind2"]=='6'):
                    $result["type"][]=$field["653"]['subfields']['a'];
                endif;
            endif;

            if(isset($field["856"])):
                if($field["856"]["ind1"]='4'&&$field["856"]["ind2"]=='0'):
                    $pid = explode('pid=', $field["856"]['subfields']['u']);
                    $pid = end($pid);
                    $result["pid"][] = $pid;
                endif;
            endif;

            if(isset($field["260"])):
                if($field["260"]["ind1"]=' '&&$field["260"]["ind2"]==' '):
                    $data=$field["260"]['subfields']['c'];
                    $result["date"][] = str_replace('.', '', $data);
                endif;
            endif;

            if(isset($field["264"])):
                if($field["264"]["ind1"]=' '&&$field["264"]["ind2"]=='1'):
                    $data=$field["260"]['subfields']['c'];
                    $result["date"][] = str_replace('.', '', $data);
                endif;
            endif;

            if (isset($field["952"])):
                if($field["952"]["ind1"]=' '&&$field["952"]["ind2"]==' '):
				    $data = $field["952"]['subfields']['d'];
				    if ($field["952"]['subfields']['f'] != null) {
					    $data .= "; ".$field["952"]['subfields']['f'];
				    }
				    $result["provenance"][]=$data;
                endif;
		    endif;

            if (isset($field["505"])):
			    $data = "";
			    if (isset($field["505"]['subfields']['a'])) {
				    $data = $field["505"]['subfields']['a'];
			    }

                if (isset($field["505"]['subfields']['g'])) {
				    $data .= " (".$field["505"]['subfields']['g'].")";
			    }
			    $result["TableOfContents"][]=$data;
            endif;

            if (isset($field["300"])):
                if($field["300"]["ind1"]=' '&&$field["300"]["ind2"]==' '):
			        $data = $field["300"]['subfields']['a'];

			        if (isset($field["300"]['subfields']['b'])) {
				        $data .= " : ".$field["300"]['subfields']['b'];
			        }
			        if (isset($field["300"]['subfields']['c'])) {
				        // if b is null, there is no need for a ; because subfields a ends with a ;
				        if (isset($field["300"]['subfields']['b'])) {
					        $data .= " ; ";
				        }
				        $data .= $field["300"]['subfields']['c'];
			        }
			        $result["Description"][]=$data;
                endif;
		    endif;

            if (isset($field["950"])):
                if($field["950"]["ind1"]=' '&&$field["950"]["ind2"]==' '):
			        $data = "";
			        if (isset($field["950"]['subfields']['a'])) {
				        $data = $field["950"]['subfields']['a'];
			        }

			        if (isset($field["950"]['subfields']['b'])) {
				        $data .= " ".$field["950"]['subfields']['b'];
			        }
			        if (isset($field["950"]['subfields']['c'])) {
				        $data .= " (" . $field["950"]['subfields']['c'] . ")";
			        }
			        $result["Illustrations"][]=$data;
                endif;
            endif;

            if(isset($field["500"])):
                if($field["500"]["ind1"]=' '&&$field["500"]["ind2"]==' '):
                    $result["Notes"][]=$field["500"]['subfields']['a'];
                endif;
            endif;

            if(isset($field["544"])):
                $result[]["source"]=$field["544"]['subfields']['a'];
                if ($field["544"]['subfields']['b'] != null) {
	        		$result["IdentifierCallnumber"][] = $field["544"]['subfields']['b'];
			    }
            endif;

            if(isset($field["852"])):
                if (!isset($field["852"]['subfields']['l'])) {

				    $data =$field["852"]['subfields']['b'];
				    switch ($data) {
				    case "BIBC":
					    $data = "KU Leuven. Division of Heritage & Culture";
					    break;
				    case "GBIB":
					    $data = "KU Leuven. Maurits Sabbe Library (Theology)";
					    break;
				    case "WBIB":
					    $data = "KU Leuven. Campuslibrary Arenberg";
					    break;
				    default:

				    }//end switch

				    $result['source'][]= $data;

				    if ($field["852"]['subfields']['h'] != null) {
					    $result["IdentifierCallnumber"][] = $field["852"]['subfields']['h'];
				    }
			    }
            endif;

            if (isset($field["700"])):
			    if($field["700"]['subfields']['4']=="stu" || $field["700"]['subfields']['4']=="pfs"
			    || $field["700"]['subfields']['4']=="aow" || $field["700"]['subfields']['4']=="aut"
			    || $field["700"]['subfields']['4']=="egr" || $field["700"]['subfields']['4']=="etc"
			    || $field["700"]['subfields']['4']=="ill" || $field["700"]['subfields']['4']=="oth"
			    || $field["700"]['subfields']['4']=="prt") {
			    // This construction is to remove duplicated entries. (thanks to the property of hashset, linked, because order matters
			    $data="";

			    if (isset($field["700"]['subfields']['a'])) {
				    $data .= $field["700"]['subfields']['a'];
			    }
			    if (isset($field["700"]['subfields']['b'])) {
                    $data .= " ".$field["700"]['subfields']['b'];
			    }
			    if (isset($field["700"]['subfields']['c'])) {
				    $data .=" (". $field["700"]['subfields']['c'] .  ")";
			    }
			    if (isset($field["700"]['subfields']['d'])) {
				    $data .=" (". $field["700"]['subfields']['d'] .  ")";
			    }
			    if (isset($field["700"]['subfields']['q'])) {
				    $data .=" (". $field["700"]['subfields']['q'] .  ")";
			    }
			    if (isset($field["700"]['subfields']['g'] )) {
				    $data .=" (". $field["700"]['subfields']['g'] .  ")";
			    }
			    if (isset($field["700"]['subfields']['3'])) {
				    $data .=" (". $field["700"]['subfields']['3'] .  ")";
			    }

	            if ($field["700"]['subfields']['4']!="stu" && $field["700"]['subfields']['4']!="pfs") {
		            switch ($field["700"]['subfields']['4']) {
		                case "aow":
			                $data .=" (author of original work)";
			                break;
		                case "aut":
			                $data .=" (author)";
			                break;
		                case "egr":
			                $data .=" (engraver)";
			                break;
		                case "etc":
			                $data .=" (etcher)";
			                break;
		                case "ill":
			                $data .=" (illustrator)";
			                break;
		                case "oth":
			                $data .=" (role not identified)";
			                break;
		                case "prt":
			                $data .=" (printer)";
			                break;
		                default:
                            $data='';
			                break;
		            }
	            }

	            if ($field["700"]['subfields']['4']=="stu") {

		            $result["Creator"][]=$data;

	            } else if ($field["700"]['subfields']['4']=="pfs") {

		            $result["Professor"][]=$data;

	            } else {
		            $result["Contributor"][]=$data;
	            }

            }
            endif;
        endforeach;

        foreach($result as $key=>$value):
            $result_imploded[$key]=implode("$$",$value);
        endforeach;

        return $result_imploded;
    }
}
?>
