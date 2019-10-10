<?php
//error reporting
ini_set('display_errors', 1);
ini_set('file_uploads',1);
error_reporting(E_ALL);
//required files for classes and validation
require_once("vendor/autoload.php");

//istantiate fat free and session
$f3 = Base::instance();
session_start();

//TODO
//required files for classes and validation
//require_once("vendor/autoload.php");
//require_once("models/validation.php");

//-----------------------------------------------------ROUTES-----------------------------------------------------------
//default route
$f3->route('GET|POST /', function ($f3){
    //destroy old sessions
    session_destroy();

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $f3->reroute('/branchprofile');
    }
    $view = new Template();
    echo $view->render('view/home.html');
});

//dbt create
$f3->route('GET|POST /createdbt', function ($f3) {
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $f3->reroute('/branchprofile');
    }
    $view = new Template();
    echo $view->render('view/createdbt.html');
});


$f3->route('GET|POST /branchprofile', function ($f3) {
    $view = new Template();
    echo $view->render('view/branchprofille.html');
});
//Run the framework
$f3->run();