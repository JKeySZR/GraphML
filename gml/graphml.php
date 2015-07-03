<?php

/**
 * Description of graphml
 *
 * @author JKeySZR 
 * @link   https://github.com/JKeySZR/GraphML
 */
define('_MAX_NODE_HEIGHT', 50);
define('_MAX_NODE_PER_COLUMN', 5);

class GraphML {

  protected $iNodeYPos = 10;

  /**
   *  Порядковый номер, индентификатор узла, которые однозначно его позиционирует
   * @var int 
   */
  protected $iNodeNumber = 0;
  protected $iNodeRows = 1;
  protected $iNodeColumn = 1;
  protected $iMaxNodeHeight = _MAX_NODE_HEIGHT;
  //---------Nodes options---------------------------------------------
  protected $defNodeFill = array(
    'color' => '#FFCC00',
    'transparent' => 'false',
  );
  protected $NodeFill;
  protected $defNodeBorderStyle = array(
    'color' => '#000000',
    'type' => 'line',
    'width' => '1.0',
  );
  protected $NodeBorderStyle;
  protected $defNodeLabel = array(
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
  protected $NodeLabel;
  //---------Nodes options--------------------------------------------- END
  //---------Edges options---------------------------------------------

  protected $EdgePath;
  protected $defEdgePath = array(
    'sx' => '0.0',
    'sy' => '0.0',
    'tx' => '0.0',
    'ty' => '0.0',
  );
  protected $EdgeLineStyle;
  protected $defEdgeLineStyle = array(
    'color' => '#000000',
    'type' => 'line',
    'width' => '1.0',
  );
  protected $EdgeArrows;
  protected $defEdgeArrows = array(
    'source' => 'none',
    'target' => 'white_delta',
  );
  protected $EdgeLabel;
  protected $defEdgeLabel = array(
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

  //---------Edges options--------------------------------------------- END

  /**
   *  ID линии связи
   * @var int  
   */
  protected $iEdgeNumber = 0;
  protected $sTemplateDir = 'templates/';
  protected $sNodeTemplate; // шаблон узла
  protected $sEdgeTemplate; // шаблон связи
  protected $sFullTemplate; // основной шаблон
  protected $syEdTemplate;
  protected $aData;

  /**
   * Временая переменная обнуляется в процедуре ...
   * @var type array  
   */
  protected $aMethods;
  protected $aAttributes;
  protected $sNodeLabel;

  /**
   * Сюда складывается в текстовом виде куски xml в 
   * котором находятся характеристики узла
   * @var string 
   */
  protected $sGeneratedNodes;

  /**
   * Сюда складывается в текстовом виде куски xml в 
   * котором находятся характеристики узла
   * @var type string  
   */
  protected $sGeneratedEdges;

  /**
   * Constructor and executor 
   * 
   * @param string $sDirectory  Initial directory, this is the first dir where .php files will be searched 
   * @param string $sExcludeDir Don't read directories matching $sExcludeDir 
   */
  public function __construct() {

    $this->sBaseDir = dirname(__FILE__) . '/';
    $this->sDirectory = $sDirectory;

//    /* Let's read the template files */
//    if (is_file($this->sBaseDir . $this->sTemplateDir . 'node.tpl')) {
//      $this->sNodeTemplate = file_get_contents($this->sBaseDir . $this->sTemplateDir . '/node.tpl');
//    }
//    else {
//      echo 'Node template file not found, exiting.' . "\n";
//      exit(1);
//    }
//    if (is_file($this->sBaseDir . $this->sTemplateDir . '/edge.tpl')) {
//      $this->sEdgeTemplate = file_get_contents($this->sBaseDir . $this->sTemplateDir . '/edge.tpl');
//    }
//    else {
//      echo 'Edge template file not found, exiting.' . "\n";
//      exit(1);
//    }
    if (is_file($this->sBaseDir . $this->sTemplateDir . '/full.tpl')) {
      $this->sFullTemplate = file_get_contents($this->sBaseDir . $this->sTemplateDir . '/full.tpl');
    }
    else {
      echo 'Full template file not found, exiting.' . "\n";
      exit(1);
    }

    if (is_file($this->sBaseDir . $this->sTemplateDir . '/yed.tpl')) {
      $this->syEdTemplate = file_get_contents($this->sBaseDir . $this->sTemplateDir . '/yed.tpl');
    }
    else {
      echo 'yEd template file not found, exiting.' . "\n";
      exit(1);
    }
  }

  private function setNodeDefault() {
    $this->NodeFill = $this->defNodeFill;
    $this->NodeBorderStyle = $this->defNodeBorderStyle;
    $this->NodeLabel = $this->defNodeLabel;
  }

  private function setEdgeDefault() {
    $this->EdgePath = $this->defEdgePath;
    $this->EdgeLineStyle = $this->defEdgeLineStyle;
    $this->EdgeArrows = $this->defEdgeArrows;
    $this->EdgeLabel = $this->defEdgeLabel;
  }

  private function setEdgeOptions($aOptions = NULL) {
    $this->setEdgeDefault();
    // Затем смотрим были ли переданы новые опции, если есть переназначим их
    if (!is_null($aOptions) && is_array($aOptions)) {
      foreach ($aOptions as $name => $opts) {
        foreach ($opts as $key => $value) {
          $this->{$name}[$key] = $value;
        }
      }
    }
  }

  /**
   *  Create a node for it in the graph 
   * @return type int присвоенный ID graph
   */
  protected function createNodes() {
    $iNodeHeight = (count($this->aAttributes) + count($this->aMethods)) * 15 + 50;
    if ($iNodeHeight > $this->iMaxNodeHeight) {
      $this->iMaxNodeHeight = $iNodeHeight;
    }
    $iNodeNumber = $this->iNodeNumber;
    //Создает XML-строку и XML-документ при помощи DOM 
    $dom = new DOMDocument();
    $dom->formatOutput = true; // мы хотим красивый вывод
    $dom->loadXML($this->syEdTemplate);

    $node = $dom->createElement('node');
    $node->setAttribute('id', 'n' . $this->iNodeNumber++);

    $data = $dom->createElement('data');
    $data->setAttribute('key', 'd2');
    $node->appendChild($data);
    unset($data);

    $data2 = $dom->createElement('data');
    $data2->setAttribute('key', 'd3');
    $UMLClass = $dom->createElement('y:UMLClassNode');

    //--- Geometry shape
    $geometry = $dom->createElement('y:Geometry');
    $geometry->setAttribute('height', $iNodeHeight);
    $geometry->setAttribute('width', '169.0');
    $geometry->setAttribute('x', ($this->iNodeColumn * 200));
    $geometry->setAttribute('y', ($this->iNodeYPos));
    $UMLClass->appendChild($geometry);
    unset($geometry);

    //--- Fill element
    $fill = $dom->createElement('y:Fill');
    foreach ($this->NodeFill as $key => $value) {
      $fill->setAttribute($key, $value);
    }
    $UMLClass->appendChild($fill);
    unset($fill);

    //--- BorderStyle
    $BorderStyle = $dom->createElement('y:BorderStyle');
    foreach ($this->NodeBorderStyle as $key => $value) {
      $BorderStyle->setAttribute($key, $value);
    }
    $UMLClass->appendChild($BorderStyle);
    unset($BorderStyle);

    //--- NodeLabel
    $NodeLabel = $dom->createElement('y:NodeLabel', $this->sNodeLabel);
    foreach ($this->NodeLabel as $key => $value) {
      $NodeLabel->setAttribute($key, $value);
    }
    $UMLClass->appendChild($NodeLabel);
    unset($NodeLabel);

    //--- UML Section start
    $UML = $dom->createElement('y:UML');
    $UML->setAttribute('clipContent', 'true');
    $UML->setAttribute('constraint', '');
    $UML->setAttribute('omitDetails', 'false');
    $UML->setAttribute('stereotype', '');
    $UML->setAttribute('use3DEffect', 'true');

    $AttributeLabel = $dom->createElement('y:AttributeLabel', implode("\n", $this->aAttributes));
    $UML->appendChild($AttributeLabel);
    unset($AttributeLabel);

    $MethodLabel = $dom->createElement('y:MethodLabel', implode("\n", $this->aMethods));
    $UML->appendChild($MethodLabel);
    unset($MethodLabel);

    $UMLClass->appendChild($UML);
    unset($UML);
    //--- UML Section stop

    $data2->appendChild($UMLClass);
    unset($UMLClass);
    $node->appendChild($data2);
    unset($data2);
    $graph = $dom->getElementsByTagName('graph')->item(0);
    $graph->appendChild($node);
    unset($graph);
    $rr = $dom->saveXML($node);
    $dbg = 0;
    $this->sGeneratedNodes .= $dom->saveXML($node);
    unset($node);
    unset($dom);

    /* If we reach the maximum node in the given row then "create" a new row */
    if ($this->iNodeNumber % _MAX_NODE_PER_COLUMN == 0) {
      $this->iLastNodeRows = $this->iNodeRows;
      $this->iNodeRows++;
      $this->iNodeColumn = 0;
      $this->iNodeYPos += $this->iMaxNodeHeight + 50;
      $this->iMaxNodeHeight = _MAX_NODE_HEIGHT;
    }
    $this->iNodeColumn++;

    return $iNodeNumber;
  }

  /**
   * 
   * This method is for finding relations between classes and creates the appropriate edges 
   * 
   */
  protected function createEdges() {
    $this->sGeneratedEdges = '';


    //Создает XML-строку и XML-документ при помощи DOM 
    $dom = new DOMDocument();
    $dom->formatOutput = true; // мы хотим красивый вывод
    $dom->loadXML($this->syEdTemplate);
    $edge = $dom->createElement('edge');
    $edge->setAttribute('id', 'e' . $this->iEdgeNumber++);
    $edge->setAttribute('source', '');
    $edge->setAttribute('target', '');

    foreach ($this->aEdgeData as $source => $values) {
      foreach ($values as $edge_item) {
        // Сначала устанавливаем необходимый минимум для опций и применяем специфичные для данного узла
        $this->setEdgeOptions($edge_item['options']);

        $edge->setAttribute('id', 'e' . $this->iEdgeNumber++);
        $edge->setAttribute('source', 'n' . $source);
        $edge->setAttribute('target', 'n' . $edge_item['target']);

        $data = $dom->createElement('data');
        $data->setAttribute('key', 'd6');
        $PolyLineEdge = $dom->createElement('y:PolyLineEdge');
        $Path = $dom->createElement('y:Path');
        foreach ($this->EdgePath as $key => $value) {
          $Path->setAttribute($key, $value);
        }
        $PolyLineEdge->appendChild($Path);
        unset($Path);

        $LineStyle = $dom->createElement('y:LineStyle');
        foreach ($this->EdgeLineStyle as $key => $value) {
          $LineStyle->setAttribute($key, $value);
        }
        $PolyLineEdge->appendChild($LineStyle);
        unset($LineStyle);

        $Arrows = $dom->createElement('y:Arrows');
        foreach ($this->EdgeArrows as $key => $value) {
          $Arrows->setAttribute($key, $value);
        }
        $PolyLineEdge->appendChild($Arrows);
        unset($Arrows);

        $EdgeLabel = $dom->createElement('y:EdgeLabel', $edge_item['label']);
        foreach ($this->EdgeLabel as $key => $value) {
          $EdgeLabel->setAttribute($key, $value);
        }
        $PolyLineEdge->appendChild($EdgeLabel);
        unset($EdgeLabel);

        $BendStyle = $dom->createElement('y:BendStyle');
        $BendStyle->setAttribute('smoothed', 'false');
        $PolyLineEdge->appendChild($BendStyle);
        unset($BendStyle);

        $data->appendChild($PolyLineEdge);
        unset($PolyLineEdge);
        $edge->appendChild($data);
        unset($data);

        $rr .= $dom->saveXML($edge);
        $this->sGeneratedEdges .= $dom->saveXML($edge);
      }
    }
    unset($edge);
    unset($dom);
  }

  /**
   * 
   *  Создание узла в графическом представлении
   * 
   *     $options = array(
   *   'NodeBorderStyle' => array(
   *     'color' => '#ffcc00'
   *     )
   *   ); 
   * 
   * @param string $sFileName filename to process 
   */
  public function addNode($sTitle, $aAttributes, $aMethods, $options = NULL) {
    // Сначала устанавливаем нобходимый минимум для опций узла
    $this->setNodeDefault();
    // Затем смотрим были ли переданы новые опции, если есть переназначим их
    if (!is_null($options) && is_array($options)) {
      foreach ($options as $name => $opts) {
        foreach ($opts as $key => $value) {
          $this->{$name}[$key] = $value;
        }
      }
    }

    $this->sNodeLabel = $sTitle;
    $this->aAttributes = $aAttributes;
    $this->aMethods = $aMethods;
//    if (!is_null($aParent))
//      $this->aEdgeData[$this->sNodeLabel] = $aParent;   
    $this->aClassData[$this->sNodeLabel] = $this->iNodeNumber;
    /* If it's not a class, then we won't work with it */
    $id = $this->createNodes();
    return $id;
  }

  /**
   *  Collect data edges in memory between 2 nodes.
   *  Собираем предварительно массив данных о связях м/у узлами.
   *  
   * 
   * @param int $id_source 
   * @param int $id_target
   * @param string $edgeLabel 
   * @param array $options
   */
  public function addEdge($id_source, $id_target, $edgeLabel = '', $options = NULL) {
    
   // Validation data here, if not - return false if good set and return true< must be =)
    
    $this->aEdgeData[$id_source][] = array(
      'target' => $id_target,
      'label' => $edgeLabel,
      'options' => $options,
    ); //;
  }

  /**
   * Creates graphml file in initial directory 
   * 
   * @param string $Filename Path to file
   */
  public function createFullGraphML($Filename = NULL) {
    /* Create edges between objects */
    $this->createEdges();

    $sContent = str_replace('%%nodes%%', $this->sGeneratedNodes, $this->sFullTemplate);
    $sContent = str_replace('%%edges%%', $this->sGeneratedEdges, $sContent);

    if (is_null($Filename)) {
      $Filename = $this->sDirectory . '/uml-' . date('Ymd_H-i-s') . '.graphml';
    }

    if ($rFp = fopen($Filename, 'w')) {
      fputs($rFp, $sContent);
      fclose($rFp);
    }
  }

}
