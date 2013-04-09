<?php
/**
 * File: QuestionnaireInfo.php
 * Created by Vladimir Gerasimov (freelancervip@gmail.com)
 * 14.03.12 17:01
 */
/**
 * Represents questionnaireInfo block of Questionnaire
 */
class QuestionnaireInfo {
  /**
   * @var string $position Should be one of the following: before, during, after or appendix
   */
  private $position;
  /**
   * @var array $text Text blocks
   */
  private $text=array();
  /*
   * Administration: self or interviewer
   * @var string $administration
   */
  private $administration;

  private $_positions = array('before', 'during', 'after', 'appendix');
  private $_administrations = array('self', 'interviewer');

  public function __construct($position, $text, $administration){
    $this->addSingleQuestionnaireInfo($position, $text, $administration);
  }
  /**
   * Adds text to questionnaireInfo block
   * @param $text
   */
  public function addText($text){
    $this->text[] = $text;
  }
  /**
   * Adds single questionnaireInfo block
   * @param $position
   * @param $text
   * @param $administration
   * @throws Exception
   */
  private function addSingleQuestionnaireInfo($position, $text, $administration){
    if(!in_array($position, $this->_positions))
      throw new Exception("Unknown position", 1);
    if(!in_array($administration, $this->_administrations))
      throw new Exception("Unknown administration", 1);
    $this->position = $position;
    $this->text[] = $text;
    $this->administration = $administration;
  }
  /**
   * Overloads toString method to return well-formed XML as output
   * @return string
   */
  public function __toString(){
    return $this->getXML();
  }
  /**
   * Returns well-formed XML string of questionnaireInfo
   * @return string
   */
  public function getXML(){
    $str = '<questionnaireInfo>';
    $str .='<position>'.$this->position.'</position>';
    for($i=0;$i<count($this->text);$i++){
      $str .='<text>'.$this->text[$i].'</text>';
    }
    $str .='<administration>'.$this->administration.'</administration>';
    $str .= '</questionnaireInfo>';

    return $str;
  }
}
