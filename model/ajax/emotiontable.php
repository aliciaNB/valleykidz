<?php
$start = $_GET['weekStart'];
$end = $_GET['weekEnd'];
$form = $_GET['form'];

$db= new database();
$result = $db->getEmotionsBetweenDates($start, $end, $form);
echo $result;