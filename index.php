<?php
//error reporting
ini_set('display_errors', 1);
ini_set('file_uploads',1);
error_reporting(E_ALL);
//required files for classes and validation
require_once("vendor/autoload.php");
require_once("model/validation.php");
//istantiate fat free and session
$f3 = Base::instance();
session_start();

$db = new database();
//-----------------------------------------------------Arrays-----------------------------------------------------------

$defaultTargets = array(
    "Suicidal Ideation","Self Harm", "Substance Use", "Medication"
);
$defaultEmotions = array(
    "Joy", "Gratitude", "Compassion", "Vulnerability", "Self Acceptance", "Sadness", "Depression", "Anger",
    "Frustration", "Anxiety"
);
$dates = array(
  "Mon", "Tue","Wed","Thurs","Fri","Sat","Sun"
);


//-----------------------------------------------------ROUTES-----------------------------------------------------------
//default route
$f3->route('GET|POST /', function ($f3) {

    global $db;
    //TODO validate db user clinician/patient
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $db->getUser($_POST['user'],$_POST['pass']);

        if ($result==="0") //clinician
        {
            $_SESSION['uuid'] = $db->getClinicianID($_POST['user']);
            $f3->reroute('/branchprofile');

        }
        elseif ($result==="1") //member
        {
            $_SESSION['uuid'] = $_POST['user'];
            $f3->reroute('/memberprofile');

        }
        else
        {
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
    if($db->getuserType($_SESSION['uuid'])!=="cln")//check if appropriate user on page redirect to home if not
    {
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }
    global $defaultEmotions;
    global $defaultTargets;

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $arrayErr = array(
            "feelings" => validateInputGroup($_POST['feelings']),
            "targets" => validateInputGroup($_POST['targets'])
        );
        if(checkErrArray($arrayErr))
        {
            $_SESSION['confirmTargets']= $_POST['targets'];
            $_SESSION['confirmEmotions']= $_POST['feelings'];

            $f3->reroute('/confirmdbtform');
        }
        $f3->set('errors', $arrayErr);
    }
    $f3->set("targets", $defaultTargets);
    $f3->set("emotions", $defaultEmotions);
    $view = new Template();
    echo $view->render('view/createdbt.html');
});

//group leader dashboard page
$f3->route('GET|POST /branchprofile', function ($f3) {
    global $db;
    if($db->getuserType($_SESSION['uuid'])!=="cln")//check if appropriate user on page redirect to home if not
    {
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $arrayErr = array(
            "addErr" => validateClientNumber($_POST['clientnum']),);
        if (checkErrArray($arrayErr))
        {
            if(isset($_POST['add'])){ //if add client update db on groups leader to reference client #
                $error=$db->addClient($_SESSION['uuid'], $_POST['clientnum']);
                if($error)
                {
                   $f3->set('dberror', $error);
                }
            }
            elseif(isset($_POST['remove'])){ //if remove selected remove from goupp leader reference to client
                $error=$db->removeClient($_SESSION['uuid'], $_POST['clientnum']);
                if($error)
                {
                    $f3->set('dberror', $error);
                }
            }
        }
        $f3->set('errors', $arrayErr);
    }
    $result = $db->getLinks($_SESSION['uuid']);
    $f3->set('links', $result);
    $view = new Template();
    echo $view->render('view/branchprofile.html');
});

//client dashboard page FIXME you are working here....
$f3->route('GET|POST /memberprofile', function ($f3) {
    $view = new Template();
    global $db;
    if($db->getuserType($_SESSION['uuid'])!=="cl")//check if appropriate user on page redirect to home if not
    {
        $_SESSION['redirect']="Your session has timed out. Please login to continue.";
        $f3->reroute('/');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $f3->reroute('/targets');
    }

    echo $view->render('view/memberprofile.html');
});

//targets page
$f3->route('GET|POST /targets', function ($f3) {
    $view = new Template();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($_REQUEST['btn-submit']=="next"){ //if next button is clicked
            $f3->reroute('/emotions');
        }
    }
    else {
        echo $view->render('view/targets.html');
    }
});


//emotions route
$f3->route('GET|POST /emotions', function ($f3) {
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if($_REQUEST['btn-submit'] == "prev") { //if previous button was clicked
            $f3->reroute('/targets');
        }
        elseif($_REQUEST['btn-submit']=="next") { //if next button is clicked
             $f3->reroute('/skills');
        }
    }
    else {
        $view = new Template();
        echo $view->render('view/emotions.html');
    }
});

//skills page
$f3->route('GET|POST /skills', function ($f3) {
    $view = new Template();

    if($_REQUEST['btn-submit'] == "prev") { //if previous button was clicked
        $f3->reroute('/emotions');
    }
    elseif($_REQUEST['btn-submit']=="save") { //if save & exit button is clicked
        $f3->reroute('/memberprofile');
    }

    echo $view->render('view/skills.html');
});


//confirmation page
$f3->route('GET|POST /confirmdbtform', function($f3){
    $view = new Template();
    echo $view->render('view/clinicianconfirm.html');
});

//view form page
$f3->route('GET|POST /viewform', function($f3){
    $view = new Template();

    $f3->set('id', $_GET['id']);

    global $db;
    $type =$db->getuserType($_SESSION['uuid']);//get the user type
    global $defaultEmotions;
    global $defaultTargets;
    global $dates;
    $f3->set("dates", $dates);
    $f3->set("targets", $defaultTargets);
    $f3->set("emotions", $defaultEmotions);
    if($type==="cln")//if clinician view get provided form
    {

    }
    elseif($type==="cl")//view own this week form
    {

    }
    echo $view->render('view/viewform.html');
});

//table selection
$f3->route('GET|POST /formtable', function($f3)
{
    $view = new Template();
    $_SESSION['clientId'] = $_GET['id'];
});
//Run the framework
$f3->run();
