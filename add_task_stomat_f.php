<?php 

//add_task_stomat_f.php
//Функция для добавления задачи стоматологов в журнал

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		//var_dump ($_SESSION['journal_tooth_status_temp']);
		if ($_POST){
			if ($_POST['client'] == ''){
				echo '
					Не выбрали пациента. Давайте еще разок =)<br /><br />
					<a href="add_task_stomat.php" class="b">Добавить запись</a>';
			}else{
				//Ищем клиента
				$clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
				//var_dump($clients);
				if ($clients != 0){
					$client = $clients[0]["id"];
					if ($clients[0]['therapist'] == 0){
						UpdateTherapist($_SESSION['id'], $clients[0]["id"], $_SESSION['id'], '');
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
						
						//$t_f_data_db = SelDataFromDB('journal_tooth_status_temp', $_POST['new_id'], 'id');
						
						//$t_f_data_temp = $t_f_data_db[0];
						$t_f_data_temp = $_SESSION['journal_tooth_status_temp'];
						
						//$stat_id = $t_f_data_temp['id'];
						//$stat_time = $t_f_data_temp['create_time'];
						$stat_time = time();
						
						//unset($t_f_data_temp['id']);
						//unset($t_f_data_temp['create_time']);
						
						//var_dump ($t_f_data_db[0]);
						//var_dump ($t_f_data_temp);
						
						$n_zuba = '';
						$stat_zuba = '';
						
						//для ЗО и остального
						$doppol_arr = array();
						
						foreach($t_f_data_temp as $key => $value){
							$n_zuba .= "`{$key}`, ";
							if (isset($value['zo'])){
								$doppol_arr[$key]['zo'] = $value['zo'];
								unset($value['zo']);
							}
							if (isset($value['shinir'])){
								$doppol_arr[$key]['shinir'] = $value['shinir'];
								unset($value['shinir']);
							}
							if (isset($value['podvizh'])){
								$doppol_arr[$key]['podvizh'] = $value['podvizh'];
								unset($value['podvizh']);
							}
							//var_dump($value['zo']);
							$rrr = implode(',', $value);
							$stat_zuba .= "'{$rrr}', ";
						}

						//echo $stat_zuba.'<br />';
						
						$n_zuba = substr($n_zuba, 0, -2);
						$stat_zuba = substr($stat_zuba, 0, -2);
						
						//var_dump($doppol_arr);
						//var_dump($shinir_arr);
						//var_dump($podvizh_arr);
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
						
						$task = mysql_insert_id();
						
						
						if (!empty($doppol_arr)){
							$n_zuba = '';
							$stat_zuba = '';
							foreach($doppol_arr as $key => $value){
								$n_zuba .= "`{$key}`, ";
								$rrr = json_encode($value, true);
								$stat_zuba .= "'{$rrr}', ";
							}
							//echo $stat_zuba.'<br />';
							
							$n_zuba = substr($n_zuba, 0, -2);
							$stat_zuba = substr($stat_zuba, 0, -2);
							
							$query = "
								INSERT INTO `journal_tooth_status_temp` (
									`id`, {$n_zuba}) 
								VALUES (
									'{$task}', {$stat_zuba}) ";
							mysql_query($query) or die(mysql_error());
							
							//var_dump($stat_zuba);
						}
						
						//удаление темповой записи
						//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
						
						//mysql_close();
										
						//WriteToDB_EditCosmet ($_POST['filial'], $client, $arr, time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], $_POST['comment']);
						
						if ($_POST['notes'] == 1){
							if ($_POST['add_notes_type'] != 0){
								if (($_POST['add_notes_months'] != 0) || ($_POST['add_notes_days'] != 0)){

									$date = date_create(date('Y-m-d', time()));
									$dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['add_notes_months'].' months'));
									$dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['add_notes_days'].' days'))) + 60*60*8;
									
									//echo date('d.m.Y H:i', $dead_line);
									
									
									//Добавим данные в базу
									//require 'config.php';
									//mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
									//mysql_select_db($dbName) or die(mysql_error()); 
									//mysql_query("SET NAMES 'utf8'");
									$time = time();
									$query = "
											INSERT INTO `notes` (
												`description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `dead_line`, `closed`) 
											VALUES (
												'{$_POST['add_notes_type']}', 'journal_tooth_status', '{$client}', '{$task}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$dead_line}, 0) ";
									//echo $query.'<br />';
									
									mysql_query($query) or die(mysql_error());

									//удаление темповой записи
									//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
									
									//mysql_close();
								}else{
									echo 'Вы не назначили срок напоминания<br /><br />';
								}
							}else{
								echo 'Не выбран тип напоминания<br /><br />';
							}
						}
						
						
						if ($_POST['remove'] == 1){
							$removeAct = json_decode($_POST['removeAct'], true);
							$removeWork = json_decode($_POST['removeWork'], true);
							foreach($removeAct as $ind => $val){
								if ($ind != 0){
									if ($val != ''){
										if ($removeWork[$ind] != ''){
											//Ищем к кому направляем
											$RemWorkers = SelDataFromDB ('spr_workers', $removeWork[$ind], 'full_name');
											//var_dump($clients);
											if ($RemWorkers != 0){
												$RemWorker = $RemWorkers[0]["id"];
												
												
												//Добавим данные в базу
												//require 'config.php';
												//mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
												//mysql_select_db($dbName) or die(mysql_error()); 
												//mysql_query("SET NAMES 'utf8'");
												$time = time();
												$query = "
														INSERT INTO `removes` (
															`description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `whom`, `closed`) 
														VALUES (
															'{$val}', 'journal_tooth_status', '{$client}', '{$task}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$RemWorker}, 0) ";
												//echo $query.'<br />';
												
												mysql_query($query) or die(mysql_error());

												//удаление темповой записи
												//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
												
												//mysql_close();
												
											}else{
												echo 'Не нашли в базе врача, к кому направляете.<br />';
											}
										}else{
											echo 'Пустое значение врача, к кому направляете.<br />';
										}
									}else{
										echo 'Пустое значение причины направления.<br />';
									}
								}
							}
						}
						
						if ($_POST['pervich'] == 1){
							$pervich_status = 1;
						}else{
							$pervich_status = 0;
						}
						if ($_POST['insured'] == 1){
							$insured_status = 1;
						}else{
							$insured_status = 0;
						}
						if ($_POST['noch'] == 1){
							$noch_status = 1;
						}else{
							$noch_status = 0;
						}
						
						$query = "
							INSERT INTO `journal_tooth_ex` (
								`id`, `pervich`, `noch`, `insured`)
							VALUES (
								'{$task}', '{$pervich_status}', '{$noch_status}', '{$insured_status}') ";

						mysql_query($query) or die(mysql_error());
							
						
						
						
						echo '
							Посещение добавлено в журнал.<br /><br />';
							
						


						echo '
							<header>
								<span style= "color: rgba(255,39,39,0.7); padding: 2px;">
									Напоминание: Если вы что-то забыли или необходимо внести изменения,<br />
									посещение можно отредактировать.
								</span>
							</header>

							<br /><br />
							<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>
							<a href="add_task_stomat.php?client='.$client.'" class="b">Добавить посещение этому пациенту</a>
							<!--<a href="add_task_stomat.php" class="b">Добавить новое посещение</a>-->
							';
						mysql_close();
					}else{
						echo '
							Вы не выбрали филиал<br /><br />
							<a href="add_task_stomat.php" class="b">Добавить запись</a>';
					}
				}else{
					echo '
						В нашей базе нет такого пациента :(<br /><br />
						<a href="add_task_stomat.php" class="b">Добавить запись</a>
						<a href="add_client.php" class="b">Добавить пациента</a>';
				}
			}
		}
	}
?>