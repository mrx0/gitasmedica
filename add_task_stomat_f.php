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
			$workerFounded = TRUE;
			
			if ($_POST['client'] == ''){
				echo '
					Не выбран пациент<br><br>';
			}else{
				//Ищем Пациента
                $clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_id');
				//var_dump($clients);
				if ($clients != 0){
                    $client_id = $client = $clients[0]["id"];
					if ($clients[0]['therapist'] == 0){
						UpdateTherapist($_SESSION['id'], $clients[0]["id"], $_SESSION['id'], '');
					}
					
					
					if ($_POST['filial'] != 0){
						//Исполнитель
						if (isset($_POST['worker'])){
						
							$workers = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
							if ($workers != 0){
								$workerFounded = TRUE;
								$worker = $workers[0]["id"];
							}else{
								$workerFounded = FALSE;
							}
						}else{
							$worker = $_SESSION['id'];
						}
						
						if ($workerFounded){
						
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
							$t_f_data_temp = $_SESSION['journal_tooth_status_temp'][$clients[0]['id']];
							
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
								if (isset($value['retein'])){
									$doppol_arr[$key]['retein'] = $value['retein'];
									unset($value['retein']);
								}
								if (isset($value['skomplect'])){
									$doppol_arr[$key]['skomplect'] = $value['skomplect'];
									unset($value['skomplect']);
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
							$time = time();

                            $msql_cnnct = ConnectToDB ();
							
							$query = "
									INSERT INTO `journal_tooth_status` (
										`office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`, `zapis_date`, `zapis_id`, {$n_zuba}) 
									VALUES (
										'{$_POST['filial']}', '{$client}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', '{$worker}', '{$_POST['comment']}', '{$_POST['zapis_date']}', '{$_POST['zapis_id']}', {$stat_zuba}) ";
							//echo $query.'<br />';

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
							
							$task = mysqli_insert_id($msql_cnnct);
							
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

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
								
								//var_dump($stat_zuba);
							}
							
							//удаление темповой записи
							//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
							
							//mysql_close();
											
							//WriteToDB_EditCosmet ($_POST['filial'], $client, $arr, time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], $_POST['comment']);
							
							if ($_POST['notes'] == 1){
								if ($_POST['add_notes_type'] != 0){
									if (($_POST['add_notes_months'] != 0) || ($_POST['add_notes_days'] != 0)){

										$date = date_create(date('Y-m-d 21:00:00', time()));
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

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

										//удаление темповой записи
										//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
										
										//mysql_close();



                                        //Добавим тикет
                                        require 'variables.php';

                                        $time = date('Y-m-d H:i:s', time());

                                        $plan_date = date('Y-m-d H:i:s', $dead_line);

                                        //Описание
                                        $descr = '<b>'.$for_notes[$_POST['add_notes_type']].'</b><br>
                                        '.date('d.m.Y H:i:s', $_POST['zapis_date']).'<br>
                                        Пациент: <a href="client.php?id='.$client_id.'" class="ahref">'.$clients[0]['full_name'].'</a><br>
                                        <br>';

                                        //Подключаемся к другой базе специально созданной для тикетов
                                        $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                                        $query = "INSERT INTO `journal_tickets` (`filial_id`, `descr`, `plan_date`, `create_time`, `create_person`)
                                        VALUES (
                                        '{$_POST['filial']}', '{$descr}', '{$plan_date}', '{$time}', '{$_SESSION['id']}')";
/*
                                        $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                                        //ID новой позиции
                                        $mysql_insert_id = mysqli_insert_id($msql_cnnct2);

                                        //Собираем строку запроса
                                        $query = '';

                                        //Добавляем категории сотрудников
                                        $permissionsWhoCanSee_arr = array(3, 9);
                                        $workers_type = $permissionsWhoCanSee_arr;

                                        //array_push($workers_type, 5);
                                        //$workers_type = array_unique($workers_type);

                                        if (!empty($workers_type) && ($workers_type != '')){
                                            foreach ($workers_type as $workers_type_id){
                                                $query .= "INSERT INTO `journal_tickets_worker_type` (`ticket_id`, `worker_type`)
                                                VALUES (
                                                '{$mysql_insert_id}', '{$workers_type_id}');";
                                            }
                                        }

                                        //Добавляем исполнителей
                                        //if (!empty($workers)){
                                        //    foreach ($workers    as $worker_id){
                                                $query .= "INSERT INTO `journal_tickets_workers` (`ticket_id`, `worker_id`)
                                                VALUES (
                                                '{$mysql_insert_id}', '{$_SESSION['id']}');";
                                        //    }
                                       // }

                                        //Добавляем лог
                                        $query .= "INSERT INTO `journal_tickets_logs` (`ticket_id`, `create_time`, `create_person`, `descr`)
                                        VALUES (
                                        '{$mysql_insert_id}', '{$time}', '{$_SESSION['id']}', 'Новый тикет добавлен');";

                                        //Добавляем отметку о прочтении (мы же создали это сами)
                                        $query .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                                        VALUES (
                                        '{$mysql_insert_id}', '{$time}', '{$_SESSION['id']}', '1');";

                                        //Добавляем комментарий
                                        $comment = trim(preg_replace('/ +/', ' ', trim($descr, ' ')), ' ');

                                        $comment = htmlspecialchars($comment);

                                        $comment = mysqli_real_escape_string($msql_cnnct2, $comment);

                                        if ($comment != ''){
                                            $query .= "INSERT INTO `journal_tickets_comments` (`ticket_id`, `create_time`, `create_person`, `descr`)
                                            VALUES (
                                            '{$mysql_insert_id}', '{$time}', '{$_SESSION['id']}', '{$comment}');";
                                        }


                                        //Добавим ассоциации где показывать
                                        $query .= "INSERT INTO `journal_ticket_associations` (`ticket_id`, `associate`, `association_id`)
                                            VALUES (
                                            '{$mysql_insert_id}', 'add_task_stomat_f.php', '{$task}');";

                                        $query .= "INSERT INTO `journal_ticket_associations` (`ticket_id`, `associate`, `association_id`)
                                            VALUES (
                                            '{$mysql_insert_id}', 'client.php', '{$client_id}');";


                                        //Делаем большой запрос
                                        $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);



*/
                                        //Закрываем соединение
                                        CloseDB ($msql_cnnct2);



									}else{
										echo 'Вы не назначили срок напоминания<br><br>';
									}
								}else{
									echo 'Не выбран тип напоминания<br><br>';
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

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

													//удаление темповой записи
													//mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");
													
													//mysql_close();
													
												}else{
													echo 'Не нашли в базе врача, к кому направляете.<br>';
												}
											}else{
												echo 'Пустое значение врача, к кому направляете.<br>';
											}
										}else{
											echo 'Пустое значение причины направления.<br>';
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

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							echo '
								<a href="task_stomat_inspection.php?id='.$task.'" class="ahref">Посещение #'.$task.'</a> добавлено в журнал.
								<br><br>
								<a href="zub_photo.php?id='.$task.'" class="b">Добавить фото</a>
								<header>
									<span style= "color: rgba(255,39,39,0.7); padding: 2px;">
										Напоминание: Если вы что-то забыли или необходимо внести изменения,<br />
										посещение можно <a href="edit_task_stomat.php?id='.$task.'" class="ahref">отредактировать</a>.
									</span>
								</header>

								<br><br>
								<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>
								<!--<a href="add_task_stomat.php?client='.$client.'&filial='.$_POST['filial'].'&insured='.$insured_status.'&pervich='.$pervich_status.'&noch='.$noch_status.'&date='.$_POST['zapis_date'].'&id='.$_POST['zapis_id'].'" class="b">Добавить посещение этому пациенту</a>-->
								';

                            CloseDB ($msql_cnnct);

						}else{
							echo '
								Указанный вами исполнитель отсутствует в нашей базе<br><br>';
						}
					}else{
						echo '
							Вы не выбрали филиал<br><br>';
					}
				}else{
					echo '
						В нашей базе нет такого пациента.<br><br>';
				}
			}
		}
	}
?>