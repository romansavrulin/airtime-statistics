<?php

    $login = 'login';
    $passwd = 'passwd';
    $max_val = 0;
    $avg_min = 1;
    $total_avg = 0;//@file_get_contents('total-avg.dat');
    $total_max = 0;//@file_get_contents('total-max.dat');
    
    if(isset($_GET['res']))
        $avg_min = (int) $_GET['res'];
    if($avg_min < 0)
        $avg_min = 1;
    $interval = 2 * $avg_min;
    $basePath = dirname(__FILE__).'/';
    //echo $basePath;
    //echo 'tc';
    try {  
        $db = new PDO('mysql:host=127.0.0.1;dbname=nfem_stat', 
                      $login, $passwd);
        //echo 'con';
        try {
            
            $sql = $db->prepare("SELECT * FROM `channels` c ORDER BY c.id DESC");  
            $sql->execute();
            $channels = $sql->fetchAll(PDO::FETCH_ASSOC);  
            //var_dump($result);  
            
            //$sql = $db->prepare("SELECT * FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) AND m.channel = :channel");  
            $sql = $db->prepare("select sub.label date, CAST(AVG(sub.total) AS UNSIGNED) users FROM
                                (SELECT m.users total, UNIX_TIMESTAMP(m.date)*1000 label,
                                (@rownum:=@rownum+1) DIV $avg_min gr FROM `minute-stat` m, (SELECT @rownum:=-1) r
                                WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MONTH) AND channel=:channel) sub GROUP BY gr");  
            $sql->bindParam(':channel',$ch);
            
            $num = 0;
            foreach($channels as $key => $data){
                $ch = $data['id'];
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);  
                //var_dump($result);  
            
                foreach($result as $key => $data){
                    $dt = (int) $data['users'];
                    //$y_data[$num][] = $dt;
                    $y_data[] = array((float) $data['date'], $dt);
                    if($dt > $max_val)
                        $max_val =  $dt;
                }
                $num++;
				break;
            }
            
            echo(json_encode($y_data));
            //echo('ok');
            exit(0);
            //Total
            //---------
            //$sql = $db->prepare("SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%d $b %k:%i') label FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) GROUP BY m.date");
            $sql = $db->prepare("select sub.label, CAST(AVG(sub.total) AS UNSIGNED) total FROM
                                (SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%k:%i<br>%d %b') label,
                                (@rownum:=@rownum+1) DIV $avg_min gr FROM `minute-stat` m, (SELECT @rownum:=-1) r
                                WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) GROUP BY m.date) sub GROUP BY gr");
            $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);  
                //var_dump($result);  
                foreach($result as $key => $data){
                    $dt = (int) $data['total'];
                    //echo $data['label'];
                    $labels[] = $data['label'];
                    $y_data[$num][] = $dt;
                    if($dt > $max_val)
                        $max_val =  $dt;
                }
            
            //AVG
            $sql = $db->prepare("SELECT CAST(AVG(sub.total) AS UNSIGNED) total_average FROM (SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%k:%i') label FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) GROUP BY m.date) sub");
            /*$sql = $db->prepare("select sub.label, CAST(AVG(sub.total) AS UNSIGNED) total FROM
                                (SELECT AVG(m.users) total, DATE_FORMAT(m.date, '%k:%i') label,
                                (@rownum:=@rownum+1) DIV $avg_min gr FROM `minute-stat` m, (SELECT @rownum:=-1) r
                                WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) GROUP BY m.date) sub GROUP BY gr");*/
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            //file_put_contents($basePath.'total-avg.dat', $result[0]['total_average']);
            $total_avg = $result[0]['total_average'];
            
            //MAX
            $sql = $db->prepare("SELECT CAST(MAX(sub.total) AS UNSIGNED) total_max FROM (SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%k:%i') label FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) GROUP BY m.date) sub");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            $total_max = $result[0]['total_max'];
            //file_put_contents($basePath.'total-max.dat', $result[0]['total_max']);
        }
        catch(PDOException $e) {  
            //$db->rollBack();  
            //echo 'Error : '.$e->getMessage();  
        }  
    } catch (PDOException $e) {  
        echo 'Error :'.$e->getMessage();  
    }
   //echo 'term';
   $db = NULL;
   $sql = NULL;
?>