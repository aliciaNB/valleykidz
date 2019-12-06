<?php
//error reporting
ini_set('display_errors', 1);
ini_set('file_uploads',1);
error_reporting(E_ALL);

//required files for classes and validation
require_once("vendor/autoload.php");
require_once("model/validation.php");

//instantiate fat free and session
$f3 = Base::instance();
session_start();

$db = new database();
$f3->set('db', $db);
//-----------------------------------------------------Arrays-----------------------------------------------------------

$dates = array(
  "Mon","Tue","Wed","Thurs","Fri","Sat","Sun"
);

$f3->set('skillcategory', array(
    'Core Mindfulness' => array('Wise Mind', 'Observe', 'Describe', 'Participate', 'Nonjudgmental Stance',
                                'One-mindfully', 'Effectiveness'),
    'Interpersonal Effectiveness' => array('Objective Effectiveness', 'Relationship Effectiveness', 'Self-Respect Effectiveness'),
    'Emotion Regulation' => array('Identifying Primary Emotions', 'Checking the Facts',
                        'Problem Solving', 'Opposite-to-emotion Action', 'Acquire Positives in the Short-term',
                        'Acquire Positives in the Long-term', 'Build Mastery', 'Cope Ahead', 'PLEASE',
                        'Mindfulness to Current Emotion'),
    'Distress Tolerance' => array('TIPP', 'Distract', 'Self-soothe', 'IMPROVE', 'Pros and Cons', 'Half-smile',
                                  'Radical acceptance', 'Turning the Mind', 'Willingness')
));

//-----------------------------------------------------ROUTES-----------------------------------------------------------
//default route
$f3->route('GET|POST /', function ($f3) {
    global $db;
    //TODO validate db user clinician/patient
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $db->getUser($_POST['user'],$_POST['pass']);
        if ($result === 0) { //clinician
            $_SESSION['uuid'] = $db->getClinicianID($_POST['user']);
            $f3->reroute('/branchprofile');
        } elseif ($result === 1) { //member
            $_SESSION['uuid'] = $_POST['user'];
            $f3->reroute('/memberprofile');
        } else if ($result === 2) { //admin
            $_SESSION['uuid']= $_POST['user'];
            $f3->reroute('/adminprofile');
        } else {
            $f3->set('error', $result);
        }
    }

    $f3->set('redirect', $_SESSION['redirect']);
    //destroy old sessions
    session_destroy();

    $view = new Template();
    echo $view->render('view/home.html');
});

//dbt create
$f3->route('GET|POST /createdbt', function ($f3) {
    global $db;
    $f3->set('id', $_GET['id']);

    if ($db->getuserType($_SESSION['uuid'])!=="cln") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    $priorFormEmotions = $db->getRecentCustomEmotions($_GET['id']);
    $priorFormTargets =$db->getRecentCustomTargets($_GET['id']);
    $f3->set('priorTarg', $priorFormTargets);
    $f3->set('priorEmo', $priorFormEmotions);

    //-------------------------------------------POST LOGIC--------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //set session variable for custoer targets
        $_SESSION['confirmTargets'] = $_POST['targets'];
        $_SESSION['confirmEmotions'] = $_POST['feelings'];

        //create new form and get its id closing old form that exists
        $formNum = $db->createForm($_GET['id']);

        //insert all the defaults at this moment form the table
        $db->insertDefaultSkills($formNum);
        $db->insertDefaultEmotions($formNum);
        $db->insertDefaultTargets($formNum);

        //-------------------------Add custom emotions and targets on post----------------------------------
        if ($_POST['feelings']) {//check if post array has values

            foreach ($_POST['feelings'] as $feeling) {//check each feeling

                if($feeling!=="") {//if feeling in post was empty ignore and do not place in db

                    $result = $db->getEmotionId($feeling);
                    if(!$result) {//id does not exist in table

                        $result=$db->insertEmotion($feeling);//add to table
                    }
                    $db->insertCustomEmotions($formNum, $result);//insert into association table to create form
                }
            }
        }

        if($_POST['targets']) {
            foreach ($_POST['targets'] as $target) {

                if($target!=="") {
                    $result = $db->getTargetId($target);

                    if(!$result) {//id does not exist in table

                        $result=$db->insertTarget($target);//add to table
                    }
                    $db->insertCustomTargets($formNum, $result);//insert into association table to create form
                }
            }
        }

        $f3->reroute('/confirmdbtform');
    }
    //grab default targets and emotions
    $defaultTargets = $db->getDefaultTargets();
    $defaultEmotions=$db->getDefaultEmotions();
    $f3->set("targets", $defaultTargets);
    $f3->set("emotions", $defaultEmotions);
    $view = new Template();
    echo $view->render('view/createdbt.html');
});

