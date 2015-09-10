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
        $base_url."/search?query=IE.dc.title=".urlencode($_GET['search'])."&step=100"
    );

    $http_response = $http_client->request();
    $data = $http_response->getBody();    
    
    if($data):
        $images = json_decode($data);    
           var_dump($data);
        //create form
        $size = sizeof($images);

        $html = "<div class='result'>";
        foreach ($images->set as $image):      
            $html .="<div class='rosetta_image'><img src='".$base_url.$image->pid."'/><Input type = 'Radio' Name ='pid' value= '".$image->pid."'>
            </div>"; 

            //get list
            $http_client->setUri($base_url."/".$image->pid."/list");
            $http_response = $http_client->request();
            $list = $http_response->getBody();
            if($list):
                $list_images = json_decode($list); 
                if(sizeof($list_images) > 1):
                    foreach ($list_images as $pid):
                        $pid = (array)$pid;
                        $pid = key($pid);
                        $html .="<div class='rosetta_image child'><img src='".$base_url.$pid."'/><Input type = 'Radio' Name ='pid' value= '".$pid."'>
                        </div>"; 
                    endforeach;
                endif;
            endif;
        endforeach;
        $html .= "</div>";

        echo $html;
    endif;
?>
