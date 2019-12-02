<?php

//task_stomat_inspection.php
//Описание осмотра стоматолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

                //require 'variables.php';

                // !!! **** тест с записью
                include_once 'showZapisRezult.php';

                include_once 'tooth_status.php';

                $filials_j = getAllFilials(true, false, true);
                //var_dump($filials_j);

                $sheduler_zapis = array();

				//$task = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');
				//var_dump($task);
				
				$closed = FALSE;
				$dop = array();

                $sheduler_zapis = array();

                $task = array();

                $msql_cnnct = ConnectToDB ();

                //Получаем данные
                $query = "
                SELECT j_ts.*, j_tex.complaints, j_tex.objectively, j_tex.diagnosis, j_tex.therapy, j_tex.recommended FROM `journal_tooth_status` j_ts
                 LEFT JOIN `journal_tooth_ex` j_tex ON j_tex.id = j_ts.id
                WHERE j_ts.id = '{$_GET['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($task, $arr);
                    }

                }
                //var_dump($task);
				
				if (!empty($task)){
                    //var_dump($task);

                    $zapis_id = $task[0]['zapis_id'];

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

                    //Если запись есть (а она должна быть, если ЗФ не очень старая, ведь раньше записи не было)
                    if (!empty($sheduler_zapis)) {
                        $client_id = $sheduler_zapis[0]['patient'];
                        $filial_id = $sheduler_zapis[0]['office'];
                    }else{
                        $client_id = $task[0]['client'];
                        $filial_id = $task[0]['office'];
                    }

                    $prev_tooth_status_id = 0;

                    //Выбрать предыдущую ЗФ
                    $query = "SELECT `id` FROM `journal_tooth_status` WHERE `client`='{$client_id}' AND `create_time` < '{$task[0]['create_time']}' ORDER BY `create_time` DESC LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            $prev_tooth_status_id = $arr['id'];
                        }
                    }

                    //$office = $filials_j[$filial_id]['name'];

                    //Стом статусы
                    $actions_stomat = SelDataFromDB('actions_stomat', '', '');

					echo '
						<script src="js/init.js" type="text/javascript"></script>
						<div id="status">
							<header>
								<h2>Посещение #'.$task[0]['id'].'';
					if (!$closed){
						if (($stom['edit'] == 1) || $god_mode){
							echo '
									<a href="edit_task_stomat.php?id='.$task[0]['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
					}
					echo '
								</h2>';
								

					//Надо найти клиента
                    $client_j = SelDataFromDB ('spr_clients', $client_id, 'client_id');

					if ($client_j != 0){
						$client = $client_j[0]["name"];
						if ($client_j[0]["birthday"] != -1577934000){
							$cl_age = getyeardiff($client_j[0]["birthday"], 0);
						}else{
							$cl_age = 0;
						}
					}else{
						$client = 'unknown';
						$cl_age = 0;
					}
					
					//Перенесено сюда снизу
					//ЗО и тд
					$dop = array();

					$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$task[0]['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							array_push($dop, $arr);
						}
					}
											
					include_once 't_surface_name.php';
					include_once 't_surface_status.php';

					$arr = array();
					$decription = $task[0];
					//var_dump($decription);
					
					unset($decription['id']);
					unset($decription['office']);
					unset($decription['client']);
					unset($decription['create_time']);
					unset($decription['create_person']);
					unset($decription['last_edit_time']);
					unset($decription['last_edit_person']);
					unset($decription['worker']);
					
					unset($decription['comment']);
					
					unset($decription['zapis_date']);
					unset($decription['zapis_id']);

                    unset($decription['complaints']);
                    unset($decription['objectively']);
                    unset($decription['diagnosis']);
                    unset($decription['therapy']);
                    unset($decription['recommended']);
                    //var_dump($decription);
					
					$t_f_data = array();
					
					//собрали массив с зубами и статусами по поверхностям
					foreach ($decription as $key => $value){
						$surfaces_temp = explode(',', $value);
						//var_dump($surfaces_temp);
						foreach ($surfaces_temp as $key1 => $value1){
							///!!!Еба костыль
							if ($key1 < 13){
								$t_f_data[$key][$surfaces[$key1]] = $value1;
							}
						}
					}
					//var_dump ($t_f_data);

					if (!empty($dop[0])){
						//var_dump($dop[0]);

						unset($dop[0]['id']);
						//var_dump($dop[0]);

						foreach($dop[0] as $key => $value){
							//var_dump($value);

							if ($value != '0'){
								//var_dump($value);

								$dop_arr = json_decode($value, true);
								//var_dump($dop_arr);

								foreach ($dop_arr as $n_key => $n_value){
									if ($n_key == 'zo'){
										$t_f_data[$key]['zo'] = $n_value;
										//$t_f_data_draw[$key]['zo'] = $n_value;
									}
									if ($n_key == 'shinir'){
										$t_f_data[$key]['shinir'] = $n_value;
										//$t_f_data_draw[$key]['shinir'] = $n_value;
									}
									if ($n_key == 'podvizh'){
										$t_f_data[$key]['podvizh'] = $n_value;
										//$t_f_data_draw[$key]['podvizh'] = $n_value;
									}
									if ($n_key == 'retein'){
										$t_f_data[$key]['retein'] = $n_value;
										//$t_f_data_draw[$key]['retein'] = $n_value;
									}
									if ($n_key == 'skomplect'){
										$t_f_data[$key]['skomplect'] = $n_value;
										//$t_f_data_draw[$key]['skomplect'] = $n_value;
									}
								}
							}
						}
					}
					//var_dump ($t_f_data);

					if (Sanation2($task[0]['id'], $t_f_data, $cl_age)){
						echo '<span style= "background: rgba(87,223,63,0.7); padding: 2px;">Санирован (ТЕСТ)</span><br />';
					}else{
						echo '<span style= "background: rgba(255,39,119,0.7); padding: 2px;">Не санирован (ТЕСТ)</span><br />';
					}

					echo '			
							</header>';

                    //Показываем карточку записи
                    echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);


					echo '
							<div id="data">';

                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size:80%;  color: #555;">Зубная формула</span><br>
                                    </div>
                                </div>';

                    echo '				
                                <div class="cellsBlock3">
                                    <div class="cellRight">';
										
					//рисуем зубную формулу
					include_once 'teeth_map_svg.php';
					DrawTeethMap($t_f_data, 0, $tooth_status, $tooth_alien_status, $surfaces, '');
												
					echo '						
                                    </div>
                                </div>';

                    //2019.11.27 - я уже не помню, зачем тут это, я это закрываю
