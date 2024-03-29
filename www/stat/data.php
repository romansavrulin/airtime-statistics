<?php

    $login = 'login';
    $passwd = 'pass';
    $max_val = 0;
    $avg_min = 1;
    $total_avg = 0;//@file_get_contents('total-avg.dat');
    $total_max = 0;//@file_get_contents('total-max.dat');
    
    if(isset($_GET['res']))
        $avg_min = (int) $_GET['res'];
    if($avg_min < 0)
        $avg_min = 1;
    $interval = 60 * $avg_min;
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
                                (SELECT m.users total, DATE_FORMAT(m.date, '%k:%i') label,
                                (@rownum:=@rownum+1) DIV $avg_min gr FROM `minute-stat` m, (SELECT @rownum:=-1) r
                                WHERE m.date >  DATE_SUB(NOW(), INTERVAL $interval MINUTE) AND channel=:channel) sub GROUP BY gr");  
            $sql->bindParam(':channel',$ch);
            
            $num = 0;
            foreach($channels as $key => $data){
                $ch = $data['id'];
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);  
                //var_dump($result);  
            
                foreach($result as $key => $data){
                    $dt = (int) $data['users'];
                    $y_data[$num][] = $dt;
                    if($dt > $max_val)
                        $max_val =  $dt;
                }
                $num++;
            }
            
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
        //echo 'Error :'.$e->getMessage();  
    }
   //echo 'term';
   $db = NULL;
   $sql = NULL;

   //
// This is the MODEL section:
//

include 'php-ofc-library/open-flash-chart.php';

// Make the chart
$chart = new open_flash_chart();

//
// Create a title object and set the text to todays date
//
//$title = new title( date("D M d Y") );
if(isset($e))
    $title = new title("Error: ".$e->getMessage());
else
    $title = new title( "$avg_min Hours RVK Statistics" );
$title->set_style( "{font-size: 20px; font-family: Courier; font-weight: bold; color: #A2ACBA; text-align: center;}" );
// add the title to the chart:
$chart->set_title( $title );

/*
$m = new ofc_menu("#E0E0ff", "#707070");
$items[] = new ofc_menu_item('Refresh ','load');
$items[] = new ofc_menu_item('1 Hour  ','show_hour1');
$items[] = new ofc_menu_item('2 Hours ','show_hour2');
$items[] = new ofc_menu_item('6 Hours ','show_hour3');
$items[] = new ofc_menu_item('12 Hours','show_hour4');
$items[] = new ofc_menu_item('1 Day    ','show_hour5');
$items[] = new ofc_menu_item('3 Days   ','show_hour6');
$items[] = new ofc_menu_item('1 Week  ','show_hour7');
$items[] = new ofc_menu_item('2 Weeks ','show_hour8');
$items[] = new ofc_menu_item('1 Month  ','show_hour9');
//$items[] = new ofc_menu_item('Save...   ','save_image');
$m->values($items);
$chart->set_menu($m);
*/
//var_dump($y_data);
$hol = new hollow_dot();
$hol->size(3)->halo_size(1)->tooltip('#key# <br>Listeners: #val#<br>Time: #x_label#');

//foreach

//var_dump($y_data);

foreach($y_data as $key => $data){
    $line_dot = new area();
    //$line_dot = new line();
    if(isset($channels[$key])){
        $legend = $channels[$key]['name'].':('.end($data).')';
        $color = $channels[$key]['color'];
        $line_dot->set_width( 1 );
    }
    else{
        $legend = 'Total:('.end($data)."/$total_avg/$total_max)";
        $color = '#e81dd7';
        $line_dot->set_width( 3 );
    }
    $line_dot->set_values( $data );
    $line_dot->set_default_dot_style($hol); 
    $line_dot->set_fill_colour( $color );
    $line_dot->set_fill_alpha( 0.05 );
    $line_dot->set_colour( $color );
    $line_dot->set_key( $legend, 10 );
    $chart->add_element( $line_dot );
    $line_dot = NULL;
}

$y = new y_axis();
// grid steps:
$y->set_range( 0, $max_val + 10, (int)(($max_val + 10)/10));
/*$y_labels = new y_axis_labels();
$y_labels->set_size (15);
$y->set_labels( $y_labels );*/

$chart->set_y_axis( $y );

$x = new x_axis();
$x->offset(true)->steps(1);
//var_dump($labels);
$chart->set_x_axis( $x );

//
// Style the X Axis Labels:
//
$x_labels = new x_axis_labels();
// show every other label:
$x_labels->set_steps( 5 );
//$x_labels->set_size( 15 );
// set them vertical
//$x_labels->set_vertical();
$x_labels->set_labels($labels);
//
// Add the X Axis Labels to the X Axis
//
$x->set_labels( $x_labels );
//$x->set_labels_from_array($labels);
   
echo $chart->toPrettyString();
?>