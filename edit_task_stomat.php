<?php

//edit_task_stomat.php
//Редактирование посещения стоматолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($stom['edit'] == 1) || $god_mode){
			if ($_GET){

                include_once('DBWorkPDO.php');

				include_once 'DBWork.php';
				include_once 'functions.php';

                require 'variables.php';

                // !!! **** тест с записью
                include_once 'showZapisRezult.php';

                include_once 'tooth_status.php';
				
				//$task = SelDataFromDB('journal_tooth_status', $_GET['id'], 'task');
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
//					if ($task[0]['office'] == 99){
//						$office = 'Во всех';
//					}else{
//						$offices = SelDataFromDB('spr_filials', '', '');
//						//var_dump ($offices);
//						//$office = $offices[0]['name'];
//					}

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


					echo '
						<script src="js/init.js" type="text/javascript"></script>
						<div id="status">
							<header>
								<h2>Редактировать посещение <a href="task_stomat_inspection.php?id='.$task[0]['id'].'" class="ahref">#'.$task[0]['id'].'</a></h2>
								<!--<a href="zub_photo.php?id='.$_GET['id'].'" class="b">Добавить снимки</a>-->
							</header>';

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

                    //Показываем карточку записи
                    echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);

					echo '
							<div id="data">';

					if (!$closed){

                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size:80%;  color: #555;">Зубная формула</span><br>
                                    </div>
                                </div>';
					
//						echo '
//									<form action="edit_task_stomat_f.php">
//										<div class="cellsBlock2">
//											<div class="cellLeft">
//												Время посещения<br>
//												<span style="font-size:70%;">
//													Согласно записи
//												</span>
//											</div>
//											<div class="cellRight">';
//						if ($task[0]['zapis_date'] != 0){
//							echo date('d.m.y H:i', $task[0]['zapis_date']);
//						}else{
//							echo 'не было привязано к записи';
//						}
//						echo '
//											</div>
//										</div>
//										<div class="cellsBlock2" style="display: none">
//											<div class="cellLeft">Дата посещения</div>
//											<div class="cellRight">';
//				if ($task[0]['create_time'] != 0){
//					//print_r  (getdate($client[0]['birthday']));
//					$bdate = getdate($task[0]['create_time']);
//					echo '
//						<input type="hidden" id="sel_seconds" name="sel_seconds" value="'.$bdate['seconds'].'">
//						<input type="hidden" id="sel_minutes" name="sel_minutes" value="'.$bdate['minutes'].'">
//						<input type="hidden" id="sel_hours" name="sel_hours" value="'.$bdate['hours'].'">';
//				}else{
//					$bdate = 0;
//				}
//				echo '<select name="sel_date" id="sel_date">';
//				$i = 1;
//				while ($i <= 31) {
//					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
//					$i++;
//				}
//				echo "</select>";
//				// Месяц
//				echo "<select name='sel_month' id='sel_month'>";
//				$month = array(
//					"Январь",
//					"Февраль",
//					"Март",
//					"Апрель",
//					"Май",
//					"Июнь",
//					"Июль",
//					"Август",
//					"Сентябрь",
//					"Октябрь",
//					"Ноябрь",
//					"Декабрь"
//				);
//				foreach ($month as $m => $n) {
//					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
//				}
//				echo "</select>";
//				// Год
//				echo "<select name='sel_year' id='sel_year'>";
//				$j = 1920;
//				while ($j <= 2020) {
//					echo "<option value='" . $j . "'", $bdate['year'] == $j ? ' selected':'' ,">$j</option>";
//					$j++;
//				}
//				echo '</select>
//
//
//											</div>
//										</div>
//
//										<div class="cellsBlock2">
//											<div class="cellLeft">Врач</div>
//											<div class="cellRight">'.WriteSearchUser('spr_workers',$task[0]['worker'], 'user', true).'</div>
//										</div>
//
//										<div class="cellsBlock2">
//											<div class="cellLeft">Пациент</div>
//											<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user', true).'</div>
//										</div>';

                /*echo '
										<div class="cellsBlock2">
											<div class="cellLeft">Филиал</div>
											<div class="cellRight">
												<select name="filial" id="filial">
													<option value="0" selected>Выберите филиал</option>';
						if ($offices !=0){
							for ($i=0;$i<count($offices);$i++){
								echo "<option value='".$offices[$i]['id']."' ", $task[0]['office'] == $offices[$i]['id'] ? 'selected' : '' ,">".$offices[$i]['name']."</option>";
							}
						}
						echo '
												</select>
											</div>
										</div>';*/

//                echo '
//										<div class="cellsBlock2">
//											<div class="cellLeft">Филиал</div>
//											<div class="cellRight">';
//
//                $offices_j = SelDataFromDB('spr_filials', $task[0]['office'] , 'offices');
//
//                echo $offices_j[0]['name'].'<input type="hidden" id="filial" name="filial" value="'.$task[0]['office'] .'">';
//
//                echo '
//											</div>
//										</div>';
//
//				echo '
//										<div class="cellsBlock2">
//											<div class="cellLeft">Описание</div>
//											<div class="cellRight">
//											</div>
//										</div>
//										';
					
//						$t_f_data_db_temp = array();
						
//						require 'config.php';
//						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
//						mysql_select_db($dbName) or die(mysql_error());
//						mysql_query("SET NAMES 'utf8'");

//						$time = time();

//						$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '{$_GET['id']}'";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                        $number = mysqli_num_rows($res);
//
//						if ($number != 0){
//                            while ($arr = mysqli_fetch_assoc($res)){
//								array_push($t_f_data_db_temp, $arr);
//							}
//							$t_f_data_db = $t_f_data_db_temp[0];
//						}else
//							$t_f_data_db = $t_f_data_db_first;
//
//
//
//
//						//ЗО и тд
//						$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$task[0]['id']}'";
//
//						$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                        $number = mysqli_num_rows($res);
//
//						if ($number != 0){
//                            while ($arr = mysqli_fetch_assoc($res)){
//								array_push($dop, $arr);
//							}
//
//						}

//						$client = $t_f_data_db['client'];
						
						//var_dump ($t_f_data_db);
						
//						unset($t_f_data_db['id']);
//						unset($t_f_data_db['office']);
//						unset($t_f_data_db['client']);
//						unset($t_f_data_db['create_time']);
//						unset($t_f_data_db['create_person']);
//						unset($t_f_data_db['last_edit_time']);
//						unset($t_f_data_db['last_edit_person']);
//						unset($t_f_data_db['worker']);
//						unset($t_f_data_db['comment']);
//						unset($t_f_data_db['zapis_date']);
//						unset($t_f_data_db['zapis_id']);
//
//
//						//Разбиваем запись с ',' на массив и записываем в новый массив
//						foreach ($t_f_data_db as $key => $value){
//							$surfaces_temp = explode(',', $value);
//							foreach ($surfaces_temp as $key1 => $value1){
//								///!!!Еба костыль
//								if ($key1 < 13){
//									$t_f_data[$key][$surfaces[$key1]] = $value1;
//								}
//							}
//						}
						
						//var_dump ($t_f_data);
						/*unset($t_f_data['id']);
						unset($t_f_data['office']);
						unset($t_f_data['client']);
						unset($t_f_data['create_time']);
						unset($t_f_data['create_person']);
						unset($t_f_data['last_edit_time']);
						unset($t_f_data['last_edit_person']);
						unset($t_f_data['worker']);
						unset($t_f_data['comment']);*/
						
						//unset($dop[0]['id']);
						
						//var_dump ($t_f_data);
//						if (!empty($dop[0])){
//							//var_dump($dop[0]);
//							unset($dop[0]['id']);
//							//var_dump($dop[0]);
//							foreach($dop[0] as $key => $value){
//								//var_dump($value);
//								if ($value != '0'){
//									//var_dump($value);
//									$dop_arr = json_decode($value, true);
//									//var_dump($dop_arr);
//									foreach ($dop_arr as $n_key => $n_value){
//										if ($n_key == 'zo'){
//											$t_f_data[$key]['zo'] = $n_value;
//											//$t_f_data_draw[$key]['zo'] = $n_value;
//										}
//										if ($n_key == 'shinir'){
//											$t_f_data[$key]['shinir'] = $n_value;
//											//$t_f_data_draw[$key]['shinir'] = $n_value;
//										}
//										if ($n_key == 'podvizh'){
//											$t_f_data[$key]['podvizh'] = $n_value;
//											//$t_f_data_draw[$key]['podvizh'] = $n_value;
//										}
//										if ($n_key == 'retein'){
//											$t_f_data[$key]['retein'] = $n_value;
//											//$t_f_data_draw[$key]['retein'] = $n_value;
//										}
//										if ($n_key == 'skomplect'){
//											$t_f_data[$key]['skomplect'] = $n_value;
//											//$t_f_data_draw[$key]['skomplect'] = $n_value;
//										}
//									}
//								}
//							}
//						}
						
						//var_dump ($t_f_data);		
						
						//!!!Тест. Пробуем записать в сессию.
						$_SESSION['journal_tooth_status_temp'][$client_id] = $t_f_data;
						//var_dump($_SESSION['journal_tooth_status_temp']);
						
						//var_dump($_SESSION);
						
						echo '						
								<div class="cellsBlock3">
									<div class="cellRight" id="teeth_map">';
									
						//рисуем зубную формулу						
						include_once 'teeth_map_svg.php';
						DrawTeethMap($t_f_data, 1, $tooth_status, $tooth_alien_status, $surfaces, '');
						
						echo '
									</div>
								</div>';
					

                        //Жалобы
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Жалобы</span><br>
                                        <textarea name="complaints" id="complaints" cols="80" rows="4">'.$task[0]['complaints'].'</textarea>
                                    </div>
                                </div>';

                        //Объективно
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Объективно</span><br>
                                        <textarea name="objectively" id="objectively" cols="80" rows="6">'.$task[0]['objectively'].'</textarea>
                                    </div>
                                </div>';

                        //Диагноз
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Диагноз</span><br>
                                        <textarea name="diagnosis" id="diagnosis" cols="80" rows="2">'.$task[0]['diagnosis'].'</textarea>
                                    </div>
                                </div>';

                        //Лечение
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Лечение</span><br>
                                        <textarea name="therapy" id="therapy" cols="80" rows="6">'.$task[0]['therapy'].'</textarea>
                                    </div>
                                </div>';

                        //Рекомендовано
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 80%; font-weight: bold;">Рекомендовано</span><br>
                                        <textarea name="recommended" id="recommended" cols="80" rows="5">'.$task[0]['recommended'].'</textarea>
                                    </div>
                                </div>';

                        //Комментарий
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size: 80%;">Комментарий</span><br>
                                            <textarea name="comment" id="comment" cols="50" rows="4">'.$task[0]['comment'].'</textarea>
                                    </div>
                                </div>';



						//Напоминания / особые отметки				
						$notes = SelDataFromDB ('notes', $_GET['id'], 'task');

						if ($notes != 0){
							echo '
							<div class="cellsBlock2">
								<div class="cellRight">
									Напоминание уже создано
								</div>
							</div>';
						}else{
							echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Создать напоминание
									<input type="checkbox" name="add_notes_show" id="add_notes_show" value="1" onclick="Add_notes_stomat_show(this)">
								</div>
								
								<div class="cellRight">
									<table id="add_notes_here" style="display:block; display:none;">
										<tr>
											<td colspan="2">
										
												<select name="add_notes_type" id="add_notes_type">
													<option value="0" selected>Выберите</option>';
                            foreach ($for_notes[5] as $for_notes_id =>  $for_notes_descr){
                                echo '<option value="'.$for_notes_id.'">'.$for_notes_descr.'</option>';
                            }

                            echo '
												</select>
											
											</td>
										</tr>
										<tr>
											<td>Месяцев</td>
											<td>Дней</td>
										</tr>
										<tr>
											<td>
												<input type="number" size="2" name="add_notes_months" id="add_notes_months" min="0" max="12" value="0">
											</td>
											<td>
												<input type="number" size="2" name="add_notes_days" id="add_notes_days" min="0" max="31" value="0">
											</td>
										</tr>
										<!--<tr>
											<td>
												<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_notes_stomat()>
											</td>
										</tr>-->
									</table>
									
								</div>
							</div>
							';
						}

                        //Направления
						//$removes = SelDataFromDB ('removes', $task[0]['id'], 'task');
						//var_dump($removes);


                        //Направления
                        //!!! Привести к одному виду с напоминаниями получение данных
                        $removes = array();

                        $args = [
                            'task' => $task[0]['id']
                        ];

                        $query = "SELECT r.*, s_c.name, s_w.name AS w_name FROM `removes` r 
                            RIGHT JOIN `spr_clients` s_c 
                            ON s_c.id = r.client  
                            RIGHT JOIN `spr_workers` s_w
                            ON s_w.id = r.create_person 
                            WHERE r.task = :task
                            ORDER BY r.create_time DESC";

                        //Выбрать все
                        $removes = $db::getRows($query, $args);

						echo WriteRemoves($removes, 0, 0, false, $finances);

						echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Добавить направление
									<input type="checkbox" name="add_remove_show" id="add_remove_show" value="1" onclick="Add_remove_stomat_show(this)">
								</div>
								<div class="cellRight">
									<div id="add_remove_here" style="display:block; display:none;">
										<table id="table_container">
										</table>
										<a href="#modal1" class="open_modal b" id="">Добавить направление</a>
										<!--<input type="button" class="b" value="Добавить поле" id="add" onclick="return add_new_image('.$_SESSION['id'].');">-->
										<div id="mini"></div>
									</div>
								</div>
							</div>
							';
//
//                        echo '
//                            <div class="cellsBlock3" style="margin-bottom: 10px;">
//                                <span style="font-size: 80%; color: #999;">
//                                    Создан: '.date('d.m.y H:i', $task[0]['create_time']).' пользователем
//                                    '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user', true).'';
//                        if ((($task[0]['last_edit_time'] != 0) || ($task[0]['last_edit_person'] !=0)) && (($task[0]['create_time'] != $task[0]['last_edit_time']))){
//                            echo '
//                                    <br>
//                                    Редактировался: '.date('d.m.y H:i', $task[0]['last_edit_time']).' пользователем
//                                    '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user', true).'';
//                        }
//                        echo '
//                                </span>
//                            </div>';



//                        //Первичка
//						$dop = array();
//
//						$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$task[0]['id']}'";
//
//						$res = mysql_query($query) or die($query);
//
//						$number = mysql_num_rows($res);
//						if ($number != 0){
//							while ($arr = mysql_fetch_assoc($res)){
//								array_push($dop, $arr);
//							}
//
//						}
//
//						if (!empty($dop) && ($dop[0]['pervich'] == 1)){
//							$checked_pervich = ' checked';
//						}else{
//							$checked_pervich = '';
//						}
//						if (!empty($dop) && ($dop[0]['insured'] == 1)){
//							$checked_insured = ' checked';
//						}else{
//							$checked_insured = '';
//						}
//						if (!empty($dop) && ($dop[0]['noch'] == 1)){
//							$checked_noch = ' checked';
//						}else{
//							$checked_noch = '';
//						}
//
//						echo '
//							<div class="cellsBlock3">
//								<div class="cellLeft">Первичный</div>
//								<div class="cellRight">
//									<input type="checkbox" name="pervich" id="pervich" value="1" '.$checked_pervich.'> да
//								</div>
//							</div>';
//						echo '
//							<div class="cellsBlock3">
//								<div class="cellLeft">Страховой</div>
//								<div class="cellRight">
//									<input type="checkbox" name="insured" id="insured" value="1" '.$checked_insured.'> да
//								</div>
//							</div>';
//						echo '
//							<div class="cellsBlock3">
//								<div class="cellLeft">Ночной</div>
//								<div class="cellRight">
//									<input type="checkbox" name="noch" id="noch" value="1" '.$checked_noch.'> да
//								</div>
//							</div>';
//
//						mysql_close();

//						echo '
//										<br />
//										<div class="cellsBlock2">
//											<div class="cellLeft">Подтвердить редактирование</div>
//											<div class="cellRight">
//												<input type="checkbox" name="change_true">
//											</div>
//										</div>';

						echo '
										<div class="cellsBlock2">
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
										</div>
										<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
										<input type="hidden" id="client_id" name="client_id" value="'.$client_id.'">
										<input type="hidden" id="client" name="client" value="'.$client_id.'">
										<input type="button" class="b" value="Применить" onclick="Ajax_edit_task_stomat()">';
					}else{
						echo '<h1>Какая-то ошибка</h1>';
					}
					echo '
							</div>
						</div>
						';

			echo '
				<script type="text/javascript">

					if (document.getElementById("#add_notes_show")){
						document.getElementById(\'add_notes_show\').checked=false;
					}
					document.getElementById(\'add_remove_show\').checked=false;
					
					function Add_notes_stomat_show(box) {
						var vis = (box.checked) ? "block" : "none";
						document.getElementById(\'add_notes_here\').style.display = vis;
					}
					function Add_remove_stomat_show(box) {
						var vis = (box.checked) ? "block" : "none";
						document.getElementById(\'add_remove_here\').style.display = vis;
					}
				</script>
			';
						
			echo '
			
			<!-- Модальные окна -->
			<div id="modal1" class="modal_div">
				<span class="modal_close">X</span>
				<table>
					<tr>
						<td>
							Причина направления
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="input_title" id="input_title" class="search_data"  autocomplete="off" style="width: 200px;">
						</td>
					</tr>
					<tr>
						<td>
							К кому направляем
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" size="50" name="searchdata3" id="search_client3" placeholder="Введите первые три буквы для поиска" value="" class="who3"  autocomplete="off" />
							<ul id="search_result3" class="search_result3"></ul>
						</td>
					</tr>
                    <tr>
                        <td>
                            <a href="#" class="b" id="close_mdd" onclick="AddRemoveData()" style="">Направить</a>
                        </td>
                    </tr>
				</table>

			</div>
			<!-- Подложка только одна -->
			<div id="overlay"></div>
			<!-- Модальные окна -->
			<div id="modal2" class="modal_div">
				<span class="modal_close">X</span>
					
						<h3>Выбор нескольких сегментов зубной формулы</h3>
						<b>Статус: </b>
						<div id="t_summ_status"></div>


							<table>
								<tr>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													18
												</td>
												<td style="border: 1px solid #BEBEBE;">
													17
												</td>
												<td style="border: 1px solid #BEBEBE;">
													16
												</td>
												<td style="border: 1px solid #BEBEBE;">
													15
												</td>
												<td style="border: 1px solid #BEBEBE;">
													14
												</td>
												<td style="border: 1px solid #BEBEBE;">
													13
												</td>
												<td style="border: 1px solid #BEBEBE;">
													12
												</td>
												<td style="border: 1px solid #BEBEBE;">
													11
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t18" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t17" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t16" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t15" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t14" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t13" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t12" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t11" value="1">
												</td>
											</tr>
										</table>
									</td>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													21
												</td>
												<td style="border: 1px solid #BEBEBE;">
													22
												</td>
												<td style="border: 1px solid #BEBEBE;">
													23
												</td>
												<td style="border: 1px solid #BEBEBE;">
													24
												</td>
												<td style="border: 1px solid #BEBEBE;">
													25
												</td>
												<td style="border: 1px solid #BEBEBE;">
													26
												</td>
												<td style="border: 1px solid #BEBEBE;">
													27
												</td>
												<td style="border: 1px solid #BEBEBE;">
													28
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t21" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t22" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t23" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t24" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t25" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t26" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t27" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t28" value="1">
												</td>
											</tr>
										</table>
										</td>
								</tr>
								<tr>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													48
												</td>
												<td style="border: 1px solid #BEBEBE;">
													47
												</td>
												<td style="border: 1px solid #BEBEBE;">
													46
												</td>
												<td style="border: 1px solid #BEBEBE;">
													45
												</td>
												<td style="border: 1px solid #BEBEBE;">
													44
												</td>
												<td style="border: 1px solid #BEBEBE;">
													43
												</td>
												<td style="border: 1px solid #BEBEBE;">
													42
												</td>
												<td style="border: 1px solid #BEBEBE;">
													41
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t48" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t47" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t46" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t45" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t44" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t43" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t42" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t41" value="1">
												</td>
											</tr>
										</table>
									</td>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													31
												</td>
												<td style="border: 1px solid #BEBEBE;">
													32
												</td>
												<td style="border: 1px solid #BEBEBE;">
													33
												</td>
												<td style="border: 1px solid #BEBEBE;">
													34
												</td>
												<td style="border: 1px solid #BEBEBE;">
													35
												</td>
												<td style="border: 1px solid #BEBEBE;">
													36
												</td>
												<td style="border: 1px solid #BEBEBE;">
													37
												</td>
												<td style="border: 1px solid #BEBEBE;">
													38
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t31" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t32" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t33" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t34" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t35" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t36" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t37" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t38" value="1">
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<input type="checkbox" name="implant" value="1"> + имплант
									</td>
								</tr>
							</table>
						<a href="#" class="b" onclick="refreshAllTeeth()">Применить</a>
					
			</div>
			
			
			
				<script type="text/javascript">

				function AddRemoveData(){
						
					var arrayRemoveAct = new Array();
					var arrayRemoveWorker = new Array();
					var maxIndex = 1;
					
					var input_title = document.getElementById("input_title").value;
					var search_client3 = document.getElementById("search_client3").value;
					//alert(input_title);
					
					$(".remove_add_search").each(function() {
						if (($(this).attr("id")).indexOf("td_title") != -1){
								var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
								arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
							}
							if (($(this).attr("id")).indexOf("td_worker") != -1){
								var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
								arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
							}
						maxIndex = Number(IndexArr)+1;
					});
					
					arrayRemoveAct[maxIndex] = input_title;
					arrayRemoveWorker[maxIndex] = search_client3;
					
					 $(document.getElementById("table_container")).empty();
					
					arrayRemoveAct.forEach(function(item, i, arrayRemoveAct){
						$(\'<tr>\')
						.attr(\'id\',\'tr_image_\'+i)
						.css({lineHeight:\'20px\'})
						.append (
							$(\'<td>\')
							.css({paddingRight:\'5px\',width:\'190px\'})
							.append(
								$(\'<input type="text" />\')
								.css({width:\'200px\'})
								.attr(\'id\',\'td_title_\'+i)
								.attr(\'class\',\'remove_add_search\')
								.attr(\'name\',\'input_title_\'+i)
								.attr(\'value\',item)
							)		
							
						)
						
						.append (
							$(\'<td>\')
							.css({paddingRight:\'5px\',width:\'200px\'})
							.append(
								$(\'<input type="text" size="50" name="" placeholder="" autocomplete="off" />\')
									.attr(\'id\',\'td_worker_\'+i)
									.attr(\'class\',\'remove_add_search\')
									.attr(\'value\',arrayRemoveWorker[i])
							)
						)	
						
						.append (
							$(\'<td>\')
							.css({width:\'60px\'})
							.append (
								$(\'<span id="progress_\'+i+\'"><a  href="#" onclick="$(\\\'#tr_image_\'+i+\'\\\').remove();" class="ico_delete"><img src="./img/delete.png" alt="del" border="0"></a></span>\')
							)
						)
						.appendTo(\'#table_container\');
						
						
						//$("#mini").append(this + "<br>");
					});
					
									//скрываем модальные окна
									$("#modal1, #modal2") // все модальные окна
										.animate({opacity: 0, top: \'45%\'}, 50, // плавно прячем
											function(){ // после этого
												$(this).css(\'display', 'none\');
												$(\'#overlay\').fadeOut(50); // прячем подложку
											}
										);	
	
				};
					
//				$(function(){
//						
//					//Живой поиск
//					$(\'.who3\').bind("change keyup input click", function() {
//						//alert(123);
//						if(this.value.length > 2){
//							$.ajax({
//								url: "FastSearchNameW.php", //Путь к обработчику
//								//statbox:"status",
//								type:"POST",
//								data:
//								{
//									\'searchdata3\':this.value
//								},
//								response: \'text\',
//								success: function(data){
//									$(".search_result3").html(data).fadeIn(); //Выводим полученые данные в списке
//								}
//							})
//						}else{
//							var elem1 = $("#search_result3"); 
//							elem1.hide(); 
//						}
//					})
//						
//					$(".search_result3").hover(function(){
//						$(".who3").blur(); //Убираем фокус с input
//					})
//						
//					//При выборе результата поиска, прячем список и заносим выбранный результат в input
//					$(".search_result3").on("click", "li", function(){
//						s_user = $(this).text();
//						$(".who3").val(s_user);
//						//$(".who").val(s_user).attr(\'disabled\', \'disabled\'); //деактивируем input, если нужно
//						$(".search_result3").fadeOut();
//					})
//					//Если click за пределами результатов поиска - убираем эти результаты
//					$(document).click(function(e){
//						var elem = $("#search_result3"); 
//						if(e.target!=elem[0]&&!elem.has(e.target).length){
//							elem.hide(); 
//						} 
//					})
//				})
					
					
					
					
					
					
					
					
					
				</script>
			
			';

			
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