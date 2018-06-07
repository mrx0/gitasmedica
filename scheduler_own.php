<?php

//scheduler_own.php
//Расписание врачей

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        require 'variables.php';

		//var_dump ($_GET);
		
		//$get_link = '';
		
		//Если есть GET
		/*if ($_GET){
			foreach ($_GET as $key => $value){
				$get_link .= '&'.$key.'='.$value;
			}
		}*/
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			if (isset($_GET['id'])){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'widget_calendar.php';
				
				$kabsInFilialExist = FALSE;
				$kabsInFilial = array();
				$dop = '';
				$di = 0;
				
				//Массив с месяцами
				/*$monthsName = array(
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
				);*/
				
				//Массив с днями недели
				$dayWarr = array(
					1 => 'ПН',
					2 => 'ВТ',
					3 => 'СР',
					4 => 'ЧТ',
					5 => 'ПТ',
					6 => 'СБ',
					7 => 'ВС',
				);
				
				$worker = SelDataFromDB('spr_workers', $_GET['id'], 'user');
				if ($worker != 0){
					$offices_j = SelDataFromDB('spr_filials', '', '');
					//var_dump ($offices_j);
					
					$offices_jarr = array();
					
					foreach ($offices_j as $filial_val){
						$offices_jarr[$filial_val['id']] = $filial_val;
					}
					//var_dump ($offices_jarr);
					
					$weekDays = '
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПН</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВТ</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СР</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ЧТ</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПТ</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СБ</b></div>
								<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВС</b></div>';
					
					//получаем шаблон графика из базы
					$query = "SELECT `filial`, `day`, `smena`, `kab`, `worker` FROM `sheduler_template` WHERE `worker` = '{$_GET['id']}'";
					
					$shedTemplate = array();

                    $msql_cnnct = ConnectToDB ();
					
					$arr = array();
					$rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							$rez[$arr['day']][$arr['smena']] = $arr;
						}
						$shedTemplate = $rez;
					}
					//var_dump($shedTemplate);
					
					//есть ли кабинеты в филиале
					$kabsInFilialExist = FALSE;
					//какие есть кабинеты в филиале
					$kabsInFilial = array();
					
					echo '
					<div id="status">
						<header style="margin-bottom: 5px;">
							<h2>График работы '.$worker[0]['name'].'</h2>
						</header>
						<a href="scheduler.php" class="b">График </a>';
					
					echo '
						<div id="data">';
					echo '
						<ul style="margin-left: 6px; margin-bottom: 10px;">
							<li style="width: auto; color:#777; font-size: 70%;">
								Примечание к графику:
								<ul>
									<li>1 смена 9:00 - 15:00</li>
									<li>2 смена 15:00 - 21:00</li>
									<li>3 смена 21:00 - 3:00</li>
									<li>4 смена 3:00 - 9:00</li>
								</ul>
							</li>
						</ul>';
						
					echo '
						<ul style="margin-left: 6px; margin-bottom: 10px;">
							<li style="width: auto; color:#777; font-size: 90%;">
								Плановый график (может не совпадать с фактическим)
							</li>
						</ul>
						<div style="margin-bottom: 20px;">
							<div class="cellsBlock">';
					echo $weekDays;
					echo '
							</div>';

					echo '
							<div class="cellsBlock">';
									
					for ($dayW = 1; $dayW <= 7; $dayW++) {
						$BgColor = ' background-color: rgba(81, 249, 89, 0.47);';
						echo '
							<div class="cellTime" style="padding: 0; text-align: center; background-color: #FEFEFE; width: 150px; min-width: 125px; max-width: 150px; vertical-align: top;">';
						//номера смен 1 - день 2- вечер 3 - ночь 4 - утро
						for ($smenaN = 1; $smenaN <= 4; $smenaN++) {
							echo '
								<div style="display: table; margin-bottom: 3px; height: 40px;">
									<div style="vertical-align: middle; width: 5px; box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2); display: table-cell !important;">
										'.$smenaN.'
									</div>';

							//переменная для вывода
							$resEcho2 = '';
							
							if (isset($shedTemplate[$dayW][$smenaN])){
								//var_dump($shedTemplate[$dayW][$smenaN]);
								
								

							
								$resEcho2 .= '
									<div style="box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);">
										<div style="text-align: right; color: #555;">
											<b>каб. '.$shedTemplate[$dayW][$smenaN]['kab'].'</b>
										</div>
										<div style="text-align: left;  padding: 4px;">'.$offices_jarr[$shedTemplate[$dayW][$smenaN]['filial']]['name'].'';

								$resEcho2 .= '
										</div>
									</div>';
								$BgColor = ' background-color: rgba(81, 249, 89, 0.47);';
							}else{
								$BgColor = ' background-color: rgba(220, 220, 220, 0.5);';
							}
							
							echo '
									<div style="text-align: middle; display: table-cell !important; width: 100%;'.$BgColor.'">';
							echo $resEcho2;
							echo '		
									</div>
									
									
								</div>';
						}
						
						echo '
							</div>';
					}
					echo '
							</div>
						</div>';
		
		
		
					//График Факт
					echo '
						<ul style="margin-left: 6px; margin-bottom: 10px;">
							<li style="width: auto; color:#777; font-size: 90%;">
								Фактический график работы
							</li>
						</ul>';
					$kabsForDoctor = 'stom';	
					
					foreach ($_GET as $key => $value){
						if (($key != 'm') && ($key != 'y'))
							$dop .= '&'.$key.'='.$value;
					}
					//var_dump ($dop);
					
					if (isset($_GET['m']) && isset($_GET['y'])){
						//операции со временем						
						$month = $_GET['m'];
						$year = $_GET['y'];
					}else{
						//операции со временем						
						$month = date('m');		
						$year = date('Y');
					}
					
					$month_stamp = mktime(0, 0, 0, $month, 1, $year);

					$day_count = date("t", $month_stamp);
					//var_dump($day_count);
					
					$weekday = date("w", $month_stamp);
					if ($weekday == 0){
						$weekday = 7;
					}
					$start = -($weekday-2);
					//var_dump($start);
					
					$last = ($day_count + $weekday - 1) % 7;
					//var_dump($last);
					
					if ($last == 0){
						$end = $day_count; 
					}else{
						$end = $day_count + 7 - $last;
					}
					$today = date("Y-m-d");
					
					//!!!!
					if(isset($_GET['filial'])){
						if ($_GET['filial'] == 0) $_GET['filial'] = 15;
						$selected_fil = $_GET['filial'];
					}
					
					if (!isset($_GET['filial'])){
						//Филиал	
						if (isset($_SESSION['filial'])){
							$_GET['filial'] = $_SESSION['filial'];
						}else{
							$_GET['filial'] = 15;
						}
					}
						
					$filial = SelDataFromDB('spr_filials', $_GET['filial'], 'offices');
					//var_dump($filial['name']);
					
					//Получаем график факт
					$query = "SELECT `id`, `day`, `smena`, `filial`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$_GET['id']}' AND `month` = '$month' AND `year` = '$year'";
					
					$markSheduler = 0;
					
					//$msql_cnnct = ConnectToDB ();
					
					$arr = array();
					$rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							//Раскидываем в массив
							$rez[$arr['day']][$arr['smena']][$arr['kab']] = $arr;
						}
						$markSheduler = 1;
					}else{
						$rez = 0;
					}
					//var_dump($rez);

                    CloseDB ($msql_cnnct);
					
					$schedulerFakt = $rez;
		
					$kabsInFilial_arr = SelDataFromDB('spr_kabs', $_GET['filial'], 'office_kabs');
					if ($kabsInFilial_arr != 0){
						$kabsInFilial_json = $kabsInFilial_arr[0][$kabsForDoctor];
						//var_dump($kabsInFilial_json);
						
						if ($kabsInFilial_json != NULL){
							$kabsInFilialExist = TRUE;
							$kabsInFilial = json_decode($kabsInFilial_json, true);
							//var_dump($kabsInFilial);
							//echo count($kabsInFilial);
							
						}else{
							$kabsInFilialExist = FALSE;
						}
					}

					echo '
						<div style="margin-bottom: 20px;">
							<ul style="margin-left: 6px; margin-bottom: 20px;">';
										
					echo widget_calendar ($month, $year, 'scheduler_own.php', $dop);
					
					echo '</ul>';
					
					//Если отметка о заполнении == 1, значит график заполнили
					if (($markSheduler == 1) && ($schedulerFakt != 0)){

							if ($kabsInFilialExist){
								echo '
									<table style="border:1px solid #BFBCB5;">
										<tr style="text-align:center; vertical-align:top; font-weight:bold; height:20px; ">
											<td style="border:1px solid #BFBCB5;">
												Понедельник
											</td>
											<td style="border:1px solid #BFBCB5;">
												Вторник
											</td>
											<td style="border:1px solid #BFBCB5;">
												Среда
											</td>
											<td style="border:1px solid #BFBCB5;">
												Четверг
											</td>
											<td style="border:1px solid #BFBCB5;">
												Пятница
											</td>
											<td style="border:1px solid #BFBCB5;">
												Суббота
											</td>
											<td style="border:1px solid #BFBCB5;">
												Воскресенье
											</td>
										</tr>';
										
								//отсутствие врачей в клинике
								$now_ahtung = TRUE;
								$ahtung = TRUE;
								
								for($d = $start; $d <= $end; $d++){
									if (!($di++ % 7)){
										echo '
											<tr style="height: 142px;">';
									}
									
									$kabsNone = '';
									$kabs = '
										<div class="cellTime" style="padding: 0; text-align: center; background-color: #FEFEFE; border: 0; width: 150px; min-width: 125px; max-width: 150px;">';
										
									//Проверяем, есть ли сегодня тут кто.
									if (isset($schedulerFakt[$d])){
										//if ($d==3) var_dump ($schedulerFakt[$d]);
										//номера смен 1 - день 2- вечер 3 - ночь 4 - утро
										for ($smenaN = 1; $smenaN <= 4; $smenaN++) {
											//отсутствие врачей в клинике
											$now_ahtung = TRUE;
											$ahtung = TRUE;
											
											if (isset($schedulerFakt[$d][$smenaN])){
												//var_dump($schedulerFakt[$d][$smenaN]);

												$kabs .= '
														<div style="outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
															<div style="vertical-align: middle; width: 20px; box-shadow: 0px 5px 10px rgba(171, 254, 213, 0.59); display: table-cell !important;">
																<div>'.$smenaN.'</div>';
												/*if (count($schedulerFakt[$d][$smenaN]) != count($kabsInFilial)){
													$kabs .= '
																<div style="bottom: 0; font-size: 120%; color: green; cursor: pointer;"><i class="fa fa-plus-square" title="Добавить сотрудника"></i></div>';
												}*/
												$kabs .= '
															</div>';
													
												//переменная для вывода
												$resEcho2 = '';
												
												//Кабинеты
												//for ($kabN = 1; $kabN <= count($kabsInFilial); $kabN++){
												//Отсортируем кабинеты
												ksort ($schedulerFakt[$d][$smenaN]);
												
												//Временный архив для кабинетов
												$kabsInFilialTemp = $kabsInFilial;
												
												foreach($schedulerFakt[$d][$smenaN] as $kab => $kabValue){
													
													$resEcho = '';
													//$resEcho .= WriteSearchUser('spr_workers',$kabValue['worker'], 'user', false).'<br>';
													$resEcho .= '<i style="font-weight: bold;">'.$offices_jarr[$kabValue['filial']]['name'].'</i>';
													$ahtung = FALSE;
													$fontSize = 'font-size: 70%;';
													$resEcho2 .= '
															<div style="box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);" >
																<div style="text-align: right; color: #555;">
																	<b>каб. '.$kab.'</b>
																</div>
																<div style="text-align: left; padding: 4px;">';
													$resEcho2 .= $resEcho;
													$resEcho2 .= '
																</div>
															</div>';
															
													//Вычеркиваем этот кабинет из списка незанятых
													unset ($kabsInFilialTemp[$kab]);
												}
												
												$BgColor = ' background-color: rgba(171, 254, 213, 0.59);';
												if ($smenaN > 2){
													$BgColor = ' background-color: rgba(220, 220, 220, 0.5);';
												}
										
												$kabs .= '
														<div style="text-align: center; display: table-cell !important; width: 130px;'.$BgColor.'">';
												$kabs .= $resEcho2;
												
												if (!empty($kabsInFilialTemp)){
													$kabs .= '
															<div class="manageScheduler" style="background-color: #FEEEEE; box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);">
																<div style="text-align: center; padding: 4px; margin: 1px;">';
																
													foreach	($kabsInFilialTemp as $keyK => $valueK){
														$kabs .= '
															<div style="display: inline-block; bottom: 0; font-size: 110%; cursor: pointer; border: 1px dotted #9F9D9D; width: 15px; margin-right: 2px;" title="Добавить сотрудника"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
													}
													$kabs .= '
																</div>
															</div>';
												}
												
												$kabs .= '		
														</div>
													</div>';
											}else{

											}
										}
									}else{

									}
									$kabs .= '
													</div>';
									//выделение сегодня цветом
									$now="$year-$month-".sprintf("%02d",$d);
									if ($now == $today){
										$today_color = 'border: 1px solid red; outline: 2px solid red;';
									}else{
										$today_color = 'border:1px solid #BFBCB5;';
									}
									//Выделение цветом выходных
									if (($di % 7 == 0) || ($di % 7 == 6)){
										$holliday_color = 'color: red;';
									}else{
										$holliday_color = '';
									}

									
									echo '
												<td style="'.$today_color.' text-align: center; text-align: -moz-center; text-align: -webkit-center; vertical-align: top;">';
									if ($d < 1 || $d > $day_count){
										echo "&nbsp";
									}else{
															
										echo '
													<div style="vertical-align:top;'.$holliday_color.'" id="blink2">
														<!--<div><span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b"  onclick="alert(\'Скоро\')">запись</span>-->
														<div>
															<span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b">';
								
										echo '
																<div class="no_print"> <a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$d.'&worker='.$_GET['id'].'" class="ahref">запись</a></div>';

										echo '					
															</span>
															<div style="text-align: right;">
																<strong>'.$d.'</strong>
															</div>
														</div>
													</div>
													<div style="text-align: middle; display: table-cell !important; width: 100%;">';
													
										echo $kabs;
										
										if (!$ahtung OR !$now_ahtung){
											//echo $kabs;
										}else{
											echo $kabsNone;									
										}
										echo '		
												</div>';
									}
															/*}else 
																echo '
																		<td style="border:1px solid #BFBCB5; vertical-align:top;">&nbsp;</td>';*/
											//			}
									echo '
												</td>';
									if (!($di % 7)){
										echo '
											</tr>';
									}
								} 
								echo '
									</table>';
							}else{
								echo '<h1>В этом филиале нет кабинетов такого типа.</h1>';
							}
						echo '
								</div>
							</div>';


					}else{
						if (($scheduler['see_all'] == 1)|| $god_mode){
							echo '<h2>График <span style="color:red">не заполнен</span></h2><br>
							<a href="scheduler_template.php" class="b">Заполнить</a>';					
						}else{
							echo '<h2>График <span style="color:red">не заполнен</span>, обратитесь к руководителю</h2>';
						}
					}
		
		
		
		
				}else{
					//!!!
				}
			}else{
				//!!!
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>