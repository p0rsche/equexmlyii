equexmlyii
==========

PHP classes for easily creating QUeXML-based valid questionnaries XML files.

Basic usage
===========

    $qre = new Questionnaire('Title of questionnaire', 'Subtitle of questionnaire', 1); // 1 is unique ID of questionnaire

    //adding data collector to questionnaire

    $qre->addDataCollector(new DataCollector(
    array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
    'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
    'http://www.deakin.edu.au/dcarf/'
    ));

    //adding investigator to questionnaire

    $qu->addInvestigator(new Investigator(
    array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
    'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
    'http://www.deakin.edu.au/dcarf/'
    ));
    // There must be a minimum of 1 investigator and 1 data collector

    //adding questionnaire info block
    $qre->addQuestionnaireInfo(new QuestionnaireInfo('before', 'This is class for Ari Shomair', 'self')); //see QuestionnaireInfo class for details

    //creating new Section of questionnaire
    $section = new Section();
    //...and add some section info; see details in Section class
    $section->addSectionInfo('title', 'section b', 'self');
    $section->addSectionInfo('before', 'Information about you', 'interviewer');

    //creating question to be attached to section
    $question = new Question('What do you think about...');
    //adding directives
    $question->addDirective('during', 'Please cross the most appropriate box', 'self'); // see Question class for details
    //adding subquestions
    $question->addSubQuestion('The first part of a matrix question', 'A1_A');
    $question->addSubQuestion('The second question in a matrix question', 'A1_B');
    //adding responses
    //when adding fixed response, it's important to add categories then (if you in need of matrix question)
    $question->addFixedResponse('A1');
    $question->addCategory('They are useful', '2');
    $question->addCategory('Don\'t Know', '3');

    // ATTACHING QUESTIONG TO SECTION

    $section->addQuestion($question);
    // section end

    //ATTACHING SECTION TO QUESTIONNAIRE
    $qre->addSection($section);

    //..adding some more sections, questions, etc...

    //the final step: output well-formed valid quexml representation of questionnaire
    echo $qu->getXML();