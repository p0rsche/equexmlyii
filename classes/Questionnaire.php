<?php
/**
 * Questionnaire base class
 *
 * BASIC USAGE:
 *
 * $qre = new Questionnaire('Title of questionnaire', 'Subtitle of questionnaire', 1); // 1 is unique ID of questionnaire
 *
 * //adding data collector to questionnaire
 *
 * $qre->addDataCollector(new DataCollector(
 * array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
 * 'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
 * 'http://www.deakin.edu.au/dcarf/'
 * ));
 *
 * //adding investigator to questionnaire
 *
 *  $qu->addInvestigator(new Investigator(
 *  array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
 *  'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
 *  'http://www.deakin.edu.au/dcarf/'
 *  ));
 * // There must be a minimum of 1 investigator and 1 data collector
 *
 * //adding questionnaire info block
 * $qre->addQuestionnaireInfo(new QuestionnaireInfo('before', 'This is class for Ari Shomair', 'self')); //see QuestionnaireInfo class for details
 *
 * //creating new Section of questionnaire
 * $section = new Section();
 * //...and add some section info; see details in Section class
 * $section->addSectionInfo('title', 'section b', 'self');
 * $section->addSectionInfo('before', 'Information about you', 'interviewer');
 *
 * //creating question to be attached to section
 * $question = new Question('What do you think about...');
 * //adding directives
 * $question->addDirective('during', 'Please cross the most appropriate box', 'self'); // see Question class for details
 * //adding subquestions
 * $question->addSubQuestion('The first part of a matrix question', 'A1_A');
 * $question->addSubQuestion('The second question in a matrix question', 'A1_B');
 * //adding responses
 * //when adding fixed response, it's important to add categories then (if you in need of matrix question)
 *  $question->addFixedResponse('A1');
 *  $question->addCategory('They are useful', '2');
 *  $question->addCategory('Don\'t Know', '3');
 *
 * // ATTACHING QUESTIONG TO SECTION
 *
 *  $section->addQuestion($question);
 *  // section end
 *
 * //ATTACHING SECTION TO QUESTIONNAIRE
 * $qre->addSection($section);
 *
 * //..adding some more sections, questions, etc...
 *
 * //the final step: output well-formed valid quexml representation of questionnaire
 * echo $qu->getXML();
 *
/**
 * Requiring necessary files
 */
require_once(dirname(__FILE__).'/Person.php');
require_once(dirname(__FILE__).'/Section.php');
require_once(dirname(__FILE__).'/QuestionnaireInfo.php');
require_once(dirname(__FILE__).'/Question.php');

/**
 * Implements questionnaire object
 */
