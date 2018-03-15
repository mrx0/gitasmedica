<?php

//scheduler.php
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'widget_calendar.php';
			
			$offices = $offices_j = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);
			
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			$dop = '';
			$di = 0;
			
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
			
			//Массив с днями недели
			/*$dayWarr = array(
				1 => 'ПН',
				2 => 'ВТ',
				3 => 'СР',
				4 => 'ЧТ',
				5 => 'ПТ',
				6 => 'СБ',
				7 => 'ВС',
			);*/
		
			$zapis_times = array (
				1 => '9:00 - 9:30',
				2 => '9:30 - 10:00',
				3 => '10:00 - 10:30',
				4 => '10:30 - 11:00',
				5 => '11:00 - 11:30',
				6 => '11:30 - 12:00',
				7 => '12:00 - 12:30',
				8 => '12:30 - 13:00',
				9 => '13:00 - 13:30',
				10 => '13:30 - 14:00',
				11 => '14:00 - 14:30',
				12 => '14:30 - 15:00',
				13 => '15:00 - 15:30',
				14 => '15:30 - 16:00',
				15 => '16:00 - 16:30',
				16 => '16:30 - 17:00',
				17 => '17:00 - 17:30',
				18 => '17:30 - 18:00',
				19 => '18:00 - 18:30',
				20 => '18:30 - 19:00',
				21 => '19:00 - 19:30',
				22 => '19:30 - 20:00',
				23 => '20:00 - 20:30',
				24 => '20:30 - 21:00',
			);

				
			foreach ($_GET as $key => $value){
				if (($key != 'm') && ($key != 'y'))
					$dop .= '&'.$key.'='.$value;
			}
			
			//тип график (космет/стомат/...)
			if (isset($_GET['who'])){
				if ($_GET['who'] == 'stom'){
					$who = '&who=stom';
					$whose = 'Стоматологов ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}elseif($_GET['who'] == 'cosm'){
					$who = '&who=cosm';
					$whose = 'Косметологов ';
					$selected_stom = ' ';
					$selected_cosm = ' selected';
					$datatable = 'scheduler_cosm';
					$kabsForDoctor = 'cosm';
					$type = 6;
				}else{
					$who = '&who=stom';
					$whose = 'Стоматологов ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}
			}else{
				$who = '&who=stom';
				$whose = 'Стоматологов ';
				$selected_stom = ' selected';
				$selected_cosm = ' ';
				$datatable = 'scheduler_stom';
				$kabsForDoctor = 'stom';
				$type = 5;
			}
			
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
			$query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year' AND `filial`='{$filial[0]['id']}'";
			
			$markSheduler = 0;
			
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			
			$arr = array();
			$rez = array();
				
			$res = mysql_query($query) or die($query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					//Раскидываем в массив
					$rez[$arr['day']][$arr['smena']][$arr['kab']] = $arr;
				}
				$markSheduler = 1;
			}else{
				$rez = 0;
			}
			//var_dump($rez[3]);
			
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
			
			//переменная, чтоб вкл/откл редактирование
			echo '
				<script>
					var iCanManage = false;
				</script>
			';

			echo '
				<div id="status">
					<header>
						<h2>График '.$whose.'на ',$monthsName[$month],' ',$year,' филиал '.$filial[0]['name'].'</h2>
					</header>
					<a href="scheduler_template.php" class="b">График план</a>
					<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
					<!--<a href="own_scheduler.php" class="b">График сотрудника</a>-->';
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
					<div id="data">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
							<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
								<div style="cursor: pointer;" onclick="manageScheduler()">
									<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>
								</div>
							</li>';
			}
			echo '			
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?who=stom" class="b">Стоматологи</a>
								<a href="?who=cosm" class="b">Косметологи</a>
							</li>
							<li style="width: auto; margin-bottom: 20px;">
								<div style="display: inline-block; margin-right: 20px;">
									<div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
										Филиалы
									</div>
									<div>
										<select name="SelectFilial" id="SelectFilial">
											';
			if ($offices_j != 0){
				for ($i=0;$i<count($offices_j);$i++){
					$selected = '';
					if (isset($_GET['filial'])){
						if ($offices_j[$i]['id'] == $_GET['filial']){
							$selected = 'selected';
						}
					}
					echo "<option value='".$offices_j[$i]['id']."' $selected>".$offices_j[$i]['name']."</option>";
				}
			}
			echo '
										</select>
									</div>
								</div>
								<div style="display: inline-block; margin-right: 20px;">

									<div style="display: inline-block; margin-right: 20px;">
										<a href="?'.$who.'" class="dotyel" style="font-size: 70%;">Сбросить</a>
									</div>
								</div>
							</li>';
								
			echo widget_calendar ($month, $year, 'scheduler.php', $dop);
			
			echo '</ul>';
			
			//Если отметка о заполнении == 1, значит график заполнили
			if (($markSheduler == 1) && ($schedulerFakt != 0)){

					if ($kabsInFilialExist){

						echo '
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
						
						echo '
								<li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE; height: 75px;">
									<div class="cellName" style="width: 50px; max-width: 50px; min-width: 50px; text-align: center; background-color:#FEFEFE;">Время</div>';
						for ($j=1; $j<=8; $j++){
							echo '<div class="cellCosmAct" style="width: auto; text-align: center; background-color:#FEFEFE;">Врач</div>';
						}
						
						echo '
								</li>';
							
						for ($t=1; $t<=24; $t++){
							echo '
								<li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE; height: 75px;">
									<div class="cellName" style="width: 50px; max-width: 50px; min-width: 50px; text-align: center; background-color:#FEFEFE;">'.$zapis_times[$t].'</div>';
							for ($j=1; $j<=8; $j++){
								echo '
									<div class="cellCosmAct" style="width: auto; text-align: center; background-color:#FEFEFE; position: relative;">
										'.$j.'
									</div>';
							}
							
							echo '
								</li>';
						}
						echo '
							</ul>';
						
						
						/*echo '
							<table style="border:1px solid #BFBCB5;">
								<tr style="text-align:center; vertical-align:top; font-weight:bold; height:20px;">
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
						
						/*for($d = $start; $d <= $end; $d++){
							if (!($di++ % 7)){
								echo '
									<tr>';
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
						/*				$kabs .= '
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
											$resEcho = WriteSearchUser('spr_workers',$kabValue['worker'], 'user', false).' <a href="scheduler_own.php?id='.$kabValue['worker'].'" class="info"><i class="fa fa-info-circle" title="График врача"></i></a>';
											$ahtung = FALSE;
											$fontSize = 'font-size: 70%;';
											$resEcho2 .= '
													<div style="box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);" onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$kab.', '.$year.', '.$month.','.$d.', '.$smenaN.')">
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
													<div style="display: inline-block; bottom: 0; font-size: 110%; cursor: pointer; border: 1px dotted #9F9D9D; width: 15px; margin-right: 2px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$keyK.', '.$year.', '.$month.','.$d.', '.$smenaN.')"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
											}
											$kabs .= '
														</div>
													</div>';
										}
										
										$kabs .= '		
												</div>
											</div>';
									}else{
										if (($smenaN == 1) || ($smenaN == 2)){
											$kabs .= '
													<div style="width: 100%; height: 35px; min-height: 35px; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
														<div style="vertical-align: middle; width: 20px; box-shadow: 0px 5px 10px rgba(171, 254, 213, 0.59); display: table-cell !important;">
															<div>'.$smenaN.'</div>
														</div>
														<div style="width: 130px; vertical-align: middle; display: table; margin-bottom: 3px; color: red;">
															<div style="margin-bottom: 7px;">никого нет</div>
															<div class="manageScheduler">';
															
											foreach	($kabsInFilial as $keyK => $valueK){
												$kabs .= '
																<div style="display: inline-block; bottom: 0; font-size: 120%; cursor: pointer; border: 1px dotted #9F9D9D; width: 20px; margin-right: 3px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$valueK.', '.$year.', '.$month.','.$d.', '.$smenaN.')"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
											}
											$kabs .= '
															</div>
														</div>
													</div>';
										}
										//Ночные смены
										if (($smenaN == 3) || ($smenaN == 4)){
											$kabs .= '
													<div class="nightSmena">
														<div style="width: 100%; height: 35px; min-height: 35px; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
															<div style="vertical-align: middle; width: 20px; box-shadow: 0px 5px 10px rgba(171, 254, 213, 0.59); display: table-cell !important;">
																<div>'.$smenaN.'</div>
															</div>
															<div style="width: 130px; vertical-align: middle; display: table; margin-bottom: 3px; color: red;">
																<div style="margin-bottom: 7px;">никого нет</div>
																<div class="manageScheduler">';
															
											foreach	($kabsInFilial as $keyK => $valueK){
												$kabs .= '
																<div style="display: inline-block; bottom: 0; font-size: 120%; cursor: pointer; border: 1px dotted #9F9D9D; width: 20px; margin-right: 3px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$valueK.', '.$year.', '.$month.','.$d.', '.$smenaN.')"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
											}
											$kabs .= '
																</div>
															</div>
														</div>
													</div>';
										}
									}
								}
							}else{
								//Если вообще никого целый день и ночь
								//Но нам нужен только день
								$ahtung = TRUE;
									for ($smenaN = 1; $smenaN <= 4; $smenaN++) {
										if (($smenaN == 1) || ($smenaN == 2)){
											$kabsNone .= '
													<div style="width: 100%; height: 35px; min-height: 35px; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
														<div style="vertical-align: middle; width: 20px; box-shadow: 0px 5px 10px rgba(171, 254, 213, 0.59); display: table-cell !important;">
															<div>'.$smenaN.'</div>
														</div>
														<div style="width: 130px; vertical-align: middle; display: table; margin-bottom: 3px; color: red;">
															<div style="margin-bottom: 7px;">никого нет</div>
															<div class="manageScheduler">';
															
											foreach	($kabsInFilial as $keyK => $valueK){
												$kabsNone .= '
																<div style="display: inline-block; bottom: 0; font-size: 120%; cursor: pointer; border: 1px dotted #9F9D9D; width: 20px; margin-right: 3px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$valueK.', '.$year.', '.$month.','.$d.', '.$smenaN.')"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
											}
											$kabsNone .= '
															</div>
														</div>
													</div>';
										}
										//Ночные смены
										if (($smenaN == 3) || ($smenaN == 4)){
											$kabsNone .= '
													<div class="nightSmena">
														<div style="width: 100%; height: 35px; min-height: 35px; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
															<div style="vertical-align: middle; width: 20px; box-shadow: 0px 5px 10px rgba(171, 254, 213, 0.59); display: table-cell !important;">
																<div>'.$smenaN.'</div>
															</div>
															<div style="width: 130px; vertical-align: middle; display: table; margin-bottom: 3px; color: red;">
																<div style="margin-bottom: 7px;">никого нет</div>
																<div class="manageScheduler">';
															
											foreach	($kabsInFilial as $keyK => $valueK){
												$kabsNone .= '
																<div style="display: inline-block; bottom: 0; font-size: 120%; cursor: pointer; border: 1px dotted #9F9D9D; width: 20px; margin-right: 3px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$filial[0]['id'].', \''.$filial[0]['name'].'\', '.$valueK.', '.$year.', '.$month.','.$d.', '.$smenaN.')"><span style="color: #333;">'.$valueK.'</span><br><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
											}
											$kabsNone .= '
																</div>
															</div>
														</div>
													</div>';
										}
									}
							}
							$kabsNone .= '
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
												<!--<div><span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b"  onclick="document.location.href = \'scheduler_day.php?y='.$year.'&m='.$month.'&d='.$d.'&filial='.$_GET['filial'].$who.'\'">запись</span>-->
												<div><span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b"  onclick="alert(\'Скоро\')">запись</span>
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
						/*	echo '
										</td>';
							if (!($di % 7)){
								echo '
									</tr>';
							}
						}*/ 
						echo '
							</table>';
					}else{
						echo '<h1>В этом филиале нет кабинетов такого типа.</h1>';
					}
				echo '
						</div>
					</div>';
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
					<div id="ShowSettingsSchedulerFakt" style="position: absolute; z-index: 105; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsSchedulerFakt()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(../img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsSchedulerFakt">
								<label id="smena_error" class="error"></label><br />
								<label id="worker_error" class="error"></label>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Число</div>
									<div class="cellRight" id="month_date"></div>
									<div style="display: none;" id="day"></div>
									<div style="display: none;" id="month"></div>
									<div style="display: none;" id="year"></div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight" id="filial_name">					
									</div>
									<div style="display: none;" id="filial_value"></div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Кабинет №</div>
									<div class="cellRight" id="kabN">
									</div>
								</div>

								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Смена</div>
									<div class="cellRight" id="smenaN">
									</div>
								</div>';
				echo '
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">

									<div class="cellRight">
										<div id="workersTodayDelete"></div>
										<div id="errrror"></div>
									</div>
								</div>';

				//Врачи
				echo '
								<div id="ShowWorkersHere" style="vertical-align: top; height: 200px; border: 1px solid #C1C1C1; overflow-x: hidden; overflow-y: scroll;">
								</div>';

				echo '	
						</div>';
			}
			echo '
						<input type="button" class="b" value="OK" onclick="if (iCanManage) ChangeWorkerShedulerFakt()" id="changeworkersheduletbutton">
						<input type="button" class="b" value="Отмена" onclick="HideSettingsSchedulerFakt()">
					</div>';
	
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

				
				echo '
				
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
								//var dayW = document.getElementById("SelectDayW").value;
								document.location.href = "?filial="+$(this).val()+"'.$who.'";
							});
							$("#SelectDayW").change(function(){
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
							});
						});
					
					
						$(function() {
							/*$(\'#SelectWho\').change(function(){
								if (document.getElementById("SelectFilial").value != 0)
									document.location.href = "?filial="+document.getElementById("SelectFilial").value+"&who="+$(this).val();	
							});
							$(\'#SelectFilial\').change(function(){
								document.location.href = "?filial="+$(this).val()+"&who="+document.getElementById("SelectWho").value;
							});*/
						});';
				if (($scheduler['edit'] == 1) || $god_mode){
					echo '		
						function ShowSettingsSchedulerFakt(filial, filial_name, kabN, year, month, day, smenaN){
							$("#ShowSettingsSchedulerFakt").show();
							$("#overlay").show();
							//alert(month_date);
							window.scrollTo(0,0)
							
							document.getElementById("filial_value").innerHTML=filial;
							document.getElementById("filial_name").innerHTML=filial_name;
							
							document.getElementById("month_date").innerHTML=day+\'.\'+month+\'.\'+year;
							document.getElementById("year").innerHTML=year;
							document.getElementById("month").innerHTML=month;
							document.getElementById("day").innerHTML=day;
							
							document.getElementById("kabN").innerHTML=kabN;
							document.getElementById("smenaN").innerHTML=smenaN;
							
							//Те, кто уже есть
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_workers_here_fakt.php",
								// какие данные будут переданы
								data: {
									day:day,
									month:month,
									year:year,
									kab:kabN,
									smena:smenaN,
									filial:filial,
									type: '.$type.'
								},
								// действие, при ответе с сервера
								success: function(workers_here){
									document.getElementById("workersTodayDelete").innerHTML=workers_here;
								}
							});

							//Те, кто свободен
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_workers_free_fakt.php",
								// какие данные будут переданы
								data: {
									day:day,
									month:month,
									year:year,
									smena:smenaN,
									type:'.$type.',
								},
								// действие, при ответе с сервера
								success: function(workers){
									document.getElementById("ShowWorkersHere").innerHTML=workers;
								}
							});	
						}
						
						function HideSettingsSchedulerFakt(){
							$("#ShowSettingsSchedulerFakt").hide();
							$("#overlay").hide();
							var input = document.getElementsByName("DateForMove");
							for (var i=0; i<input.length; i++)  {
								if(input[i].value=="0") input[i].checked="checked";
							}
							
														
							$(\'.error\').hide();
							document.getElementById("errror").innerHTML = \'\';
						}
						
						function ShowWorkersSmena(){
							var smena = 0;
							if ( $("#smena1").prop("checked")){
								if ( $("#smena2").prop("checked")){
									smena = 9;
								}else{
									smena = 1;
								}
							}else if ( $("#smena2").prop("checked")){
								smena = 2;
							}
							
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "show_workers_free.php",
								// какие данные будут переданы
								data: {
									day:$(\'#day\').val(),
									month:$(\'#month\').val(),
									year:$(\'#year\').val(),
									smena:smena,
									datatable:"'.$datatable.'"
								},
								// действие, при ответе с сервера
								success: function(workers){
									document.getElementById("ShowWorkersHere").innerHTML=workers;
								}
							});	
						}
						
					//Удаляем врача из смены
					function DeleteWorkersSmenaFakt(worker, filial, day, month, year, smena, kab, type){
						var rys = confirm("Удалить сотрудника из смены?");
						if (rys){
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_worker_delete_fakt.php",
								// какие данные будут переданы
								data: {
									worker:worker,
									filial:filial,
									day:day,
									month:month,
									year:year,
									smena:smena,
									kab:kab,
									type:type
								},
								// действие, при ответе с сервера
								success: function(request){
									document.getElementById("workersTodayDelete").innerHTML=request;
									setTimeout(function () {
										location.reload()
									}, 100);
								}
							});	
						}
					}';
				}	
				echo '	
					</script>
				
				
				
					<script>  
						function changeStyle(idd){
							if ( $("#"+idd).prop("checked"))
								document.getElementById(idd+"_2").style.background = \'#83DB53\';
							else
								document.getElementById(idd+"_2").style.background = \'#F0F0F0\';
						}

						$(document).ready(function() {
							$("#smena1").click(function() {
								var checked_status = this.checked;
								 $(".smena1").each(function() {
									this.checked = checked_status;
									if ( $(this).prop("checked"))
										this.style.background = \'#83DB53\';
									else
										this.style.background = \'#F0F0F0\';
								});
								
								var ShowWorkersSmena1 = ShowWorkersSmena();
							});
							$("#smena2").click(function() {
								var checked_status = this.checked;
								 $(".smena2").each(function() {
									this.checked = checked_status;
									if ( $(this).prop("checked"))
										this.style.background = \'#83DB53\';
									else
										this.style.background = \'#F0F0F0\';
								});
								
								var ShowWorkersSmena1 = ShowWorkersSmena();
							});
						});';
			if (($scheduler['edit'] == 1) || $god_mode){					
				echo '
						function ChangeWorkerShedulerFakt() {

							$(".error").hide();
							document.getElementById("errrror").innerHTML = "";
						
							// получение данных из полей
							var day = document.getElementById("day").innerHTML;
							var month = document.getElementById("month").innerHTML;
							var year = document.getElementById("year").innerHTML;
							var filial = document.getElementById("filial_value").innerHTML;
							var kab = document.getElementById("kabN").innerHTML;
							var smena = document.getElementById("smenaN").innerHTML;
							var type = '.$type.';

							var worker = $("input[name=worker]:checked").val();
							if(typeof worker == "undefined") worker = 0;

							$.ajax({
								dataType: "json",
								//statbox:SettingsScheduler,
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_worker_edit_fakt_f.php",
								// какие данные будут переданы
								data: {
									day:day,
									month:month,
									year:year,
									filial:filial,
									kab:kab,
									smena:smena,
									type:type,
									worker:worker,
								},
								// действие, при ответе с сервера
								success: function(data){
									//document.getElementById("errrror").innerHTML = data;
									if (data.req == "ok"){
										// прячем текст ошибок
										$(".error").hide();
										document.getElementById("errrror").innerHTML = "";
										setTimeout(function () {
											location.reload()
										}, 100);
									}
								}
							});						
						};';
			}
			echo '					
			</script>
					
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
				if (($scheduler['see_all'] == 1)|| $god_mode){
					echo '<h2>График <span style="color:red">не заполнен</span></h2><br>
					<a href="../scheduler_template.php" class="b">Заполнить</a>';
				}else{
					echo '<h2>График <span style="color:red">не заполнен</span>, обратитесь к руководителю</h2>';
				}
			}
			echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
								//var dayW = document.getElementById("SelectDayW").value;
								document.location.href = "?filial="+$(this).val()+"'.$who.'";
							});
							$("#SelectDayW").change(function(){
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
							});
						});
						
					</script>
			';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="../index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>