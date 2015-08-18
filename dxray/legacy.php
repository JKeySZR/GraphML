<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function dxray_create_graphml(&$aMenu, $file = 'sample_4.tgf') {
  $j = $aMenu['id'];
  $GML = new GraphML();
  $ID = $GML->addNode($aMenu['title'], array(
    $aMenu['file'],
    $aMenu['id'],
    $aMenu['description'],
    $aMenu['module'],
    $aMenu['tab_parent'],
    $aMenu['tab_root'],
    $aMenu['path']), array(
    $aMenu['page calback'],
    $aMenu['access calback'])
  );

  $RES = dxray_create_graphml_node($GML, $aMenu['child'], $ID);
  $dir = __DIR__;
  $file = __DIR__ . '/uml-' . date('d-m-Y_H-i-s') . '.graphml';
  $GML->createFullGraphML($file);

  $dbg = 'see array';
}

function dxray_create_graphml_node(&$GML, &$items, $rootID) {
  foreach ($items as $key => $value) {

    $atrib = array(
      $value['file'],
      $value['id'],
      $value['description'],
      $value['module'],
      $value['tab_parent'],
      $value['tab_root'],
      $value['path'],
    );

    $methods = array(
      $value['page calback'],
      $value['access calback']
    );
//    if ($value['page calback'] == '' && $value['title'] == '') {
//      $dbg = 0;
//    }


    switch ($value['page calback']) {
      case 'views_page':
        $options = array(
          'NodeFill' => array('color' => '#00ffff'),
        );
        break;

      default:
        $options = NULL;
        break;
    }


    if (trim($key) == '%') {
      $options = array(
        'NodeFill' => array('color' => '#ff00ff'),
      );
      $iNum = $GML->addNode($key, $atrib, $methods, $options);
    }
    else {
      $iNum = $GML->addNode($value['title'], $atrib, $methods, $options);
    }


    if (isset($value['child'])) {
      $out = dxray_create_graphml_node($GML, $items[$key]['child'], $iNum);
    }
    //if ($rootID != '')
    $opts = array(
      'EdgeLineStyle' => array(
        'color' => '#GGFFAA',
      ),
    );
    $GML->addEdge($rootID, $iNum, 'Opa-opa', $opts);
  }
}


/**
 * Потрошим массив возвращенный  menu_get_router
 * преобразуем его в свой ассоциатиный массив
 * 
 * @param type $routers
 * @return array ассоциативный массив
 */
function dxray_get_routers() {
  $routers = menu_get_router();
  $aMNU = array();
  $gID = 0; // Глобальный ID для каждого элемета
  foreach ($routers as $key => $item) {
    $aa = explode('/', $key);
    $men = dxray_build_tree($aa, $item, $gID);
    $aMNU = array_merge_recursive($aMNU, $men);
    //unset($item);
    $gID++;
  }
  unset($key);
  unset($men);
  unset($routers);
  return $aMNU;
}

/**
 *  Вспомогоательная рекурсивная функция, для построение ассоциативного массива меню
 * 
 * @param type $aAA
 * @param type $item ссылка на ассоциативный массив элемента routers
 * @return array Возвращает ассоциативный массив
 */
function dxray_build_tree(&$aAA, &$item, &$id) {
  $aResult = array();
  if ($item['title'] == '' || $item['page callback'] == '' || $item['access callback'] == '') {
    $dbg = 0;
  }
  while ($pp = array_shift($aAA)) {
    // если там есть еще чего-то ниже, углубляемся      
    if (count($aAA) > 0) {
      $dbg = 0;
      $aResult[$pp]['child'] = dxray_build_tree($aAA, $item, $id);
    }
    else {
      $aResult[$pp]['id'] = $id;
      $aResult[$pp]['module'] = $item['module'];
      $aResult[$pp]['title'] = $item['title'];
      $aResult[$pp]['tab_parent'] = $item['tab_parent'];
      $aResult[$pp]['tab_root'] = $item['tab_root'];
      $aResult[$pp]['file'] = $item['file'];
      $aResult[$pp]['page calback'] = $item['page callback'];
      $aResult[$pp]['access calback'] = $item['access callback'];
      $aResult[$pp]['path'] = $item['path'];
      $aResult[$pp]['description'] = $item['desciption'];
    }
  }
  return $aResult;
}