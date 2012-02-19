<?php
    $basePath = dirname(__FILE__).'/';
    $interval = 24;
    $rvk_stat = @file_get_contents('http://www.radiovkontakte.ru/play/ofc-stat.php');
    //var_dump($rvk_stat);
    if($rvk_stat)
        $rvk_stat = explode(' ', $rvk_stat);
    else
        $rvk_stat = array(-1, -1, -1);
        
    if (count($rvk_stat) > 3)
        $rvk_stat = array(-1, -1, -1);
        
    $wowza_stat = @file_get_contents('http://localhost:1935/');
    if ($wowza_stat)
        $rvk_stat[] =  str_replace('server=', '', $wowza_stat);
    else
        $rvk_stat[] = -1;
        
    //var_dump($rvk_stat);
    
    $login = 'login';
    $passwd = 'pass';
    //echo 'tc';
    try {  
        $db = new PDO('mysql:host=localhost;dbname=rvkstat', 
                      $login, $passwd);
        //echo 'con';
        try {                   
            $sql = $db->prepare("INSERT INTO rvkstat.`minute-stat`(date, channel, users) VALUES (:date,:channel,:users)");  
            $sql->bindParam(':channel',$ch);  
            $sql->bindParam(':users',$us);  
            $sql->bindParam(':date',$date);
            $date = date ("Y-m-d H:i");
            //var_dump($date);
            // назначение значений и вставка новой строки  
            //var_dump($rvk_stat);
            foreach($rvk_stat as $key => $data){
                $ch = $key + 1;  
                $us = $data;  
                $sql->execute();
                //echo 'ex';
            }
            /*
            //AVG
            $sql = $db->prepare("SELECT CAST(AVG(sub.total) AS UNSIGNED) total_average FROM (SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%k:%i') label FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY m.date) sub");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            file_put_contents($basePath.'total-avg.dat', $result[0]['total_average']);
            
            //MAX
            $sql = $db->prepare("SELECT CAST(MAX(sub.total) AS UNSIGNED) total_max FROM (SELECT SUM(m.users) total, DATE_FORMAT(m.date, '%k:%i') label FROM `minute-stat` m WHERE m.date >  DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY m.date) sub");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            file_put_contents($basePath.'total-max.dat', $result[0]['total_max']);
            */
        }  
        catch(PDOException $e) {  
            //$db->rollBack();  
            echo 'Error : '.$e->getMessage();  
        }  
    } catch (PDOException $e) {  
        echo 'Error :'.$e->getMessage();  
    }
   //echo 'term';
   $db = NULL;
   $sql = NULL;
?>