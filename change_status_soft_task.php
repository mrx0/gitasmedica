<?php

//change_status_soft_task.php
//

	
	if ($_POST){
		if (isset($_POST['id'])){
			if (isset($_POST['change_status_val']) && $_POST['change_status_val'] != 0){
				if ($_POST['change_status_val'] == 3){
					$inwork = 0;
				}elseif($_POST['change_status_val'] == 1){
					$inwork = 1;
				}elseif($_POST['change_status_val'] == 2){
					$inwork = 2;
				}else{
					$inwork = 0;
				}
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "UPDATE `journal_soft` SET `inwork` = '".$inwork."' WHERE `id`='{$_POST['id']}'";
				//echo $query.'<br />';
				
				mysql_query($query) or die(mysql_error());
				
				mysql_close();
				
				echo 'Статус изменён. Обновите страничку, чтобы увидеть изменения!';
			}
		}else{
			
		}
	}

?>