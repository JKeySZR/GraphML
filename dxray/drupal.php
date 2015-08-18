<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Получаем список NODE_TYPE
 * modules/node/node.module
 * @link https://api.drupal.org/api/drupal/modules%21node%21node.api.php/function/hook_node_info/7 description param
 * @link https://www.drupal.org/node/1027630 info
 */
function dxray_get_node_type() {
  $GML = new GraphML();

  $aNtypes = node_type_get_types();
  foreach ($aNtypes as $oType) {
    $data['attributes'] = array(
      'Base: ' . $oType->base,
      'Type: ' . $oType->type,
      'Help: ' . PHP_EOL . $oType->help . PHP_EOL,
      'Custom: ' . $oType->custom,
      'Modified: ' . $oType->modified,
      'Locked: ' . $oType->locked,
      'Disabled: ' . $oType->disabled,
      'Is new: ' . $oType->is_new,
      'Has title: ' . $oType->has_title,
      'Title label: ' . $oType->title_label,
      'Module: ' . $oType->module,
    );
    $ID = $GML->addNode($oType->name, 'UMLClassNode', null, $data);
  }

  $file = DXRAY_OUTPATH . '/NodeType-' . date('d-m-Y_H-i-s') . '.graphml';
  $GML->createFullGraphML($file);
  $dbg = 0;
}

function dxray_get_fields_info() {
  $REID = array(
    'nodes' => array(),
    'edges' => array(),
  ); // будем вести учет ID в GML и его названия
  $GML = new GraphML();

  $fields = field_info_fields();
  $aMethods = array();
  foreach ($fields as $field) {
    $data['attributes'] = array(
      'Name: ' . $field['field_name'],
      'Type: ' . $field['type'],
      'Module: ' . $field['module'],
    );
    //$field['bundles'] ['node'][0] = 'faq';        
    $ID = $GML->addNode($field['field_name'],'UMLClassNode', null , $data);
    _dxray_parse_bundles($ID, $field['bundles'], $REID, $GML);
    $REID['nodes'][$field['field_name']] = $ID;
  }
  $file = DXRAY_OUTPATH . '/FieldS-' . date('d-m-Y_H-i-s') . '.graphml';
  $GML->createFullGraphML($file);
  $dbg = 0;
}

function _dxray_parse_bundles(&$IDFIELD, &$bundles, &$REID, &$GML) {
  foreach ($bundles as $entity => $items) {
    // 1. Шаг первый смотрим что за название сущности
    if (!isset($REID['nodes'][$entity])) {
      // Выделим цветом сущность 
      $options['NodeFill']['color'] = '#cc99ff';
      $ID = $GML->addNode($entity, 'ShapeNode', $options);
      $REID['nodes'][$entity] = $ID;
    }
    // 2. Шаг второй перебираем ипостаси этой сущности
    foreach ($items as $name) {
      if (!isset($REID['nodes'][$name])) {
        // Выделим цветом bundles
        $options['NodeFill']['color'] = '#00ffff';
        $ID = $GML->addNode($name, 'ShapeNode', $options);
        $REID['nodes'][$name] = $ID;
      }
      $idEntity = $REID['nodes'][$entity];
      $idBundle = $REID['nodes'][$name];
      if (!isset($REID['edges'][$idEntity][$idBundle])) {
        $GML->addEdge($idEntity, $idBundle);
        $REID['edges'][$idEntity][$idBundle] = 1;
      }

      if (!isset($REID['edges'][$idBundle][$IDFIELD])) {
        $GML->addEdge($idBundle, $IDFIELD);    
        $REID['edges'][$idBundle][$IDFIELD] = 1;
      }
      
    }
  }
}
