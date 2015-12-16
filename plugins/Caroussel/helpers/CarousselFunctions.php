<?php
/**
 * @package Caroussel
 */

/**
 * Render the caroussel.
 *
 */
function caroussel_show()
{
    //get images
    $dh  = opendir(CAROUSSEL_IMAGE_DIR);
    while (false !== ($filename = readdir($dh))) {
        $files[] = $filename;
    }
    $images=preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $files);


    $html = "<div id='gallery' class='header-image cycle-slideshow' data-cycle-slides='div'>";
    foreach($images as $image):           
        $html .= "<div style='background-image:url(".CAROUSSEL_IMAGE_WEB."/".$image.")'></div>";
    endforeach;        
    $html .="</div>";
    return $html;
}
?>