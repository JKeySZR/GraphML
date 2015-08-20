<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//================================= EDGES CLASS ================================

class GML_EDGEOPT {

  private $options = array();

  public function __construct() {
    $this->setOptions();
  }

  public function getOptions($name = NULL) {
    if (is_null($name)) {
      return $this->options;
    }
    else {
      return $this->options[$name];
    }
  }

  public function setOptions() {

    $this->options['EdgeLineStyle'] = array(
      'color' => '#000000',
      'type' => 'line',
      'width' => '1.0',
    );

    $this->options['EdgePath'] = array(
      'sx' => '0.0',
      'sy' => '0.0',
      'tx' => '0.0',
      'ty' => '0.0',
    );

    $this->options['EdgeArrows'] = array(
      'source' => 'none',
      'target' => 'white_delta',
    );

    $this->options['EdgeLabel'] = array(
      'alignment' => 'center',
      'distance' => '2.0',
      'fontFamily' => 'Dialog',
      'fontSize' => '12',
      'fontStyle' => 'plain',
      'hasBackgroundColor' => 'false',
      'hasLineColor' => 'false',
      'height' => '4.0',
      'modelName' => 'six_pos',
      'modelPosition' => 'tail',
      'preferredPlacement' => 'anywhere',
      'ratio' => '0.5',
      'textcolor' => '#000000',
      'visible' => 'true',
      'width' => '4.0',
    );
  }

  public static function get_thisOPtions() {    
    return $this;
  }  
  
//-------------------------   LineStyle  ---------------------------------------

  public function LineStyle_setColor($color = '#000000') {
    $this->options['EdgeLineStyle']['color'] = $color;
    return $this;
  }

  public function LineStyle_setType($type = 'line') {
    $this->options['EdgeLineStyle']['type'] = $type;
    return $this;
  }

  public function LineStyle_setWidth($width = '1.0') {
    $this->options['EdgeLineStyle']['width'] = $width;
    return $this;
  }

  //-------------------------  EdgePath  ---------------------------------------

  public function EdgePath_setSX($sx = '0.0') {
    $this->options['EdgePath']['sx'] = $sx;
    return $this;
  }

  public function EdgePath_setSY($sy = '0.0') {
    $this->options['EdgePath']['sy'] = $sy;
    return $this;
  }

  public function EdgePath_setTX($tx = '0.0') {
    $this->options['EdgePath']['tx'] = $tx;
    return $this;
  }

  public function EdgePath_setTY($ty = '0.0') {
    $this->options['EdgePath']['ty'] = $ty;
    return $this;
  }

//-------------------------  EdgeArrows  ---------------------------------------  

  public function EdgeArrows_setSource($source = 'none') {
    $this->options['EdgeArrows']['source'] = $source;
    return $this;
  }

  public function EdgeArrows_setTarget($target = 'white_delta') {
    $this->options['EdgeArrows']['target'] = $target;
    return $this;
  }

//-------------------------  EdgeLabel  ---------------------------------------  


  public function EdgeLabel_setAlignment($alignment = 'center') {
    $this->options['EdgeLabel']['alignment'] = $alignment;
    return $this;
  }

  public function EdgeLabel_setDistance($distance = '2.0') {
    $this->options['EdgeLabel']['distance'] = $distance;
    return $this;
  }

  public function EdgeLabel_setFontFamily($fontFamily = 'Dialog') {
    $this->options['EdgeLabel']['fontFamily'] = $fontFamily;
    return $this;
  }

  public function EdgeLabel_setFontSize($fontSize = '12') {
    $this->options['EdgeLabel']['fontSize'] = $fontSize;
    return $this;
  }

  public function EdgeLabel_setFontStyle($fontStyle = 'plain') {
    $this->options['EdgeLabel']['fontStyle'] = $fontStyle;
    return $this;
  }

  public function EdgeLabel_setHasBackgroundColor($hasBackgroundColor = 'false') {
    $this->options['EdgeLabel']['hasBackgroundColor'] = $hasBackgroundColor;
    return $this;
  }

  public function EdgeLabel_setHasLineColor($hasLineColor = 'false') {
    $this->options['EdgeLabel']['hasLineColor'] = $hasLineColor;
    return $this;
  }

  public function EdgeLabel_setHeight($height = '4.0') {
    $this->options['EdgeLabel']['height'] = $height;
    return $this;
  }

  public function EdgeLabel_setModelName($modelName = 'six_pos') {
    $this->options['EdgeLabel']['modelName'] = $modelName;
    return $this;
  }

  public function EdgeLabel_setModelPosition($modelPosition = 'tail') {
    $this->options['EdgeLabel']['modelPosition'] = $modelPosition;
    return $this;
  }

  public function EdgeLabel_setPreferredPlacement($preferredPlacement = 'anywhere') {
    $this->options['EdgeLabel']['preferredPlacement'] = $preferredPlacement;
    return $this;
  }

  public function EdgeLabel_setRatio($ratio = '0.5') {
    $this->options['EdgeLabel']['ratio'] = $ratio;
    return $this;
  }

  public function EdgeLabel_setVisible($visible = 'true') {
    $this->options['EdgeLabel']['visible'] = $visible;
    return $this;
  }

