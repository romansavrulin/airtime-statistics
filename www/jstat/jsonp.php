<?php
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	$data = array(/*$_REQUEST['callback'] => array(*/
		array(1104710400000,26.74),
		array(1104796800000,26.84),
		array(1104883200000,26.78),
		array(1104969600000,26.75),
		array(1105056000000,26.67),
		array(1105315200000,26.80),
		array(1105401600000,26.73),
		array(1105488000000,26.78),
		array(1105574400000,26.27),
		array(1105660800000,26.12),
		array(1106006400000,26.32),
		array(1106092800000,25.98),
		array(1106179200000,25.86),
		array(1106265600000,25.65),
		array(1106524800000,25.67),
		array(1106611200000,26.02),
		array(1106697600000,26.01),
		array(1106784000000,26.11),
		array(1106870400000,26.18),
		array(1107129600000,26.28),
	);
	
	echo(json_encode($data));
?>