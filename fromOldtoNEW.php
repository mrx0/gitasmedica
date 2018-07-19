<?php

	/*$hostname = "localhost";
	$username = "root";
	$db_pass = "";
	$dbName = "asmed1";

	$rez = array();
	$arr = array();
	$rez2 = array();
	$arr2 = array();
	
	mysql_connect($hostname, $username, $db_pass) OR DIE("Не возможно создать соединение ");
	mysql_select_db($dbName) or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");
	$query = "SELECT * FROM `journal_cosmet1` WHERE `id` > '7849';";
	$res = mysql_query($query) or die(mysql_error());
	$number = mysql_num_rows($res);
	if ($number != 0){
		while ($arr = mysql_fetch_assoc($res)){
			//var_dump($arr);
			array_push($rez, $arr);
			//$rez[$arr['id']] = $arr['nickname'];
		}
		//var_dump ($rez);
	}else{
		echo 'XUY';
	}
	
	foreach ($rez as $value){
		$query = "SELECT * FROM `spr_clients` WHERE `id` = '".$value['client']."';";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr2 = mysql_fetch_assoc($res)){
				//var_dump($arr);
				array_push($rez2, $arr2);
				//$rez[$arr['id']] = $arr['nickname'];
			}
		}else{
			echo 'XUY2';
		}
	}
	
	//var_dump ($rez2);	
	
	//**********************///
	
	/*mysql_select_db('cash') or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");
	
	foreach($rez as $value){
		$query = "INSERT INTO `journal_cosmet1` (
			`id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`,
			`c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, 
			`c11`, `c12`, `c13`, `c14`, `c15`, `c16`, `c17`, `c18`, `c19`, `c20`,
			`c21`, `c22`, `c200`, `c201`		
			) 
			VALUES (
			'".$value['id']."', '".$value['office']."', '".$value['client']."', '".$value['create_time']."', '".$value['create_person']."', 
			'".$value['last_edit_time']."', '".$value['last_edit_person']."', '".$value['worker']."', '".$value['comment']."',
			'".$value['c1']."', '".$value['c2']."', '".$value['c3']."', '".$value['c4']."', '".$value['c5']."', '".$value['c6']."', '".$value['c7']."', '".$value['c8']."', '".$value['c9']."', '".$value['c10']."', 
			'".$value['c11']."', '".$value['c12']."', '".$value['c13']."', '".$value['c14']."', '".$value['c15']."', '".$value['c16']."', '".$value['c17']."', '".$value['c18']."', '".$value['c19']."', '".$value['c20']."',
			'".$value['c21']."', '".$value['c22']."', '".$value['c200']."', '".$value['c201']."') ";
			
			$res = mysql_query($query) or die(mysql_error());
	}
	echo $query.'OK<br />';
	
	foreach($rez2 as $value){
		$query = "INSERT INTO `spr_clients` (
			`id`, `name`, `full_name`, `f`, `i`, `o`, `contacts`, `sex`, `birthday`, `therapist`, `therapist2`, `dms`)
			VALUES (
			'".$value['id']."', '".$value['name']."', '".$value['full_name']."',
			'".$value['f']."', '".$value['i']."', '".$value['o']."',
			'".$value['contacts']."', '".$value['sex']."', '".$value['birthday']."',
			'".$value['therapist']."', '".$value['therapist2']."', '".$value['dms']."'
			) ";
			
		$res = mysql_query($query) or die(mysql_error());
	}
	
	echo 'OK2';
	
	
	mysql_close();
*/
	
	
	
	
	$hostname = "localhost";
	$username = "root";
	$db_pass = "";
	$dbName = "cash";
	$dbName2 = "asmed1";

	$rez = array();
	$arr = array();
	$rez2 = array();
	$arr2 = array();
	$rez3 = array();
	$arr3 = array();
	
	$last_id = 0;
	
	mysql_connect($hostname, $username, $db_pass) OR DIE("Не возможно создать соединение ");
	
	
	
	function CHANGE_CLIENT ($old, $new){

		mysql_connect("localhost", "root", "") OR DIE("Не возможно создать соединение ");
		mysql_select_db('cash') or die(mysql_error()); 
		
		$query = "UPDATE `journal_cosmet1` SET `client`=$new WHERE `client`=$old";
		
		mysql_query($query) or die(mysql_error());
		
	}
	
	
	
	
	
	//mysql_connect($hostname, $username, $db_pass) OR DIE("Не возможно создать соединение ");
	mysql_select_db($dbName) or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");
	
	
	$query = "SELECT * FROM `spr_clients`";
	$res = mysql_query($query) or die(mysql_error());
	$number = mysql_num_rows($res);
	if ($number != 0){
		while ($arr2 = mysql_fetch_assoc($res)){
			//var_dump($arr2);
			array_push($rez2, $arr2);
			//$rez[$arr['id']] = $arr['nickname'];
		}
		//var_dump ($rez2);
	}else{
		echo 'XUY';
	}
	
	//var_dump ($rez2);	
	
	//mysql_select_db($dbName2) or die(mysql_error()); 
	foreach ($rez2 as $value){
		mysql_select_db($dbName2) or die(mysql_error()); 
		$query = "SELECT * FROM `spr_clients` WHERE `full_name` = '".$value['full_name']."'";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr3 = mysql_fetch_assoc($res)){
				//var_dump($arr3);
				array_push($rez3, $arr3);
				//$rez[$arr['id']] = $arr['nickname'];
				
				echo $value['id'].' -> '.$value['f'].' = '.$arr3['id'].' -> '.$arr3['f'].' +<br />';
				
				CHANGE_CLIENT ($value['id'], $arr3['id']);
				
				
			}
			//var_dump ($rez3);
		}else{

			$query2 = "INSERT INTO `spr_clients` (
				`name`, `full_name`, `f`, `i`, `o`, `contacts`, `sex`, `birthday`, `therapist`, `therapist2`, `dms`)
				VALUES (
				'".$value['name']."', '".$value['full_name']."',
				'".$value['f']."', '".$value['i']."', '".$value['o']."',
				'".$value['contacts']."', '".$value['sex']."', '".$value['birthday']."',
				'".$value['therapist']."', '".$value['therapist2']."', '".$value['dms']."'
				) ";
				
			$res2 = mysql_query($query2) or die(mysql_error());
			
			$last_id = mysql_insert_id();
			
			echo $value['id'].' -> '.$value['f'].' XUY !!! новый ID:'.$last_id.'<br />'.$query2.'<br />';
			
			
			CHANGE_CLIENT ($value['id'], $last_id);
		}
	}
	
	//var_dump ($rez3);
	
	
	mysql_select_db($dbName) or die(mysql_error()); 
	$query = "SELECT * FROM `journal_cosmet1`";
	$res = mysql_query($query) or die(mysql_error());
	$number = mysql_num_rows($res);
	if ($number != 0){
		while ($arr = mysql_fetch_assoc($res)){
			//var_dump($arr);
			array_push($rez, $arr);
			//$rez[$arr['id']] = $arr['nickname'];
		}
		//var_dump ($rez);
		
		
		
		
	}else{
		echo 'XUY';
	}
	
	
	
	mysql_select_db($dbName2) or die(mysql_error()); 
	foreach ($rez as $value){
		$query = "INSERT INTO `journal_cosmet1` (
			`office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`,
			`c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, 
			`c11`, `c12`, `c13`, `c14`, `c15`, `c16`, `c17`, `c18`, `c19`, `c20`,
			`c21`, `c22`, `c200`, `c201`		
			) 
			VALUES (
			'".$value['office']."', '".$value['client']."', '".$value['create_time']."', '".$value['create_person']."', 
			'".$value['last_edit_time']."', '".$value['last_edit_person']."', '".$value['worker']."', '".$value['comment']."',
			'".$value['c1']."', '".$value['c2']."', '".$value['c3']."', '".$value['c4']."', '".$value['c5']."', '".$value['c6']."', '".$value['c7']."', '".$value['c8']."', '".$value['c9']."', '".$value['c10']."', 
			'".$value['c11']."', '".$value['c12']."', '".$value['c13']."', '".$value['c14']."', '".$value['c15']."', '".$value['c16']."', '".$value['c17']."', '".$value['c18']."', '".$value['c19']."', '".$value['c20']."',
			'".$value['c21']."', '".$value['c22']."', '".$value['c200']."', '".$value['c201']."') ";
			
			$res = mysql_query($query) or die(mysql_error());
			
	}
	

	
	//**********************///
/*

	mysql_select_db('asmed1') or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");
	
	foreach($rez as $value){
		$query = "INSERT INTO `journal_cosmet1` (
			`id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`,
			`c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, 
			`c11`, `c12`, `c13`, `c14`, `c15`, `c16`, `c17`, `c18`, `c19`, `c20`,
			`c21`, `c22`, `c200`, `c201`		
			) 
			VALUES (
			'".$value['id']."', '".$value['office']."', '".$value['client']."', '".$value['create_time']."', '".$value['create_person']."', 
			'".$value['last_edit_time']."', '".$value['last_edit_person']."', '".$value['worker']."', '".$value['comment']."',
			'".$value['c1']."', '".$value['c2']."', '".$value['c3']."', '".$value['c4']."', '".$value['c5']."', '".$value['c6']."', '".$value['c7']."', '".$value['c8']."', '".$value['c9']."', '".$value['c10']."', 
			'".$value['c11']."', '".$value['c12']."', '".$value['c13']."', '".$value['c14']."', '".$value['c15']."', '".$value['c16']."', '".$value['c17']."', '".$value['c18']."', '".$value['c19']."', '".$value['c20']."',
			'".$value['c21']."', '".$value['c22']."', '".$value['c200']."', '".$value['c201']."') ";
			
			$res = mysql_query($query) or die(mysql_error());
	}
	echo $query.'OK<br />';
	
	foreach($rez2 as $value){
		$query = "INSERT INTO `spr_clients` (
			`id`, `name`, `full_name`, `f`, `i`, `o`, `contacts`, `sex`, `birthday`, `therapist`, `therapist2`, `dms`)
			VALUES (
			'".$value['id']."', '".$value['name']."', '".$value['full_name']."',
			'".$value['f']."', '".$value['i']."', '".$value['o']."',
			'".$value['contacts']."', '".$value['sex']."', '".$value['birthday']."',
			'".$value['therapist']."', '".$value['therapist2']."', '".$value['dms']."'
			) ";
			
		$res = mysql_query($query) or die(mysql_error());
	}
	
	echo 'OK2';
	
*/	
	mysql_close();

	
	

?>