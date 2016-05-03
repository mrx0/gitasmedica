<?php 

//ajax_tempzapis_edit_enter_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['datatable'] == 'scheduler_stom'){
				$datatable = 'zapis_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$datatable = 'zapis_cosm';
			}else{
				$datatable = 'zapis_stom';
			}
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			$query = "UPDATE `$datatable` SET `enter`='{$_POST['enter']}' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error());
			mysql_close();
			
			
		}
	}
?>