<?php

/**
* @package omeka
* @subpackage rosetta plugin
* @copyright 2014 Libis.be
*/

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
                        'proxy_port' => 8080,
                        'timeout' => 30
        );
        $http_client->setConfig($config);
    endif;

    $http_client->setUri($url);

    $http_response = $http_client->request();
    $data = $http_response->getBody();

    if($data):
        return $data;
    else:
        return false;
    endif;
}

/**
 * communicate with resolver
 *
 * @param type $url
 * @return return array or boolean
 */
function rosetta_download_image($url){
    $data = rosetta_talk_resolver($url);

    //if no image can be created from data return false
    if($image = @imagecreatefromstring($data)):
        return $data;
    else:
        return false;
    endif;
}

function rosetta_get_mime_type($file){
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->buffer($file);
    return $type;
}

/**
 * get an object's metadata (see http://resolver.libis.be/help)
 *
 * @param rosettaObject
 * @return array
 */
function rosetta_get_metadata($url){

    if($data = rosetta_talk_resolver($url)):
        $data = json_decode($data);

        if(isset($data->status)):
            return false;
        endif;
        $id = key($data);
        $data = (array)$data->$id;

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
function rosetta_get_list($url){

    if($data = rosetta_talk_resolver($url)):
        $data = json_decode($data);
        $array = (array)$data;
        $first = key($array);
        $list= (array)$array[$first];
        if(sizeof($list) > 1):
           return $list;
        else:
            return false;
        endif;
    else:
        return false;
    endif;
}

/**
 * partial admin view
 *
 * @param type $item
 * @return type
 */
function rosetta_admin_form($item){
    ob_start();?>
    <h2>Insert file with Rosetta ID</h2>
    <p class="explanation">Just fill in the pid (if known).</p>
    <Input type = 'text' Name ='known-pid' value= ''>
    <br><br>
    <label>Search for child objects (case sensitive)</label>
	<br>
    <input name='fileUrl' placeholder='IE2710861' id='fileUrl' type='text' class='fileinput' />
    <button style="float:none;" class="rosetta-search">Search</button>
    <br><br>
    <div id="wait" style="display:none;">Please wait, this might take a few seconds.</div>

    <br style="clear:both;" />

    <div id="result" >
        <div class="result"></div>
    </div>


	<script>
	jQuery( document ).ready(function() {
            jQuery('.rosetta-search').click(function(event) {
                    event.preventDefault();
                    jQuery('#Searchresult').hide('slow');
                    jQuery('#wait').show('slow');

                    jQuery.get('<?php echo url("rosetta/index/cgi/");?>',{ search: jQuery('#fileUrl').val()} , function(data) {
                            jQuery('#wait').hide('slow');
                            jQuery('#result').html(data);
                    });
            });
        });
	</script>

	<?php
	$ht = ob_get_contents();
	ob_end_clean();

	return $ht;
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
                        'proxy_port' => 8080,
                        'timeout' => 30
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
				'proxy_port' => 8080,
        'timeout' => 30
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
