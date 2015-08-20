<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//================================= EDGES CLASS ================================
abstract class GML_EdgeLineStyle {

  protected $options = array();

  final public static function setOptions() {
   $this->options['EdgeLineStyle']= array(
    'color' => '#000000',
    'type' => 'line',
    'width' => '1.0',
  );    
    return $this ;
  }

  public function setColor($color = '#000000') {
    $this->options['EdgeLineStyle']['color'] = $color;
    //return $color;
  }

  public function setType($type = 'line') {
    $this->options['EdgeLineStyle']['type'] = $type;
    //return $type;
  }

  public function setWidth($width = '1.0') {
    $this->options['EdgeLineStyle']['width'] = $width;
    //return $width;
  }

}

abstract class GML_EdgePath {

  protected $options = array();

  final public static function setOptions() {
   $this->options['EdgePath']= array(
      'sx' => '0.0',
      'sy' => '0.0',
      'tx' => '0.0',
      'ty' => '0.0',
    );
    return $this ;
  }

  public function setSX($sx = '0.0') {
    $this->options['EdgePath']['sx'] = $sx;
    //return $color;
  }
  public function setSY($sy = '0.0') {
    $this->options['EdgePath']['sy'] = $sy;
    //return $color;
  }

  public function setTX($tx = '0.0') {
    $this->options['EdgePath']['tx'] = $tx;
    //return $color;
  }
  public function setTY($ty = '0.0') {
    $this->options['EdgePath']['ty'] = $ty;
    //return $color;
  }
  
}

abstract class GML_EdgeArrows {

  protected $options = array();

  final public static function setOptions() {
   $this->options['EdgeArrows']= array(
    'source' => 'none',
    'target' => 'white_delta',
    );
    return $this ;
  }
  public function setSource($source = 'none') {
    $this->options['EdgeArrows']['source'] = $source;    
  }  
  public function setTarget($target = 'white_delta') {
    $this->options['EdgeArrows']['target'] = $target;    
  }    
}

abstract class GML_EdgeLabel {

  protected $options = array();

  final public static function setOptions() {
   $this->options['EdgeLabel']= array(
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
    return $this ;
  }
  
  public function setAlignment($alignment =  'center') {
    $this->options['EdgeLabel']['alignment'] = $alignment;    
  }  
  public function setDistance($distance =  '2.0') {
    $this->options['EdgeLabel']['distance'] = $distance;    
  }    
  public function setFontFamily($fontFamily =  'Dialog') {
    $this->options['EdgeLabel']['fontFamily'] = $fontFamily;    
  } 
  public function setFontSize($fontSize =  '12') {
    $this->options['EdgeLabel']['fontSize'] = $fontSize;    
  }   
  public function setFontStyle($fontStyle =  'plain') {
    $this->options['EdgeLabel']['fontStyle'] = $fontStyle;    
  }     
  public function setHasBackgroundColor($hasBackgroundColor =  'false') {
    $this->options['EdgeLabel']['hasBackgroundColor'] = $hasBackgroundColor;    
  }     
  public function setHasLineColor($hasLineColor =  'false') {
    $this->options['EdgeLabel']['hasLineColor'] = $hasLineColor;    
  }       
  public function setHeight($height =  '4.0') {
    $this->options['EdgeLabel']['height'] = $height;    
  }         
  public function setModelName($modelName =  'six_pos') {
    $this->options['EdgeLabel']['modelName'] = $modelName;    
  }          
  public function setModelPosition($modelPosition =  'tail') {
    $this->options['EdgeLabel']['modelPosition'] = $modelPosition;    
  }            
  public function setPreferredPlacement($preferredPlacement =  'anywhere') {
    $this->options['EdgeLabel']['preferredPlacement'] = $preferredPlacement;    
  }    
  public function setRatio($ratio =  '0.5') {
    $this->options['EdgeLabel']['ratio'] = $ratio;    
  }      
  public function setVisible($visible =  'true') {
    $this->options['EdgeLabel']['visible'] = $visible;    
  }     
  public function setTextcolor($textcolor =  '#000000') {
    $this->options['EdgeLabel']['textcolor'] = $textcolor;    
  }       
  public function setWidth($width =  '#000000') {
    $this->options['EdgeLabel']['width'] = $width;    
  }        
}

class GML_EDGEOPT {

  public $options = array();
  public $EdgePath = array();
  public $EdgeLineStyle = array();
  public $EdgeArrows = array();
  public $EdgeLabel = array();
    
  final public static function EddePath() {    
    return  GML_EdgePath::setOptions();
  }
  
  final public static function EdgeLineStyle() {
    return GML_EdgeLineStyle::setOptions();
  }
  
  final public static function EdgeArrows() {
    return GML_EdgeArrows::setOptions();
  }

  final public static function EdgeLabel() {
    return GML_EdgeLabel::setOptions();
  } 
  public function getOptions($optname = NULL){
    if(is_null($optname) ){
      return ;
    }
  }
}

//================================= NODES CLASS ================================
/**
 *  Вспомогательный клас,в котором необходимо описать возможные опции
 */
class GML_NODEOPT {
  
}