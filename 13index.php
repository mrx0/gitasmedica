<?php

//Какие-то манипуляции с ЗФ, я уже не помню
//Скорее всего менялся вид представления в БД

	$query = "SELECT `id`,
					`11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`,
					`21`, `22`, `23`, `24`, `25`, `26`, `27`, `28`,
					`31`, `32`, `33`, `34`, `35`, `36`, `37`, `38`,
					`41`, `42`, `43`, `44`, `45`, `46`, `47`, `48`
					FROM `journal_tooth_status`";

	require 'config.php';
	mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
	mysql_select_db($dbName) or die(mysql_error()); 
	mysql_query("SET NAMES 'utf8'");

	$rez = array();
	$arr = array();
	$rez_arr = array();
	
	$res = mysql_query($query) or die($query);
	$number = mysql_num_rows($res);
	if ($number != 0){
		while ($arr = mysql_fetch_assoc($res)){
			array_push($rez, $arr);
		}
	}
	
	
	//...
	//var_dump($rez);
	
	foreach($rez as $value){
		//var_dump ($rez_arr);
		$need_id = $value['id'];
		unset($value['id']);
		
		foreach($value as $n_tooth => $val){
			$rez_arr = explode(',', $val);
			if (!isset($rez_arr[12])){
				echo $need_id.' => '.$n_tooth.' -> было<br />';
				var_dump ($rez_arr);
				
				$rez_arr[12] = '0';
				
				$query = "UPDATE `journal_tooth_status` SET `$n_tooth`='".implode(',', $rez_arr)."' WHERE `id`=$need_id";
				
				var_dump($query);
				
				$res = mysql_query($query) or die($query);
				
				//echo 'стало<br />';
				//var_dump ($rez_arr);
			}
			
			

		}
	}
	mysql_close();
	
?>