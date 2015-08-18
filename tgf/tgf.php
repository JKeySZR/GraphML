<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function test_tgf($routers) {

// Получаем дерево ссыллок сайта на Drupal
  $i = 1;
  $aURL = array();
  $aCallback = array();
  foreach ($routers as $key => $item) {
    $aCallback[$item['page callback']]['access'] = $item['access callback'];
    $aURL[$i] = array(
      'path' => $key,
      'module' => $item['module'],
      'page calback' => $item['page callback'],
    );
    $i++;
  }
//print_r($aURL);

  $j = 0;
  $file = 'drurl.tgf';
// Записывем сначала модули , т.к. на них будем потом строи ть связи
  foreach ($aCallback as $key => $value) {
    $aCallback[$key]['ID'] = $j;
    $current = $j . ' ' . $key . PHP_EOL;
    file_put_contents($file, $current, FILE_APPEND);
    $j++;
  }
  $aLinks = array();
  foreach ($routers as $key => $item) {
    $current = $j . ' ' . $key . PHP_EOL;
    file_put_contents($file, $current, FILE_APPEND);
    $aLinks[] = $j . ' ' . $aCallback[$item['page callback']]['ID'] . PHP_EOL;
    $j++;
  }
  file_put_contents($file, '# ' . PHP_EOL, FILE_APPEND);
  file_put_contents($file, $aLinks, FILE_APPEND);
}

function dxray_create_tgf(&$aMenu, $file = 'sample_4.tgf'){
  $j = $aMenu['id'];
  $trf = $aMenu['id'] . ' ' . $aMenu['title'] . PHP_EOL;

  $trf .= dxray_tgf_index_create($aMenu['child'], $j);
  file_put_contents($file, $trf, FILE_APPEND);
  // Добовляем разделитель в файл, после которго пойдут описания связей
  file_put_contents($file, '# ' . PHP_EOL, FILE_APPEND);
  // Формирем связи и пишим их в файл
  $edge = dxray_create_tgf_edge($aMenu['id'], $aMenu);
  file_put_contents($file, $edge, FILE_APPEND);
  $dbg = 'see array';
}

/**
 * Переопределим индексы 
 * 
 * @param type $items
 * @param type $index
 * @return string
 */
function dxray_tgf_index_create(&$items, &$index) {
  $out = '';
  foreach ($items as $key => $value) {
    $index++;
    $items[$key]['id_parent'] = $index - 1;
    $items[$key]['id'] = $index;
    if (isset($value['child'])) {
      $out .= dxray_tgf_index_create($items[$key]['child'], $index);
    }

    //$out .= $items[$key]['id'] . ' ' . $key . ' || '. $value['page calback'] . PHP_EOL;
    $label = '<html>';
    $label .= '<h2 align="center">' . $key . '</h2>';
    $label .= '<p>' . $value['path'] . '</p>';
    $label .= '<ul>';
    $label .= '<li>' . $value['module'] . '</li>';
    $label .= '<li>' . $value['file'] . '</li>';
    $label .= '</ul>';
    $label .= '</html>';
//    $out .= $items[$key]['id'] . ' ' . $key . PHP_EOL;
    $out .= $items[$key]['id'] . ' ' . $label . PHP_EOL;
  }
  return $out;
}


/**
 *  Создаем линии связи и одписываем их 
 * 
 * @param string $res "ID1 ID2 NAME_LINK \n"
 */
function dxray_create_tgf_edge($id_root, $aMenu) {
  $res = '';
  if (isset($aMenu['child'])) {
    foreach ($aMenu['child'] as $key => $value) {
      $res .= dxray_create_tgf_edge($aMenu['id'], $value);
    }
  }
  $res .= $id_root . ' ' . $aMenu['id'] . ' ' . $aMenu['page calback'] . PHP_EOL;

  return $res;
}
