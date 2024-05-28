<style>
    h2 {
        font-family: Arial, Helvetica, sans-serif;
    }

    table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    tr:nth-child(even){background-color: #f2f2f2;}

    tr:hover {background-color: #ddd;}

    th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    }
</style>

<?php

require_once('../src/StarTracker.php');

$StarTracker = new StarTracker();

$search = array(
    'latitude' => '-84.39733',
    'longitude' => '38.775867',
    'elevation' => '0',
    'from_date' => date('Y-m-d'),
    'to_date' => date('Y-m-d', strtotime('+7 day')),
    'time' => date('H:i:s'),
    'output' => 'rows'
);

$res = $StarTracker->searchPositions($search);

echo "<h2>Planetary object positions between {$search['from_date']} and {$search['to_date']}</h2>";

echo $StarTracker->toScreen($res);