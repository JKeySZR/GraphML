<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function sub_node_add($id_root, &$GML, $aElements) {
    foreach ($aElements as $key => $value) {
        $id = $GML->addNode($key);
        $GML->addEdge($id_root, $id);
    }
}

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

require_once './GraphML/gml/graphml.php';

$entity = entity_get_info();
$GML = new GraphML;
$NodeOpts = array(
    'NodeFill' => array(
        'color' => '#ffcc00'
    )
);
// Set Parametr rot Node who be entity
$NodeOpts['NodeLabel']['modelName']='sides';
$NodeOpts['NodeLabel']['modelPosition']='e';
$NodeOpts['NodeShape']['type']='triangle';

$entity_id = $GML->addNode('Entity','ShapeNode', $NodeOpts);
unset($NodeOpts);

foreach ($entity as $key => $item) {
    $data = array();
    foreach ($item as $optname => $value) {
      $data['attributes'][] =  $optname . ': ' .  $value; 
    }
    

    $aMethods = array();
    $NodeOpts['NodeFill']['color'] = '#ffaa55';
    $graph_id = $GML->addNode($item['label'],'UMLClassNode', $NodeOpts,$data);
    $GML->addEdge($entity_id,$graph_id);
    if (isset($item['bundles'])) {
        $NodeOpts['NodeFill']['color'] = '#aaff55';
        $bundle_id = $GML->addNode('Bundles','ShapeNode',$NodeOpts);
        $GML->addEdge($graph_id, $bundle_id);
        sub_node_add($bundle_id, $GML, $item['bundles']);
    }
    if (isset($item['view modes'])) {
        $NodeOpts['NodeFill']['color'] = '#ff00ff';
        $view_id = $GML->addNode('View modes', 'ShapeNode',$NodeOpts);
        $GML->addEdge($graph_id, $view_id);
        sub_node_add($view_id, $GML, $item['view modes']);
    }

    //$item['bundles']                    
    //$item['view modes']  (oy key)       
}
$GML->createFullGraphML('/srv/www/htdocs/drp_develop/test32.graphml');

$dbg = 5;



