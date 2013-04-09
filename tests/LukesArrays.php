<?php
/**
 *
 * @author Vladimir Gerasimov <freelancervip@gmail.com>
 * @link http://www.gradematic.com
 * @copyright Copyright &copy; 2012 Gradematic Inc.
 */

$questionnaire = array(
  //useful questionnaire info that does not regards to form builder
  //...
  //array of sections
  'sections'=>array(
    //first section, non-associative array, all sections will be numbered by quexml
    array(
      'sectionInfo'=>array(
        //The position within the section where the information is made available
        //For initial version only 'title' and 'before' will work
        'position'=>'title' || 'before' || 'during' || 'after',
        //The text of the information, for example, Demographics
        //user should enter this
        'text'=>'Demographics',
        //The mode where the information will be displayed.
        //"self" for self administered questionnaires such as web based and paper based.
        //"interviewer" for interviewer administered questionnaires such as CATI and CAPI
        //For initial version, should be 'self'
        'administration'=>'self',
      ),
      //non-associative array of all questions attached to current section
      'questions'=>array(
        //first question
        array(
          'id'=>'26634', //optional
          //The text of the question. If repeated, may be used to add carriage returns in some stylesheets
          'text'=>'What do you think about...',
          //A directive to the administrator of the questionnaire
          //We will omit this in first release
          'directive'=>array(),
          //If subQuestion elements are defined, the question is assumed to be a matrix question.
          //Each subQuestion becomes a new entry in the matrix.
          //A varName must be defined for each subQuestion
          //May or may not be presented, system will check
          //If presented, must be non-associative array of subquestions, even if only one subquestion defined
          'subQuestions'=>array(
            array(
              'text'=> 'Crocodiles',
              //A unique name for the variable to be associated with this matrix question
              'varName'=>'A1_A',
              //in first release we will omit this, but for future...
              'skip'=>array(),
            ),
            array(
              'text'=> 'hippopotamus',
              //A unique name for the variable to be associated with this matrix question
              'varName'=>'A1_B',
              //in first release we will omit this, but for future...
              'skip'=>array(),
            ),
            //...
          ),
          //Contains non-associative array of response(s) to the question.
          //If subQuestions are defined, each sub question will have the same response but the varName will be taken
          //from the subQuestion element, not this element
          'responses'=>array(
            //each response may be either 'free' or 'fixed', so we need to specify its type
            array(
              'type'=>'free',
              //Defines the type of data that may be entered; needed for queXF image parser
              //must be one of presented values
              'format'=>'text' || 'longtext' || 'integer' || 'date' || 'currency',
              //The maximum length of the data
              'length'=>24,
              //A label for the field
              'label'=>'First Name',
              //There are some other fields in schema, but we will omit 'em all in first release
              //I have implemented them in Questionnaire class already so it will be easier to add functionality needed in the future
            ),
            array(
              //A fixed response has categories defined
              'type'=>'fixed',
              //A category that may be selected
              //should be non-associative array of categories
              'categories'=>array(
                array(
                  'label'=>'Yes',
                  //omit in initial release
                  'skipTo'=>'B2',
                  //The value to assign to the label
                  'value'=>'1',
                  //A free response question that is to be asked only if this category is chosen. For example: Please specify
                  //Usually used with 'skipTo' responses
                  //we can omit it in initial release so we'll get only yes\no response (or similar to yes\no)
                  'contingentQuestion'=>array(
                    'text'=>'Last Name',
                    'length'=>100500,
                    //required field
                    //A unique name for the variable to be associated with this contingent question
                    'varName'=>'C1'
                  ),
                ),
                array(
                  'label'=>'No',
                  'value'=>'0',
                ),
              ),

            ),
            //...
          ),

        ),
        //second question, third, etc..
        //...
      ),

    ),
    //second section
    array(),
    //etc...
  ),
);
/*
 * All user created sections and questions should be sent in this format
 * My suggestion is to do all this work on client side (yeah, they have powerful PC) and then send this as json object
 * On the server-side we'll just encode this object into php array and then do some magic using Questionnaire class
 */