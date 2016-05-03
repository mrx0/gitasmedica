<?php 

//add_task_stomat_f.php
//Функция для добавления задачи стоматологов в журнал

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['client'] == ''){
				echo '
					Не выбрали пациента. Давайте еще разок =)<br /><br />
					<a href="add_task_cosmet.php" class="b">Добавить запись</a>
					<a href="cosmet.php" class="b">В журнал</a>';
			}else{
				//Ищем клиента
				$clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
				//var_dump($clients);
				if ($clients != 0){
					$client = $clients[0]["id"];
					if ($clients[0]['therapist'] == 0){
						UpdateTherapist($clients[0]["id"], $_SESSION['id']);
					}
					
					
					if ($_POST['filial'] != 0){
						$arr = array();
						$rezult = '';
						
						/*foreach ($_POST as $key => $value){
							if (mb_strstr($key, 'action') != FALSE){
								//array_push ($arr, $value);
								$key = str_replace('action', 'c', $key);
								//echo $key.'<br />';
								$arr[$key] = $value;
							}				
						}*/
						
						//var_dump ($arr);
						//$rezult = json_encode($arr);
						//echo $rezult.'<br />';
						//echo strlen($rezult);
						
						$t_f_data_db = SelDataFromDB('journal_tooth_status_temp', $_POST['new_id'], 'id');
						
						$t_f_data_temp = $t_f_data_db[0];
						
						$stat_id = $t_f_data_temp['id'];
						$stat_time = $t_f_data_temp['create_time'];
						
						unset($t_f_data_temp['id']);
						unset($t_f_data_temp['create_time']);
						
						//var_dump ($t_f_data_db[0]);
						//var_dump ($t_f_data_temp);
						
						$n_zuba = '';
						$stat_zuba = '';
						
						foreach($t_f_data_temp as $key => $value){
							$n_zuba .= "`{$key}`, ";
							$stat_zuba .= "'{$value}', ";
						}
						
						$n_zuba = substr($n_zuba, 0, -2);
						$stat_zuba = substr($stat_zuba, 0, -2);
						
						
						//echo $n_zuba.'<br />';
						//echo $stat_zuba.'<br />';
						
						//Добавим данные в базу
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$time = time();
						$query = "
								INSERT INTO `journal_tooth_status` (
									`office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`, {$n_zuba}) 
								VALUES (
									'{$_POST['filial']}', '{$client}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', '{$_SESSION['id']}', '{$_POST['comment']}', {$stat_zuba}) ";
						//echo $query.'<br />';
						mysql_query($query) or die(mysql_error());

						//удаление темповой записи
						mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
						
						mysql_close();
										
						//WriteToDB_EditCosmet ($_POST['filial'], $client, $arr, time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], $_POST['comment']);
						
						echo '
							Добавлено в журнал.
							<br /><br />
							<a href="stomat.php" class="b">В журнал</a>
							<a href="add_task_stomat.php" class="b">Добавить ещё</a>
							';
					}else{
						echo '
							Вы не выбрали филиал<br /><br />
							<a href="add_task_stomat.php" class="b">Добавить запись</a>
							<a href="cosmet.php" class="b">В журнал</a>';
					}
				}else{
					echo '
						В нашей баз нет такого пациента :(<br /><br />
						<a href="add_task_stomat.php" class="b">Добавить запись</a>
						<a href="add_client.php" class="b">Добавить пациента</a>
						<a href="cosmet.php" class="b">В журнал</a>';
				}
			}
		}
	}
?>