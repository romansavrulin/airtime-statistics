<html>
<head>

<script type="text/javascript" src="js/json2.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
swfobject.embedSWF(
    "open-flash-chart.swf", 
    "my_chart", 
    "100%", 
    "95%", 
    "9.0.0",
    "expressInstall.swf",
    {"data-file":"data.php",
     "allowScriptAccess":"always"} 
    );
</script>

<script type="text/javascript">

OFC = {};

OFC.jquery = {
    name: "jQuery",
    version: function(src) { return $('#'+ src)[0].get_version() },
    rasterize: function (src, dst) { $('#'+ dst).replaceWith(OFC.jquery.image(src)) },
    image: function(src) { return "<img src='data:image/png;base64," + $('#'+src)[0].get_img_binary() + "' />"},
    popup: function(src) {
        var img_win = window.open('', 'Charts: Export as Image')
        with(img_win.document) {
            write('<html><head><title>Charts: Export as Image<\/title><\/head><body>' + OFC.jquery.image(src) + '<\/body><\/html>') }
		// stop the 'loading...' message
		img_win.document.close();
     }
}

// Using an object as namespaces is JS Best Practice. I like the Control.XXX style.
//if (!Control) {var Control = {}}
//if (typeof(Control == "undefined")) {var Control = {}}
if (typeof(Control == "undefined")) {var Control = {OFC: OFC.jquery}}


// By default, right-clicking on OFC and choosing "save image locally" calls this function.
// You are free to change the code in OFC and call my wrapper (Control.OFC.your_favorite_save_method)
// function save_image() { alert(1); Control.OFC.popup('my_chart') }
function save_image() { /*alert(1);*/ OFC.jquery.popup('my_chart') }
function moo() { /*alert(99);*/ };

var resolution = 1;

function load(){
    //alert('load');
    clearInterval(timer);
    $.get("data.php?res=" + resolution + '&' + Math.random( ),
        function(data){
            findSWF("my_chart").load(data);
        });
    timer = setInterval(load, 60000);
}
var timer;

  $(document).ready(function(){
        timer = setInterval(load, 60000);
        
  /*window.bind("keypress", function(e){
      alert('kp');
      });*/
  $(document).keypress(function(e){
      //alert('kp');
        ofc_keydown(e.which, '');
      }
  );

});
  
  function ofc_keydown(key, id){
    //alert("ofc_kp key:" + key + " id:" + id);
    key = parseInt(key);
    switch(key){
        case 83://save
        case 115:
        case 1099:
        case 1067:
            save_image();
            break;
        case 82://save
        case 114:
        case 1082:
        case 1050:
            load();
            break;
        case 49:
            show_hour1();
            break;
        case 50:
            show_hour2();
            break;
        case 51:
            show_hour3();
            break;
        case 52:
            show_hour4();
            break;
        case 53:
            show_hour5();
            break;
        case 54:
            show_hour6();
            break;
        case 55:
            show_hour7();
            break;
        case 56:
            show_hour8();
            break;
        case 57:
            show_hour9();
            break;
        /*
        case 37: //left
            break;
        case 39: //right
            break;
        case 38: //up
            break;
        case 40: //down
            break;
        default:
            alert('undef');*/
    }
  }
  
  function set_res(res){
    resolution = res;
    load();
  }

  
<?php
    $fun = array(1,2,6,12,24,72,24*7,24*14,24*30);
    //$fun = 1;
    
    foreach($fun as $key => $data){
?>

function show_hour<? echo $key + 1 ?>(chart){
    resolution = <? echo $data ?>;
    load();
}
<?php } ?>
function ofc_ready()
{
}

function open_flash_chart_data()
{
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}

</script>


</head>
<body>

<div id="my_chart"></div>
<input type="button" onclick="javascript: load();" value="Refresh (R)" />
<input type="button" onclick="javascript: save_image();" value="Save... (S)" />
<input type="button" onclick="javascript: show_hour1();" value="1 Hour (1)" />
<input type="button" onclick="javascript: show_hour2();" value="2 Hours (2)" />
<input type="button" onclick="javascript: show_hour3();" value="6 Hours (3)" />
<input type="button" onclick="javascript: show_hour4();" value="12 Hours (4)" />
<input type="button" onclick="javascript: show_hour5();" value="1 Day (5)" />
<input type="button" onclick="javascript: show_hour6();" value="3 Days (6)" />
<input type="button" onclick="javascript: show_hour7();" value="1 Week (7)" />
<input type="button" onclick="javascript: show_hour8();" value="2 Weeks (8)" />
<input type="button" onclick="javascript: show_hour9();" value="1 Month (9)" />


</body>
</html>