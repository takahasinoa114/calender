<?php
    $id = $_GET['id'];
    list($year, $month, $day) = explode('-', $id);

    function getSchedule($date){
        $pdo = new PDO('sqlite:calendar.db');
        $stmt = $pdo->prepare('SELECT date, title FROM schedule WHERE date = :date');
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach($result as $row){
            return $row['title'];
        }
        return '';
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="calendarEdit.css?v=<?php echo time(); ?>">
        <script src="jquery-3.7.1.js"></script>
    </head>
    <body>
        <input type="hidden" id="id" value="<?php print $id; ?>">
        <h2><?php print $year . '年' . intval($month) . '月' . intval($day) . '日'; ?></h2>
        <textarea id="content"><?php
            $content = getSchedule($id);
            print str_replace('<br>', "\n", $content);
        ?></textarea>
        <div id="ok-button">OK</div>
        <script>
        $(function(){
            $('#ok-button').on('click', function(){
                const id = $('#id').val();
                let content = $('#content').val();
                content = content.replace(/\n/g, '<br>');
                $.ajax({
                    url: 'calendarUpdate.php',
                    type: 'get',
                    data: { id: id, content: content, _: new Date().getTime() } // キャッシュ防止
                }).done(function(){
                    if(window.opener && window.opener.drawCalendar){
                        const ary = id.split('-');
                        const year = parseInt(ary[0]);
                        const month = parseInt(ary[1]) - 1;
                        window.opener.drawCalendar(new Date(year, month, 1));
                    }
                    window.close();
                });
            });
        });
        </script>
    </body>
</html>