//                    echo '
//                                <div class="cellsBlock3">
//                                    <div class="cellRight">';
//
//						$z = 0;
//						$descr_rez = '';
//
//						//echo '<div><a href="#open1" onclick="show(\'hidden_'.$z.'\',200,5)">Подробно</a></div>';
//						echo '<div id=hidden_'.$z.' style="display:none;">';
//						foreach($t_f_data as $key => $value){
//							//var_dump ($value);
//							foreach ($value as $key1 => $value1){
//
//								if ($key1 == 'status'){
//									//var_dump ($value1);
//									if ($value1 != 0){
//										//$descr_rez .=
//										echo t_surface_name('t'.$key.'NONE', 1).' '.t_surface_status($value1, 0).'';
//									}
//								}elseif($key1 == 'pin'){
//									if ($value1 != 0){
//										echo t_surface_status(3, 0);
//									}
//								}elseif($key1 == 'alien'){
//
//								}elseif($key1 == 'zo'){
//
//								}else{
//									if ($value1 != 0){
//										echo t_surface_name('t'.$key.$key1, 1).' '.t_surface_status(0, $value1);
//									}
//								}
//							}
//
//						}
//						echo '</div>';
//
//					echo '
//                                    </div>
//                                </div>';

                    //Жалобы
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Жалобы</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['complaints'].'</i>
                                    </div>
                                </div>';

                    //Объективно
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Объективно</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['objectively'].'</i>
                                    </div>
                                </div>';

                    //Диагноз
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Диагноз</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['diagnosis'].'</i>
                                    </div>
                                </div>';

                    //Лечение
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Лечение</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['therapy'].'</i>
                                    </div>
                                </div>';

                    //Рекомендовано
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Рекомендовано</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['recommended'].'</i>
                                    </div>
                                </div>';

                    //Комментарий
                    echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size: 80%;">Комментарий</span><br>
                                        <i style="font-size: 120%;">'.$task[0]['comment'].'</i>
                                    </div>
                                </div>';

                    echo '
                                <div class="cellsBlock3" style="margin-bottom: 10px;">
                                    <span style="font-size: 80%; color: #999;">
                                        Создан: '.date('d.m.y H:i', $task[0]['create_time']).' пользователем
                                        '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user', true).'';
					if ((($task[0]['last_edit_time'] != 0) || ($task[0]['last_edit_person'] !=0)) && (($task[0]['create_time'] != $task[0]['last_edit_time']))){
						echo '
                                        <br>
                                        Редактировался: '.date('d.m.y H:i', $task[0]['last_edit_time']).' пользователем
                                        '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user', true).'';
					}
					echo '
                                    </span>
                                </div>';


                    if ($prev_tooth_status_id != 0) {
                        echo '
                        <div class="cellsBlock2">
                            <!--<a href = "task_stomat_inspection.php?id='.$prev_tooth_status_id.'" class="ahref" style="font-weight:bold; font-size: 80%; color: #353535;">
                                Открыть предыдущую</i>
                            </a>-->
                        </div>
                        <div class="cellsBlock2" style="margin-bottom: 10px;">
                            <a href = "#" onclick = "window.open(\'task_stomat_inspection_window.php?id='.$prev_tooth_status_id.'\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;" class="ahref" style="font-weight:bold; font-size: 80%; color: #353535;">
                                Открыть предыдущую ЗФ в новом окне <i class="fa fa-external-link" aria-hidden="true"></i>
                            </a>
                            </div>
                        <div>
                        ';
                    }


                    if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
                        echo '	
								<a href="stom_history.php?client='.$task[0]['client'].'" class="b">История ЗФ</a>';
                    }
                    echo '<a href="zub_photo.php?id='.$_GET['id'].'" class="b">Добавить снимки</a>';

                    echo '</div>';




                    //2019-11-27 закрыл тикеты, не пользуются
                    //Тикеты
