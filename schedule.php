<?php
    $start = $_GET['start'];
    $end = $_GET['end'];

    $pdo = new PDO('sqlite:calendar.db');
    $stmt = $pdo->prepare('SELECT date, title FROM schedule WHERE date >= :start AND date <= :end');
    $stmt->bindValue(':start', $start, PDO::PARAM_STR);
    $stmt->bindValue(':end', $end, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $obj = array();
    foreach( $result as $row ){
        $obj += array( $row['date'] => $row['title'] );
    }
    print json_encode($obj, JSON_UNESCAPED_UNICODE );
?>