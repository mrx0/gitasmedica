<?php 

//Close_removes_stomat_f.php
//

	session_start();
	
	if (empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			//Добавим данные в базу
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			$query = "UPDATE `removes` SET `closed` = '1' WHERE `id`='{$_POST['id']}'";
			//echo $query.'<br />';
			
			mysql_query($query) or die(mysql_error());
			
			mysql_close();
							
			echo '
				Направление закрыто, обновите страничку.<br /><br />';
		}
	}
?>