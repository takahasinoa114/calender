<?php
    $retry_count = 10;
    $retry_wait_ms=10;
    while(true){
        try{
            $pdo = new PDO('sqlite:calendar.db',",",
                array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
            $pdo->beginTransaction();
            setDatabase($pdo);
            $result = true;
            $pdo->commit();
        }
        catch(PDOException $e){
            $pdo->rollBack();
            $result = false;
            print $e->getMessage();
        }
        if(!$result && $retry_count > 0){
            usleep($retry_wait_ms * 1000);
            $retry_count--;
        }
        else{
            break;
        }
    }
    print $result;

    function setDatabase($pdo){
        $date = $_GET['id'];
        $content = $_GET['content'];
        $stmt = $pdo->prepare('SELECT count(date) FROM schedule WHERE date = :date');
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        $num = $stmt->fetchColumn();
        if($num == 0 && $content != ''){
            $stmt = $pdo->prepare('INSERT INTO schedule(date, title) values(:date, :title)');
            $stmt->bindValue(':date', $date, PDO::PARAM_STR);
            $stmt->bindValue(':title', $content, PDO::PARAM_STR);
            $stmt->execute();
        }
        else {
            if($content != ''){
                $stmt = $pdo->prepare('UPDATE schedule SET title = :title WHERE date = :date');
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindValue(':title', $content, PDO::PARAM_STR);
                $stmt->execute();
            }
            else{
                $stmt = $pdo->prepare('DELETE FROM schedule WHERE date = :date');
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }
?>