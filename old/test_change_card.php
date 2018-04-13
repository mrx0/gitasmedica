<?php

	require 'config.php';
	mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
	mysql_select_db($dbName) or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");
	
	$arr = array();
	$rez = array();
	
	$query = "SELECT `id`,`card` FROM `spr_clients` WHERE `card` <> 'NULL' AND `card` <> ''";
	
	$res = mysql_query($query) or die($query);
	$number = mysql_num_rows($res);
	if ($number != 0){
		while ($arr = mysql_fetch_assoc($res)){
			$rez[$arr['id']] = $arr['card'];
		}
	}else{
		$rez = 0;
	}
	
	if ($rez != 0){
		//var_dump($rez);
		
		foreach ($rez as $id => $card){
			$card = str_replace(" ", "", $card);
			$card = mb_strtoupper($card, "UTF-8");
			$card = str_replace(";","; ",$card);
			$card = str_replace(",",", ",$card);
			$card = str_replace("/","/ ",$card);
			
			$query = "UPDATE `spr_clients` SET `card`='$card' WHERE `id`='$id'";
			
			mysql_query($query) or die(mysql_error());
			
			echo $id.' => '.$card.'<br>';
			
		}
		
		mysql_close();
	}
	
?>