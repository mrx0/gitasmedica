<?php 

//add_task_cosmet_f.php
//Функция для добавления задачи косметологов в журнал

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
        include_once('DBWorkPDO.php');
		//var_dump ($_POST);
		
		if ($_POST){
			$workerFounded = TRUE;

            if (isset($_POST['zapis_id'])){
				//Ищем клиента
				//$clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
				//var_dump($clients);

                $db = new DB();

                $zapis_id = $_POST['zapis_id'];

                $args = [
                    'zapis_id' => $zapis_id
                ];

                //Получаем данные по записи
                $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";

                //Выбрать все
                $sheduler_zapis = $db::getRows($query, $args);

                //Если запись есть (а она должна быть)
                if (!empty($sheduler_zapis)) {

                    //$client = $clients[0]["id"];

                    $client_id = $sheduler_zapis[0]['patient'];
                    $filial_id = $sheduler_zapis[0]['office'];
                    $worker = $sheduler_zapis[0]['worker'];

                    $start_time_h = floor($sheduler_zapis[0]['start_time'] / 60);
                    $start_time_m = $sheduler_zapis[0]['start_time'] % 60;
                    if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
//                    $end_time_h = floor(($sheduler_zapis[0]['start_time'] + $sheduler_zapis[0]['wt']) / 60);
//                    if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
//                    $end_time_m = ($sheduler_zapis[0]['start_time'] + $sheduler_zapis[0]['wt']) % 60;
//                    if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;

                    $zapis_date = strtotime($sheduler_zapis[0]['day'] . '.' . $sheduler_zapis[0]['month'] . '.' .$sheduler_zapis[0]['year'] . ' ' . $start_time_h . ':' . $start_time_m);

                    //Получаем все данные по клиенту
                    $client_j = SelDataFromDB('spr_clients', $client_id, 'user');

//
//					if ($clients[0]['therapist2'] == 0){
//						UpdateTherapist($_SESSION['id'], $clients[0]["id"], $_SESSION['id'], '2');
//					}

                    //Если нет лечащего врача, добавим его
                    if ($client_j[0]['therapist'] == 0){
                        UpdateTherapist($_SESSION['id'], $client_id, $_SESSION['id'], '');
                    }
					
					//if ($_POST['filial'] != 0){
						//Исполнитель
//						if (isset($_POST['worker'])){
//
//							$workers = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
//							if ($workers != 0){
//								$workerFounded = TRUE;
//								$worker = $workers[0]["id"];
//							}else{
//								$workerFounded = FALSE;
//							}
//						}else{
//							$worker = $_SESSION['id'];
//						}
						
						
						//if ($workerFounded){
							
							//Отметки процедур
							$arr = array();
							$rezult = '';
							
							foreach ($_POST as $key => $value){
								if (mb_strstr($key, 'action') != FALSE){
									//array_push ($arr, $value);
									$key = str_replace('action', 'c', $key);
									//echo $key.'<br />';
									$arr[$key] = $value;
								}				
							}
							
							//var_dump ($arr);
							//$rezult = json_encode($arr);
							//echo $rezult.'<br />';
							//echo strlen($rezult);
							
							//новая отметка о первичке
							if ($_POST['pervich'] == 1){
								$pervich_status = 1;
							}else{
								$pervich_status = 0;
							}
							
							
							$task = WriteToDB_EditCosmet ($filial_id, $client_id, $arr, time(), $_SESSION['id'], $worker, $_POST['comment'], $pervich_status, $zapis_date, $zapis_id);


                            //Напоминания
                            if ($_POST['notes'] == 1){
                                if ($_POST['add_notes_type'] != 0){
                                    if (($_POST['add_notes_months'] != 0) || ($_POST['add_notes_days'] != 0)){

                                        $date = date_create(date('Y-m-d 21:00:00', time()));
                                        $dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['add_notes_months'].' months'));
                                        $dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['add_notes_days'].' days'))) + 60*60*8;

                                        $time = time();

                                        $args = [
                                            'type' => 6,
                                            'description' => $_POST['add_notes_type'],
                                            'dtable' => 'journal_cosmet1',
                                            'client_id' => $client_id,
                                            'task' => $task,
                                            'create_time' => $time,
                                            'create_person' => $_SESSION['id'],
                                            'last_edit_time' => $time,
                                            'last_edit_person' => $_SESSION['id'],
                                            'dead_line' => $dead_line,
                                            'closed' => 0
                                        ];

                                        $query = "
                                            INSERT INTO `notes` (
                                                `type`, `description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `dead_line`, `closed`) 
                                            VALUES (
                                                :type, :description, :dtable, :client_id, :task, :create_time, :create_person, :last_edit_time, :last_edit_person, :dead_line, :closed) ";

                                        $db::sql($query, $args);

                                        //Добавим тикет
        //                                require 'variables.php';
        //
        //                                $time = date('Y-m-d H:i:s', time());
        //
        //                                $plan_date = date('Y-m-d H:i:s', $dead_line);
        //
        //                                //Описание
        //                                $descr = '<b>'.$for_notes[$_POST['add_notes_type']].'</b><br>
        //                                        '.date('d.m.Y H:i:s', $_POST['zapis_date']).'<br>
        //                                        Пациент: <a href="client.php?id='.$client_id.'" class="ahref">'.$clients[0]['full_name'].'</a><br>
        //                                        <br>';
        //
        //                                //Подключаемся к другой базе специально созданной для тикетов
        //                                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');
        //
        //                                $query = "INSERT INTO `journal_tickets` (`filial_id`, `descr`, `plan_date`, `create_time`, `create_person`)
        //                                        VALUES (
        //                                        '{$_POST['filial']}', '{$descr}', '{$plan_date}', '{$time}', '{$_SESSION['id']}')";
        //
        //                                //Закрываем соединение
        //                                CloseDB ($msql_cnnct2);

                                    }else{
                                        echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Вы не назначили срок напоминания</span>';
                                    }
                                }else{
                                    echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Не выбран тип напоминания</span>';
                                }
                            }



							echo '
								<a href="task_cosmet.php?id='.$task.'" class="ahref">Посещение #'.$task.'</a> добавлено в журнал.
								<br><br>
								<header>
									<span style= "color: rgba(255,39,39,0.7); padding: 2px;">
										Напоминание: Если вы что-то забыли или необходимо внести изменения,<br />
										посещение можно <a href="edit_task_cosmet.php?id='.$task.'" class="ahref">отредактировать</a>.
									</span>
								</header>
								<a href="client.php?id='.$client_id.'" class="b">В карточку пациента</a>';
//						}else{
//							echo '
//								Указанный вами исполнитель отсутствует в нашей базе
//								<br><br>
//								<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>';
//						}
					/*}else{
						echo '
							Вы не выбрали филиал
							<br><br>
							<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>';
					}*/
				}/*else{
					echo '
						В нашей базе нет такого пациента
						<br><br>';
				}*/
			}
		}
	}
?>