<?php 

//Close_notes_stomat_f.php
//

	session_start();
	
	if (empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			$date = date_create(date('Y-m-d', time()));
			$dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['change_notes_months'].' months'));
			$dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['change_notes_days'].' days'))) + 60*60*8;
			
			
			//Добавим данные в базу
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			$query = "UPDATE `notes` SET `dead_line` = '{$dead_line}', `description` = '{$_POST['change_notes_type']}' WHERE `id`='{$_POST['id']}'";
			//echo $query.'<br />';
			
			mysql_query($query) or die(mysql_error());
			
			mysql_close();
							
			echo '
				Напоминалка обновлена, обновите страничку.<br /><br />';
		}
	}
?>