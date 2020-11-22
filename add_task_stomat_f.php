<?php 

//add_task_stomat_f.php
//Функция для добавления задачи стоматологов в журнал

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		//var_dump ($_SESSION['journal_tooth_status_temp']);
		if ($_POST){
			//$workerFounded = TRUE;

			if (isset($_POST['zapis_id'])){

                $msql_cnnct = ConnectToDB ();

                $sheduler_zapis = array();

                $zapis_id = $_POST['zapis_id'];

                //Получаем данные по записи
                $query = "SELECT * FROM `zapis` WHERE `id`='".$zapis_id."' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($sheduler_zapis, $arr);
                    }
                }
                //var_dump($sheduler_zapis);

                //Если запись есть (а она должна быть)
                if (!empty($sheduler_zapis)) {

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

                    //Если нет лечащего врача, добавим его
                    if ($client_j[0]['therapist'] == 0){
                        UpdateTherapist($_SESSION['id'], $client_id, $_SESSION['id'], '');
                    }

                    //Данные ЗФ, сохраненные в сессии
                    $t_f_data_temp = $_SESSION['journal_tooth_status_temp'][$client_id];

                    $n_zuba = '';
                    $stat_zuba = '';

                    //для ЗО и остального
                    $doppol_arr = array();

                    foreach($t_f_data_temp as $key => $value) {
                        $n_zuba .= "`{$key}`, ";
                        if (isset($value['zo'])) {
                            $doppol_arr[$key]['zo'] = $value['zo'];
                            unset($value['zo']);
                        }
                        if (isset($value['shinir'])) {
                            $doppol_arr[$key]['shinir'] = $value['shinir'];
                            unset($value['shinir']);
                        }
                        if (isset($value['podvizh'])) {
                            $doppol_arr[$key]['podvizh'] = $value['podvizh'];
                            unset($value['podvizh']);
                        }
                        if (isset($value['retein'])) {
                            $doppol_arr[$key]['retein'] = $value['retein'];
                            unset($value['retein']);
                        }
                        if (isset($value['skomplect'])) {
                            $doppol_arr[$key]['skomplect'] = $value['skomplect'];
                            unset($value['skomplect']);
                        }
                        //var_dump($value['zo']);
                        $rrr = implode(',', $value);
                        $stat_zuba .= "'{$rrr}', ";
                    }

                    $n_zuba = substr($n_zuba, 0, -2);
                    $stat_zuba = substr($stat_zuba, 0, -2);


                    //Добавим данные в базу
                    $time = time();

                    $msql_cnnct = ConnectToDB ();

                    $query = "
                                INSERT INTO `journal_tooth_status` (
                                    `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`, `zapis_date`, `zapis_id`, {$n_zuba}) 
                                VALUES (
                                    '{$filial_id}', '{$client_id}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', '{$worker}', '{$_POST['comment']}', '{$zapis_date}', '{$zapis_id}', {$stat_zuba}) ";
                    //echo $query.'<br />';

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $task = mysqli_insert_id($msql_cnnct);

                    //ЗО и остальное
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


                    //Дополнительные данные
                    //pervich, insured, noch - берем теперь из записи
                    //Зачем их еще и сюда добавлять теперь? не понятно

                    $pervich_status = $sheduler_zapis[0]['pervich'];
                    $insured_status = $sheduler_zapis[0]['insured'];
                    $noch_status = $sheduler_zapis[0]['noch'];

                    $month = dateTransformation($sheduler_zapis[0]['month']);

//                    $start_time_h = floor($sheduler_zapis[0]['start_time'] / 60);
//                    $start_time_m = $sheduler_zapis[0]['start_time'] % 60;
//                    if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
//                    $end_time_h = floor(($sheduler_zapis[0]['start_time'] + $sheduler_zapis[0]['wt']) / 60);
//                    if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
//                    $end_time_m = ($sheduler_zapis[0]['start_time'] + $sheduler_zapis[0]['wt']) % 60;
//                    if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;

                    $query = "
								INSERT INTO `journal_tooth_ex` (
									`id`, `pervich`, `noch`, `insured`, `complaints`, `objectively`, `diagnosis`, `therapy`, `recommended`)
								VALUES (
									'{$task}', '{$pervich_status}', '{$noch_status}', '{$insured_status}', '{$_POST['complaints']}', '{$_POST['objectively']}', '{$_POST['diagnosis']}', '{$_POST['therapy']}', '{$_POST['recommended']}') ";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    echo '
								<a href="task_stomat_inspection.php?id='.$task.'" class="ahref">Посещение #'.$task.'</a> добавлено в журнал.
								<br><br>
								<a href="zub_photo.php?id='.$task.'" class="b">Добавить фото</a>
								<a href="invoice_advance_add.php?client=' . $sheduler_zapis[0]['patient'] . '&filial=' . $sheduler_zapis[0]['office'] . '&date=' . strtotime($sheduler_zapis[0]['day'] . '.' . $month . '.' . $sheduler_zapis[0]['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $sheduler_zapis[0]['id'] . '&worker=' . $sheduler_zapis[0]['worker'] . '&type=' . $sheduler_zapis[0]['type'] . '" class="b">Предварительный расчёт</a>';


                    //Напоминания
                    if ($_POST['notes'] == 1){
                        if ($_POST['add_notes_type'] != 0){
                            if (($_POST['add_notes_months'] != 0) || ($_POST['add_notes_days'] != 0)){

                                $date = date_create(date('Y-m-d 21:00:00', time()));
                                $dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['add_notes_months'].' months'));
                                $dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['add_notes_days'].' days'))) + 60*60*8;

                                $time = time();

                                $query = "
												INSERT INTO `notes` (
													`description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `dead_line`, `closed`) 
												VALUES (
													'{$_POST['add_notes_type']}', 'journal_tooth_status', '{$client_id}', '{$task}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$dead_line}, 0) ";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

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


                    //Направления
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
																'{$val}', 'journal_tooth_status', '{$client_id}', '{$task}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$RemWorker}, 0) ";
                                            //echo $query.'<br />';

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                            //удаление темповой записи
                                            //mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");

                                            //mysql_close();

                                        }else{
                                            echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Не нашли в базе врача, к кому направляете.</span>';
                                        }
                                    }else{
                                        echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Пустое значение врача, к кому направляете.</span>';
                                    }
                                }else{
                                    echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Пустое значение причины направления.</span>';
                                }
                            }
                        }
                    }

                    echo '
                            <br><br>
                                <header>
									<span style= "color: rgba(255,39,39,0.7); padding: 2px;">
										Напоминание: Если вы что-то забыли или необходимо внести изменения,<br />
										посещение можно <a href="edit_task_stomat.php?id='.$task.'" class="ahref">отредактировать</a>.
									</span>
								</header>';

                    echo '<br><br>
								<a href="client.php?id='.$client_id.'" class="b">В карточку пациента</a>';
                }

                CloseDB ($msql_cnnct);

            }
		}
    }
?>