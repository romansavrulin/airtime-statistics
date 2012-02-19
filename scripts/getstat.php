<?php
    $basePath = dirname(__FILE__).'/';
    $interval = 24;
    /*$rvk_stat = @file_get_contents('http://www.radiovkontakte.ru/play/ofc-stat.php');
    //var_dump($rvk_stat);
    if($rvk_stat)
        $rvk_stat = explode(' ', $rvk_stat);
    else
        $rvk_stat = array(-1, -1, -1);
        
    if (count($rvk_stat) > 3)
        $rvk_stat = array(-1, -1, -1);*/
        
    $wowza_stat = @file_get_contents('http://localhost:8086/connectioninfo');
    if ($wowza_stat)
        $rvk_stat[] =  str_replace('server=', '', $wowza_stat);
    else
        $rvk_stat[] = -1;
        
    //var_dump($rvk_stat);
    //die();
    $login = 'login';
    $passwd = 'pass';
    //echo 'tc';
    try {  
        $db = new PDO('mysql:host=127.0.0.1;dbname=nfem_stat', 
                      $login, $passwd);
        
        $db_pg = new PDO('pgsql:host=localhost;dbname=airtime',
        		'airtime', 'airtime');
        
        //echo 'con';
        //echo 'con';
        try {
        
        	//for mysql                   
            $sql = $db->prepare("INSERT INTO nfem_stat.`minute-stat`(date, channel, users) VALUES (:date,:channel,:users)");  
            $sql->bindParam(':channel',$ch);  
            $sql->bindParam(':users',$us);  
            $sql->bindParam(':date',$date);
            
            //for postgres
            $sql_pg = $db_pg->prepare("INSERT INTO cc_stat_intervals (date, channel, users) VALUES (:date,:channel,:users)");
            $sql_pg->bindParam(':channel',$ch);
            $sql_pg->bindParam(':users',$us);
            $sql_pg->bindParam(':date',$date);
            
            $date = date ("Y-m-d H:i:s");
            //var_dump($date);
            // назначение значений и вставка новой строки  
            //var_dump($rvk_stat);
            foreach($rvk_stat as $key => $data){
                $ch = $key;  
                $us = $data;  
                $sql->execute();
                $sql_pg->execute();
                //var_dump($sql_pg->execute(), $sql_pg->errorInfo());
                //var_dump($sql, $ch, $us);
                //var_dump($sql_pg);
            }
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