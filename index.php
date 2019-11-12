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

$defaultTargets = $db->getDefaultTargets();
$defaultEmotions=$db->getDefaultEmotions();

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

        if ($result==="0") { //clinician
            $_SESSION['uuid'] = $db->getClinicianID($_POST['user']);
            $f3->reroute('/branchprofile');

        } elseif ($result==="1") { //member
            $_SESSION['uuid'] = $_POST['user'];
            $f3->reroute('/memberprofile');

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
    //collect default emoitons and targets form db
    global $defaultEmotions;
    global $defaultTargets;

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
        if ($_POST['feelings'])//check if post array has values
        {
            foreach ($_POST['feelings'] as $feeling)//check each feeling
            {
                if($feeling!=="")//if feeling in post was empty ignore and do not place in db
                {
                    $result = $db->getEmotionId($feeling);
                    var_dump($result);
                    if(!$result)//id does not exist in table
                    {
                        $result=$db->insertEmotion($feeling);//add to table
                    }
                    $db->insertCustomEmotions($formNum, $result);//insert into association table to create form
                }
            }
        }

        if($_POST['targets'])
        {
            foreach ($_POST['targets'] as $target)
            {
                if($target!=="")
                {
                    $result = $db->getTargetId($target);
                    if(!$result)//id does not exist in table
                    {
                        $result=$db->insertTarget($target);//add to table
                    }
                    $db->insertCustomTargets($formNum, $result);//insert into association table to create form
                }
            }
        }

        $f3->reroute('/confirmdbtform');
    }


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
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['id']))
        {
            $db->closeForm($_POST['id']);
        }
        else{
            $arrayErr = array("addErr" => validateClientNumber($_POST['clientnum']),);
            if (checkErrArray($arrayErr))
            {
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

    if ($db->getuserType($_SESSION['uuid'])!=="cl") { //check if appropriate user on page redirect to home if not
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $f3->reroute('/dbtdiary');
    }

    echo $view->render('view/memberprofile.html');
});

//diary card form page
$f3->route('GET|POST /dbtdiary', function ($f3) {
    $view = new Template();
    $f3->set('customTargets', $f3->get('db')->getFormTargets($_SESSION['uuid']));
    $f3->set('customEmotions', $f3->get('db')->getFormEmotions($_SESSION['uuid']));
    $f3->set('skills', $f3->get('db')->getSkills());

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $isValid = validateForm($_POST, $f3->get('skills'));

        if(!$isValid)
        {
            $f3->reroute('/memberprofile?confirm=An Error Occurred While Saving the Form');
        }
        $f3->reroute('/memberprofile?confirm=Your Diary Entry Has Been Saved Successfully');
    }
    echo $view->render('view/diarycardform.html');
});


//confirmation page
$f3->route('GET|POST /confirmdbtform', function($f3) {
    $view = new Template();

    $check = isEmptyStringOrNUll($_SESSION['confirmTargets']);
    $check2 = isEmptyStringOrNUll($_SESSION['confirmEmotions']);

    $f3->set('isEmptyTargets', $check );
    $f3->set('isEmptyEmotions', $check2);

    echo $view->render('view/clinicianconfirm.html');
});

//view form page
$f3->route('GET|POST /viewform', function($f3) {
    $view = new Template();
    $f3->set('id', $_GET['id']);

    //Format Dates to be displayed
    $displayStart = new DateTime($_GET['weekStart']);
    $displayEnd = new DateTime($_GET['weekEnd']);
    $f3->set('displayStart', $displayStart->format("M d,Y"));
    $f3->set('displayEnd', $displayEnd->format("M d, Y"));

    //GRAB ALL INFORMATION FOR Tables
    global $defaultEmotions;
    global $defaultTargets;
    global $dates;

    $f3->set("dates", $dates);
    $f3->set("targets", $defaultTargets);//TODO pull from db
    $f3->set("emotions", $defaultEmotions);//TODO pull from db

    echo $view->render('view/viewform.html');
});

//table selection
$f3->route('GET|POST /formtable', function($f3) {
    $view = new Template();
    global $db;
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
