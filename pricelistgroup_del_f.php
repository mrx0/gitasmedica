<?php 

//pricelistgroup_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WritePricelistGroupToDB_Delete ($_POST['session_id'], $_POST['id']);
			
			$DeleteAll = FALSE;
			
			if (isset($_POST['deleteallin'])){
				if ($_POST['deleteallin'] == 1){
					//блокируем (удаляем) записи непосредственно в этой группе
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					
					$query = "UPDATE `spr_pricelist` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9' WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$_POST['id']}')";
					
					mysql_query($query) or die(mysql_error().' -> '.$query);
		
					mysql_close();	

					$DeleteAll = TRUE;
				}
			}else{
			}
			
			echo '
				<div class="query_ok" style="padding-bottom: 10px;">
					<h3>Группа удалена (заблокирована).</h3>';
					if ($DeleteAll){
						echo 'Со всем содержимым';
					}
			echo '
				</div>';	
		}

	}
	
?>