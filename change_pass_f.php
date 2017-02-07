<?php 

//change_pass_f.php
//

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'functions.php';
		//var_dump($_SESSION);
		
		if ($_POST) {
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			
			//Генератор пароля
			$password = PassGen();
				
			$query = "UPDATE `spr_workers` SET `password`='{$password}' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error().' -> '.$query);

			echo 'Новый пароль: '.$password;
		}
	}
?>