<?php
		
		$rez = array();
		$arr = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `teeth_map_pin`";
		$res = mysql_query($query) or die($q);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			
		}else echo 'XUY';

		mysql_close();
		
		echo '$teeth_map_pin_db = array (<br />';
		foreach ($rez as $key => $value){
			//var_dump ($value);
			echo $key.' => '.'array(<br />';
			echo '\'tooth\' => \''.$value['tooth'].'\',<br />';
			echo '\'coord\' => \''.$value['coord'].'\'<br />),<br />';
		}
		echo ');';
		
?>