//group leader dashboard page
$f3->route('GET|POST /branchprofile', function ($f3) {
    global $db;
    $f3->set('db', $db);
    if ($db->getuserType($_SESSION['uuid'])!=="cln") { //check if appropriate user on page redirect to home if not
        //$_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/memberprofile');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if(isset($_POST['id'])) {
            $db->closeForm(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
        } else{
            $arrayErr = array("addErr" => validateClientNumber($_POST['clientnum']),);

            if (checkErrArray($arrayErr)) {

                if (isset($_POST['add'])) { //if add client update db on groups leader to reference client #
                    $error=$db->addClient($_SESSION['uuid'], $_POST['clientnum']);

                    if ($error) {
                       $f3->set('dberror', $error);
                    }

                } elseif (isset($_POST['remove'])) { //if remove selected remove from goupp leader reference to client
                    $error=$db->removeClient($_SESSION['uuid'], $_POST['clientnum']);

                    if($error) {
                        $f3->set('dberror', $error);
                    }
                }
            }
            $f3->set('errors', $arrayErr);
        }
    }

    $result = $db->getLinks($_SESSION['uuid']);
    $f3->set('links', $result);

    $view = new Template();
    echo $view->render('view/branchprofile.html');
});

//client dashboard page
$f3->route('GET|POST /memberprofile', function ($f3) {
    $view = new Template();
    global $db;

    $f3->set('dateRange', $f3->get('db')->getDateRange($_SESSION['uuid']));
    $currentDate = new DateTime('Today');
    $f3->set('currentDate', $currentDate->format('M. d'));
    //var_dump($f3->get('dateRange'));

    if ($db->getuserType($_SESSION['uuid'])!=="cl") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_SESSION['chosenDate'] = $_POST['date'];
        $clientData = $f3->get('db')->getClientFormData($_SESSION['uuid'], $_SESSION['chosenDate']);
        $_SESSION['clientData'] = $clientData;
        $f3->reroute('/dbtdiary');
    }

    echo $view->render('view/memberprofile.html');
});

