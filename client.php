<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'tooth_status.php';
			
			require 'config.php';
			
			//Массив с месяцами
			$monthsName = array(
				'01' => 'Январь',
				'02' => 'Февраль',
				'03' => 'Март',
				'04' => 'Апрель',
				'05' => 'Май',
				'06' => 'Июнь',
				'07'=> 'Июль',
				'08' => 'Август',
				'09' => 'Сентябрь',
				'10' => 'Октябрь',
				'11' => 'Ноябрь',
				'12' => 'Декабрь'
			);
			
			$text_tooth_status = array(
				'up' => -9,
				'down' => 138,
				'left' => array (
					1 => 258,
					2 => 221,
					3 => 186,
					4 => 149,
					5 => 113,
					6 => 77,
					7 => 42,
					8 => 5,						
				),
				'right' => array (
					1 => 311,
					2 => 350,
					3 => 386,
					4 => 422,
					5 => 459,
					6 => 495,
					7 => 529,
					8 => 566,			
				),
			);
			
			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			
			//var_dump($client);
			if ($client != 0){
				echo '
					<script src="js/init.js" type="text/javascript"></script>
					<script src="js/init2.js" type="text/javascript"></script>
					<div id="status">
						<header>
							<h2>
								Карточка пациента #'.$client[0]['id'].'';
				
				if (($clients['edit'] == 1) || $god_mode){
					if ($client[0]['status'] != 9){
						echo '
									<a href="client_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					if (($client[0]['status'] == 9) && (($clients['close'] == 1) || $god_mode)){
						echo '
							<a href="#" onclick="Ajax_reopen_client('.$_SESSION['id'].', '.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
					}
				}
				if (($clients['close'] == 1) || $god_mode){
					if ($client[0]['status'] != 9){
						echo '
									<a href="client_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
					}
				}

				echo '
							</h2>';
							
				if ($client[0]['status'] == 9){
					echo '<i style="color:red;">Пациент удалён (заблокирован).</i><br>';												
				}
				
				echo '
							Номер карты: '.$client[0]['card'].'
						</header>';
				echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">
						<div class="cellRight">
							<span style="font-size: 70%;">Быстрый поиск пациента</span><br />
							<input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите первые три буквы для поиска" value="" class="who_fc"  autocomplete="off">
							<!--<ul id="search_result_fc" class="search_result_fc"></ul><br />-->
							<div id="search_result_fc2"></div>
						</div>
					</div>';

				echo '
						<div id="data">';


				echo '

								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">'.$client[0]['full_name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">
										', $client[0]['birthday'] == '-1577934000' ? 'не указана' : date('d.m.Y', $client[0]['birthday']) ,'<br>
										полных лет <b>'.getyeardiff($client[0]['birthday']).'</b></div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">';
				if ($client[0]['sex'] != 0){
					if ($client[0]['sex'] == 1){
						echo 'М';
					}
					if ($client[0]['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo 
									'</div>
								</div>';
				
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Телефон</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">мобильный</span><br>
											'.$client[0]['telephone'].'
										</div>';
				if ($client[0]['htelephone'] != ''){
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">домашний</span><br>
											'.$client[0]['htelephone'].'
										</div>';
				}
				echo '
									</div>
								</div>';
								
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Паспорт</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер</span><br>
											'.$client[0]['passport'].'
										</div>';
				if (($client[0]['alienpassportser'] != NULL) && ($client[0]['alienpassportnom'] != NULL)){
					echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
											'.$client[0]['alienpassportser'].'
											'.$client[0]['alienpassportnom'].'
										</div>';
				}
				echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Выдан когда</span><br>
											'.$client[0]['passportvidandata'].'
										</div>
										<div>
											<span style="font-size: 70%; color: #AAA">Кем</span><br>
											'.$client[0]['passportvidankem'].'
										</div>
									</div>
								</div>';
								
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">
										'.$client[0]['address'].'
									</div>
								</div>';
				if ($client[0]['polis'] != ''){
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Полис</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Номер</span><br>
											'.$client[0]['polis'].'
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Дата</span><br>
											'.$client[0]['polisdata'].'
										</div>';
					if ($client[0]['insure'] == 0){
						$insure = 'не указана';
					}else{
						$insures_j = SelDataFromDB('spr_insure', $client[0]['insure'], 'offices');
						if ($insures_j == 0){
							$insure = 'ошибка';
						}else{
							$insure = $insures_j[0]['name'];
						}
					}
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>
											'.$insure.'
										</div>';
					echo '					
									</div>
								</div>';
				}

				if (($client[0]['fo'] != '') || ($client[0]['io'] != '')){
					echo '
							<div class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block;">
								<div class="cellLeft" style="font-weight: bold; width: 500px;">
									Опекун
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									'.$client[0]['fo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									'.$client[0]['io'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									'.$client[0]['oo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Телефон</div>
								<div class="cellRight">
									<div>
										<span style="font-size: 80%; color: #AAA">мобильный</span><br>
										'.$client[0]['telephoneo'].'
									</div>';
					if ($client[0]['htelephoneo'] != ''){
						echo '
									<div>
										<span style="font-size: 80%; color: #AAA">домашний</span><br>
										'.$client[0]['htelephoneo'].'
									</div>';
					}
					echo '
								</div>
							</div>';
				}
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">'.$client[0]['comment'].'</div>
								</div>';
								
				if (TRUE){
				echo '				
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">стоматология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client[0]['therapist'], 'user', true).'</div>
								</div>';
				}
				if (TRUE){
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">косметология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client[0]['therapist2'], 'user', true).'</div>
								</div>';
				}
								
				echo '
								<div class="cellsBlock2">
									<span style="font-size:80%;">';
				if (($client[0]['create_time'] != 0) || ($client[0]['create_person'] != 0)){
					echo '
										Добавлен: '.date('d.m.y H:i', $client[0]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client[0]['create_person'], 'user', true).'<br>';
				}else{
					echo 'Добавлен: не указано<br>';
				}
				if (($client[0]['last_edit_time'] != 0) || ($client[0]['last_edit_person'] != 0)){
					echo '
										Последний раз редактировался: '.date('d.m.y H:i', $client[0]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client[0]['last_edit_person'], 'user', true).'';
				}
				echo '
									</span>
								</div>';
				
				echo '
					<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
						<ul>
							<li><a href="#tabs-1">Посещения (запись)</a></li>
							<li><a href="#tabs-2">Счёт</a></li>
							<li><a href="#tabs-3">Стоматология</a></li>
							<li><a href="#tabs-4">Косметология</a></li>
						</ul>';
						
				echo '
						<div id="tabs-1">';
				
				//Запись пациента (aka посещения)
			
				echo '
					<div style="margin: 10px 0;">
						<ul style="margin-left: 6px; margin-bottom: 20px;">
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<span style="color: rgb(125, 125, 125);">Посещения (запись)</span>
							</li>';
				$sheduler_zapis = array();
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$query = "SELECT * FROM `zapis` WHERE `patient`='".$client[0]['id']."' ORDER BY `year`, `month`, `day`, `start_time` ASC";
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($sheduler_zapis, $arr);
					}
				}else
					$sheduler_zapis = 0;
				
				//var_dump ($sheduler_zapis);
				
				if ($sheduler_zapis != 0){
				
					$sheduler_zapis = array_reverse($sheduler_zapis);
					
					for ($z = 0; $z < count($sheduler_zapis); $z++){
						$t_f_data_db = array();
						$show_this = FALSE;
						
						if ($sheduler_zapis[$z]['type'] == 5){
							if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
								$show_this = TRUE;
							}
						}elseif ($sheduler_zapis[$z]['type'] == 6){
							if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
								$show_this = TRUE;
							}
						}
						
						if ($show_this){
							$back_color = '';
						
							if ($sheduler_zapis[$z]['enter'] == 1){
								$back_color = 'background-color: rgba(119, 255, 135, 1);';
							}elseif($sheduler_zapis[$z]['enter'] == 9){
								$back_color = 'background-color: rgba(239,47,55, .7);';
							}elseif($sheduler_zapis[$z]['enter'] == 8){
								$back_color = 'background-color: rgba(137,0,81, .7);';
							}else{
								//Если оформлено не на этом филиале
								if($sheduler_zapis[$z]['office'] != $sheduler_zapis[$z]['add_from']){
									$back_color = 'background-color: rgb(119, 255, 250);';
								}else{
									$back_color = 'background-color: rgba(255,255,0, .5);';
								}
							}
									
							$dop_img = '';
									
							if ($sheduler_zapis[$z]['insured'] == 1){
								$dop_img .= '<img src="img/insured.png" title="Страховое"> ';
							}
							if ($sheduler_zapis[$z]['pervich'] == 1){
								$dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
							}
							if ($sheduler_zapis[$z]['noch'] == 1){
								$dop_img .= '<img src="img/night.png" title="Ночное"> ';
							}
									
							echo '
								<li class="cellsBlock" style="width: auto;">
									<!--<div class="cellCosmAct">-->';
							
							$query = "SELECT `id`, `zapis_date`  FROM `journal_tooth_status` WHERE `zapis_id` = '{$sheduler_zapis[$z]['id']}' ORDER BY `create_time`";
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);	
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
									array_push($t_f_data_db, $arr);
								}
							}else
								$t_f_data_db = 0;
							//var_dump($t_f_data_db);
							
							if ($t_f_data_db != 0){
								foreach($t_f_data_db as $ids){
									/*echo '
										<div>
											<a href="#" onclick="window.open(\'task_stomat_inspection_window.php?id='.$ids['id'].'\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;">
												<img src="img/tooth_state/1.png">
											</a>	
										</div>';*/
										
									/*echo '
										<div>
											<a href="task_stomat_inspection.php?id='.$ids['id'].'">
												<img src="img/tooth_state/1.png">
											</a>	
										</div>';*/
								}
							}
							echo '
									<!--</div>-->
									<div class="cellName" style="position: relative; '.$back_color.'">';
							$start_time_h = floor($sheduler_zapis[$z]['start_time']/60);
							$start_time_m = $sheduler_zapis[$z]['start_time']%60;
							if ($start_time_m < 10) $start_time_m = '0'.$start_time_m;
							$end_time_h = floor(($sheduler_zapis[$z]['start_time']+$sheduler_zapis[$z]['wt'])/60);
							if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
							$end_time_m = ($sheduler_zapis[$z]['start_time']+$sheduler_zapis[$z]['wt'])%60;
							if ($end_time_m < 10) $end_time_m = '0'.$end_time_m;
							
							if ($sheduler_zapis[$z]['month'] < 10) $month = '0'.$sheduler_zapis[$z]['month'];
							else $month = $sheduler_zapis[$z]['month'];
							
							echo 
								'<b>'.$sheduler_zapis[$z]['day'].' '.$monthsName[$month].' '.$sheduler_zapis[$z]['year'].'</b><br>'.
								$start_time_h.':'.$start_time_m.' - '.$end_time_h.':'.$end_time_m;
												
							echo '
										<div style="position: absolute; top: 1px; right: 1px;">'.$dop_img.'</div>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Пациент <br /><b>'.WriteSearchUser('spr_clients', $sheduler_zapis[$z]['patient'], 'user', true).'</b>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							
							$offices = SelDataFromDB('spr_office', $sheduler_zapis[$z]['office'], 'offices');
							echo 
										'Филиал:<br>'.
										$offices[0]['name'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										$sheduler_zapis[$z]['kab'].' кабинет<br>'.'Врач: <br><b>'.WriteSearchUser('spr_workers', $sheduler_zapis[$z]['worker'], 'user', true).'</b>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Описание:<br>'.
										$sheduler_zapis[$z]['description'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo '
										Добавлено<br>'.date('d.m.y H:i', $sheduler_zapis[$z]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $sheduler_zapis[$z]['create_person'], 'user', true);
							if (($sheduler_zapis[$z]['last_edit_time'] != 0) || ($sheduler_zapis[$z]['last_edit_person'] != 0)){
								echo '<hr>
										Изменено: '.date('d.m.y H:i', $sheduler_zapis[$z]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $sheduler_zapis[$z]['last_edit_person'], 'user', true).'';
							}
							echo '
									</div>';
									
									
							//Формулы и посещения
							echo '
									<div class="cellName" style="vertical-align: top;">';
									
							if ($t_f_data_db != 0){
								foreach($t_f_data_db as $ids){
									echo '
										<div style="border: 1px solid #BFBCB5; margin-top: 1px;">
											<a href="task_stomat_inspection.php?id='.$ids['id'].'" class="ahref">
												<div style="display: inline-block; vertical-align: middle;"><img src="img/tooth_state/1.png"></div><div style="display: inline-block; vertical-align: middle;">'.date('d.m.y H:i', $ids['zapis_date']).'</div>
											</a>	
										</div>';
								}
							}

							echo '
									</div>';

							if (($_SESSION['id'] == $sheduler_zapis[$z]['worker']) || ($god_mode)){
								echo '
									<div class="cellName" style="background-color: #d8d8d8; vertical-align: top;">';
								if($sheduler_zapis[$z]['office'] == $sheduler_zapis[$z]['add_from']){
									if($sheduler_zapis[$z]['enter'] == 1){
										if(($_SESSION['permissions'] == 5) || $god_mode){
											echo 
												'<div style="border: 1px solid #BFBCB5; margin-top: 1px; padding: 2px;">
													<a href="add_task_stomat.php?client='.$sheduler_zapis[$z]['patient'].'&filial='.$sheduler_zapis[$z]['office'].'&insured='.$sheduler_zapis[$z]['insured'].'&pervich='.$sheduler_zapis[$z]['pervich'].'&noch='.$sheduler_zapis[$z]['noch'].'&date='.strtotime ($sheduler_zapis[$z]['day'].'.'.$month.'.'.$sheduler_zapis[$z]['year'].' '.$start_time_h.':'.$start_time_m).'&id='.$sheduler_zapis[$z]['id'].'&worker='.$sheduler_zapis[$z]['worker'].'" class="ahref">Внести Осмотр/Зубную формулу</a>
												</div>';
										}
										if(($_SESSION['permissions'] == 6) || $god_mode){
											echo 
												'<div style="border: 1px solid #BFBCB5; margin-top: 1px; padding: 2px;">
													<a href="add_task_cosmet.php?client='.$sheduler_zapis[$z]['patient'].'" class="ahref">Внести посещение косм.</a>
												</div>';
										}
										$zapisDate = strtotime($sheduler_zapis[$z]['day'].'.'.$sheduler_zapis[$z]['month'].'.'.$sheduler_zapis[$z]['year']);
										if (time() < $zapisDate + 60*60*24){
											/*echo 
												'<a href="#">Внести Акт</a><br />';*/
										}
									}
								}else{
									echo "&nbsp";
								}
								echo '
									</div>';
							}
			
							echo '
								</li>';
						}

					}
				}
				echo '</ul>
					</div>';
					
				echo '</div>';

				if ($client[0]['status'] != 9){
					
					echo '
						<div id="tabs-2">';	
					
					if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode){
						echo '				
									<div class="cellsBlock2">
										<a href="client_finance.php?client='.$client[0]['id'].'" class="b">Счёт <i class="fa fa-rub"></i></a> ';
						//Долги/авансы					
						$clientDP = DebtsPrepayments ($client[0]['id']);
						//var_dump ($clientDP);
						$allPayed = true;					
						
						if ($clientDP != 0){
							for ($i=0; $i<count($clientDP); $i++){
								$repayments = Repayments($clientDP[$i]['id']);
								if ($repayments != 0){
									$ostatok = 0;
									foreach($repayments as $value){
										$ostatok += $value['summ'];
									}
									if ($clientDP[$i]['summ'] - $ostatok == 0){
										//echo '<i>ЗАКРЫТО</i>';
									}else{
										$allPayed = false;
									}
								}
									
							}
						}
						if (!$allPayed)
							echo '<i style="color:red;">Есть не погашенное</i>';					
										
						echo '</div>';
					}
					
					echo '</div>';
					
					/*echo '				
									<div class="cellsBlock2">';
					/*if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '
										<a href="#" id="showDiv1" class="b">Стоматология</a>';
					}
					if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
						echo '
										<a href="#" id="showDiv2" class="b">Косметология</a>';
					}
					echo '
									</div>';*/
		
					echo '
						<div id="tabs-3">';	
					/*echo '
									<div id="div1">';*/
					if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || ($god_mode)){
						echo '	
										<a href="add_task_stomat.php?client='.$client[0]['id'].'" class="b">Добавить осмотр</a>';
					}
					
					if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '	
										<a href="stom_history.php?client='.$client[0]['id'].'" class="b">История</a>';
					}
					if (($clients['close'] == 1) || $god_mode){
						echo '
						<a href="stom_move.php?id='.$client[0]['id'].'" class="b">Переместить</a>';
					}
					
						
						
					//Выберем из базы последнюю запись
					$t_f_data_db = array();
					
					/*require 'config.php';*/
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` DESC LIMIT 1";
					$res = mysql_query($query) or die($q);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($t_f_data_db, $arr);
						}
					}else
						$t_f_data_db = 0;
					
					
					if ($t_f_data_db != 0){
						//var_dump ($t_f_data_db);
						

						
						
						//echo '							<script src="js/init.js" type="text/javascript"></script>';
						//Выберем из базы первую запись
						$t_f_data_db_first = array();
						
						/*require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$time = time();
						$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` ASC LIMIT 1";
						$res = mysql_query($query) or die($q);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($t_f_data_db_first, $arr);
							}
						}else
							$t_f_data_db_first = 0;
						mysql_close();*/
						
						//$t_f_data_db = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');								
						//var_dump ($t_f_data_db);
						//var_dump ($t_f_data_db_first);
						/*if ($t_f_data_db_first !=0){
							if ($t_f_data_db_first[0]['id'] != $t_f_data_db[0]['id']){
								$t_f_data_db[count($t_f_data_db)] = $t_f_data_db_first[0];
							}
						}*/
								
						for ($z = 0; $z < count ($t_f_data_db); $z++){
							$dop = array();
							
							
							//ЗО и тд
							$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$t_f_data_db[$z]['id']}'";
							$res = mysql_query($query) or die($query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
									array_push($dop, $arr);
								}
								
							}
							
							echo '
								<div class="cellsBlock3">';
							echo '
									<div class="cellLeft">
										<a href="task_stomat_inspection.php?id='.$t_f_data_db[$z]['id'].'" class="ahref">'.date('d.m.y H:i', $t_f_data_db[$z]['create_time']).'</a>
									</div>
									<div class="cellRight">';
									
							include_once 'teeth_map_db.php';
							include_once 't_surface_name.php';
							include_once 't_surface_status.php';

							include_once 'root_status.php';
							include_once 'surface_status.php';
							include_once 't_context_menu.php';
										
							$t_f_data = array();
							
							if ($z == 0){
								$n = '';
							}else{
								$n = $z;
							}
							
							$sw = 0;
							$stat_id = $t_f_data_db[$z]['id'];
							
							unset($t_f_data_db[$z]['id']);
							unset($t_f_data_db[$z]['create_time']);
							//echo "echo$sw";
							//var_dump ($surfaces);
							$t_f_data_temp_refresh = '';
							
							unset($t_f_data_db[$z]['id']);
							unset($t_f_data_db[$z]['office']);
							unset($t_f_data_db[$z]['client']);
							unset($t_f_data_db[$z]['create_time']);
							unset($t_f_data_db[$z]['create_person']);
							unset($t_f_data_db[$z]['last_edit_time']);
							unset($t_f_data_db[$z]['last_edit_person']);
							unset($t_f_data_db[$z]['worker']);
							unset($t_f_data_db[$z]['comment']);
							unset($t_f_data_db[$z]['zapis_date']);
							unset($t_f_data_db[$z]['zapis_id']);
							
							foreach ($t_f_data_db[$z] as $key => $value){
								//$t_f_data_temp_refresh .= $key.'+'.$value.':';
								
								
								//var_dump(json_decode($value, true));
								$surfaces_temp = explode(',', $value);
								//var_dump ($surfaces_temp);
								foreach ($surfaces_temp as $key1 => $value1){
									//$t_f_data[$key] = json_decode($value, true);
									///!!!Еба костыль
									if ($key1 < 13){
										$t_f_data[$key][$surfaces[$key1]] = $value1;
									}
								}
							}
							
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
						
							//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
							//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
							//var_dump($t_f_data);
							//echo $t_f_data_temp_refresh;
							
							
							echo '
										<div class="map'.$n.'" id="map'.$n.'">
											<div class="text_in_map" style="left: 15px">8</div>
											<div class="text_in_map" style="left: 52px">7</div>
											<div class="text_in_map" style="left: 87px">6</div>
											<div class="text_in_map" style="left: 123px">5</div>
											<div class="text_in_map" style="left: 159px">4</div>
											<div class="text_in_map" style="left: 196px">3</div>
											<div class="text_in_map" style="left: 231px">2</div>
											<div class="text_in_map" style="left: 268px">1</div>
											
											<div class="text_in_map" style="left: 321px">1</div>
											<div class="text_in_map" style="left: 360px">2</div>
											<div class="text_in_map" style="left: 396px">3</div>
											<div class="text_in_map" style="left: 432px">4</div>
											<div class="text_in_map" style="left: 469px">5</div>
											<div class="text_in_map" style="left: 505px">6</div>
											<div class="text_in_map" style="left: 539px">7</div>
											<div class="text_in_map" style="left: 576px">8</div>
											';

							

							//var_dump ($teeth_map_temp);	
							
							//!!!ТЕСТ ИНКЛУДА ОТРИСОВКИ ЗФ
							//require_once 'for32_teeth_map_svg.php';
							
							
							//$teeth_map_temp = SelDataFromDB('teeth_map', '', '');
							$teeth_map_temp = $teeth_map_db;
							foreach ($teeth_map_temp as $value){
								$teeth_map[mb_substr($value['tooth'], 0, 3)][mb_substr($value['tooth'], 3, strlen($value['tooth'])-3)]=$value['coord'];
							}
							//$teeth_map_d_temp = SelDataFromDB('teeth_map_d', '', '');
							$teeth_map_d_temp = $teeth_map_d_db;
							foreach ($teeth_map_d_temp as $value){
								$teeth_map_d[$value['tooth']]=$value['coord'];
							}
							//$teeth_map_pin_temp = SelDataFromDB('teeth_map_pin', '', '');
							$teeth_map_pin_temp = $teeth_map_pin_db;
							foreach ($teeth_map_pin_temp as $value){
								$teeth_map_pin[$value['tooth']]=$value['coord'];
							}
							
							for ($i=1; $i <= 4; $i++){
								for($j=1; $j <= 8; $j++){
									
									$DrawRoots = TRUE;				
									$menu = 't_menu';
									if (isset($sw)){
										if ($sw == '1'){
											$t_status = 'yes';
										}else{
											$t_status = 'no';
										}
									}else{
										$t_status = 'yes';
									}
									//$t_status = 'yes';
									$color = "#fff";
									$color_stroke = '#74675C';
									$stroke_width = 1;
									$n_zuba = 't'.$i.$j;
									//echo $n_zuba.'<br />';
									if ($t_f_data[$i.$j]['alien'] == '1'){
										$color_stroke = '#F7273F';
										$stroke_width = 3;
									}
									
									foreach($teeth_map[$n_zuba] as $surface => $coordinates){
										
										$color = "#fff";
										//!!!! попытка с молочным зубом
										if ($t_f_data[$i.$j]['status'] == '19'){
											$color_stroke = '#FF9900';
										}
										$DrawMenu = TRUE;
										if (isset($t_f_data[$i.$j][$surface])){
										$s_stat = $t_f_data[$i.$j][$surface];
										}
										//!!! надо как-то получать статус в строку, чтоб писать в описании
										//t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
										
										if ($t_f_data[$i.$j]['status'] == '3'){
											//штифт
											$surface = 'NONE';
											$color = "#9393FF";
											$color_stroke = '#5353FF';
											$coordinates = $teeth_map_pin[$n_zuba];
											$stroke_width = 1;
																
											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path'.$n.'="'.$coordinates.'"
													fill-color'.$n.'=\'"fill": "'.$color.'"\'
													t_menu'.$n.' = "
															<div class=\'cellsBlock4\'>
																<div class=\'cellLeft\'>
																	'.t_surface_name($n_zuba.$surface, 2).'<br />';
													
											DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
											
											echo '
																</div>
															</div>';
											echo					
													'"
													t_menuA'.$n.' = "
															'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
													
											//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
													
											echo					
													'"
												>
												</div>
											';
										}else{
										
										
											//Если надо рисовать корень, но в бд написано, что тут имплант
											if (($t_f_data[$i.$j]['pin'] == '1') && (mb_strstr($surface, 'root') != FALSE)){
												$DrawRoots = FALSE;
											}else{
												if  ((mb_strstr($surface, 'root') == TRUE) && 
													(($t_f_data[$i.$j]['status'] == '1') || ($t_f_data[$i.$j]['status'] == '2') || 
													($t_f_data[$i.$j]['status'] == '18') || ($t_f_data[$i.$j]['status'] == '19') || 
													($t_f_data[$i.$j]['status'] == '9'))){
													$DrawRoots = FALSE;
												}else{
													if (isset($t_f_data[$i.$j][$surface])){
														//echo $i.$j.'<br />';
														//var_dump ($t_f_data[$i.$j][$surface]);
														if  ((mb_strstr($surface, 'root') == TRUE) && ($t_f_data[$i.$j][$surface] != '0')){
															$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
														}
														$DrawRoots = TRUE;
													}
												}
											}
											//!!!!учим рисовать корни с коронками - начало  - кажется, это все говно. надо иначе
											/*if ($t_f_data[$i.$j]['status'] == '19'){
												$DrawRoots = TRUE;
											}*/
											if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
												//Если в массиве натыкаемся не на корни или если чужой, то корни не рисуем, а рисум кружок просто
												if ((($surface != 'root1') && ($surface != 'root2') && ($surface != 'root3')) || ($t_f_data[$i.$j]['alien'] == '1')){
													//без корней + коронки и всякая херня
													$surface = 'NONE';
													$color = $tooth_status[$t_f_data[$i.$j]['status']]['color'];
													$coordinates = $teeth_map_d[$n_zuba];								
												}
											}else{
												//Если у какой-то из областей зуба есть статус в бд.
												if (isset($t_f_data[$i.$j][$surface])){
												if ($t_f_data[$i.$j][$surface] != '0'){
													if (array_key_exists($t_f_data[$i.$j][$surface], $root_status)){
														$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
													}elseif(array_key_exists($t_f_data[$i.$j][$surface], $surface_status)){
														$color = $surface_status[$t_f_data[$i.$j][$surface]]['color'];
													}else{
														$color = "#fff";
													}
												}
												}
											}
											
											
							//!Костыль для радикса(корень)/статус 34
							if ((($t_f_data[$i.$j]['root1'] == '34') || ($t_f_data[$i.$j]['root2'] == '34') || ($t_f_data[$i.$j]['root3'] == '34')) && 
									(($t_f_data[$i.$j]['status'] != '1') && ($t_f_data[$i.$j]['status'] != '2') && 
									($t_f_data[$i.$j]['status'] != '18') && ($t_f_data[$i.$j]['status'] != '19') &&
									($t_f_data[$i.$j]['status'] != '9')))
							{
												$surface = 'NONE';
												$color = '#FF0000';
												$coordinates = $teeth_map_d[$n_zuba];								
											}
											
											
											if (mb_strstr($surface, 'root') != FALSE){
												$menu = 'r_menu';
											}elseif((mb_strstr($surface, 'surface') != FALSE) || (mb_strstr($surface, 'top') != FALSE)){
												$menu = 's_menu';
											}else{
												$DrawMenu = FALSE;
											}
											
											if ($DrawRoots){
												echo '
													<div id="'.$n_zuba.$surface.'"
														status-path=\'
														"stroke": "'.$color_stroke.'", 
														"stroke-width": '.$stroke_width.', 
														"fill-opacity": "1"\' 
														class="mapArea'.$n.'" 
														t_status = "'.$t_status.'"
														data-path'.$n.'="'.$coordinates.'"
														fill-color'.$n.'=\'"fill": "'.$color.'"\'
														t_menu'.$n.' = "
															<div class=\'cellsBlock4\'>
																<div class=\'cellLeft\'>
																	'.t_surface_name($n_zuba.'NONE', 1).'<br />';
															
												DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
														echo '
																</div>
																<div class=\'cellRight\'>
																	'.t_surface_name($n_zuba.$surface, 0).'<br />';
												if ($DrawMenu){ DrawTeethMapMenu($key, $n_zuba, $surface, $menu);}	
												echo '
																</div>
															</div>';	
												echo
														'"
														t_menuA'.$n.' = "
															'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
														
												//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
												
												echo					
														'"
														>
														</div>
														';
											}
										}
									}
									
									if ($t_f_data[$i.$j]['pin'] == '1'){
										//штифт
										$surface = 'NONE';
										$color = "#9393FF";
										$color_stroke = '#5353FF';
										$coordinates = $teeth_map_pin[$n_zuba];
										$stroke_width = 1;
										if ($t_f_data[$i.$j]['alien'] == '1'){
											$color_stroke = '#F7273F';
											$stroke_width = 3;
										}				
										echo '
											<div id="'.$n_zuba.$surface.'"
												status-path=\'
												"stroke": "'.$color_stroke.'", 
												"stroke-width": '.$stroke_width.', 
												"fill-opacity": "1"\' 
												class="mapArea'.$n.'" 
												t_status = "'.$t_status.'"
												data-path'.$n.'="'.$coordinates.'"
												fill-color'.$n.'=\'"fill": "'.$color.'"\'
												t_menu'.$n.' = "
													<div class=\'cellsBlock4\'>
														<div class=\'cellLeft\'>
															'.t_surface_name($n_zuba.$surface, 2).'<br />';
												
										DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
										echo '
														</div>
													</div>';
										echo					
												'"
												t_menuA'.$n.' = "
															'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
												
										//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
										
										echo					
												'"
												>
												</div>
												';
									}
									
									
									//Для ЗО и дополнительно
									if (isset($t_f_data[$i.$j]['zo'])){
										$surface = 'NONE';
										if ($t_f_data[$i.$j]['zo'] == '1'){
											$color = "#FF0000";
										}else{
											$color = "#FFF";
										}
										$color_stroke = '#5353FF';
										$coordinates = $teeth_map_zo_db[$i.$j];
										$stroke_width = 1;
									
										echo '
											<div id="'.$n_zuba.$surface.'"
												status-path=\'
												"stroke": "'.$color_stroke.'", 
												"stroke-width": '.$stroke_width.', 
												"fill-opacity": "1"\' 
												class="mapArea'.$n.'" 
												t_status = "'.$t_status.'"
												data-path="'.$coordinates.'"
												fill-color=\'"fill": "'.$color.'"\'
												t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
										echo					
													'
												t_menuA = "
															'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
									
										echo					
												'"
												>
												</div>
												';
									}
									
									$text_status_div = '';
									$text_status_div_shinir = '';
									$text_status_div_podvizh = '';
									$text_status_div_retein = '';
									$text_status_div_skomplect = '';
																	
									//Для Шинирования и дополнительно
									if (isset($t_f_data[$i.$j]['shinir'])){
										$text_status_div_shinir = 'ш';
										if ($i == 1){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										if ($i == 2){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 3){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 4){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['left'][$j];
										}
									}
									//Для Подвижности и дополнительно
									if (isset($t_f_data[$i.$j]['podvizh'])){
										$text_status_div_podvizh = 'A';
										if ($i == 1){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										if ($i == 2){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 3){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 4){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										$text_status_div .= '
											<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
									}
									//Для Ретейнер и дополнительно
									if (isset($t_f_data[$i.$j]['retein'])){
										$text_status_div_retein = 'р';
										if ($i == 1){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										if ($i == 2){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 3){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 4){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										$text_status_div .= '
											<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
									}
									//Для Сверхкомплекта и дополнительно
									if (isset($t_f_data[$i.$j]['skomplect'])){
										$text_status_div_skomplect = 'c';
										if ($i == 1){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										if ($i == 2){
											$top_tts = $text_tooth_status['up'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 3){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['right'][$j];
										}
										if ($i == 4){
											$top_tts = $text_tooth_status['down'];
											$left_tts = $text_tooth_status['left'][$j];
										}
										$text_status_div .= '
											<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
									}
									if ((isset($t_f_data[$i.$j]['shinir'])) || (isset($t_f_data[$i.$j]['podvizh'])) || (isset($t_f_data[$i.$j]['retein'])) || (isset($t_f_data[$i.$j]['skomplect']))){
										echo '<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">'.$text_status_div_shinir.''.$text_status_div_podvizh.''.$text_status_div_retein.''.$text_status_div_skomplect.'</div>';
									}
									
								}
							}
							
							
							
							
							echo '
									</div>
								</div>
							</div>';
							echo '
							<div class="cellsBlock3" style="font-size:80%;">
								<div class="cellLeft">';

							//$decription = $t_f_data_db[$z];

							/*$t_f_data = array();
				
							//собрали массив с зубами и статусами по поверхностям
							foreach ($decription as $key => $value){
								$surfaces_temp = explode(',', $value);
								foreach ($surfaces_temp as $key1 => $value1){
									$t_f_data[$key][$surfaces[$key1]] = $value1;
								}
							}
							
							unset($t_f_data['id']);
							unset($t_f_data['office']);
							unset($t_f_data['client']);
							unset($t_f_data['create_time']);
							unset($t_f_data['create_person']);
							unset($t_f_data['last_edit_time']);
							unset($t_f_data['last_edit_person']);
							unset($t_f_data['worker']);
							unset($t_f_data['comment']);
							
							//var_dump ($t_f_data);			
				*/
							$descr_rez = '';
							echo '<div><a href="#open1" onclick="show(\'hidden_'.$z.'\',200,5)">Подробно</a></div>';	
							echo '<div id=hidden_'.$z.' style="display:none;">';		
							foreach($t_f_data as $key => $value){
								//var_dump ($value);
								foreach ($value as $key1 => $value1){
									
									if ($key1 == 'status'){
										//var_dump ($value1);	
										if ($value1 != 0){
											//$descr_rez .= 
											echo t_surface_name('t'.$key.'NONE', 1).' '.t_surface_status($value1, 0).'';
										}
									}elseif($key1 == 'pin'){
										if ($value1 != 0){
											echo t_surface_status(3, 0);
										}
									}elseif($key1 == 'alien'){
										
									}elseif($key1 == 'zo'){
										
									}else{
										if ($value1 != 0){
											echo t_surface_name('t'.$key.$key1, 1).' '.t_surface_status(0, $value1);
										}
									}
								}
					
							}
							echo '</div>';				
									
							echo '					
									</div>
								</div>';
										
						}
						
						$notes = SelDataFromDB ('notes', $client[0]['id'], 'client');
						include_once 'WriteNotes.php';
						echo WriteNotes($notes);
						
						$removes = SelDataFromDB ('removes', $client[0]['id'], 'client');
						include_once 'WriteRemoves.php';
						echo WriteRemoves($removes);
						
					}else{
						echo '
								<div class="cellsBlock3">
									<div class="cellLeft">
										Не было посещений стоматолога
									</div>
								</div>';
					}
						
				
					mysql_close();	
					
					echo '
						</div>';
					echo '</div>';	
					
					echo '
						<div id="tabs-4">';
				
					
					/*echo '
						<div id="div2">';*/
						
					if (($cosm['add_own'] == 1) || ($cosm['see_all'] == 1) || ($god_mode)){
						echo '
							<a href="add_task_cosmet.php?client='.$client[0]['id'].'" class="b">Добавить посещение</a>		
							<a href="add_kd.php?client='.$client[0]['id'].'" class="b">Добавить КД</a>
							<a href="kd.php?client='.$client[0]['id'].'" class="b">КД</a>
							<a href="etaps.php?client='.$client[0]['id'].'" class="b">Исследования</a>';		
						if (($clients['close'] == 1) || $god_mode){
							echo '
							<a href="cosm_move.php?id='.$client[0]['id'].'" class="b">Переместить</a>';
						}
					}				
				$cosmet_task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'client_cosm_id');
				//var_dump ($cosmet_task);
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
				
				if ($cosmet_task != 0){
					for ($i=0; $i < count($cosmet_task); $i++){
						//!а если нет офиса или работника??
						$worker = SelDataFromDB('spr_workers', $cosmet_task[$i]['worker'], 'worker_id');
						$offices = SelDataFromDB('spr_office', $cosmet_task[$i]['office'], 'offices');
						echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									<a href="task_cosmet.php?id='.$cosmet_task[$i]['id'].'" class="ahref">
										'.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'
										<br />
										'.$worker[0]['name'].'
										<br />
										'.$offices[0]['name'].'
									</a>
								</div>';
						
						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						
						/*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
						foreach($cosmet_task[$i] as $key => $value){
							if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
								$decription_temp_arr[mb_substr($key, 1)] = $value;
							}
						}
							
							//var_dump ($decription_temp_arr);
							
							$decription = $decription_temp_arr;

						
						
						
						
						
						
						/*$decription = array();
						$decription = json_decode($cosmet_task[$i]['description'], true);
						var_dump ($actions_cosmet);	*/	
						
						echo '<div class="cellLeft">';
						
						for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
							$action = '';
							if (isset($decription[$j])){
								if ($decription[$j] != 0){
									$action = '<div style="margin: 2px; border: 1px solid #CCC; padding-left: 3px; background-color: '.$actions_cosmet[$j-1]['color'].'">'.$actions_cosmet[$j-1]['full_name'].'</div>';
								}else{
									$action = '';
								}
								echo $action;
							}else{
								echo '';
							}
						}
						
						echo '
								</div>
								<div class="cellRight">';
						//!!!!!!if ($SESION_ID == )
						echo $cosmet_task[$i]['comment'];
						echo '
								</div>
							</div>';
						
						//echo ''.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'<br />';
					}
				}else{
						echo '
								<div class="cellsBlock3">
									<div class="cellLeft">
										Не было посещений косметолога
									</div>
								</div>';
				}
				echo '
					</div>
					</div>';
			}
								
								
								
			echo '					
					
				</div>
			</div>


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
					
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>