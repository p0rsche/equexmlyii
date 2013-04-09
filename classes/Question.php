<?php
/**
 * File: Question.php
 * Created by Vladimir Gerasimov (freelancervip@gmail.com)
 * 14.03.12 17:01
 */
/**
 * Represents a question of questionnaire
 */
class Question {
  /**
   * @var string The text of the question. If repeated, may be used to add carriage returns in some stylesheets
   */
  private $text;
  /**
   * @var array Set of directives
   */
  private $directives = array();
  /**
   * @var array Set of subquestions
   */
  private $subQuestions = array();
  /**
   * @var array Set of responses
   */
  private $responses = array();
  /**
   * @var array Allowed positions
   */
  private $_directive_positions = array('before', 'during', 'after');
  /**
   * @var array Allowed administrations
   */
  private $_directive_administrations = array('self', 'interviewer');
  /**
   * Default constructor
   * @param string $text The text of the question. @see text
   */
  public function __construct($text){
    $this->text = $text;
  }
  /**
   * A directive to the administrator of the questionnaire
   * @param string $position The position within the question where the information is made available
   * @param string $text The text of the directive (for example: Enter numbers only)
   * @param string $administration The mode where the directive will be displayed. "self" for self administered questionnaires such as web based and paper based. "interviewer" for interviewer administered questionnaires such as CATI and CAPI
   * @throws Exception
   */
  public function addDirective($position, $text, $administration){
    if(!in_array($position, $this->_directive_positions))
      throw new Exception('Unknown directive position');
    if(!in_array($administration, $this->_directive_administrations))
      throw new Exception('Unknown directive administration');
    $directive = array();
    $directive['position'] = $position;
    $directive['text'] = $text;
    $directive['administration'] = $administration;

    $this->directives[] = $directive;
  }
  /**
   * If subQuestion elements are defined, the question is assumed to be a matrix question.
   * Each subQuestion becomes a new entry in the matrix. A varName must be defined for each subQuestion
   * @param string $text The text of the sub question
   * @param string $varName A unique name for the variable to be associated with this matrix question.
   * @param array $skip Used to skip from a particular sub question
   * <code>
   * $skip = array(
   *   'ifValue'=>'Skip to the target defined below only if the value of the response is defined here',
   *   'to'=>'The skip target - the varName attribute'
   * );
   * </code>
   */
  public function addSubQuestion($text, $varName, array $skip = null){
    $subQuestion = array();
    $subQuestion['varName'] = $varName;
    $subQuestion['text'] = $text;
    if(($skip) && !empty($skip['ifValue']) && !empty($skip['to']))
      $subQuestion['skip'] = $skip;

    $this->subQuestions[] = $subQuestion;
  }
  /**
   * Adds a free response.
   * A free response is an open field.
   * @param string $format Defines the type of data that may be entered. Valid values are text, longtext, integer, date or currency
   * @param int $length The maximum length of the data
   * @param string|null $label A label for the field
   * @param string|null $varName A unique name for the variable to be associated with this response. Will appear in the data output.
   * @param string|null $min A minimum value
   * @param string|null $max A maximum value
   * @param string|null $skipTo A skip target
   */
  public function addFreeResponse($format, $length, $label = null, $varName = null, $min=null, $max=null, $skipTo=null){
    $freeresponse = array();
    $freeresponse['format'] = $format;
    $freeresponse['length'] = $length;
    if(!empty($label))
      $freeresponse['label'] = $label;
    if(!empty($min))
      $freeresponse['min'] = $min;
    if(!empty($max))
    $freeresponse['max'] = $max;
    if(!empty($skipTo))
      $freeresponse['skipTo'] = $skipTo;
    $resp = array();
    if(!empty($varName))
      $resp['varName'] = $varName;
    else
      $resp['varName'] = rand(0,999);//TODO change this to check availability
    $resp['type'] = 'free';
    $resp['data'] = $freeresponse;

    $this->responses[] = $resp;
  }
  /**
   * Adds a single fixed response.
   * A fixed response has categories defined.
   *
   * @param string $label A label
   * @param string $value The value to assign to the label
   * @param array|null $contingentQuestion A free response question that is to be asked only if this category is chosen. For example: Please specify
   * @param string|null $skipTo A target to skip to if this category is selected.
   * Be wary of the use of this element where subQuestions are defined, as it will apply to each sub question.
   * Use the skip element of the subQuesstion tag instead
   * @param string|null $image An image to assign to the category. Not supported by queXML PDF parser. Added for compatibility with XSD-schema
   * @return array
   */
  private function addSingleCategory($label, $value, $contingentQuestion = null, $skipTo = null, $image = null){
    $category = array();
    $category['label'] = $label;
    $category['value'] = $value;
    if(!empty($contingentQuestion) && !empty($contingentQuestion['text'])){
      if(!isset($contingentQuestion['text']) or !isset($contingentQuestion['length']))
        trigger_error('Can\'t find neccessary fields of contingent question', E_USER_ERROR);
      if(!isset($contingentQuestion['varName']))
        $contingentQuestion['varName'] = rand(0, 99);
      $category['contingentQuestion'] = $contingentQuestion;
    }
    if(!empty($skipTo))
      $category['skipTo'] = $skipTo;

    return $category;
  }
  /**
   * Adds a fixed response.
   * A fixed response has categories defined.
   *
   * @param string $label A label
   * @param string $value The value to assign to the label
   * @param string $varName A unique name for the variable to be associated with this response. Will appear in the data output.
   * @param array|null $contingentQuestion A free response question that is to be asked only if this category is chosen. For example: Please specify
   * @param string|null $skipTo A target to skip to if this category is selected.
   * Be wary of the use of this element where subQuestions are defined, as it will apply to each sub question.
   * Use the skip element of the subQuesstion tag instead
   * @param string|null $image An image to assign to the category. Not supported by queXML PDF parser. Added for compatibility with XSD-schema
   * @return array
   */
  public function addFixedResponse($varName){
    $resp = array();
    $resp['varName'] = $varName;
    $resp['type'] = 'fixed';
    //$resp['data'][] = $this->addFixedResponseSingle($varName);
    $pos = array_push($this->responses, $resp);
    //returning position so we can use it to paste additional categories
    return $pos;
  }
  /**
   * Adds category to previously created fixed response
   *
   * @param string $label A label
   * @param string $value The value to assign to the label
   * @param array|null $contingentQuestion A free response question that is to be asked only if this category is chosen. For example: Please specify
   * @param string|null $skipTo A target to skip to if this category is selected.
   * Be wary of the use of this element where subQuestions are defined, as it will apply to each sub question.
   * Use the skip element of the subQuestion tag instead
   * @param array|null $image An image to assign to the category. Not supported by queXML PDF parser. Added for compatibility with XSD-schema
   * @param null $id ID of response to be attached. Defaults to last added response
   */
  public function addCategory($label, $value, array $contingentQuestion = null, $skipTo = null, $image=null, $id=null){
    //if isset $id then use this id as array pointer, otherwise using last pasted response
    $pos = $id ? $id : (count($this->responses)-1);
    if($this->responses[$pos]['type'] != 'fixed')
      trigger_error("Category must be attached only to fixed response. If you've setting up it by hands, check this value. Otherwise, add fixed response first", E_USER_ERROR);
    $this->responses[$pos]['data'][] = $this->addSingleCategory($label, $value, $contingentQuestion, $skipTo, $image);
  }
  /**
   * @return string well-formatted queXMl string
   */
  public function getXML(){
    return $this->__toString();
  }
  public function __toString(){
    $str = '<question>';
    $str .= '<text>'.$this->text.'</text>';
    //directives
    $str .= $this->directivesToXml();
    //subQuestions
    $str .= $this->subQuestionsToXml();
    //response
    $str .= $this->responsesToXml();
    $str .= '</question>';

    return $str;
  }

