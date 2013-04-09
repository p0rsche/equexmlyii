<?php
/**
 * File: test.php
 * Created by Vladimir Gerasimov (freelancervip@gmail.com)
 * 14.03.12 15:28
 */
require_once(dirname(__FILE__) . '/../classes/Questionnaire.php');

//do some simple test
$qu = new Questionnaire('DCARF Example Questionnaire', 'Example', 1);
$qu->addDataCollector(new DataCollector(
  array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
  'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
  'http://www.deakin.edu.au/dcarf/'
));
$qu->addInvestigator(new Investigator(
  array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
  'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
  'http://www.deakin.edu.au/dcarf/'
));

$qu->addQuestionnaireInfo(new QuestionnaireInfo('before', 'This is class for Ari Shomair', 'self'));
$qu->addQuestionnaireInfo(new QuestionnaireInfo('after', 'Thanks for filling! Viva Ari!', 'self'));
/* creating new section */
$section = new Section();
$section->addSectionInfo('title', 'section b', 'self');
$section->addSectionInfo('before', 'Information about you', 'interviewer');
$question = new Question('What do you think about...');
$question->addDirective('during', 'Please cross the most appropriate box', 'self');
$question->addSubQuestion('The first part of a matrix question', 'A1_A');
$question->addSubQuestion('The second question in a matrix question', 'A1_B');
//when adding fixed response, it's important to add categories then (if you in need of matrix question)
$question->addFixedResponse('They are not useful', '1', 'A1');
$question->addCategory('They are useful', '2');
$question->addCategory('Don\'t Know', '3');
$section->addQuestion($question);
/* section end*/
$qu->addSection($section);
echo $qu->getXML();
