<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define('DXRAY_MSG_STATUS', 0);
define('DXRAY_MSG_INFO', 1);
define('DXRAY_MSG_WARNING', 2);
define('DXRAY_MSG_ERROR', 3);
define('DXRAY_MSG_DEBUG', 4);

function dxray_debug_stdout($msg, $type = DXRAY_MSG_STATUS) {
  switch ($type) {
    case DXRAY_MSG_STATUS:
      echo $msg . PHP_EOL;
      break;
    case DXRAY_MSG_DEBUG:
      echo "[DEBUG] " . var_dump(debug_backtrace()) . $msg . PHP_EOL;
      break;

    default:
      echo $msg . PHP_EOL;
      break;
  }
}

/**
 *  Получаем список NODE_TYPE
 * modules/node/node.module
 * @link https://api.drupal.org/api/drupal/modules%21node%21node.api.php/function/hook_node_info/7 description param
 * @link https://www.drupal.org/node/1027630 info
 */
function dxray_get_node_type() {
  $GML = new GraphML();

  $optN1 = $GML->setOptionsNode()
      ->Fill_setColor('#ccffff')
      ->Label_setFontSize('22')
      ->getOptions();

  $aNtypes = node_type_get_types();
  foreach ($aNtypes as $oType) {
    // 1. Сначала создаем узел NODE_TYPE 
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
    $options['NodeFill']['color'] = '#ccffff';
    $ID_bundle = $GML->addNode($oType->name, 'UMLClassNode', $optN1, $data);

    dxray_debug_stdout('Добавили bundle номер: ' . $ID_bundle);
    // 2. Получаем поля данного контента и строем зависимости
    $fields = field_info_instances('node', $oType->type);
    foreach ($fields as $field) {
      $finfo = field_info_field($field['field_name']);
      $data['attributes'] = array(
        'Label: ' . $field['label'],
        'Required: ' . $field['required'],
        'Module: ' . $finfo['module'],
        'Locked: ' . $finfo['locked'],
        'Cardinality: ' . $finfo['cardinality'],
        'Description: ' . $field['description'],
      );

      $dataHTML['attributes'] = array(
        '<html>',
        'Label: ' . $field['label'] . '<br>',
        'Required: ' . $field['required'] . '<br>',
        '<b>Module:</b> ' . $finfo['module'] . '<br>',
        'Locked: ' . $finfo['locked'] . '<br>',
        'Cardinality: ' . $finfo['cardinality'] . '<br>',
        'Description: ' . $field['description'] . '<br>',
        '</html>',
      );


      $ID_field = $GML->addNode($field['field_name'], 'UMLClassNode', null, $dataHTML);
      dxray_debug_stdout('Добавили field номер: ' . $ID_field);
      dxray_debug_stdout("{EDGE} SRC: $ID_bundle TARGET: $ID_field " . $ID_field);
      $GML->addEdge($ID_bundle, $ID_field);
    }
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
    $ID = $GML->addNode($field['field_name'], 'UMLClassNode', null, $data);
    _dxray_parse_bundles($ID, $field['bundles'], $REID, $GML);
    $REID['nodes'][$field['field_name']] = $ID;
  }
  $file = DXRAY_OUTPATH . '/FieldS-' . date('d-m-Y_H-i-s') . '.graphml';
  $GML->createFullGraphML($file);
}

/**
 *  Вспомогательная функция для dxray_get_fields_info() по разбору bundles
 * @param type $IDFIELD
 * @param type $bundles
 * @param type $REID
 * @param type $GML
 */