class Questionnaire
{
  private $defaults = array(
    'name' => array(
      'salutation' => null,
      'firstName' => 'John',
      'lastName' => 'Doe',
    ),
    'organisation' => null,
    'address' => array(),
    'phoneNumber' => null,
    'faxNumber' => null,
    'emailAddress' => null,
    'website' => null

  );
  /**
   * A unique id for this questionnaire. Defaults to an integer that is greater than or equal to 1 and  less than or equal to 999999
   * @var int Unique ID of questionnaire
   */
  private $id;
  /**
   * The title of the questionnaire
   * @var string Title
   */
  private $title;
  /**
   * The subtitle of the questionnaire (if relevant)
   * @var string Subtitle
   */
  private $subtitle;
  /**
   * @var array Set of investigators
   */
  private $investigators=array();
  /**
   * @var array Set of data collectors
   */
  private $dataCollectors=array();
  /**
   * @var array Contains questionnaireInfo blocks
   */
  private $questionnaireInfo=array();
  /**
   * @var array Contains all questionnaire sections
   */
  private $sections = array();
  /**
   * @return int ID of questionnaire
   */
  public function getid(){
    return $this->id;
  }
  /**
   * @return string Overloaded methods which returns well-formated queXML string
   */
  public function __toString(){
    return $this->getXML();
  }
  /**
   * @return string Returns well-formatted queXML string of questionnaire
   */
  public function getXML(){
    $schema = Yii::app()->getBaseUrl(true) . '/shared/quexml.xsd';
    $qre = '<?xml version="1.0" encoding="UTF-8"?>';
    $qre .= '<questionnaire xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="'.$schema.'" id="'.$this->id.'">';
    $qre .='<title>'.$this->title.'</title>';
    if(!empty($this->subtitle))
      $qre .='<subtitle>'.$this->subtitle.'</subtitle>';
    //adding investigators and data collectors
    $inv = count($this->investigators);
    if($inv == 0)
      trigger_error("There must be at least 1 investigator", E_USER_ERROR);
    for($i=0;$i<$inv;$i++){
      $qre .= $this->investigators[$i];
    }
    $dc = count($this->dataCollectors);
    if($dc == 0)
      trigger_error("There must be at least 1 data collector", E_USER_ERROR);
    for($i=0;$i<$dc;$i++){
      $qre .= $this->dataCollectors[$i];
    }
    //adding questionnaire info blocks
    for($i=0;$i<count($this->questionnaireInfo);$i++){
      $qre .= $this->questionnaireInfo[$i];
    }
    //adding questionnaire info blocks
    for($i=0;$i<count($this->sections);$i++){
      $qre .= $this->sections[$i];
    }
    $qre .= '</questionnaire>';

    return $qre;
  }
  /**
   * Default constructor
   * @param string $title The title of the questionnaire
   * @param string $subtitle The subtitle of the questionnaire (if relevant)
   * @param int $id Unique questionnaire ID
   */
  public function __construct($title, $subtitle=null, $id=null){
    $this->title = $title;
    $this->subtitle = $subtitle;
    if(!isset($id))
      $this->id = rand(0,999);
    else
      $this->id = $id;
  }
  /**
   * Adds investigator to the questionnaire
   * @param Investigator $person Investigator object
   */
  public function addInvestigator(Investigator $person){
      $this->investigators[] = $person->getXml();
  }
  /**
   * Adds investigator to the questionnaire
   * @param DataCollector $person DataCollector object
   */
  public function addDataCollector(DataCollector $person){
    $this->dataCollectors[] = $person->getXml();
  }
  /**
   * Adds questionnaire info to the questionnaire
   * @param QuestionnaireInfo $info QuestionnaireInfo object
   */
  public function addQuestionnaireInfo(QuestionnaireInfo $info){
    $this->questionnaireInfo[] = $info->getXml();
  }
  /**
   * Adds section to the questionnaire
   * @param Section $section Section object
   */
  public function addSection(Section $section){
    $this->sections[] = $section->getXml();
  }
  //------------ useful helper methods -----------------------//
  public function addDefaultInvestigator($firstName, $lastName, $email){
    $this->addDefaultPerson('investigator', $firstName, $lastName, $email);
  }
  public function addDefaultDataCollector($firstName, $lastName, $email){
    $this->addDefaultPerson('datacollector', $firstName, $lastName, $email);
  }
  public function addDefaultPerson($type, $firstName = null, $lastName = null, $email = null){
    $firstName = $firstName ? $firstName : $this->defaults['name']['firstName'];
    $lastName = $lastName ? $lastName : $this->defaults['name']['lastName'];
    $email = $email ? $email : $this->defaults['emailAddress'];
    switch($type){
      case 'investigator':
        $person = new Investigator(array('firstName' => $firstName, 'lastName'=> $lastName));
        $person->emailAddress = $email;//all is OK, I have implemented __set method with restrictions

        $this->addInvestigator($person);
        break;
      case 'datacollector':
        $person = new DataCollector(array('firstName' => $firstName, 'lastName'=> $lastName));
        $person->emailAddress = $email;

        $this->addDataCollector($person);
        break;
    }
  }
}
