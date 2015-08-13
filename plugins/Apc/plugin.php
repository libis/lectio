<?php
class ApcPlugin extends Omeka_Plugin_AbstractPlugin{
protected $_hooks = array(
'define_routes'
);
function hookDefineRoutes($args){
$router = $args['router'];
$router->addRoute(
'apc_index',
new Zend_Controller_Router_Route(
'/apc',
array('module' => 'apc')
)
);
} 	
}