  public function EdgeLabel_setTextcolor($textcolor = '#000000') {
    $this->options['EdgeLabel']['textcolor'] = $textcolor;
    return $this;
  }

  public function EdgeLabel_setWidth($width = '#000000') {
    $this->options['EdgeLabel']['width'] = $width;
    return $this;
  }

}

//================================= NODES CLASS ================================
/**
 *  Вспомогательный клас,в котором необходимо описать возможные опции
 */
class GML_NODEOPT {

  private $options = array();

  public function __construct() {
    $this->setOptions();
  }

  public function getOptions($name = NULL) {
    if (is_null($name)) {
      return $this->options;
    }
    else {
      return $this->options[$name];
    }
  }

  public function setOptions() {

    $this->options['NodeShape'] = array(
      'type' => 'rectangle',
    );

    $this->options['NodeFill'] = array(
      'color' => '#FFCC00',
      'transparent' => 'false',
    );

    $this->options['NodeBorderStyle'] = array(
      'color' => '#000000',
      'type' => 'line',
      'width' => '1.0',
    );

    $this->options['NodeLabel'] = array(
      'alignment' => 'center',
      'autoSizePolicy' => 'content',
      'fontFamily' => 'Dialog',
      'fontSize' => '13',
      'fontStyle' => 'bold',
      'hasBackgroundColor' => 'false',
      'hasLineColor' => 'false',
      'height' => '19.92626953125',
      'modelName' => 'internal',
      'modelPosition' => 'c',
      'textColor' => '#000000',
      'visible' => 'true',
      'width' => '33.6181640625',
      'x' => '67.69091796875',
      'y' => '26.701171875',
    );
  }

  /**
   * Set the form of graphical shape 
   * 
   * @param string $type Avalible single variant :
   *     "rectangle",
    "roundrectangle",
    "ellipse",
    "parallelogram",
    "hexagon",
    "triangle",
    "rectangle3d",
    "octagon",
    "diamond",
    "trapezoid",
    "trapezoid2"
   * @return \GML_NODEOPT
   */
  public function NodeShape_setType($type = 'rectangle') {
    $this->options['NodeShape']['type'] = $type;
    return $this;
  }

  public function NodeFill_setColor($color = '#FFCC00') {
    $this->options['NodeFill']['color'] = $color;
    return $this;
  }

  public function NodeFill_setTransparent($transparent = 'false') {
    $this->options['NodeFill']['transparent'] = $transparent;
    return $this;
  }

  public function NodeBorderStyle_setColor($color = '#FFCC00') {
    $this->options['NodeBorderStyle']['transparent'] = $color;
    return $this;
  }

  public function NodeBorderStyle_setType($type = 'line') {
    $this->options['NodeBorderStyle']['type'] = $type;
    return $this;
  }

  public function NodeBorderStyle_setWidth($width = '1.0') {
    $this->options['NodeBorderStyle']['width'] = $width;
    return $this;
  }

  public function NodeLabel_setAlignment($alignment = 'center') {
    $this->options['NodeLabel']['alignment'] = $alignment;
    return $this;
  }

  public function NodeLabel_setAutoSizePolicy($autoSizePolicy = 'content') {
    $this->options['NodeLabel']['autoSizePolicy'] = $autoSizePolicy;
    return $this;
  }

  public function NodeLabel_setFontFamily($fontFamily = 'Dialog') {
    $this->options['NodeLabel']['fontFamily'] = $fontFamily;
    return $this;
  }

  public function NodeLabel_setFontSize($fontSize = '12') {
    $this->options['NodeLabel']['fontSize'] = $fontSize;
    return $this;
  }

  public function NodeLabel_setFontStyle($fontStyle = 'bold') {
    $this->options['NodeLabel']['fontStyle'] = $fontStyle;
    return $this;
  }

  public function NodeLabel_setHasBackgroundColor($hasBackgroundColor = 'false') {
    $this->options['NodeLabel']['hasBackgroundColor'] = $hasBackgroundColor;
    return $this;
  }

  public function NodeLabel_setHasLineColor($hasLineColor = 'false') {
    $this->options['NodeLabel']['hasLineColor'] = $hasLineColor;
    return $this;
  }

  public function NodeLabel_setHeight($height = '19.92626953125') {
    $this->options['NodeLabel']['height'] = $height;
    return $this;
  }

  public function NodeLabel_setModelName($modelName = 'internal') {
    $this->options['NodeLabel']['modelName'] = $modelName;
    return $this;
  }

  public function NodeLabel_setModelPosition($modelPosition = 'c') {
    $this->options['NodeLabel']['modelPosition'] = $modelPosition;
    return $this;
  }

  public function NodeLabel_setTextColor($textColor = '#000000') {
    $this->options['NodeLabel']['textColor'] = $textColor;
    return $this;
  }

  public function NodeLabel_setVisible($visible = 'true') {
    $this->options['NodeLabel']['visible'] = $visible;
    return $this;
  }

  public function NodeLabel_setWidth($width = '33.6181640625') {
    $this->options['NodeLabel']['width'] = $width;
    return $this;
  }

  public function NodeLabel_setX($x = '67.69091796875') {
    $this->options['NodeLabel']['x'] = $x;
    return $this;
  }

  public function NodeLabel_setY($y = '26.701171875') {
    $this->options['NodeLabel']['y'] = $y;
    return $this;
  }

}
