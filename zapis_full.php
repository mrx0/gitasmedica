<?php

//zapis_full.php
//Вся щапись на день 

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			$offices = SelDataFromDB('spr_office', '', '');
			//var_dump ($offices);

			
			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			
			$NextSmenaArr_Bool = FALSE;
			$NextSmenaArr_Zanimayu = 0;

			/*$zapis_times = array (
				0 => '0:00 - 0:30',
				30 => '0:30 - 1:00',
				60 => '1:00 - 1:30',
				90 => '1:30 - 2:00',
				120 => '2:00 - 2:30',
				150 => '2:30 - 3:00',
				180 => '3:00 - 3:30',
				210 => '3:30 - 4:00',	
				240 => '4:00 - 4:30',
				270 => '4:30 - 5:00',
				300 => '5:00 - 5:30',
				330 => '5:30 - 6:00',
				360 => '6:00 - 6:30',
				390 => '6:30 - 7:00',
				420 => '7:00 - 7:30',
				450 => '7:30 - 8:00',
				480 => '8:00 - 8:30',
				510 => '8:30 - 9:00',	
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
				1260 => '21:00 - 21:30',
				1290 => '21:30 - 22:00',
				1320 => '22:00 - 22:30',
				1350 => '22:30 - 23:00',
				1380 => '23:00 - 23:30',
				1410 => '23:30 - 00:00',
			);*/
			
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
				if (isset($_GET['kab']))
					$kab = $_GET['kab'];
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
				
				
				$filial = SelDataFromDB('spr_office', $_GET['filial'], 'offices');
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
								<h2>Запись пациентов на '.$d.' ',$month_names[$m-1],' ',$y,' филиал '.$filial[0]['name'].' кабинет '.$kab.'</h2>
								<a href="zapis.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$d.'&m='.$m.'&y='.$y.'" class="b">Запись</a>
								<a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'" class="b">График</a>
								<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
								<br><br>';
					/*echo '
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
								</form>';	*/
					echo '			
							</header>';
							
					echo '
					
							<div id="data">';
							
					$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $kab, $type);
					//var_dump($ZapisHereQueryToday);
					
					if ($ZapisHereQueryToday != 0){

						for ($z = 0; $z < count($ZapisHereQueryToday); $z++){
							$back_color = '';
							
							
							if ($ZapisHereQueryToday[$z]['enter'] == 1){
								$back_color = 'background-color: rgba(119, 255, 135, 1);';
							}elseif($ZapisHereQueryToday[$z]['enter'] == 9){
								$back_color = 'background-color: rgba(239,47,55, .7);';
							}elseif($ZapisHereQueryToday[$z]['enter'] == 8){
								$back_color = 'background-color: rgba(137,0,81, .7);';
							}else{
								//Если оформлено не на этом филиале
								if($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']){
									$back_color = 'background-color: rgb(119, 255, 250);';
								}else{
									$back_color = 'background-color: rgba(255,255,0, .5);';
								}
							}
							
							
							echo '
								<div class="cellsBlock">';
							/*echo '
									<div class="cellName" style="'.$back_color.'">';
							echo 
										$ZapisHereQueryToday[$z]['day'].' '.$month_names[$ZapisHereQueryToday[$z]['month']-1].' '.$ZapisHereQueryToday[$z]['year'];
							echo '
									</div>';*/
							echo '
									<div class="cellName" style="'.$back_color.'">';
							$start_time_h = floor($ZapisHereQueryToday[$z]['start_time']/60);
							$start_time_m = $ZapisHereQueryToday[$z]['start_time']%60;
							if ($start_time_m < 10) $start_time_m = '0'.$start_time_m;
							$end_time_h = floor(($ZapisHereQueryToday[$z]['start_time']+$ZapisHereQueryToday[$z]['wt'])/60);
							if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
							$end_time_m = ($ZapisHereQueryToday[$z]['start_time']+$ZapisHereQueryToday[$z]['wt'])%60;
							if ($end_time_m < 10) $end_time_m = '0'.$end_time_m;
							echo 
										$start_time_h.':'.$start_time_m.' - '.$end_time_h.':'.$end_time_m;
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Пациент <br /><b>'.WriteSearchUser('spr_clients', $ZapisHereQueryToday[$z]['patient'], 'user', true).'</b><br />'.$ZapisHereQueryToday[$z]['contacts'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Филиал:<br>'.
										$filial[0]['name'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										$ZapisHereQueryToday[$z]['kab'].' кабинет<br>'.'Врач: <br><b>'.WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['worker'], 'user', true).'</b>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Описание:<br>'.
										$ZapisHereQueryToday[$z]['description'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo '
										Добавлено<br>'.date('d.m.y H:i', $ZapisHereQueryToday[$z]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['create_person'], 'user', true);
							if (($ZapisHereQueryToday[$z]['last_edit_time'] != 0) || ($ZapisHereQueryToday[$z]['last_edit_person'] != 0)){
								echo '<hr>
										Изменено: '.date('d.m.y H:i', $ZapisHereQueryToday[$z]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['last_edit_person'], 'user', true).'';
							}
							echo '
									</div>';
							echo '
									<div class="cellRight">';									
							if (isset($_SESSION['filial'])){
								if ($_SESSION['filial'] == $ZapisHereQueryToday[$z]['office']){
									if($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']){
										echo '
												<a href="#" onclick="Ajax_TempZapis_edit_OK('.$ZapisHereQueryToday[$z]['id'].', '.$ZapisHereQueryToday[$z]['office'].')">Подтвердить</a><br />';
									}
									echo 
												'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 1)">Пришёл</a><br />
												<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 9)">Не пришёл</a><br />
												<a href="#" onclick="alert(\'Временно не работает\')">Редактировать</a><br />
												<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 8)">Ошибка, отметить на удаление</a><br />
												<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 0)">Отменить все изменения</a><br />
												';
								}
							}
							echo '
									</div>';
							echo '
								</div>
								<div id="req"></div>';
						}
					}else{
						echo 'Нет записи';
					}
				}
			}else{
				echo '
					<div id="status">
						<header>
';
				echo '			
				</header>';
			}

			echo '
					</div>
				</div>';


					
			echo '	
						
					</div>';					
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>