//                    $tickets_arr = array();
//
//                    $today = date('Y-m-d', time());
//                    $today3daysplus = date('Y-m-d', strtotime('+3 days'));
//
//                    //$filials_j = getAllFilials(false, false);
//
//                    $show_option_str_for_paginator = '';
//
//                    //Подключаемся к другой базе специально созданной для тикетов
//                    $msql_cnnct2 = ConnectToDB_2 ('config_ticket');
//
//                    $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
//                    GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
//                    FROM `journal_tickets` j_ticket
//                    LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
//                    LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
//                    LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = j_ticket.id
//                    WHERE j_ticket.id IN (SELECT `ticket_id` FROM `journal_ticket_associations` WHERE `ticket_id`= j_ticket.id AND `associate`='add_task_stomat_f.php' AND `association_id`='{$task[0]['id']}')
//                    AND j_ticket.status <> '9' AND j_ticket.status <> '1'
//                    GROUP BY `id` ORDER BY /*`plan_date` ASC,*/ `id` DESC";
//
//                    $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);
//
//                    $number = mysqli_num_rows($res);
//
//                    if ($number != 0){
//                        while ($arr = mysqli_fetch_assoc($res)){
//                            array_push($tickets_arr, $arr);
//                        }
//                    }
//                    //var_dump($tickets_arr);
//
//                    CloseDB ($msql_cnnct2);
//
//                    if (!empty($tickets_arr)) {
//                        echo '<div style="margin: 15px 0 0 -5px;">';
//                        foreach ($tickets_arr as $j_tickets) {
//
//                            $ticket_style = 'ticketBlock';
//                            $expired_icon = '';
//
//                            //Если просрочен
//                            if ($j_tickets['plan_date'] != '0000-00-00') {
//                                //время истечения срока
//                                $pd = $j_tickets['plan_date'];
//                                //текущее
//                                $nd = $today;
//                                //сравнение не прошли ли сроки исполнения
//                                if (strtotime($pd) > strtotime($nd) + 2 * 24 * 60 * 60) {
//                                    $expired = false;
//                                } else {
//                                    if (strtotime($pd) < strtotime($nd)) {
//                                        $expired = true;
//                                        $ticket_style = 'ticketBlockexpired';
//                                        $expired_icon = 'fa fa-exclamation-circle';
//                                    } else {
//                                        $expired = true;
//                                        $ticket_style = 'ticketBlockexpired2';
//                                        $expired_icon = 'fa fa-exclamation';
//                                    }
//                                }
//                                /*var_dump(strtotime($nd));
//                                var_dump(strtotime($pd));
//                                var_dump(strtotime($pd)-strtotime($nd));
//                                var_dump(3*24*60*60);*/
//                                //var_dump(date('Y-m-d', time()));
//                                //var_dump(strtotime(date('Y-m-d', time())));
//                            } else {
//                                $expired = false;
//                            }
//                            //Если выполнен и закрыт
//                            if ($j_tickets['status'] == 1) {
//                                $ticket_done = true;
//                                $ticket_style = 'ticketBlockdone';
//                            } else {
//                                $ticket_done = false;
//                            }
//                            //Если удалён
//                            if ($j_tickets['status'] == 9) {
//                                $ticket_deleted = true;
//                                $ticket_style = 'ticketBlockdeleted';
//                            } else {
//                                $ticket_deleted = false;
//                            }
//                            //Если прочитано
//                            if ($j_tickets['read_status'] == 1) {
//                                //$readStateClass = 'display: none;';
//                                $newTopic = false;
//                            } else {
//                                $newTopic = true;
//                            }
//
//
//                            //Длина строки проверка, если больше, то сокращаем
//                            if (strlen($j_tickets['descr']) > 100) {
//                                $descr = mb_strimwidth($j_tickets['descr'], 0, 50, "...", 'utf-8');
//                            } else {
//                                $descr = $j_tickets['descr'];
//                            }
//
//                            echo '
//                        <div class="' . $ticket_style . '" style="font-size: 95%;">
//                            <div class="ticketBlockheader">
//                                <div style="margin-left: 5px; text-align: left; float: left;">
//                                    <span style=" color: rgb(29, 29, 29); font-size: 80%; font-weight: bold; margin-right: 3px;">#' . $j_tickets['id'] . '</span>';
//                            if (!$ticket_deleted) {
//                                if ($ticket_done) {
//                                    echo '
//                                    <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
//                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">' . date('d.m.Y', strtotime($j_tickets['fact_date'])) . '</span>';
//                                } else {
//                                    if ($j_tickets['plan_date'] != '0000-00-00') {
//                                        echo '
//                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">до ' . date('d.m.Y', strtotime($j_tickets['plan_date'])) . '</span>';
//                                        //<i class="fa fa-times" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
//                                    }
//                                }
//                                if (!$ticket_done && $expired) {
//                                    echo '
//                                    <i class="' . $expired_icon . '" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"  title=""></i>';
//                                }
//                            } else {
//                                echo '
//                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">удалён</span>';
//                            }
//                            echo '
//                                </div>
//                                <div style="margin-right: 5px; text-align: right; float: right;">';
//                            if ($ticket_deleted) {
//                                echo '
//                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(244, 244, 244, 0.8); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Удалено"></i>
//                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
//                            } else {
//                                if ($_SESSION['id'] == $j_tickets['worker_id']) {
//                                    echo '
//                                        <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Вы исполнитель"></i>';
//                                }
//                                if ($newTopic) {
//                                    echo '
//                                        <i class="fa fa-bell" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Обновлено"></i>';
//                                }
//                            }
//
//
//                            echo '
//                                </div>
//                            </div>
//                            <a href="ticket.php?id=' . $j_tickets['id'] . '&' . $show_option_str_for_paginator . '" class="ticketBlockmain ahref">
//                                ' . $descr . '<br>
//                                <span style="font-size: 80%; color: rgb(115, 112, 112);">нажмите, чтобы открыть</span>
//                            </a><br>
//
//                            <div class="ticketBlockfooter">
//                                <!--создан ' . date('d.m.y H:i', strtotime($j_tickets['create_time'])) . '<br>-->
//                                автор: <span style="color: rgb(51, 51, 51);">' . WriteSearchUser('spr_workers', $j_tickets['create_person'], 'user', false) . '</span><br>
//                                <!--где создано: ', $j_tickets['filial_id'] == 0 ? 'не указано' : $filials_j[$j_tickets['filial_id']]['name'], '-->';
//                            if ($j_tickets['filials'] != NULL) {
//                                echo 'филиалы: ';
//                                $filials_arr_temp = explode(',', $j_tickets['filials']);
//
//                                if (!empty($filials_arr_temp)) {
//                                    foreach ($filials_arr_temp as $f_id) {
//                                        $bgColor_filialHere = '';
//                                        if (isset($_SESSION['filial'])) {
//                                            if ($f_id == $_SESSION['filial']) {
//                                                $bgColor_filialHere = 'background-color: rgba(144,247,95, 1); border: 1px dotted rgba(65, 33, 222, 0.34);';
//                                            }
//                                        }
//                                        echo '<div style="display: inline-block; font-size: 80%; margin-right: 5px; color: rgb(59, 9, 111); ' . $bgColor_filialHere . '">' . $filials_j[$f_id]['name2'] . '</div>';
//                                    }
//                                }
//
//                            }
//                            echo '
//                            </div>
//                        </div>';
//                        }
//                        echo '</div>';
//                    }


                    echo '
									
									<!--<input type="hidden" id="ended" name="ended" value="">-->
									<input type="hidden" id="task_id" name="task_id" value="'.$task[0]['id'].'">
									<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';



                    //Напоминания
					$notes = SelDataFromDB ('notes', $task[0]['id'], 'task');

					if ($notes != 0) {
                        echo '
                                <div class="cellsBlock3" style="margin: 10px 0 0px;">
                                    <div class="cellLeft">
                                        <span style="font-size: 80%;">Напоминания</span><br>
                                    </div>
                                </div>';
                    }

					echo WriteNotes($notes, 0, true);

					//Направления
					$removes = SelDataFromDB ('removes', $task[0]['id'], 'task');

                    if ($removes != 0) {
                        echo '
                                <div class="cellsBlock3" style="margin: 10px 0 -10px;">
                                    <div class="cellLeft">
                                        <span style="font-size: 80%;">Направления</span><br>
                                    </div>
                                </div>';
                    }

					echo WriteRemoves($removes, 0, 0, false);
									
					//Фотки			
    				$arr = array();
					$rez = array();

                    $msql_cnnct = ConnectToDB ();

					$query = "SELECT * FROM `journal_zub_img` WHERE `task`='{$task[0]['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							array_push($rez, $arr);
						}
						$rezult = $rez;
					}else{
						$rezult = 0;
					}

					$price = 0;
					
					if ($rezult != 0){
						//var_dump($rezult);
						echo '
							<div style=" margin-bottom: 30px; margin-top: 30px;">
								<div style="border: 1px solid #ccc; width: 400px; margin: 0 0 2px 0; padding: 2px;">
									Снимки
								</div>';
						for($i = 0; $i < count($rezult); $i++){
							echo '
								<div style="display: inline-block; border: 1px solid #ccc; vertical-align: top;">
									<div style=" border: 1px solid #eee;">'.date('d.m.y H:i', $rezult[$i]['uptime']).'</div>
								';
							echo '									
									<div>';		
							if (file_exists ('zub_photo/'.$rezult[$i]['id'].'.jpg')){
								echo '
									<a href="#" onclick="window.open(\'zub_photo/'.$rezult[$i]['id'].'.jpg\',\'Image'.$rezult[$i]['id'].'\',\'width=700,height=350,status=no,resizable=yes,top=200,left=200\');">
										<img src="zub_photo/'.$rezult[$i]['id'].'.jpg" width="200">
									</a>';
							}elseif (file_exists ('zub_photo/'.$rezult[$i]['id'].'.png')){
								echo '
								<a href="#" onclick="window.open(\'zub_photo/'.$rezult[$i]['id'].'.png\',\'Image'.$rezult[$i]['id'].'\',\'width=700,height=350,status=no,resizable=yes,top=200,left=200\');">
									<img src="zub_photo/'.$rezult[$i]['id'].'.png" width="200">
								</a>';									
									
							}else{
								echo 'Ошибка изображения '.$rezult[$i]['id'];
							}
							echo '
									</div>
								</div>';
						}
						echo '
							</div>';
					}else{
						//echo 'нет фотки';
					}


                    //Наряд
                    if ($task[0]['zapis_id'] != 0) {

                        $summ = 0;
                        $summins = 0;

                        $sheduler_zapis = array();
                        $invoice_ex_j = array();
                        $invoice_ex_j_mkb = array();

                        //Выберем запись
                        $query = "SELECT * FROM `zapis` WHERE `id`='" . $task[0]['zapis_id'] . "'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($sheduler_zapis, $arr);
                            }
                        } else {
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //if ($client !=0){

                        if (!empty($sheduler_zapis)) {

                            //Выберем наряды
                            //$query = "SELECT * FROM `journal_invoice`  WHERE `zapis_id`='" . $task[0]['zapis_id'] . "';";
                            $query = "SELECT * FROM `journal_invoice` ji
                            LEFT JOIN `journal_invoice_ex` jiex 
                            ON ji.id = jiex.invoice_id
                            WHERE ji.zapis_id = '" . $task[0]['zapis_id'] . "' AND ji.status <> 9;";


                            //$invoice_j = SelDataFromDB('journal_invoice', $task[0]['zapis_id'], 'id');

                            //$query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='" . $task[0]['zapis_id'] . "';";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    if (!isset($invoice_ex_j[$arr['invoice_id']])) {
                                        $invoice_ex_j[$arr['invoice_id']] = array();
                                    }
                                    if (!isset($invoice_ex_j[$arr['invoice_id']][$arr['ind']])) {
                                        $invoice_ex_j[$arr['invoice_id']][$arr['ind']] = array();
                                    }


                                    array_push($invoice_ex_j[$arr['invoice_id']][$arr['ind']], $arr);

                                }
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($invoice_ex_j, $arr);
                                }*/
                            } //else
                            //$invoice_ex_j = 0;


                            //сортируем зубы по порядку
                            /*if (!empty($invoice_ex_j)) {
                                foreach ($invoice_ex_j as $keyy => $invoice_ex_j_data){
                                    ksort($invoice_ex_j_data);
                                }

                            }*/

                            //var_dump($invoice_ex_j);

                            if (!empty($invoice_ex_j)) {

                                foreach ($invoice_ex_j as $invoice_ex_j_id => $invoice_ex_j_data) {

                                    $invoice_ex_j_mkb = array();

                                    //Для МКБ
                                    $query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='" . $invoice_ex_j_id . "';";
                                    //var_dump ($query);

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                    $number = mysqli_num_rows($res);
                                    if ($number != 0) {
                                        while ($arr = mysqli_fetch_assoc($res)) {
                                            if (!isset($invoice_ex_j_mkb[$arr['ind']])) {
                                                $invoice_ex_j_mkb[$arr['ind']] = array();
                                                array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                            } else {
                                                array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                            }
                                        }
                                    }

                                    echo '			
                                        <div class="invoice_rezult" style="margin-top: 30px; display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">
                                            <div id="errror" class="invoceHeader" style="">
                                                 <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <a href="invoice.php?id='.$invoice_ex_j_id.'" class="ahref" style="">Наряд #' . $invoice_ex_j_id . '</a>
                                                    </div>
                                                </div>
                                            </div>';

                                    echo '
                                            <div id="invoice_rezult" style="float: none; width: 850px;">';

                                    echo '
                                                <div class="cellsBlock">
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center;">';
                                    if ($sheduler_zapis[0]['type'] == 5) {
                                        echo '
                                                        <i><b>Зуб</b></i>';
                                    }
                                    if (($sheduler_zapis[0]['type'] == 6) || ($sheduler_zapis[0]['type'] == 10)) {
                                        echo '
                                                        <i><b>№</b></i>';
                                    }
                                    echo '
                                                    </div>
                                                    <div class="cellText2" style="font-size: 100%; text-align: center;">
                                                        <i><b>Наименование</b></i>
                                                    </div>';

                                    echo '
                                                </div>';


                                    foreach ($invoice_ex_j_data as $ind => $invoice_data) {

                                        //var_dump($invoice_data);
                                        echo '
                                        <div class="cellsBlock">
                                            <div class="cellCosmAct toothInInvoice" style="text-align: center;">';
                                        if ($ind == 99) {
                                            echo 'П';
                                        } else {
                                            echo $ind;
                                        }
                                        echo '
                                            </div>';

                                        //Диагноз
                                        if ($sheduler_zapis[0]['type'] == 5) {

                                            if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])) {
                                                echo '
                                                <div class="cellsBlock" style="font-size: 100%;" >
                                                    <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
                                                        <b>';
                                                if ($ind == 99) {
                                                    echo '<i>Полость</i>';
                                                } else {
                                                    echo '<i>Зуб</i>: ' . $ind;
                                                }
                                                echo '
                                                        </b>. <i>Диагноз</i>: ';

                                                foreach ($invoice_ex_j_mkb[$ind] as $mkb_key => $mkb_data_val) {
                                                    $rez = array();
                                                    //$rezult2 = array();

                                                    $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                    $number = mysqli_num_rows($res);
                                                    if ($number != 0) {
                                                        while ($arr = mysqli_fetch_assoc($res)) {
                                                            $rez[$mkb_data_val['mkb_id']] = $arr;
                                                        }
                                                    } else {
                                                        $rez = 0;
                                                    }
                                                    if ($rez != 0) {
                                                        foreach ($rez as $mkb_name_val) {
                                                            echo '
                                                            <div class="mkb_val" style="background: rgb(239, 255, 255); border: 1px dotted #bababa;"><b>' . $mkb_name_val['code'] . '</b> ' . $mkb_name_val['name'] . '
            
                                                            </div>';
                                                        }
                                                    } else {
                                                        echo '<div class="mkb_val">???</div>';
                                                    }

                                                }

                                                echo '
                                                    </div>
                                                </div>';
                                            }


                                            /*if (isset($invoice_ex_j_mkb[''])){
                                                echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.14) none repeat scroll 0% 0%;">
                                                            <b>';
                                                if ($ind == 99){
                                                    echo '<i>Полость</i>';
                                                }else{
                                                    echo '<i>Зуб</i>: '.$ind;
                                                }
                                                echo '
                                                            </b>. <i>Диагноз</i>: '.$invoice_data[0]['mkb_id'].'
                                                        </div>
                                                    </div>';
                                            }*/

                                            /*            }


                                                        }
                                                    }
                                                }
                                            }*/

                                            foreach ($invoice_data as $item) {
                                                //var_dump($item);

                                                //часть прайса
                                                //if (!empty($invoice_data)){

                                                //foreach ($invoice_data as $key => $items){
                                                echo '
                                                            <div class="cellsBlock" style="font-size: 100%;" >
                                                            <!--<div class="cellCosmAct" style="">
                                                                -
                                                            </div>-->
                                                                <div class="cellText2" style="">';

                                                //Хочу имя позиции в прайсе
                                                $arr = array();
                                                $rez = array();

                                                $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$item['price_id']}'";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                $number = mysqli_num_rows($res);
                                                if ($number != 0) {
                                                    while ($arr = mysqli_fetch_assoc($res)) {
                                                        array_push($rez, $arr);
                                                    }
                                                    $rezult2 = $rez;
                                                } else {
                                                    $rezult2 = 0;
                                                }

                                                if ($rezult2 != 0) {

                                                    echo $rezult2[0]['name'];

                                                    //Узнать цену
                                                    /*$arr = array();
                                                    $rez = array();
                                                    $price = 0;
                                                    $stoim_item = 0;
                                                    //Для отбора цены по времени создания наряда
                                                    $price_arr = array();



                                                    $query = "SELECT `date_from`, `price` FROM `spr_priceprices` WHERE `item`='{$item['price_id']}' ORDER BY `date_from` DESC, `create_time`";

                                                    if ($item['insure'] != 0){
                                                        $query = "SELECT `date_from`, `price` FROM `spr_priceprices_insure` WHERE `item`='{$item['price_id']}' AND `insure`='".$item['insure']."' ORDER BY `date_from` DESC, `create_time`";
                                                    }

                                                    $res = mysql_query($query) or die(mysql_error().' -> '.$query);
                                                    $number = mysql_num_rows($res);
                                                    if ($number != 0){
                                                        //если кол-во цен == 1
                                                        if ($number == 1){
                                                            $arr = mysql_fetch_assoc($res);
                                                            $price = $arr['price'];
                                                        //если > 1
                                                        }else{
                                                            while ($arr = mysql_fetch_assoc($res)){
                                                                $price_arr[$arr['date_from']] = $arr;
                                                            }
                                                            //обратная сортировка
                                                            krsort($price_arr);
                                                            //var_dump($price_arr);
                                                            //var_dump(strtotime($invoice_j[0]['create_time']));

                                                            foreach($price_arr as $date_from => $value_arr){
                                                                if (strtotime($invoice_j[0]['create_time']) > $date_from){
                                                                    $price = $value_arr['price'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        $price = '?';
                                                    }*/

                                                } else {
                                                    echo '?';
                                                }

                                                echo '
                                                     </div>
                                                </div>';
                                            }
                                            echo '
                                                </div>';
                                            /*    $price = $item['price'];

                                                if ($sheduler_zapis[0]['type'] == 5) {
                                                    if ($item['insure'] != 0) {
                                                        //Написать страховую
                                                        $insure_j = SelDataFromDB('spr_insure', $item['insure'], 'id');

                                                        if ($insure_j != 0) {
                                                            $insure_name = $insure_j[0]['name'];
                                                        } else {
                                                            $insure_name = '?';
                                                        }
                                                    } else {
                                                        $insure_name = 'нет';
                                                    }
                                                }

                                                if ($sheduler_zapis[0]['type'] == 5) {
                                                    /*echo '
                                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;">
                                                                    ' . $insure_name . '
                                                                </div>';*/


                                            /*                   if ($item['insure'] != 0) {
                                                                   if ($item['insure_approve'] == 1) {
                                                                       /*echo '
                                                                                       <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                                                           <i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
                                                                                       </div>';*/
                                            /*                        } else {
                                                                        /*echo '
                                                                                    <div class="cellCosmAct" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;">
                                                                                        <i class="fa fa-ban" aria-hidden="true"></i>
                                                                                    </div>';*/
                                            /*                        }

                                                                } else {
                                                                    /*echo '
                                                                                <div class="cellCosmAct" insureapprove="' . $item['insure_approve'] . '" style="font-size: 70%; text-align: center;">
                                                                                    -
                                                                                </div>';*/
                                            /*                    }
                                                            }

                                                            /*echo '
                                                                        <div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                                            <b>' . $price . '</b>
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            ' . $item['spec_koeff'] . '
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            <b>' . $item['quantity'] . '</b>
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            ' . $item['discount'] . '
                                                                        </div>
                                                                        <div class="cellCosmAct settings_text" guarantee="' . $item['guarantee'] . '" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">';
                                                            if ($item['guarantee'] != 0) {
                                                                echo '
                                                                                <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                                            } else {
                                                                echo '-';
                                                            }*/
                                            /*echo '
                                                        </div>
                                                        <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                            <b>';*/

                                            /*
                                                                                if (($item['itog_price'] != 0) && ($price != 0)) {

                                                                                    $stoim_item = $item['itog_price'];

                                                                                } else {
                                                                                    //вычисляем стоимость
                                                                                    //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                                                                    $stoim_item = $item['quantity'] * $price;

                                                                                    //с учетом скидки акции
                                                                                    if ($item['insure'] == 0) {
                                                                                        //$stoim_item = $stoim_item - ($stoim_item * $invoice_j[0]['discount'] / 100);
                                                                                        $stoim_item = $stoim_item - ($stoim_item * $item['discount'] / 100);
                                                                                        //$stoim_item = round($stoim_item/10) * 10;
                                                                                        $stoim_item = round($stoim_item);
                                                                                    }
                                                                                    //$stoim_item = round($stoim_item/10) * 10;
                                                                                }

                                                                                if ($item['guarantee'] == 0) {
                                                                                    //echo $stoim_item;
                                                                                } else {
                                                                                    //echo 0;
                                                                                }

                                                                                //Общая стоимость
                                                                                if ($item['guarantee'] == 0) {
                                                                                    if ($item['insure'] != 0) {
                                                                                        if ($item['insure_approve'] != 0) {
                                                                                            $summins += $stoim_item;
                                                                                        }
                                                                                    } else {
                                                                                        $summ += $stoim_item;
                                                                                    }
                                                                                }


                                                                                echo '</b>
                                                                                            <!--</div>-->
                                                                                        </div>';
                                                                            }
                                                                            echo '
                                                                                    </div>';
                                                                        }


                                                                        echo '
                                                                                        <div class="cellsBlock" style="font-size: 90%;" >
                                                                                            <div class="cellText2" style="padding: 2px 4px;">
                                                                                            </div>
                                                                                            <!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
                                                                                                Итого:-->';
                                                                        if (($summ != $invoice_j[0]['summ']) || ($summins != $invoice_j[0]['summins'])) {
                                                                            /*echo '<br>
                                                                                <span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
                                            /*               }

                                                           echo '

                                                                               <!--</div>
                                                                               <div class="cellName" style="padding: 2px 4px;">
                                                                                   <div>
                                                                                       <div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">' . $summ . '</div> руб.</div>
                                                                                   </div>-->';
                                                           if ($sheduler_zapis[0]['type'] == 5) {
                                                               echo '
                                                                                   <!--<div>
                                                                                       <div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">' . $summins . '</div> руб.</div>
                                                                                   </div>-->';
                                                           }
                                                           echo '
                                                                               </div>';

                                                       }
                                                   }else{
                                                       //echo 'не было привязано к записи';
                                                   }
                               */



                                        }
                                    }
                                    echo '</div></div>';
                                }

                            }

                        }
                    }


                    CloseDB ($msql_cnnct);
									
					/*if (!$closed){
						echo '
									<input type=\'button\' class="b" value=\'Назначить исполнителя\' onclick=\'
										ajax({
											url:"task_add_worker_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}*/

					/*if ($closed){
						echo '
									<input type=\'button\' class="b" value=\'Вернуть в работу\' onclick=\'
										ajax({
											url:"task_reopen_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}else{
						echo '
									<input type=\'button\' class="b" value=\'Закрыть\' onclick=\'
										ajax({
											url:"task_close_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}*/
					echo '
								</form>';	
					

					echo '
			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
			 </script>
					';
					
					echo '
							</div>
						</div>';
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>