  /**
   * Responses in XML
   * @return string well-formatted queXMl string
   */
  private function responsesToXml(){
    $str = '';
    for($i=0;$i<count($this->responses);$i++){
      $str .= '<response varName="'.$this->responses[$i]['varName'].'">';
      if(($this->responses[$i]['type']) == 'free'){
        $str .= '<free>';
        foreach($this->responses[$i]['data'] as $key=>$value){
          $str .= '<'.$key.'>'.$value.'</'.$key.'>';
        }
        $str .= '</free>';
        $str .= '</response>';
      }
      else {
        $str .= '<fixed>';
        for($y=0; $y<count($this->responses[$i]['data']);$y++){
          $str .= '<category>';
          $str .= '<label>'.$this->responses[$i]['data'][$y]['label'].'</label>';
          $str .= '<value>'.$this->responses[$i]['data'][$y]['value'].'</value>';
          if(isset($this->responses[$i]['data'][$y]['contingentQuestion'])){
            $str .= '<contingentQuestion varName="'.$this->responses[$i]['data'][$y]['contingentQuestion']['varName'].'">';
            $str .= '<text>'.$this->responses[$i]['data'][$y]['contingentQuestion']['text'].'</text>';
            $str .= '<length>'.$this->responses[$i]['data'][$y]['contingentQuestion']['length'].'</length>';
            $str .= '</contingentQuestion>';
          }
          if(isset($this->responses[$i]['data'][$y]['skipTo']))
            $str .= '<skipTo>'.$this->responses[$i]['data'][$y]['skipTo'].'</skipTo>';
          $str .= '</category>';
        }
        $str .= '</fixed>';
        $str .= '</response>';
      }
    }
    return $str;
  }
  /**
   * Directives in XML
   * @return string well-formatted queXML string
   */
  private function directivesToXml(){
    $str = '';
    for($i=0;$i<count($this->directives);$i++){
      $str .= '<directive>';
      $str .= '<position>'.$this->directives[$i]['position'].'</position>';
      $str .= '<text>'.$this->directives[$i]['text'].'</text>';
      $str .= '<administration>'.$this->directives[$i]['administration'].'</administration>';
      $str .= '</directive>';
    }
    return $str;
  }
  /**
   * SubQuestions in XML
   * @return string well-formatted queXML-string
   */
  private function subQuestionsToXml(){
    $str = '';
    for($i=0;$i<count($this->subQuestions);$i++){
      $str .= '<subQuestion varName="'.$this->subQuestions[$i]['varName'].'">';
      $str .= '<text>'.$this->subQuestions[$i]['text'].'</text>';
      if(isset($this->subQuestions[$i]['skip'])){
        $str .= '<skip>';
        $str .= '<ifValue>'.$this->subQuestions[$i]['skip']['ifValue'].'</ifValue>';
        $str .= '<to>'.$this->subQuestions[$i]['skip']['to'].'</to>';
        $str .= '</skip>';
      }
      $str .= '</subQuestion>';
    }
    return $str;
  }
}
