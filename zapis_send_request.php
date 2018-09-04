<?php

//zapis_send_request.php
//

//---------

	require 'zapis_config.php';
	
	$msql_connect = mysqli_connect($hostname, $username, $db_pass, $dbName);
	
	if ($msql_connect){
		var_dump('Ok');
		
		mysqli_query($msql_connect, "SET NAMES 'utf8'");
		
		$datetime = date('Y-m-d H:i:s', time());
		
		$name = trim(strip_tags(stripcslashes(htmlspecialchars('name1'))));
		$email = trim(strip_tags(stripcslashes(htmlspecialchars('email2'))));
		$phone = trim(strip_tags(stripcslashes(htmlspecialchars('phone3'))));
		$time = trim(strip_tags(stripcslashes(htmlspecialchars('time4'))));
		$place = trim(strip_tags(stripcslashes(htmlspecialchars('place5'))));
		$comments = trim(strip_tags(stripcslashes(htmlspecialchars('comments6'))));
		
		$query = "INSERT INTO `zapis` (
			`datetime`, `name`, `email`, `phone`, `time`, `place`, `comments`) 
			VALUES (
			'$datetime', '$name', '$email', '$phone', '$time', '$place', '$comments') ";

		$result = mysqli_query($msql_connect, $query);
		
		mysqli_close($msql_connect);
		
		var_dump($query);
	}else{
		var_dump('neOk');
	}
	
	
	
	

?>
