<?php
session_start();
require_once('../db.php');
$start = $_SESSION['start'];
$end = $_SESSION['end'];
$form = $_SESSION['form'];

$db = new database();
$result = $db->getSkillsBetweenDates($start, $end, $form);
echo json_encode($result);