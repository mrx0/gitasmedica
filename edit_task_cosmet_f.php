<?php 

//edit_task_cosmet_f.php
//Функция для редактирования посещения косметолога

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
        include_once('DBWorkPDO.php');

		//var_dump ($_POST);
		if ($_POST){
            if (isset($_POST['id'])){

                $db = new DB();
				
				//$create_time = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year'].' '.$_POST['sel_hours'].':'.$_POST['sel_minutes'].':'.$_POST['sel_seconds']);
				$create_time = '';

						/*foreach ($_POST as $key => $value){
							if (($key != 'id') && ($key != 'filial') && ($key != 'comment') && ($key != 'sel_date') && ($key != 'sel_month') && ($key != 'sel_year') && ($key != 'sel_seconds') && ($key != 'sel_minutes') && ($key != 'sel_hours') && ($key != 'ajax')){
								//array_push ($arr, $value);
								$key = str_replace('action', '', $key);
								//echo $key.'<br />';
								$arr[$key] = $value;
							}				
						}*/
						
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
				
				//!!!Временно можно менять время добавления!!!
				WriteToDB_UpdateCosmet ($_POST['id'], 0, time(), $_SESSION['id'], $_POST['comment'], '', $arr);

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
                                'client_id' => $_POST['client_id'],
                                'task' => $_POST['id'],
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
                            echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Вы не назначили срок напоминания</span><br><br>';
                        }
                    }else{
                        echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Не выбран тип напоминания</span><br><br>';
                    }
                }

				echo '
					Посещение отредактировано.
					<br><br>
					<a href="task_cosmet.php?id='.$_POST['id'].'" class="b">В посещение</a>
					';
			}else{
				echo '
					Что-то пошло не так<br /><br />
					<a href="task_cosmet.php?id='.$_POST['id'].'" class="b">В посещение</a>';
			}
		}

	}
	
?>