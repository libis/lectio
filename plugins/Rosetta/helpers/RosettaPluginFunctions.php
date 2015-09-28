<?php

/**
* @package omeka
* @subpackage rosetta plugin
* @copyright 2014 Libis.be
*/

/**
 * returns rosetta objects of an item
 * 
 * @param type $item
 * @return type or boolean
 */
function rosetta_get_rosetta_objects($item){
    $objects = get_db()->getTable('RosettaObject')->findRosettaObjectByItem($item,false);   
    if($objects){
        return $objects;
    }
    else{
        return false;
    }
}

/**
 * returns an array of image urls to the rosetta obejcts belonging to an item
 * 
 * @param type $item
 * @param type $size
 * @return type
 */
function rosetta_get_images($item,$size='thumbnail'){
    
    $objects = rosetta_get_rosetta_objects($item);
    
    if(!$objects):
        return false;
    endif;
    
    $images = array();
    
    foreach($objects as $object):
        if($size == 'thumbnail'):
            $images[] = $object->get_thumb();
        elseif($size == 'high'):    
            $images[] = $object->get_high();
        endif;        
    endforeach;        
    
    return $images;
}

/**
 * communicate with resolver
 * 
 * @param type $url
 * @return return array or boolean
 */
function rosetta_talk_resolver($url){    
    $http_client = new Zend_Http_Client();
            
    if(get_option('rosetta_proxy')):               
        $config = array(
                        'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
                        'proxy_host' => get_option('rosetta_proxy'),
                        'proxy_port' => 8080
        );
        $http_client->setConfig($config);
    endif;    

    $http_client->setUri($url);

    $http_response = $http_client->request();
    $data = $http_response->getBody();  
    
    if($data):
        return json_decode($data);
    else:
        return false;
    endif;
}

/**
 * get an object's metadata (see http://resolver.libis.be/help)
 * 
 * @param rosettaObject
 * @return array 
 */
function rosetta_get_metadata($object){
    $base_url = get_option('rosetta_resolver'); 
    $url = $base_url."/".$object->pid."/metadata";
    
    if($data = rosetta_talk_resolver($url)): 
        $data = (array)$data;
        $data = (array)$data[key($data)];
        return $data;
    endif;       
    
    return false;       
}

/**
 * get a list of all ID's attached to an IE (see http://resolver.libis.be/help)
 * 
 * @param rosettaObject
 * @return array or boolean
 */
function rosetta_get_list($object){
    $base_url = get_option('rosetta_resolver');
    
    $url = $base_url."/".$object->pid."/list?quality=all";
        
    if($data = rosetta_talk_resolver($url)):
        $list = array();
        foreach ($data as $object):
            $object = (array)$object;
            $root = key($object);            
            
            foreach($object as $key=>$size):
               $size = (array)$size; 
               $list[$key] = array('pid'=>key($size),'label'=>$size[key($size)]);
            endforeach;                    
        endforeach;
        
        return $list;
        
    endif;       
    
    return false;
}

/**
 * partial admin view
 * 
 * @param type $item
 * @return type
 */
function rosetta_admin_form($item){
    ob_start();
    if(rosetta_item_has_rosetta_object($item)):?>
    
    <b>Digitool images currently associated with this item:</b>
    <br>
    <?php 
        $objects = rosetta_get_rosetta_objects($item);
        foreach($objects as $object):?>
        <div class='rosetta_image'>
            <img src="<?php echo $object->get_thumb();?>" />
            <a href="<?php echo (url('rosetta/index/delete-confirm/' . $object->id));?>">Delete</a>
        </div>
        <?php endforeach;?>    
   
    <br><br>
    <?php endif;?>
    <label><b>PID</b></label>
    <p class="explanation">Just fill in the pid (if known).</p>    
    <Input type = 'text' Name ='known-pid' value= ''>
    <br><br>
    <label>Search for child objects (case sensitive)</label>
	<br>
    <input name='fileUrl' placeholder='IE2710861' id='fileUrl' type='text' class='fileinput' />
    <button style="float:none;" class="rosetta-search">Search</button>
    <br><br>
    <div id="wait" style="display:none;">Please wait, this might take a few seconds.</div>

    <div id="Pagination"></div>
    <br style="clear:both;" />
    <div id="Searchresult">
    This content will be replaced when pagination inits.
    </div>

    <!-- Container element for all the Elements that are to be paginated -->
    <div id="hiddenresult" style="display:none;">
        <div class="result">TEST</div>
    </div>


	<script>
	jQuery( document ).ready(function() {
		jQuery('.rosetta-search').click(function(event) {
			event.preventDefault();
			jQuery('#Searchresult').hide('slow');
			jQuery('#Pagination').hide('slow');
			jQuery('#wait').show('slow');

			jQuery.get('<?php echo url("rosetta/index/cgi/");?>',{ search: jQuery('#fileUrl').val()} , function(data) {
				jQuery('#wait').hide('slow');
				jQuery('#hiddenresult').html(data);
				initPagination();
				pageselectCallback(0);
				jQuery('#Pagination').show('slow');
				jQuery('#Searchresult').show('slow');
			});

		});

		jQuery('.digi-child').click(function(event) {
			event.preventDefault();
			jQuery('#wait').show('slow');
			jQuery.get('<?php echo url("rosetta/index/childcgi/");?>',{ child: jQuery('.digi-child').val()} , function(data) {
				jQuery('#wait').hide('slow');
				jQuery('.result-child').html(data);
			});

		});

		/**
		* Callback function that displays the content.
		*
		* Gets called every time the user clicks on a pagination link.
		*
		* @param {int}page_index New Page index
		* @param {jQuery} jq the container with the pagination links as a jQuery object
		*/
		function pageselectCallback(page_index, jq){
			var new_content = jQuery('#hiddenresult div.result:eq('+page_index+')').clone();
			jQuery('#Searchresult').empty().append(new_content);
		                return false;
		}

		/**
		* Callback function for the AJAX content loader.
		*/
		function initPagination() {
			var num_entries = jQuery('#hiddenresult div.result').length;
			// Create pagination element
			jQuery("#Pagination").pagination(num_entries, {
				num_edge_entries: 0,
				num_display_entries: 5,
				callback: pageselectCallback,
			                    items_per_page:4
			});
		}
                });
	</script>

	<?php
	$ht = ob_get_contents();
	ob_end_clean();

	return $ht;
}

