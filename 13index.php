<?php

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
	
	mysql_close();
	
	//var_dump($rez);
	
	foreach($rez as $value){
		//var_dump ($rez_arr);
		$need_id = $value['id'];
		
		
		foreach($value as $val){
			$rez_arr = explode(',', $val);
			if (!isset($rez_arr[12])){
				echo $need_id.'было<br />';
				var_dump ($rez_arr);
				
				$rez_arr[12] = '0';
				//echo 'стало<br />';
				//var_dump ($rez_arr);
			}
			
			

		}
	}
	
?>