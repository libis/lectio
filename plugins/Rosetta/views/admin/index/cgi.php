<div class='result'>
<?php
    $base_url = get_option('rosetta_resolver');
    $html='';
       
    if($list = rosetta_get_list($base_url."/".urlencode($_GET['search'])."/list")):
        foreach ($list as $key => $value):                
            $html .="<div class='rosetta_image child'><img alt='".$value."' src='".$base_url."/".$key."'/><Input type = 'Radio' Name ='pid' value= '".$key."'>
            </div>"; 
        endforeach;
        echo $html;
    else:?>
    <p>No results found</p>
    <?php endif;?>
</div>