//admin portal route
$f3->route('GET|POST /adminprofile', function ($f3) {
    $view = new Template();
    global $db;
    $f3->set('clientAcc', 'active');

    if ($db->getuserType($_SESSION['uuid'])!=="a") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    $f3->set('clientAccCreate', array("tab"=>"active", "form"=>"show active"));
    $f3->set('clinicianAccCreate', array("tab"=>"", "form"=>""));
    $f3->set('clientAccChange', array("tab"=>"", "form"=>""));
    $f3->set('clinicianAccChange', array("tab"=>"", "form"=>""));

    //Check if create client account form is the form submitted on the page
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'createClient') {
        $clientId = $_POST['newClientId'];
        $password = $_POST['ncPassword'];
        $password2 = $_POST['ncPasswordConfirm'];

        $f3->set('clientId', $clientId);
        $f3->set('password', $password);
        $f3->set('password2', $password2);

        //validate the form, if not valid display error
        if (validCreateClientForm()) {
            //check if client id does not already exists
            if ($f3->get('db')->checkIfClientExists($clientId)) {
                $f3->set("errors['newClientId']", 'Client ID already exists');
            } else { //otherwise valid create the account
                $result = $f3->get('db')->insertClientAccount($clientId, $password);

                if ($result) { // success
                    $f3->set('clientAccSuccess', "Account for Client Id: " . $clientId . " successfully created.");
                } else {
                    // otherwise display something went wrong.
                    $f3->set("errors['clientAccFail']", 'Something went wrong on our end. Please try again.');
                }
            }
        }
    }

    //Check if create clinician/group leader account is the form submitted on the page
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'createClinician') {

        //set default pill
        $f3->set('clientAccCreate', array("tab"=>"", "form"=>""));
        $f3->set('clinicianAccCreate', array("tab"=>"active", "form"=>"show active"));

        $clnUsername = $_POST['nClnUsername'];
        $clnPassword = $_POST['nclnPassword'];
        $clnPassword2 = $_POST['nclnPasswordConfirm'];
        $f3->set('clnUsername', $clnUsername);
        $f3->set('clnPassword', $clnPassword);
        $f3->set('clnPassword2', $clnPassword2);

        //validate the form, if not valid display error
        if (validCreateClinicianForm()) {
            //check if the clinician id does not already exist
            if ($f3->get('db')->checkIfClinicianUsernameExists($clnUsername)){
                $f3->set("errors['clnUsername']", 'Username already exists');
            } else { // otherwise valid create the account,
                //call db inserts
                $result = $f3->get('db')->insertClinicianAccount($clnPassword, $clnUsername);
                if ($result) {
                    $f3->set('clnAccSuccess', "Account for Clinician: " . $clnUsername . " successfully created.");
                } else {
                    $f3->set("errors['clnAccFail']", 'Something went wrong on our end. Please try again.');
                }
            }
        }
    }

    //Check if change client account password is the form submitted on the page
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'changeClientPassword') {

        //set default pill
        $f3->set('clientAccCreate', array("tab"=>"", "form"=>""));
        $f3->set('clientAccChange', array("tab"=>"active", "form"=>"show active"));

        $f3->set('clientAcc', '');
        $f3->set('clientPass', 'active');

        $clientId = $_POST['chgClientPwId'];
        $chgPwNewPw = $_POST['chgPwNewPw'];
        $chgPwNewPw2 = $_POST['chgPwNewPwConfirm'];

        $f3->set('chgClientPwId', $clientId);
        $f3->set('chgPwNewPw', $chgPwNewPw);
        $f3->set('chgPwNewPw2', $chgPwNewPw2);

        //validate the form, if not valid display error
        if (validChangeClientPasswordForm()) {

            //check if client exists
            if ($f3->get('db')->checkIfClientExists($clientId)) {

                // if exists run update password for client statement
                $result = $f3->get('db')->changeClientPassword($clientId, $chgPwNewPw);
                if ($result) {
                    $f3->set('updatePwSuccess', "Password changed successfully for Client Id: " . $clientId);
                } else {
                    $f3->set("errors['updatePwFail']", 'Something went wrong on our end. Please try again.');
                }
            } else {
                $f3->set("errors['chgClientPwId']", 'Client id does not match any existing records.');
            }
        }
    }

    //Check if change clinician account password is the form submitted on the page
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'changeClnPassword') {

        //set default pill
        $f3->set('clientAccCreate', array("tab"=>"", "form"=>""));
        $f3->set('clinicianAccChange', array("tab"=>"active", "form"=>"show active"));

        $f3->set('clientAcc', '');
        $f3->set('clinicianPass', 'active');

        $clnUsername = $_POST['chgPwClnUsername'];
        $chgPwClnNewPw = $_POST['chgPwClnNewPw'];
        $chgPwClnNewPw2 = $_POST['chgPwClnNewPw2'];

        $f3->set('chgPwClnUsername', $clnUsername);
        $f3->set('chgPwClnNewPw', $chgPwClnNewPw);
        $f3->set('chgPwClnNewPw2', $chgPwClnNewPw2);

        if (validChangeClnPasswordForm()) {
            //check if clinician username exists
            if ($f3->get('db')->checkIfClinicianUsernameExists($clnUsername)) {

                //if exists run update, retrieve clinician_id and run update password for clinician statement
                $id = $f3->get('db')->checkIfClinicianUsernameExists($clnUsername);
                $result = $f3->get('db')->changeClinicianPassword($id, $chgPwClnNewPw);

                if ($result) {
                    $f3->set('updateClnPwSuccess', "Password changed successfully for Clinician: " . $clnUsername);
                } else {
                    $f3->set("errors['updateClnPwFail']", 'Something went wrong on our end. Please try again.');
                }
            } else {
                $f3->set("errors['chgPwClnUsername']", 'Username does not match any existing records.');
            }
        }
    }

    //TODO keep submitted form tab as the active tab/pill
    echo $view->render('view/adminprofile.html');
});

