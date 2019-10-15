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

//TODO
//required files for classes and validation
//require_once("vendor/autoload.php");
//require_once("models/validation.php");

//-----------------------------------------------------ROUTES-----------------------------------------------------------
//default route
$f3->route('GET|POST /', function ($f3) {
    //destroy old sessions
    session_destroy();

    //TODO validate db user clinician/patient
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['user'] == "jadivan" && $_POST['pass'] == "test")//clinician
            $f3->reroute('view/branchprofile');
        elseif ($_POST['user' == "patient"] && $_POST['pass'] == test) {//patient
        } else {
            $f3->set('error', "Invalid Username or password");
        }
    }

    $view = new Template();
    echo $view->render('view/home.html');
});

//dbt create
$f3->route('GET|POST /createdbt', function ($f3) {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $arrayErr = array(
            "feelings" => validateInputGroup($_POST['feelings']),
            "targets" => validateInputGroup($_POST['targets'])
        );
        if(checkErrArray($arrayErr))
        {
           // $f3->reroute('view/branchprofile');
        }
        $f3->set('errors', $arrayErr);
    }
    $view = new Template();
    echo $view->render('view/createdbt.html');
});


$f3->route('GET|POST /branchprofile', function ($f3) {
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $arrayErr = array(
            "addErr" => validateClientNumber($_POST['clientnum']),);
        if (checkErrArray($arrayErr))
        {
            if($_REQUEST['btn-submit'] == "add"){ //if add clied update db on groups leader to reference client #

            }
            elseif($_REQUEST['btn-submit']=="remove"){ //if remove selected remove from goup leder reference to cline#

            }
        }
        $f3->set('errors', $arrayErr);
    }
    $view = new Template();
    echo $view->render('view/branchprofille.html');
});
//Run the framework
$f3->run();
