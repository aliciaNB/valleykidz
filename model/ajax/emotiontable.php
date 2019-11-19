<?php
$start = $_SESSION['start'];
$end = $_SESSION['end'];
$form = $_SESSION['form'];

$db= new database();
$result = $db->getEmotionsBetweenDates($start, $end, $form);
echo $result;