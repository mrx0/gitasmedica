<?php

//scheduler2.php
// v 2.0
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump ($_GET);
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$dopQuery = '';
			
			$dayWarr = array(
				1 => 'ПН',
				2 => 'ВТ',
				3 => 'СР',
				4 => 'ЧТ',
				5 => 'ПТ',
				6 => 'СБ',
				7 => 'ВС',
			);
			
			$offices = $offices_j = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);
			
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
			
			//Филиал
			if (isset($_GET['filial'])){
				if ($_GET['filial'] != 0){
					$dopQuery .= " AND `filial`='{$_GET['filial']}'";
					$offices = SelDataFromDB('spr_filials', $_GET['filial'], 'id');
					$wFilial = '';
				}	
			}
			
			$weekDays = '
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПН</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СР</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ЧТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СБ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВС</b></div>';
			
			//День недели
			if (isset($_GET['dayw'])){
				if ($_GET['dayw'] != 0){
					$dopQuery .= " AND `day`='{$_GET['dayw']}'";
				}
			}
			
			//получаем шаблон графика из базы
			$query = "SELECT `filial`, `day`, `smena`, `kab`, `worker` FROM `sheduler_template` WHERE `type` = '$type'".$dopQuery;
			
			$shedTemplate = 0;
			
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
					$rez[$arr['filial']][$arr['day']][$arr['smena']][$arr['kab']] = $arr['worker'];
				}
				$shedTemplate = $rez;
			}else{
				$shedTemplate = 0;
			}
			//var_dump($shedTemplate);
			
			//есть ли кабинеты в филиале
			$kabsInFilialExist = FALSE;
			//какие есть кабинеты в филиале
			$kabsInFilial = array();
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Текущий график план</h1>
					'.$whose.'
				</header>';
			
			echo '
				<div id="data">
					<ul style="margin-left: 6px; margin-bottom: 20px;">
						<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
							<a href="?who=stom" class="b">Стоматологи</a>
							<a href="?who=cosm" class="b">Косметологи</a>
						</li>
						<li>
							<div style="display: inline-block; margin-right: 20px;">
								<div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
									Филиалы
								</div>
								<div>
									<select name="SelectFilial" id="SelectFilial">
										<option value="0">Все</option>';
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
								<div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">День недели</div>
								<div>
									<select name="SelectDayW" id="SelectDayW">
										<option value="0">Все</option>';
				for ($i=1; $i<=count($dayWarr); $i++){
					$selected = '';
					if (isset($_GET['dayw'])){
						if ($i == $_GET['dayw']){
							$selected = 'selected';
							
							if ($_GET['dayw'] != 0){
								//Какой день отображать
								$weekDays = '
									<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>'.$dayWarr[$i].'</b></div>';
							}
						}
					}
					echo "<option value='$i' $selected>".$dayWarr[$i]."</option>";
			}
			echo '
									</select>
								</div>
							</div>
							<div style="margin-top: 10px;">
								<a href="scheduler2.php" class="dotyel">Сброс</a>
							</div>
						</li>
					</ul>';
			echo '
				<div style="margin-bottom: 20px;">
					<div class="cellsBlock">
						<div class="cellName" style="font:size: 110%; text-align: center; background-color:#CCC; width: 120px; min-width: 120px; max-width: 120px;"></div>';
			echo $weekDays;
			echo '
					</div>';
			
			if ($offices != 0){
				//Пробегаемся по филиалам
				foreach ($offices as $filial_val){
					//смотрим, какие кабинеты есть
					$kabsInFilial_arr = SelDataFromDB('spr_kabs', $filial_val['id'], 'office_kabs');
					if ($kabsInFilial_arr != 0){
						$kabsInFilial_json = $kabsInFilial_arr[0][$kabsForDoctor];
						//var_dump($kabsInFilial_json);
						
						if ($kabsInFilial_json != NULL){
							$kabsInFilialExist = TRUE;
							$kabsInFilial = json_decode($kabsInFilial_json, true);
							//var_dump($kabsInFilial);
							
						}else{
							$kabsInFilialExist = FALSE;
						}
						
					}
					
					//Если кабинеты все таки есть
					if ($kabsInFilialExist){
						//var_dump($kabsInFilial);
						echo '
							<div class="cellsBlock cellsBlockHover">
								<div class="cellName" style="font:size: 110%; text-align: left; background-color: #FEFEFE; width: 120px; min-width: 120px; max-width: 120px;">
									'.$filial_val['name'].'
								</div>
						';			
						
						//Дни недели
						$dayWcount = 7;
						if (isset($_GET['dayw'])){
							if ($_GET['dayw'] != 0){
								$dayWcount = 1;
								$dayWvalue = $_GET['dayw'];
							}
						}
						
						for ($dayW = 1; $dayW <= $dayWcount; $dayW++) {
							if ($dayWcount > 1) $dayWvalue = $dayW;
							echo '
								<div class="cellTime" style="padding: 0; text-align: center; background-color: #FEFEFE; width: 150px; min-width: 125px; max-width: 150px;">';
							//номера смен 1 - день 2- вечер 3 - ночь 4 - утро
							for ($smenaN = 1; $smenaN <= 4; $smenaN++) {
								echo '
									<div style="outline: 1px solid #666; display: table; margin-bottom: 3px;">
										<div style="vertical-align: middle; width: 5px; box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2); display: table-cell !important;">
											'.$smenaN.'
										</div>';
								
								//отсутствие врачей в клинике
								$now_ahtung = TRUE;
								$ahtung = TRUE;
								//переменная для вывода
								$resEcho2 = '';
								
								//Кабинеты
								for ($kabN = 1; $kabN <= count($kabsInFilial); $kabN++){
									$resEcho = '';
									//если врач есть
									if (isset($shedTemplate[$filial_val['id']][$dayWvalue][$smenaN][$kabN])){
										$resEcho = WriteSearchUser('spr_workers', $shedTemplate[$filial_val['id']][$dayWvalue][$smenaN][$kabN], 'user', false);
										$ahtung = FALSE;
										$fontSize = 'font-size: 100%;';
									}else{
										$resEcho = '<span style="color: red;">никого</span>';
										$now_ahtung = TRUE;
										$fontSize = '';
									}
									$resEcho2 .= '
											<div style="box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);" onclick="ShowSettingsScheduler('.$filial_val['id'].', \''.$filial_val['name'].'\', '.$dayWvalue.', '.$smenaN.', '.$kabN.')">
												<div style="text-align: right; color: #555;">
													<b>каб. '.$kabN.'</b>
												</div>
												<div style="text-align: left; '.$fontSize.' padding: 4px;">';
									$resEcho2 .= $resEcho;
									$resEcho2 .= '
												</div>
											</div>';
								}
								
								if (!$ahtung OR !$now_ahtung){
									$BgColor = ' background-color: rgba(81, 249, 89, 0.47);';
								}else{
									$BgColor = ' background-color: rgba(252, 153, 153, 0.7);';
								}
								if ($smenaN > 2){
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
							</div>';
					}else{
						echo '
							<div class="cellsBlock cellsBlockHover" style="height: auto;">
								<div class="cellName" style="text-align: left; background-color: #FEFEFE; width: auto;">
									'.$filial_val['name'].' нет кабинетов '.$whose.'
								</div>
							</div>
						';	
					}
				}
			}
			 
			
			/*if (isset($_GET['m']) && isset($_GET['y'])){
				$year = $_GET['y'];
				$month = $_GET['m'];
			}else{
				$year = date("Y");
				$month = date("m");
			}
			
			$offices = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);
			
			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();

			/*$sheduler_times = array (
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
			);*/
			
			/*$who = '&who=stom';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';
			
			if ($_GET){
				//var_dump ($_GET);
				

				
				$month_stamp = mktime(0, 0, 0, $month, 1, $year);
				$day_count = date("t",$month_stamp);
				$weekday = date("w", $month_stamp);
				if ($weekday == 0)
					$weekday = 7;
				$start = -($weekday-2);
				$last = ($day_count + $weekday - 1) % 7;
				if ($last == 0) 
					$end = $day_count; 
				else 
					$end = $day_count + 7 - $last;
				$today = date("Y-m-d");
				$go_today = date('?\m=m&\y=Y', mktime (0, 0, 0, date("m"), 1, date("Y"))); 
				
				/*$prev = date('?\m=m&\y=Y', mktime (0, 0, 0, $month-1, 1, $year));  
				$next = date('?\m=m&\y=Y', mktime (0, 0, 0, $month+1, 1, $year));
				if(isset($_GET['filial'])){
					$prev .= '&filial='.$_GET['filial']; 
					$next .= '&filial='.$_GET['filial'];
					$go_today .= '&filial='.$_GET['filial'];
					if ($_GET['filial'] == 0) $_GET['filial'] = 15;
					$selected_fil = $_GET['filial'];
				}*/
				/*$i = 0;

				$filial = SelDataFromDB('spr_filials', $_GET['filial'], 'offices');
				//var_dump($filial['name']);
				
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
				
				
				if ($filial != 0){
					echo '
						<div id="status">
							<header>
								<h2>График '.$whose.'на ',$month_names[$month-1],' ',$year,' филиал '.$filial[0]['name'].'</h2>
								<a href="own_scheduler.php" class="b">График работы врачей</a><br /><br />';
					echo '
								<form>';
					echo 'Выберите филиал';
					echo '
									<select name="SelectFilial" id="SelectFilial">
										<option value="0">Выберите филиал</option>';
					if ($offices != 0){
						for ($off=0;$off<count($offices);$off++){
							echo "
										<option value='".$offices[$off]['id']."' ", $selected_fil == $offices[$off]['id'] ? "selected" : "" ,">".$offices[$off]['name']."</option>";
						}
					}

					echo '
									</select><br />';
									
					echo 'Выберите врачей';
					echo '
									<select name="SelectWho" id="SelectWho">
										<option value="stom"'.$selected_stom.'>Стоматологи</option>
										<option value="cosm"'.$selected_cosm.'>Косметологи</option>
									</select>
								</form>';	
					echo '			
							</header>';
							
					echo '
					
							<style>
								.label_desc{
									display: block;
								}
								.error{
									display: none;
								}
								.error_input{
									border: 2px solid #FF0000; 
								}
							</style>	
					
					
							<div id="data">';

					if ($kabsInFilialExist){
						echo '
							<table style="border:1px solid #BFBCB5; height:600px;">
								<tr>
									<td colspan="7">
											<table width="100%" border=0 cellspacing=0 cellpadding=0> 
												<tr> 
													<td align="left"><a href="'.$prev.$who.'">&lt;&lt; предыдущий</a></td> 
													<td align="center"><strong>',$month_names[$month-1],' ',$year,'</strong> (<a href="'.$go_today.$who.'">текущий</a>)</td> 
													<td align="right"><a href="'.$next.$who.'">следующий &gt;&gt;</a></td> 
												</tr> 
											</table> 
									</td>
								</tr>
								<tr style="text-align:center; vertical-align:top; font-weight:bold; height:20px;">
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px; text-align:center; ">
										Понедельник
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Вторник
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Среда
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Четверг
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Пятница
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Суббота
									</td>
									<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
										Воскресенье
									</td>
								</tr>';
								

						
						for($d = $start; $d <= $end; $d++){
							if (!($i++ % 7)){
								echo '
									<tr>';
							}
							//все кабинеты свободны
							$ahtung = TRUE;
							$ahtung_smena1 = TRUE;	
							$ahtung_smena2 = TRUE;	
							$kabs = '
								<div class="smena_div">';
							//!!!по кабинетам бегаем
							for ($k = 1; $k <= count($kabsInFilial); $k++){
								//смотрим че там в этом кабинете сегодня 
								$Kab_work_today = FilialKabSmenaWorker($datatable, $year, $month, $d, $_GET['filial'], $k);
								$smena1_work = '
											<div class="smena">
												<br />
											</div>';
								$smena2_work = '
											<div class="smena">
												<br />
											</div>';
								if ($Kab_work_today !=0){
									//var_dump($Kab_work_today);
									
									for($t=0; $t<count($Kab_work_today); $t++){
										$worker_today = $Kab_work_today[$t]['worker'];
										if ($Kab_work_today[$t]['smena'] == 1){
											$smena1_work = '
														<div class="smena smena_work help">
															1 см<br />
															<span class="airhelp">
																	'.WriteSearchUser('spr_workers', $worker_today, 'user').'
															</span>
														</div>';
											$ahtung_smena1 = FALSE;		
										}elseif ($Kab_work_today[$t]['smena'] == 2){
											$smena2_work = '
														<div class="smena smena_work help">
															2 см<br />
															<span class="airhelp">
																	'.WriteSearchUser('spr_workers', $worker_today, 'user').'
															</span>
														</div>';
											$ahtung_smena2 = FALSE;	
										}elseif ($Kab_work_today[$t]['smena'] == 9){
											$smena1_work = '
														<div class="smena smena_work help">
															1 см<br />
															<span class="airhelp">
																	'.WriteSearchUser('spr_workers', $worker_today, 'user').'
															</span>
														</div>';
											$smena2_work = '
														<div class="smena smena_work help">
															2 см<br />
															<span class="airhelp">
																	'.WriteSearchUser('spr_workers', $worker_today, 'user').'
															</span>
														</div>';
											$ahtung_smena1 = FALSE;	
											$ahtung_smena2 = FALSE;	
										}
										if (!$ahtung_smena1 && !$ahtung_smena2)
											$ahtung = FALSE;
									}
									
								}
								
								$kabs .= '
											<div class="kab_filial" onclick="ShowSettingsScheduler('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$d.')">
												<div class="n_kab_filial">
													№'.$k.'	
												</div>
												<div class="smena_div">
													'.$smena1_work.'
													'.$smena2_work.'
												</div>
											</div>';
							}
							$kabs .= '
											</div>';
							//выделение сегодня цветом
							$now="$y-$m-".sprintf("%02d",$d);
							if ($now == $today){
								$today_color = 'border:1px solid red;';
							}else{
								$today_color = 'border:1px solid #BFBCB5;';
							}
							//Выделение цветом выходных
							if (($i % 7 == 0) || ($i % 7 == 6)){
								$holliday_color = 'color: red;';
							}else{
								$holliday_color = '';
							}
							
							if ($ahtung){
								$ahtung_color = 'id="blink2"';
							}else{
								$ahtung_color = '';
							}
							
							echo '
										<td style="'.$today_color.' text-align: center; text-align: -moz-center; text-align: -webkit-center;">';
							if ($d < 1 || $d > $day_count){
								echo "&nbsp";
							}else{

															/*echo '		$now="$y-$m-".sprintf("%02d",$d);
																<td style="border:1px solid #BFBCB5; text-align: center; text-align: -moz-center; text-align: -webkit-center;">
																	<div style="vertical-align:top; text-align: right;">
																		<font color=red>
																			<strong>'.$week[$i][$j].'</strong>
																		</font>
																	</div>
																	'.$kabs.'
																</td>';*/
														/*}else*/
														
								/*echo '
											<div style="vertical-align:top;'.$holliday_color.'" '.$ahtung_color.'>
												<div><span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 2px 4px;" class="b"  onclick="document.location.href = \'scheduler_day.php?y='.$year.'&m='.$month.'&d='.$d.'&filial='.$_GET['filial'].$who.'\'">К ЗАПИСИ</span>
													<div style="text-align: right;">
														<strong>'.$d.'</strong>
													</div>
												</div>
											</div>
											'.$kabs.'';
							}
													/*}else 
														echo '
																<td style="border:1px solid #BFBCB5; vertical-align:top;">&nbsp;</td>';*/
									//			}
							/*echo '
										</td>';
							if (!($i % 7)){
								echo '
									</tr>';
							}
						} 
						echo '
							</table>';
					}else{
						echo '<h1>В этом филиале нет кабинетов такого типа.</h1>';
					}
				}
			}else{
				echo '
					<div id="status">
						<header>
							<h2>График</h2>
							<a href="own_scheduler.php" class="b">График работы врачей</a><br /><br />';
				echo '
					<form>';
				echo 'Выберите филиал';
				echo '
						<select name="SelectFilial" id="SelectFilial">
							<option value="0" selected>Выберите филиал</option>';
				if ($offices != 0){
					for ($i=0;$i<count($offices);$i++){
						echo "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
					}
				}
				echo '
							</select><br />';
									
				echo 'Выберите врачей';
				echo '
						<select name="SelectWho" id="SelectWho">
							<option value="stom"'.$selected_stom.'>Стоматологи</option>
							<option value="cosm"'.$selected_cosm.'>Косметологи</option>
						</select>
					</form>';
				echo '			
				</header>';
			}

			echo '
					</div>
				</div>';*/

			echo '
					<div id="ShowSettingsScheduler" style="position: absolute; z-index: 105; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsScheduler()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsScheduler">
								<label id="smena_error" class="error"></label><br />
								<label id="worker_error" class="error"></label>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">День недели</div>
									<div class="cellRight" id="dayW">
									</div>
									<div style="display: none;" id="dayW_value"></div>
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
								</div>
								';

								
			/*foreach ($sheduler_times as $shedul_key => $shedul_value){
				if ($shedul_key < 13){
					$smena_class = ' class="smena1"';
					$smena_class2 = ' smena1';
				}else{
					$smena_class = ' class="smena2"';
					$smena_class2 = ' smena2';
				}
				//Для JS
				$js_data .= '
					var sh_value'.$shedul_key.' = $("input[name=sh_'.$shedul_key.']:checked").val();
				';
				$post_data .= '
					sh_'.$shedul_key.':sh_value'.$shedul_key.',';
				echo '
								<div class="cellsBlock2" style="font-size:70%; font-weight: bold; width:350px; display: none;">
									<div class="cellLeft'.$smena_class2.'" id="sh_'.$shedul_key.'_2">'.$shedul_value.'</div>
									<div class="cellRight">
										<input type="checkbox" name="sh_'.$shedul_key.'" id="sh_'.$shedul_key.'" value="1"'.$smena_class.' onclick="changeStyle(\'sh_'.$shedul_key.'\')">
									</div>
								</div>
								';
			}*/
			
			
			//Врачи
			echo '
								<div id="ShowWorkersHere" style="vertical-align: top; height: 200px; border: 1px solid #C1C1C1; overflow-x: hidden; overflow-y: scroll;">
								</div>';

			echo '	
						</div>';

			echo '
						<input type="button" class="b" value="Применить" onclick=ChangeWorkerSheduler()>
						<input type="button" class="b" value="Отмена" onclick="HideSettingsScheduler()">
					</div>';	
					
					
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			
			echo '
			
				<script>
				
					$(function() {
						$("#SelectFilial").change(function(){
						    
						    blockWhileWaiting (true);
						    
							var dayW = document.getElementById("SelectDayW").value;
							document.location.href = "?filial="+$(this).val()+"&dayw="+dayW+"'.$who.'";
						});
						$("#SelectDayW").change(function(){
						    
						    blockWhileWaiting (true);
						
							var filial = document.getElementById("SelectFilial").value;
							document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
						});
					});';
					
					
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '	
					function ShowSettingsScheduler(filial, filial_name, dayW, smenaN, kabN){
						$("#ShowSettingsScheduler").show();
						$("#overlay").show();
						//console.log (dict.config.dw4[dayW]);
						//!!!! убрать скролл
						window.scrollTo(0,0)
						
						document.getElementById("dayW").innerHTML = dict.config.dw4[dayW];
						document.getElementById("dayW_value").innerHTML = dayW;
						document.getElementById("filial_value").innerHTML = filial;
						document.getElementById("filial_name").innerHTML = filial_name;
						document.getElementById("kabN").innerHTML = kabN;
						document.getElementById("smenaN").innerHTML = smenaN;
						
						//Те, кто уже есть
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "scheduler_workers_here.php",
							// какие данные будут переданы
							data: {
								filial:filial,
								dayW:dayW,
								smenaN:smenaN,
								kabN:kabN,
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
							url: "scheduler_workers_free.php",
							// какие данные будут переданы
							data: {
								dayW:dayW,
								smenaN:smenaN,
								type:'.$type.',
							},
							// действие, при ответе с сервера
							success: function(workers){
								document.getElementById("ShowWorkersHere").innerHTML=workers;
							}
						});	
						
					}
					
					//Закрываем диалоговое окно
					function HideSettingsScheduler(){
						$(\'#ShowSettingsScheduler\').hide();
						$(\'#overlay\').hide();
						//!!!!! проверить всё ли тут нужно
						var input = document.getElementsByName(\'DateForMove\');
						for (var i=0; i<input.length; i++)  {
							if(input[i].value=="0") input[i].checked="checked";
						}
						
						$("#ShowWorkersHere").html(\'<div class="cellsBlock2" style="width:320px; font-size:80%;"><div class="cellRight">Не выбрана смена</div></div>\');
						
						$(".error").hide();
						
						$("#errror").html("");
					}
					
					
					//!!!
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
					function DeleteWorkersSmena(worker, filial, day, smena, kab, type){
						var rys = confirm("Удалить сотрудника из смены?");
						if (rys){
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_worker_delete.php",
								// какие данные будут переданы
								data: {
									worker:worker,
									filial:filial,
									day:day,
									smena:smena,
									kab:kab,
									type:type
								},
								// действие, при ответе с сервера
								success: function(request){
									document.getElementById("workersTodayDelete").innerHTML=request;
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
					function ChangeWorkerSheduler() {

						$(".error").hide();
						document.getElementById("errrror").innerHTML = "";
					
						// получение данных из полей
						var day = document.getElementById("dayW_value").innerHTML;
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
							url: "scheduler_worker_edit_f.php",
							// какие данные будут переданы
							data: {
								day:day,
								filial:filial,
								kab:kab,
								smena:smena,
								type:type,
								worker:worker,
							},
							// действие, при ответе с сервера
							success: function(data){
								//document.getElementById("errrror").innerHTML=data;
								if (data.req == "ok"){
									// прячем текст ошибок
									$(".error").hide();
									document.getElementById("errrror").innerHTML = "";
									
									window.location.href = data.text;
								}
								if (data.req == "error"){
									document.getElementById("errrror").innerHTML = data.text;
								}
							}
						});						
					};';
			}
			echo '					
			</script>
				
			<script>
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>