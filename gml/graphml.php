<?php

/**
 * Class for create graph in graphml format
 * 
 * 
 * @author JKeySZR 
 * @link   https://github.com/JKeySZR/GraphML
 */
define('_MAX_NODE_HEIGHT', 50);
define('_MAX_NODE_PER_COLUMN', 5);
include 'graphml_options.php';

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

  protected $defNodeType = 'ShapeNode'; // UMLClassNode ShapeNode GenericNode SVGNode

  /**
   * Тип создаваемого графического объекта
   * @var string  Avaliable variant: UMLClassNode | ShapeNode | GenericNode | SVGNode 
   */
  public $NodeOptions;
  protected $NodeType;
  protected $NodeShape;
  protected $NodeFill;
  protected $NodeBorderStyle;
  protected $NodeLabel;
  //---------Nodes options--------------------------------------------- END

  //---------Edges options---------------------------------------------
  public $EdgeOptions;
  protected $EdgePath;
  protected $EdgeLineStyle;
  protected $EdgeArrows;
  protected $EdgeLabel;

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
    $this->NodeOptions = new GML_NODEOPT();
    $this->EdgeOptions = new GML_EDGEOPT();

    $this->sBaseDir = dirname(__FILE__) . '/';
    $this->sDirectory = $sDirectory;

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

  public static function setOptionsNode() {    
    return new GML_NODEOPT;
  }
  public static function setOptionsEdge() {    
    return new GML_EDGEOPT;
  }
  
  
  private function setNodeDefault() {    
    $options = &$this->NodeOptions;
    $this->NodeType = $options->getOptions('NodeType');
    $this->NodeShape = $options->getOptions('NodeShape');
    $this->NodeFill = $options->getOptions('NodeFill');
    $this->NodeBorderStyle = $options->getOptions('NodeBorderStyle');
    $this->NodeLabel = $options->getOptions('NodeLabel');
  }

  private function setEdgeDefault() {
    $options = &$this->EdgeOptions;
    $this->EdgePath = $options->getOptions('EdgePath');
    $this->EdgeLineStyle = $options->getOptions('EdgeLineStyle');
    $this->EdgeArrows = $options->getOptions('EdgeArrows');
    $this->EdgeLabel = $options->getOptions('EdgeLabel');
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

    if ($iNodeHeight > $this->iMaxNodeHeight) {
      $this->iMaxNodeHeight = $iNodeHeight;
    }
    //$iNodeHeight = $this->iMaxNodeHeight;        
    // Count size node based on sise of font.
    $iNodeHeight = $this->NodeLabel['fontSize'] + 15;
    $iNodeWidth = mb_strlen($this->sNodeLabel) * 8.5;
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


    switch ($this->NodeType) {
      case 'UMLClassNode':
        $iNodeHeight = (count($this->aAttributes) + count($this->aMethods)) * 15 + 50;

        // Convert array to an array of string lengths
        $widthLabel = mb_strlen($this->sNodeLabel);
        $lengths = array_map('mb_strlen', $this->aAttributes);
        $max1 = max($lengths);
        $lengths = array_map('mb_strlen', $this->aMethods);
        $max2 = max($lengths);
        $maxWidth = max(array($max1, $max2, $widthLabel));
        $iNodeWidth = $maxWidth * 7.14;

        $wrapperInnerData = $dom->createElement('y:UMLClassNode');
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

        $wrapperInnerData->appendChild($UML);
        unset($UML);
        break;

      case 'ShapeNode':
        $iNodeWidth = mb_strlen($this->sNodeLabel) * 8.5;
        $wrapperInnerData = $dom->createElement('y:ShapeNode');
        $ShapeType = $dom->createElement('y:Shape');
        $ShapeType->setAttribute('type', $this->NodeShape['type']);
        $wrapperInnerData->appendChild($ShapeType);
        unset($ShapeType);

        break;

      default:
        $iNodeWidth = 169;
        break;
    }


    //--- Geometry shape
    $geometry = $dom->createElement('y:Geometry');
    $geometry->setAttribute('height', $iNodeHeight);
    $geometry->setAttribute('width', $iNodeWidth);
    $geometry->setAttribute('x', ($this->iNodeColumn * 200));
    $geometry->setAttribute('y', ($this->iNodeYPos));
    $wrapperInnerData->appendChild($geometry);
    unset($geometry);

    //--- Fill element
    $fill = $dom->createElement('y:Fill');
    foreach ($this->NodeFill as $key => $value) {
      $fill->setAttribute($key, $value);
    }
    $wrapperInnerData->appendChild($fill);
    unset($fill);

    //--- BorderStyle
    $BorderStyle = $dom->createElement('y:BorderStyle');
    foreach ($this->NodeBorderStyle as $key => $value) {
      $BorderStyle->setAttribute($key, $value);
    }
    $wrapperInnerData->appendChild($BorderStyle);
    unset($BorderStyle);

    //--- NodeLabel
    $NodeLabel = $dom->createElement('y:NodeLabel', $this->sNodeLabel);
    foreach ($this->NodeLabel as $key => $value) {
      $NodeLabel->setAttribute($key, $value);
    }
    $wrapperInnerData->appendChild($NodeLabel);
    unset($NodeLabel);


    $data2->appendChild($wrapperInnerData);
    unset($wrapperInnerData);
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
    echo "Count of EdgeData: " . count($this->aEdgeData) . PHP_EOL;
    $cnt = 0;
    foreach ($this->aEdgeData as $source => $values) {
      foreach ($values as $edge_item) {

        $edge = $dom->createElement('edge');
        $edge->setAttribute('id', 'e' . $this->iEdgeNumber++);
        $edge->setAttribute('source', '');
        $edge->setAttribute('target', '');

        $cnt++;
        echo "[$cnt]" . $this->echo_memory_usage();
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

        $this->sGeneratedEdges .= $dom->saveXML($edge) . PHP_EOL;
        //$this->echo_strsize(strlen($this->sGeneratedEdges));
        unset($edge);
      }
    }
    unset($dom);
  }

  protected function echo_strsize($mem_usage) {
    //$mem_usage = strlen($string); 

    if ($mem_usage < 1024)
      echo 'String size: ' . $mem_usage . " bytes";
    elseif ($mem_usage < 1048576)
      echo 'String size: ' . round($mem_usage / 1024, 2) . " kilobytes";
    else
      echo 'String size: ' . round($mem_usage / 1048576, 2) . " megabytes";

    echo PHP_EOL;
  }

  protected function echo_memory_usage() {
    $mem_usage = memory_get_usage(true);

    if ($mem_usage < 1024)
      echo $mem_usage . " bytes";
    elseif ($mem_usage < 1048576)
      echo round($mem_usage / 1024, 2) . " kilobytes";
    else
      echo round($mem_usage / 1048576, 2) . " megabytes";

    echo PHP_EOL;
  }

  /**
   *  Создание узла в графическом представлении
   * 
   *     $options = array(
   *   'NodeBorderStyle' => array(
   *     'color' => '#ffcc00'
   *     )
   *   ); 
   * @param string $NodeType  UMLClassNode || ShapeNode || GenericNode || SVGNode
   * @param string $sFileName filename to process 
   */
  public function addNode($sTitle, $NodeType = NULL, $options = NULL, $data = NULL) {
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
    if (!is_null($NodeType)) {
      $this->NodeType = $NodeType;
    }

    if (!is_null($data)) {
      if (isset($data['attributes']))
        $this->aAttributes = $data['attributes'];
      if (isset($data['methods']))
        $this->aMethods = $data['methods'];
    }
    $this->sNodeLabel = $sTitle;
    //$this->aClassData[$this->sNodeLabel] = $this->iNodeNumber;
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

  /**
   * Wrapper для облегчения добаввления узлов.
   * 
   * @param type $sTitle
   * @param type $options
   */
  public function addNodeShape($sTitle, $options) {
    $id = $this->addNode($sTitle, 'ShapeNode', $options);
    return $id;
  }

}

