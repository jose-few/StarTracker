<?php

require_once('../src/StarTracker.php');

$StarTracker = new StarTracker();

//Observer position and date of observation.
$observer = array('latitude' => 50.79094, 'longitude' => -3.20736, 'date' => date('Y-m-d'));

//Observe a constellation, and set the constellation as Leo
//Codes can be found at https://docs.astronomyapi.com/requests-and-response/constellation-enums
$view = array('type' => 'constellation', 'parameters' => array('constellation' => 'leo'));

$res = $StarTracker->starChart($observer, $view);

echo "<img src='{$res}'/>";
