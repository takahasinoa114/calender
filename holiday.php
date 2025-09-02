<?php
    date_default_timezone_set('Asia/Tokyo');
    $start = $_GET['start'];
    $end = $_GET['end'];
    $obj = array();
    foreach(file('https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv') as $line){
        $line = mb_convert_encoding($line, 'UTF-8', 'sjis-win');
        list($date, $name) = explode(',', trim($line));
        $date = date('Y-m-d', strtotime($date));
        if($date >= $start && $date <= $end){
            $obj += array($date => $name);
        }
    }
    print json_encode($obj, JSON_UNESCAPED_UNICODE);
?>
