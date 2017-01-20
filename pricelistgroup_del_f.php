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
			//переменная для отслеживания конца каскада дерева при проходе
			$rezultNextMove = TRUE;
			
			$arr4delete = array();
			
			$arr = array();
			$rez = array();
			$DeleteAll = FALSE;
			
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			//Обновили статус родителю
			$query = "UPDATE `spr_storagegroup` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error().' -> '.$query);
			
			array_push($arr4delete, $_POST['id']);
			//var_dump($arr4delete);
			
			while ($rezultNextMove){
				//if (isset($_POST['deleteallin'])){
				//	if ($_POST['deleteallin'] == 1){
					
						//собираем все позиции в этой группе и удаляем их из группы и их самих
						$query = "SELECT * FROM `spr_itemsingroup` WHERE `group` = '{$_POST['id']}'";
						$res = mysql_query($query) or die(mysql_error().' -> '.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($rez, $arr);
							}
						}else{
							$rez = 0;
						}
						//var_dump($rez);
						
						if ($rez != 0){
							//...удаляем их из группы
							$query = "DELETE FROM `spr_itemsingroup` WHERE `group` = '{$_POST['id']}'";
							mysql_query($query) or die(mysql_error().' -> '.$query);
							
							foreach ($rez as $ids){
								//var_dump($ids);
								//...и их самих
								$query = "UPDATE `spr_pricelist` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9' WHERE `id`='{$ids['id']}'";
								//var_dump($query);
								mysql_query($query) or die(mysql_error().' -> '.$query);								
							}
						}
						
						//Обнуляем массивы для следующего исп-ния
						$arr = array();
						$rez = array();
						
						//получаем группы, которые в этом родителе
						$query = "SELECT * FROM `spr_storagegroup` WHERE `level` = '{$_POST['id']}'";
						$res = mysql_query($query) or die(mysql_error().' -> '.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($rez, $arr);
							}
						}else{
							$rez = 0;
						}
						
						//Обнуляем родителя у групп
						if ($rez != 0){
							foreach ($rez as $ids){
								//var_dump($ids);
								//...и их самих
								$query = "UPDATE `spr_pricelist` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `level`='0' WHERE `id`='{$ids['id']}'";
								//var_dump($query);
								mysql_query($query) or die(mysql_error().' -> '.$query);								
							}
						}else{
							$rezultNextMove = FALSE;
						}
				//	}
				//}else{
				//}
			}
			
			mysql_close();
			
			echo '
				<div class="query_ok" style="padding-bottom: 10px;">
					<h3>Группа удалена (заблокирована).</h3>';
			if ($DeleteAll){
				echo 'Всё содержимое откреплено и удалено';
			}
			echo '
				</div>';	
		}

	}
	
?>