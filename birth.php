<?php
		$temp_arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "UPDATE `spr_clients` SET `birthday` = '-1577934000' WHERE `birthday`='0'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
?>