//diary card form page
$f3->route('GET|POST /dbtdiary', function ($f3) {
    global $db;
    $view = new Template();

    if ($db->getuserType($_SESSION['uuid'])!=="cl") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    $f3->set('customTargets', $f3->get('db')->getFormTargets($_SESSION['uuid']));
    $f3->set('customEmotions', $f3->get('db')->getFormEmotions($_SESSION['uuid']));
    $f3->set('skills', $f3->get('db')->getSkills());

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $isValid = validateForm($_POST, $f3->get('skills'), $f3->get('db')->getCurrentFormTargets($_SESSION['uuid']));

        if(!$isValid) {
            $f3->reroute('/memberprofile?confirm=An Error Occurred While Saving the Form');
        }

        $f3->get('db')->submitClientData($_POST, $_SESSION['uuid']);
        $f3->reroute('/memberprofile?confirm=Your Diary Entry Has Been Saved Successfully');
    }
    echo $view->render('view/diarycardform.html');
});


//confirmation page
$f3->route('GET|POST /confirmdbtform', function($f3) {
    global $db;

    if ($db->getuserType($_SESSION['uuid'])!=="cln") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    $view = new Template();

    $check = isEmptyStringOrNUll($_SESSION['confirmTargets']);
    $check2 = isEmptyStringOrNUll($_SESSION['confirmEmotions']);

    $f3->set('isEmptyTargets', $check );
    $f3->set('isEmptyEmotions', $check2);

    echo $view->render('view/clinicianconfirm.html');
});

//view form page
$f3->route('GET|POST /viewform', function($f3) {
    global $db;

    if ($db->getuserType($_SESSION['uuid'])!=="cln") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    $view = new Template();
    $f3->set('id', $_GET['id']);
    $tableCreate = new BuildTable();
    $f3->set('table', $tableCreate);

    //set sessions
    $_SESSION['form'] = $_GET['form'];
    $_SESSION['start'] = $_GET['weekStart'];
    $_SESSION['end'] = $_GET['weekEnd'];
    //Format Dates to be displayed
    $displayStart = new DateTime($_GET['weekStart']);
    if($displayStart->format("N")!=1) {
        $displayStart= $displayStart->modify('last monday');
    }
    $displayEnd = new DateTime($_GET['weekEnd']);
    if($displayEnd->format("N")!=7) {
        $displayEnd= $displayEnd->modify('next sunday');
    }
    $f3->set('displayStart', $displayStart->format("M d,Y"));
    $f3->set('displayEnd', $displayEnd->format("M d, Y"));
    //GRAB ALL INFORMATION FOR Tables

    echo $view->render('view/viewform.html');
});

//table selection
$f3->route('GET|POST /formtable', function($f3) {
    $view = new Template();
    global $db;

    if(!$_SESSION['uuid']) {
        $f3->reroute('/');
    }

    if ($db->getuserType($_SESSION['uuid'])==="cl") { //check if appropriate user on page redirect to home if not
        if($_SESSION['uuid'] !== $_GET['id']) {
            $_SESSION['redirect']="Your session has timed out. Please login to continue.";
            $f3->reroute('/');
        }
    }

    $type =$db->getuserType($_SESSION['uuid']);//get the user type

    if ($type!=="cln") { //this page only viewable by clinicians
        $f3->reroute('/');
    }

    $formsplit = new Formsplitter();
    $f3->set('formsplit', $formsplit);
    echo $view->render('view/clienttable.html');
});

//Run the framework
$f3->run();