function _dxray_parse_bundles(&$IDFIELD, &$bundles, &$REID, &$GML) {
  foreach ($bundles as $entity => $items) {
    // 1. Шаг первый смотрим что за название сущности
    if (!isset($REID['nodes'][$entity])) {
      // Выделим цветом сущность 
      $options['NodeFill']['color'] = '#cc99ff';
      $ID = $GML->addNode($entity, 'ShapeNode', $options);
      $REID['nodes'][$entity] = $ID;
    }

    $edge = new GML_EDGEOPT;
    $edge->LineStyle_setColor('#ffaaff');

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
      // Проверяем есть ли уже такая связь между сущностью и эпостасью
      if (!isset($REID['edges'][$idEntity][$idBundle])) {
        $GML->addEdge($idEntity, $idBundle, '', $edge->getOptions());
        $REID['edges'][$idEntity][$idBundle] = 1;
      }
      // Проверяем есть ли уже такая связь между эпостасью и ее полями
      if (!isset($REID['edges'][$idBundle][$IDFIELD])) {
        $GML->addEdge($idBundle, $IDFIELD);
        $REID['edges'][$idBundle][$IDFIELD] = 1;
      }
    }
  }
}

function dxray_convert_bool($bool){
  $val = '';
  switch ($bool) {
    case TRUE:      
      $val = 'True';    
      break;
    case FALSE:      
      $val = 'False';    
      break;
    
    default:
      break;
  }
  return $val;
  
}

/**
 *  Получить список VIEWS
 */
function dxray_get_views() {
  $GML = new GraphML();
  $VIEWS = views_get_all_views();

  $nodeView = $GML->setOptionsNode()
      ->Fill_setColor('#ccffff')
      ->getOptions();

  $nodeDisplay = $GML->setOptionsNode()
      ->Shape_setType('ellipse')
      ->Fill_setColor('#ccffcc')
      ->getOptions();
  
  $nodeDisplayPlug = $GML->setOptionsNode()
      ->Shape_setType('parallelogram')
      ->Fill_setColor('#33cccc')
      ->getOptions();
  
  foreach ($VIEWS as $view) {
    //$id_view = $GML->addNodeShape($view->human_name);
    $v_data['attributes'] = array(
      'Human name: ' . $view->human_name,
      'Base_table: ' . $view->base_table,
      'Base_field: ' . $view->base_field,
      'Built: ' . dxray_convert_bool($view->built), // boolean
      'Executed: ' . dxray_convert_bool($view->executed), // boolean
      'Editing: ' . dxray_convert_bool($view->executed), // boolean
      'Type: ' . $view->type, // string
    );
            
    if($view->attachment_before != ''){
      $v_data['attributes'][] = 'Attachment before: ' . $view->attachment_before; // string  
    }
    
    if($view->attachment_after != ''){
      $v_data['attributes'][] = 'Attachment after: ' . $view->attachment_after; // string  
    }    
    
    //$id_view = $GML->addNodeUMLClass($view->name, $v_data, $nodeView);
    $id_view = $GML->addNode($view->name, 'UMLClassNode', $nodeView, $v_data);
    $id_display = $GML->addNodeShape('Display', $nodeDisplay);
    $GML->addEdge($id_view, $id_display);

    $id_disp_plug_type = array(); // name -> GML_ID

    foreach ($view->display as $display) {
      if (!isset($id_disp_plug_type[$display->display_plugin])) {
        $id_display_type = $GML->addNodeShape($display->display_plugin, $nodeDisplayPlug);        
        $GML->addEdge($id_display, $id_display_type);
        $id_disp_plug_type[$display->display_plugin] = $id_display_type;
      }else{
        $id_display_type = $id_disp_plug_type[$display->display_plugin];
      }

      $data['attributes'] = array(
        'ID: ' . $display->id,
        'Display_Title: ' . $display->display_title,
        'Display_plugin: ' . $display->display_plugin,
        'DB table: ' . $display->db_table,
      );

      if (isset($display->display_options['path'])) {
        $data['attributes'][] = 'Path: ' . $display->display_options['path'];
      }

      $id = $GML->addNodeUMLClass($display->display_title, $data);
      $GML->addEdge($id_display_type, $id);
    }
  }
  $dbg = 0;
  $file = DXRAY_OUTPATH . '/Views-' . date('d-m-Y_H-i-s') . '.graphml';
  $GML->createFullGraphML($file);
}