/**
* Checks if item has a rosetta object
* @param Item $item
* @return true or false
**/
function rosetta_item_has_rosetta_object($item = null){
    $objects = rosetta_get_rosetta_objects($item);
    
    if($objects){
        return true;
    }
    else{
        return false;
    }
}

/**
 * returns items that share a rosetta object
 * 
 * @param type $item
 * @param type $pid
 * @return items or boolean
 */
function rosetta_find_items_with_same_pid($item=null,$pid=null){
    
    if($item == null){ return false;}

    if($pid == null){
        $objects = rosetta_get_rosetta_objects($item);
             $pid = $objects[0]->pid;
        }

        $db = get_db();
        //echo $object->pid;
        $select = $db->query("SELECT item_id
                FROM omeka_rosetta_objects
                WHERE pid = '".$pid."'
                ORDER BY item_id ASC
        ");

	$s_items = $select->fetchAll();

	foreach($s_items as $s_item){
            if($s_item['item_id'] != $item->id){
                return $s_item['item_id'];
            }
	}

	//if everything fails
	return false;
}

/**
 * Calculates restricted dimensions with a maximum of $goal_width by $goal_height
 * 
 * @param type $goal_width
 * @param type $goal_height
 * @param type $imageobject
 * @return type
 */
function rosetta_resize_dimensions($goal_width,$goal_height,$imageobject) {
    //using this because cobject didn't work
    if(get_option('rosetta_proxy')){
        $vo_http_client = new Zend_Http_Client();
        $config = array(
                        'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
                        'proxy_host' => get_option('rosetta_proxy'),
                        'proxy_port' => 8080
        );
        $vo_http_client->setConfig($config);
        $vo_http_client->setUri($imageobject);

        $vo_http_response = $vo_http_client->request();
        $image = $vo_http_response->getBody();
        //echo($image);    

        $new_image = imageCreateFromString($image);
        
        // Get new dimensions
        $width = imagesx($new_image);
        $height = imagesy($new_image);
    }else{
        $size = getimagesize($imageobject);
        //var_dump($size);
        $width = $size[0];
        $height = $size[1];
    }    
    
    $return['width'] = $width;
    $return['height'] = $height;        
    
    // If the ratio > goal ratio and the width > goal width resize down to goal width
    if ($width/$height > $goal_width/$goal_height && $width > $goal_width) {
        $return['width'] = $goal_width;
        $return['height'] = $goal_width/$width * $height;
    }
    // Otherwise, if the height > goal, resize down to goal height
    else if ($height > $goal_height) {
        $return['width'] = $goal_height/$height * $width;
        $return['height'] = $goal_height;        
    }    
}

/**
 * Check if an image exists in the folder images/rosetta and if not creates one using imageMagick
 * @param pid
 * @return image name
 **/
function rosetta_get_image_from_file($pid){
        $settings = array('w'=>800,'scale'=>true);
	return rosetta_resize($pid,$settings);
}

/**
 * function by Wes Edling .. http://joedesigns.com
 *
 * SECURITY:
 * It's a bad idea to allow user supplied data to become the path for the image you wish to retrieve, as this allows them
 * to download nearly anything to your server. If you must do this, it's strongly advised that you put a .htaccess file
 * in the cache directory containing something like the following :
 * <code>php_flag engine off</code>
 * to at least stop arbitrary code execution. You can deal with any copyright infringement issues yourself :)
 *
 * @param string $imagePath - either a local absolute/relative path, or a remote URL (e.g. http://...flickr.com/.../ ). See SECURITY note above.
 * @param array $opts (w(pixels), h(pixels), crop(boolean), scale(boolean), thumbnail(boolean), maxOnly(boolean), canvas-color(#abcabc), output-filename(string), cache_http_minutes(int))
 * @return new URL for resized image.
 */
function rosetta_resize($pid,$opts=null){
    
        $view_object = get_option('rosetta_view');

	$imagePath = objectdecode($view_object.$pid."&custom_att_3=stream");
	# start configuration
	$cacheFolder = "/".FILES_DIR.'/'; # path to your cache folder, must be writeable by web server
        $remoteFolder = "/".FILES_DIR.'/'; # path to the folder you wish to download remote images into

	$defaults = array('crop' => false, 'scale' => 'false', 'thumbnail' => false, 'maxOnly' => false,
			'canvas-color' => 'transparent', 'output-filename' => false,
			'cacheFolder' => $cacheFolder, 'remoteFolder' => $remoteFolder, 'quality' => 90, 'cache_http_minutes' => 0);

	$opts = array_merge($defaults, $opts);

	$cacheFolder = $opts['cacheFolder'];
	$remoteFolder = $opts['remoteFolder'];

	$path_to_convert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert

	## you shouldn't need to configure anything else beyond this point

	$pobject = parse_object($imagePath);
	$finfo = pathinfo($imagePath);
	$ext = "jpg";//$finfo['extension'];

	# check for remote image..
	if(isset($pobject['scheme']) && ($pobject['scheme'] == 'http' || $pobject['scheme'] == 'https')):
	# grab the image, and cache it so we have something to work with..
	//list($filename) = explode('?',$finfo['basename']);
	$filename = $pid.".jpg";
	$local_filepath = $remoteFolder.$filename;
	$download_image = true;
	if(file_exists($remoteFolder.$pid."_w800.jpg")):
		// Sam: if file exists toegevoegd anders een exception
		if(file_exists($local_filepath)):
		if(filemtime($local_filepath) < strtotime('+'.$opts['cache_http_minutes'].' minutes')):
			//return filemtime($local_filepath).' - '.strtotime('+'.$opts['cache_http_minutes'].' minutes');
			$download_image = false;
		endif;
		$download_image = false;
		endif;
		// Sam: toegevoegd anders werden de bestanden altijd gedownload
		$download_image = false;
	endif;
	if($download_image == true):
		
		$vo_http_client = new Zend_Http_Client();
		$config = array(
				'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
				'proxy_host' => get_option('rosetta_proxy'),
				'proxy_port' => 8080
		);
		$vo_http_client->setConfig($config);
		$vo_http_client->setUri($imagePath);

		$vo_http_response = $vo_http_client->request();
		$thumb = $vo_http_response->getBody();
		//die($thumb);

		file_put_contents($local_filepath,$thumb);

	endif;
	$imagePath = $local_filepath;
	endif;

	if(file_exists($imagePath) == false):
            // Sam: toegevoegd anders moet het moeder bestand er altijd staan Er stond Document root + $imagepath
            $imagePath = $remoteFolder.$pid."_w800.jpg";
            if(file_exists($imagePath) == false):
                return 'image not found';
            endif;
	endif;

	if(isset($opts['w'])): $w = $opts['w']; endif;
	if(isset($opts['h'])): $h = $opts['h']; endif;

	$filename = $pid;

	// If the user has requested an explicit output-filename, do not use the cache directory.
	if(false !== $opts['output-filename']) :
	$newPath = $opts['output-filename'];
	else:
	if(!empty($w) and !empty($h)):
	$newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
	elseif(!empty($w)):
	$newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;
	elseif(!empty($h)):
	$newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
	else:
	return false;
	endif;
	endif;

	$create = true;

	if(file_exists($newPath) == true):
	$create = false;
	$origFileTime = date("YmdHis",filemtime($imagePath));
	$newFileTime = date("YmdHis",filemtime($newPath));
	if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
	$create = true;
	endif;
	endif;

	if($create == true):
	if(!empty($w) and !empty($h)):

	list($width,$height) = getimagesize($imagePath);
	$resize = $w;

	if($width > $height):
	$resize = $w;
	if(true === $opts['crop']):
	$resize = "x".$h;
	endif;
	else:
	$resize = "x".$h;
	if(true === $opts['crop']):
	$resize = $w;
	endif;
	endif;

	if(true === $opts['scale']):
	$cmd = $path_to_convert ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
	" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
	else:
	$cmd = $path_to_convert." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
	" -size ". escapeshellarg($w ."x". $h) .
	" xc:". escapeshellarg($opts['canvas-color']) .
	" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
	endif;

	else:
	$cmd = $path_to_convert." " . escapeshellarg($imagePath) .
	" -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
	(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
	" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);
	endif;

	$c = exec($cmd, $output, $return_code);
	if($return_code != 0) {
		error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
		return false;
	}
	endif;

	# return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
}
?>