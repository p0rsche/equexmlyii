<?php
/**
 * File: Section.php
 * Created by Vladimir Gerasimov (freelancervip@gmail.com)
 * 14.03.12 17:00
 */
class Section {
  /**
   * @var bool|null Last attribute of the response or not. Not supported by queXML
   */
  private $attr_last;
  /*
   * respondent selection
   */
  private $attr_respondentselection;
  /**
   * @var array set of section infos
   */
  private $sectionInfos = array();
  /**
   * @var array set of questions
   */
  private $questions = array();
  /**
   * @var array allowed section info positions
   */
  private $_positions = array('before', 'during', 'after', 'title');
  /**
   * @var array allowed section info administrations
   */
  private $_administrations = array('self', 'interviewer');

  /**
   * Default constructor
   *
   * @param bool|null $last If this section last of the questionnaire
   * @param bool|null $respondentselection If this section is the respondent selection part of the questionnaire
   */
  public function __construct($last=null, $respondentselection=null){
    $this->attr_last = $last;
    $this->attr_respondentselection = $respondentselection;
  }
  /**
   * Adds section information
   *
   * @param string $position The position within the section where the information is made available. Must be either before, during, after or title
   * @param string $text The text of the information (for example: Demographics)
   * @param string $administration The mode where the information will be displayed.
   * "self" for self administered questionnaires such as web based and paper based.
   * "interviewer" for interviewer administered questionnaires such as CATI and CAPI
   */
  public function addSectionInfo($position, $text, $administration){
    $this->addSingleSectionInfo($position, $text, $administration);
  }
  /**
   * Adds single section info to set of section infos
   *
   * @param string $position The position within the section where the information is made available. Must be either before, during, after or title
   * @param string $text The text of the information (for example: Demographics)
   * @param string $administration The mode where the information will be displayed.
   * "self" for self administered questionnaires such as web based and paper based.
   * "interviewer" for interviewer administered questionnaires such as CATI and CAPI
   * @param string|null $image Display an image. Not supported by queXML PDF parser. Added for XSD-schema compatibility
   * @throws Exception
   */
  private function addSingleSectionInfo($position, $text, $administration, $image=null){
    if(!in_array($position, $this->_positions))
      throw new Exception("Unknown position", 1);
    if(!in_array($administration, $this->_administrations))
      throw new Exception("Unknown administration", 1);
    $ssinfo = array();
    $ssinfo['position'] = $position;
    $ssinfo['text'][] = $text;
    $ssinfo['administration'] = $administration;
    $this->sectionInfos[] = $ssinfo;
  }
  /**
   * Adds text to previosly created section info
   *
   * @param string $text Text to be pasted into section info
   */
  public function addSectionInfoText($text){
    $pos = count($this->sectionInfos);
    $this->sectionInfos[$pos-1]['text'][] = $text;
  }
  /**
   * Returns well-formed sectionInfo XML
   *
   * @return string $str well-formatted queXML string
   */
  public function sectionInfoXml(){
    $str = '';
    for($i=0;$i<count($this->sectionInfos);$i++){
      $str .= '<sectionInfo>';
      $str .= '<position>'.$this->sectionInfos[$i]['position'].'</position>';
      for($y=0;$y<count($this->sectionInfos[$i]['text']);$y++){
        $str .= '<text>'.$this->sectionInfos[$i]['text'][$y].'</text>';
      }
      $str .= '<administration>'.$this->sectionInfos[$i]['administration'].'</administration>';
      $str .= '</sectionInfo>';
    }
    return $str;
  }
  public function addQuestion(Question $question){
    $this->questions[] = $question->getXML();
  }
  /**
   * Returns well-formatted quexml question string
   *
   * @return string well-formatted quexml question string
   */
  private function questionXml(){
    $str = '';
    for($i=0;$i<count($this->questions);$i++){
      $str .= $this->questions[$i];
    }
    return $str;
  }
  /**
   * Overloaded method. Returns well-formatted quexml section string
   *
   * @return string well-formatted quexml section string
   */
  public function __toString(){
    $str = '<section>';
    $str .= $this->sectionInfoXml();
    $str .= $this->questionXml();
    $str .= '</section>';
    return $str;
  }
  /**
   * Returns well-formatted quexml section string
   *
   * @return string well-formatted quexml section string
   */
  public function getXML(){
    return $this->__toString();
  }

}
