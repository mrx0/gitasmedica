<?php

//scheduler.php
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			$offices = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);
			
			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();

			$sheduler_times = array (
				540 => '9:00 - 9:30',
				570 => '9:30 - 10:00',
				600 => '10:00 - 10:30',
				630 => '10:30 - 11:00',
				660 => '11:00 - 11:30',
				690 => '11:30 - 12:00',
				720 => '12:00 - 12:30',
				750 => '12:30 - 13:00',
				780 => '13:00 - 13:30',
				810 => '13:30 - 14:00',
				840 => '14:00 - 14:30',
				870 => '14:30 - 15:00',
				900 => '15:00 - 15:30',
				930 => '15:30 - 16:00',
				960 => '16:00 - 16:30',
				990 => '16:30 - 17:00',
				1020 => '17:00 - 17:30',
				1050 => '17:30 - 18:00',
				1080 => '18:00 - 18:30',
				1110 => '18:30 - 19:00',
				1140 => '19:00 - 19:30',
				1170 => '19:30 - 20:00',
				1200 => '20:00 - 20:30',
				1230 => '20:30 - 21:00',
			);
			
			$who = '&who=stom';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';
			
			if ($_GET){
				//var_dump ($_GET);
				
				//тип график (космет/стомат/...)
				if (isset($_GET['who'])){
					if ($_GET['who'] == 'stom'){
						$who = '&who=stom';
						$whose = 'Стоматологов ';
						$selected_stom = ' selected';
						$selected_cosm = ' ';
						$datatable = 'scheduler_stom';
						$kabsForDoctor = 'stom';
					}elseif($_GET['who'] == 'cosm'){
						$who = '&who=cosm';
						$whose = 'Косметологов ';
						$selected_stom = ' ';
						$selected_cosm = ' selected';
						$datatable = 'scheduler_cosm';
						$kabsForDoctor = 'cosm';
					}else{
						$who = '&who=stom';
						$whose = 'Стоматологов ';
						$selected_stom = ' selected';
						$selected_cosm = ' ';
						$datatable = 'scheduler_stom';
						$kabsForDoctor = 'stom';
					}
				}else{
					$who = '&who=stom';
					$whose = 'Стоматологов ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
				}
				
				$month_names=array(
					"Январь",
					"Февраль",
					"Март",
					"Апрель",
					"Май",
					"Июнь",
					"Июль",
					"Август",
					"Сентябрь",
					"Октябрь",
					"Ноябрь",
					"Декабрь"
				); 
				if (isset($_GET['y']))
					$y = $_GET['y'];
				if (isset($_GET['m']))
					$m = $_GET['m']; 
				if (isset($_GET['d']))
					$d = $_GET['d']; 
				if (isset($_GET['date']) && strstr($_GET['date'],"-"))
					list($y,$m) = explode("-",$_GET['date']);
				if (!isset($y) || $y < 1970 || $y > 2037)
					$y = date("Y");
				if (!isset($m) || $m < 1 || $m > 12)
					$m = date("m");
				if (!isset($d))
					$d = date("d");
				$month_stamp = mktime(0, 0, 0, $m, 1, $y);
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
				$go_today = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, date("m"), date("d"), date("Y"))); 
				
				$prev = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, $m, $d-1, $y));  
				$next = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, $m, $d+1, $y));
				if(isset($_GET['filial'])){
					$prev .= '&filial='.$_GET['filial']; 
					$next .= '&filial='.$_GET['filial'];
					$go_today .= '&filial='.$_GET['filial'];
					
					$selected_fil = $_GET['filial'];
				}
				$i = 0;
				
				
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
								<h2>График '.$whose.'на '.$d.' ',$month_names[$m-1],' ',$y,' филиал '.$filial[0]['name'].'</h2>
								<a href="own_scheduler.php">График работы врачей</a> 
								<a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">График работы филиала</a><br /><br />';
					echo '
								<form>
									<select name="SelectFilial" id="SelectFilial">
										<option value="0">Выберите филиал</option>';
					if ($offices != 0){
						for ($off=0;$off<count($offices);$off++){
							echo "
										<option value='".$offices[$off]['id']."' ", $selected_fil == $offices[$off]['id'] ? "selected" : "" ,">".$offices[$off]['name']."</option>";
						}
					}

					echo '
									</select>
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
							<table style="border:1px solid #BFBCB5; min-width: 700px;">
								<tr>
									<td colspan="7">
											<table width="100%" border=0 cellspacing=0 cellpadding=0> 
												<tr> 
													<td align="left"><a href="'.$prev.$who.'">&lt;&lt; предыдущий</a></td> 
													<td align="center"><strong>'.$d.' ',$month_names[$m-1],' ',$y,'</strong> (<a href="'.$go_today.$who.'">сегодня</a>)</td> 
													<td align="right"><a href="'.$next.$who.'">следующий &gt;&gt;</a></td> 
												</tr> 
											</table> 
									</td>
								</tr>
								
								<tr>';
						
						$Work_Today_arr = array();
						
						$Work_Today = FilialWorker	($datatable, $y, $m, $d, $_GET['filial']);
						if ($Work_Today != 0){
							//var_dump($Work_Today);
							
							foreach($Work_Today as $Work_Today_value){
								//var_dump($Work_Today_value);
								//!!!Бля такой тут пиздец с этой 9ой сменой....
								//а, сука, потому что сразу надо было головой думать
								if ($Work_Today_value['smena'] == 9){
									$Work_Today_arr[$Work_Today_value['kab']][1] = $Work_Today_value;
									$Work_Today_arr[$Work_Today_value['kab']][2] = $Work_Today_value;
								}else{
									$Work_Today_arr[$Work_Today_value['kab']][$Work_Today_value['smena']] = $Work_Today_value;
								}
							}
						}else{
							//никто не работает тут сегодня
						}
						//var_dump($Work_Today_arr);
						
						//смена 1	
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top;">';
							
							//$Kab_work_today_smena1 = FilialKabSmenaWorker($datatable, $y, $m, $d, $_GET['filial'], $k);
							//var_dump($Kab_work_today_smena1);
							
							//$wt = 1;
							//if ($Kab_work_today_smena1 != 0){
							if (isset($Work_Today_arr[$k][1])){
									//var_dump($Kab_work_today_smena1);
									
									echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker" style="background-color:rgba(39, 183, 127, .5)">
												1 смена каб '.$k.' '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][1]['worker'], 'user', false).'
											</div>
										</div>';
								//Выбрать записи пациентов, если есть
								$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $k);
								
								$NextTime = FALSE;
								$ThatTimeFree = TRUE;
								$PeredannNextTime = FALSE;
								$NextTime_val = 0;
								for ($wt=540; $wt < 900; $wt=$wt+30){
									if ($ZapisHereQueryToday != 0){
										//var_dump ($ZapisHereQueryToday);
										$ZapisHere = FALSE;
										if ($NextTime){
											$PeredannNextTime = TRUE;
										}
										foreach ($ZapisHereQueryToday as $key => $ZapisHereQueryToday_val){
											//var_dump ($ZapisHereQueryToday[$key]);

											if ((($ZapisHereQueryToday[$key]['start_time']+$ZapisHereQueryToday[$key]['wt'] <= $wt+30) || ($ZapisHereQueryToday[$key]['start_time'] < $wt+30))
												&& ($ZapisHereQueryToday[$key]['start_time'] >= $wt)){
												
												//echo '000<br />';
												$ZapisHere = TRUE;
												//Если длительность приёма захватывает следующий промежуток
												if ($ZapisHereQueryToday[$key]['start_time']+$ZapisHereQueryToday[$key]['wt'] > $wt+30){
													//echo '001<br />';
													$ThatTimeFree = FALSE;
													$NextTime = TRUE;
													//Если время больше чем на два промежутка
													//if ($ZapisHereQueryToday[$key]['wt'] >= 60){
													$NextTime_val = $ZapisHereQueryToday[$key]['wt']-30;
													//}
												//Если длительность приёма НЕ полностью покрывает промежуток
												}elseif ($ZapisHereQueryToday[$key]['start_time']+$ZapisHereQueryToday[$key]['wt'] < $wt+30){
													//echo '002<br />';
													$ThatTimeFree = TRUE;
													$NextTime = FALSE;
												//Если длительность приёма полностью покрывает промежуток
												}elseif ($ZapisHereQueryToday[$key]['start_time']+$ZapisHereQueryToday[$key]['wt'] = $wt+30){
													//echo '003<br />';
													$ThatTimeFree = FALSE;
													$NextTime = FALSE;
												}
											}else{
												if (!$ZapisHere){
													$ThatTimeFree = TRUE;
													$NextTime = FALSE;
												}
												//break;
											}
												
										}
										
										
	
									}else{
										$ZapisHere = FALSE;
										$NextTime = FALSE;
										$ThatTimeFree = TRUE;
									}
									
									//var_dump($ZapisHere);
									//var_dump($ThatTimeFree);
									//var_dump($NextTime);
									
									//Если есть запись
									if ($ZapisHere){
										if ($ThatTimeFree){
											$NextTimeBgCol = 'background:#FF9900;';
										}else{
											$NextTimeBgCol = 'background:#83DB53;';
										}
									}else{
										if ($PeredannNextTime){
											if (($wt + $NextTime_val) > ($wt + 30)){
												//echo '004:'.$wt.' + '.$NextTime_val.' = '.$wt.' + 30';
												$NextTimeBgCol = 'background:#83DB53;';
												$NextTime = TRUE;
												$NextTime_val = $NextTime_val - 30;
											}elseif (($wt + $NextTime_val) == ($wt + 30)){
												//echo '005:'.$wt.' + '.$NextTime_val.' = '.$wt.' + 30';
												$NextTimeBgCol = 'background:#83DB53;';
												$NextTime = FALSE;
												$NextTime_val = 0;
											}else{
												//echo '006:'.$wt.' + '.$NextTime_val.' = '.$wt.' + 30';
												$NextTimeBgCol = 'background:#FF9900;';
												$NextTime = FALSE;
												$NextTime_val = 0;
											}
										}else{
											$NextTimeBgCol = '';
										}
									}
									$PeredannNextTime = FALSE;
								
									echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker" style="padding:0;">
												<div onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$y.', '.$m.','.$d.', 1, '.$wt.', '.$Work_Today_arr[$k][1]['worker'].', \''.WriteSearchUser('spr_workers', $Work_Today_arr[$k][1]['worker'], 'user', false).'\')" style="float:left; margin: 5px;">'.$sheduler_times[$wt].'</div>
												<div style="float:right; width: 60%; margin: 0; height:25px;">
													<div style="'.$NextTimeBgCol.' height:100%; text-align: center;">';
									//echo $NextTime_val;
									//var_dump($ZapisHere);
									//var_dump($ThatTimeFree);
									//var_dump($NextTime);
									echo '
													</div>
												</div>
											</div>
										</div>
									';
									//$wt = $wt+60;
								}
							}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker">
												1 смена каб '.$k.'
											</div>
										</div>
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker">
												нет врача по <a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">графику</a>
											</div>
										</div>';
							}

							echo '
									</td>';
						}
						echo '
								<tr>
								</tr>';
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top;">';
							//смена 2
							//$Kab_work_today_smena2 = FilialKabSmenaWorker($datatable, $y, $m, $d, $_GET['filial'], $k);
							//var_dump($Kab_work_today_smena2);
							
							//if ($Kab_work_today_smena2 != 0){
							if (isset($Work_Today_arr[$k][2])){
								//var_dump($Kab_work_today_smena2);
								
									echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker" style="background-color:rgba(39, 183, 127, .5);">
												2 смена каб '.$k.' '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][2]['worker'], 'user', false).'
											</div>
										</div>';
								for ($wt=900; $wt < 1260; $wt=$wt+30){
									echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker">
												<div onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$y.', '.$m.','.$d.', 1, '.$wt.', '.$Work_Today_arr[$k][2]['worker'].', \''.WriteSearchUser('spr_workers', $Work_Today_arr[$k][2]['worker'], 'user', false).'\')">'.$sheduler_times[$wt].'</div>
											</div>
										</div>
									';
									//$wt = $wt+60;
								}
							}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker">
												2 смена каб '.$k.'
											</div>
										</div>
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker">
												нет врача по <a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">графику</a>
											</div>
										</div>';
							}

							echo '
									</td>';
						} 
						echo '
								</tr>
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
							<a href="own_scheduler.php">График работы врачей</a><br /><br />';
				echo '
					<form>
						<select name="SelectFilial" id="SelectFilial">
							<option value="0" selected>Выберите филиал</option>';
				if ($offices != 0){
					for ($i=0;$i<count($offices);$i++){
						echo "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
					}
				}
				echo '
						</select>
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
				</div>';

			echo '
					<div id="ShowSettingsAddTempZapis" style="position: absolute; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; z-index: 105; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsAddTempZapis()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsAddTempZapis">

							<div style="display:inline-block;">
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
									<div class="cellLeft">Число</div>
									<div class="cellRight" id="month_date">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
									<div class="cellLeft">Смена</div>
									<div class="cellRight" id="month_date_smena">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight" id="filial_name">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
									<div class="cellLeft">Кабинет №</div>
									<div class="cellRight" id="kab">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
									<div class="cellLeft">Врач</div>
									<div class="cellRight" id="worker_name">
									</div>
								</div>

								<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Пациент</div>
									<div class="cellRight" style="">
										<input type="text" size="30" name="patient" id="patient" placeholder="Введите ФИО пациента" value="" autocomplete="off">
									</div>
								</div>
								<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Контакты</div>
									<div class="cellRight" style="">
										<input type="text" size="30" name="contacts" id="contacts" placeholder="Введите контакт" value="" autocomplete="off">
									</div>
								</div>
								<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Описание</div>
									<div class="cellRight" style="">
										<textarea name="description" id="description" style="width:100%; overflow:auto; height:150px;"></textarea>
									</div>
								</div>		
							</div>';
			echo '
							<div style="display:inline-block; vertical-align: top; width: 360px; border: 1px solid #C1C1C1;">
								<div id="ShowTimeSettingsHere">
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Время записи</div>
									<div class="cellRight">
										<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Время приёма</div>
									<div class="cellRight">
										<!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
										<select name="PriemTime" id="PriemTime" onchange="PriemTimeCalc(this.value);">
											<option value="5">5 мин</option>
											<option value="10">10 мин</option>
											<option value="15">15 мин</option>
											<option value="30" selected>30 мин</option>
											<option value="60">1 час</option>
											<option value="90">1 час 30 мин</option>
										</select>
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Время окончания</div>
									<div class="cellRight">
										<div id="work_time_h_end" style="display:inline-block;"></div>:<div id="work_time_m_end" style="display:inline-block;"></div>
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellRight">
										<div id="exist_zapis" style="display:inline-block;"></div>
									</div>
								</div>
							</div>';



			echo '
						<input type="hidden" id="day" name="day" value="0">
						<input type="hidden" id="month" name="month" value="0">
						<input type="hidden" id="year" name="year" value="0">
						<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
						<input type="hidden" id="filial" name="filial" value="0">
						<input type="hidden" id="start_time" name="start_time" value="0">
						<input type="hidden" id="wt" name="wt" value="0">
						<input type="hidden" id="worker_id" name="worker_id" value="0">
						<div id="errror"></div>
						<input type=\'button\' class="b" value=\'Добавить\' id="Ajax_add_TempZapis" onclick=Ajax_add_TempZapis()>
					</div>';	
					
			echo '	
						
					</div>';					
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			
			echo '
			
				<script>
				
					$(function() {
						$(\'#SelectWho\').change(function(){
							if (document.getElementById("SelectFilial").value != 0){
							
							    blockWhileWaiting (true);
							
								document.location.href = "?filial="+document.getElementById("SelectFilial").value+"&who="+$(this).val();
							}
						});
						$(\'#SelectFilial\').change(function(){
						
						    blockWhileWaiting (true);
						
							document.location.href = "?filial="+$(this).val()+"&who="+document.getElementById("SelectWho").value;
						});
					});';
			//if (($scheduler['edit'] == 1) || $god_mode){
				echo '		
					function ShowSettingsAddTempZapis(filial, filial_name, kab, year, month, day, smena, time, worker_id, worker_name){
						$(\'#ShowSettingsAddTempZapis\').show();
						$(\'#overlay\').show();
						//alert(month_date);
						window.scrollTo(0,0)
						
						document.getElementById("Ajax_add_TempZapis").disabled = false;
						
						document.getElementById("filial").value=filial;
						document.getElementById("year").value=year;
						document.getElementById("month").value=month;
						document.getElementById("day").value=day;
						document.getElementById("start_time").value=time;
						document.getElementById("wt").value=30;
						document.getElementById("worker_id").value=worker_id;
						
						document.getElementById("filial_name").innerHTML=filial_name;
						document.getElementById("worker_name").innerHTML=worker_name;
						document.getElementById("kab").innerHTML=kab;
						document.getElementById("month_date").innerHTML=day+\'.\'+month+\'.\'+year;
						document.getElementById("month_date_smena").innerHTML=smena
						
						var real_time_h = time/60|0;
						var real_time_m = time%60;
						if (real_time_m == 0) real_time_m = \'00\';
						
						var real_time_h_end = (time+30)/60|0;
						var real_time_m_end = (time+30)%60;
						if (real_time_m_end == 0) real_time_m_end = \'00\';
						
						document.getElementById("work_time_h").innerHTML=real_time_h;
						document.getElementById("work_time_m").innerHTML=real_time_m;
						
						document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
						document.getElementById("work_time_m_end").innerHTML=real_time_m_end;
						
						$.ajax({
							dataType: "json",
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "exist_zapis_here.php",
							// какие данные будут переданы
							data: {
								filial:filial,
								kab:kab,
								year:year,
								month:month,
								day:day,
								time:time,
								datatable:"'.$datatable.'"
							},
							// действие, при ответе с сервера
							success: function(exist_zapis){
								//alert (exist_zapis.req);
								if (exist_zapis.req == \'full\'){
									document.getElementById("exist_zapis").innerHTML=\'<span style="color: red">Всё занято</span>\';
									document.getElementById("Ajax_add_TempZapis").disabled = true;
								}else if (exist_zapis.req == \'hard\'){
									document.getElementById("exist_zapis").innerHTML=\'<span style="color: red">Всё занято</span>\';
									document.getElementById("Ajax_add_TempZapis").disabled = true;
								}else if (exist_zapis.req == \'light\'){
									document.getElementById("exist_zapis").innerHTML=\'<span style="color: green">Есть свободное время</span>\';
									document.getElementById("Ajax_add_TempZapis").disabled = false;
								}else{
									
									if (exist_zapis.time_start > 0){
										//alert (exist_zapis.time_start);
										document.getElementById("exist_zapis").innerHTML=exist_zapis.req + exist_zapis.time_start;
										
										//надо поменять время начала записи
										real_time_h = exist_zapis.time_start/60|0;
										real_time_m = exist_zapis.time_start%60;
										if (real_time_m == 0) real_time_m = \'00\';
										
										document.getElementById("work_time_h").innerHTML=real_time_h;
										document.getElementById("work_time_m").innerHTML=real_time_m;

										document.getElementById("start_time").value=exist_zapis.time_start;
										document.getElementById("wt").value=time+30-exist_zapis.time_start;
										//редактирование select
										//document.getElementById("PriemTime").getElementsByTagName("option")[15].selected = "selected"
										
									}else{
										document.getElementById("exist_zapis").innerHTML=exist_zapis.req;
									}
								}
								//if (exist_zapis != 0){
									
								
								//}else{
								//	document.getElementById("exist_zapis").innerHTML=0;
								//}
							}
						});	
						
					}
					
					function HideSettingsAddTempZapis(){
						$(\'#ShowSettingsAddTempZapis\').hide();
						$(\'#overlay\').hide();
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
					}';
			//}	
			echo '	
				</script>
			
			
			
				<script>  
					function changeStyle(idd){
						if ( $("#"+idd).prop("checked"))
							document.getElementById(idd+"_2").style.background = \'#83DB53\';
						else
							document.getElementById(idd+"_2").style.background = \'#F0F0F0\';
					}
					
					function PriemTimeCalc(wt){
						var start_time = Number(document.getElementById("start_time").value);
						//alert(wt);
						//alert(start_time);
						//alert(start_time+wt);
						
						var real_time_h_end = (start_time + Number(wt))/60|0;
						var real_time_m_end = (start_time + Number(wt))%60;
						if (real_time_m_end < 10) real_time_m_end = \'0\'+real_time_m_end;
						
						document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
						document.getElementById("work_time_m_end").innerHTML=real_time_m_end;
						
						document.getElementById("wt").value=wt;
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
					function Ajax_add_TempZapis() {
						 
						// получение данных из полей
						var filial = $(\'#filial\').val();
						var author = $(\'#author\').val();
						var year = $(\'#year\').val();
						var month = $(\'#month\').val();
						var day = $(\'#day\').val();
						
						var patient = $(\'#patient\').val();
						var contacts = $(\'#contacts\').val();
						var description = $(\'#description\').val();
						
						var start_time = $(\'#start_time\').val();
						var wt = $(\'#wt\').val();
						
						var kab = document.getElementById("kab").innerHTML;

						var worker = $(\'#worker_id\').val();
						if(typeof worker == "undefined") worker = 0;

						$.ajax({
							//statbox:SettingsScheduler,
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "edit_schedule_day_f.php",
							// какие данные будут переданы
							data: {
								type:"scheduler_stom",
								author:author,
								filial:filial,
								kab:kab,
								day:day,
								month:month,
								year:year,
								start_time:start_time,
								wt:wt,
								worker:worker,
								description:description,
								contacts:contacts,
								patient:patient,
								datatable:"'.$datatable.'"
							},
							// действие, при ответе с сервера
							success: function(data){
								document.getElementById("ShowSettingsAddTempZapis").innerHTML=data;
								window.scrollTo(0,0)
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>