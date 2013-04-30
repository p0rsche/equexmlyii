equexmlyii
==========

PHP classes for easily creating QUeXML-based valid questionnaries XML files.

Basic usage
===========

First of all, we need to create new Questionnaire object which will keep all our data

    $qre = new Questionnaire('Title of questionnaire', 'Subtitle of questionnaire', 1);

1 is unique ID of questionnaire. QueXML requires it to be unique between questionnaries.

Next step is adding Data Collector and Investigator to our questionnaire

    $qre->addDataCollector(new DataCollector(
    array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
    'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
    'http://www.deakin.edu.au/dcarf/'
    ));

    $qu->addInvestigator(new Investigator(
    array('salutation'=>'Dr', 'firstName'=>'Betsy', 'lastName'=>'Blunsdon'),
    'Deakin University', array('street'=>'221 Burwood Highway', 'suburb'=>'BURWOOD', 'postcode'=>3125, 'country'=>'Australia'),
    'http://www.deakin.edu.au/dcarf/'
    ));

There must be a minimum of 1 investigator and 1 data collector.
Next, adding questionnaire info block

    $qre->addQuestionnaireInfo(new QuestionnaireInfo('before', 'This is class for your, my lord!', 'self'));

For additional details you may take a look at QuestionnaireInfo class, which is well-documented.

Okay, creating new Section of questionnaire

    $section = new Section();

...and add some section info; see details in Section class

    $section->addSectionInfo('title', 'section b', 'self');
    $section->addSectionInfo('before', 'Information about you', 'interviewer');

Then creating question to be attached to section

    $question = new Question('What do you think about...');

Adding directives

    $question->addDirective('during', 'Please cross the most appropriate box', 'self'); // see Question class for details

Adding subquestions

    $question->addSubQuestion('The first part of a matrix question', 'A1_A');
    $question->addSubQuestion('The second question in a matrix question', 'A1_B');

Adding responses. When adding fixed response, it's important to add categories then (if you in need of matrix question)

    $question->addFixedResponse('A1');
    $question->addCategory('They are useful', '2');
    $question->addCategory('Don\'t Know', '3');

Don't forget to attach your question to section created earlier.

    $section->addQuestion($question);

And attach section to our questionnaire

    $qre->addSection($section);

Final step is to get well-formed valid quexml representation of questionnaire

    echo $qu->getXML();
