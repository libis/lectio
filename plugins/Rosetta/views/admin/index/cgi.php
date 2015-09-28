<?php
    $base_url = get_option('rosetta_resolver');
    $http_client = new Zend_Http_Client();
            
    if(get_option('rosetta_proxy')):               
        $config = array(
                        'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
                        'proxy_host' => get_option('rosetta_proxy'),
                        'proxy_port' => 8080
        );
        $http_client->setConfig($config);
    endif;    

    $http_client->setUri(
        $base_url."/".urlencode($_GET['search'])."/list"
    );

    $http_response = $http_client->request();
    $list = $http_response->getBody();    
    
    if($list):
        $html = "<div class='result'>";        
        
        $list_images = json_decode($list);    
        $array = (array)$list_images;
        $list_images= (array)$array[$_GET['search']];
        var_dump($list_images);
        if(sizeof($list_images) > 1):
            foreach ($list_images as $key => $value):
                
                $html .="<div class='rosetta_image child'><img alt='".$value."' src='".$base_url."/".$key."'/><Input type = 'Radio' Name ='pid' value= '".$key."'>
                </div>"; 
            endforeach;
        endif;
               
        $html .= "</div>";

        echo $html;
    endif;
?>
