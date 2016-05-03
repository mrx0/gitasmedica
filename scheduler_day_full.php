<?php

//scheduler.php
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
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
								<h2>Запись на '.$d.' ',$month_names[$m-1],' ',$y,' филиал '.$filial[0]['name'].' кабинет '.$kab.'</h2>
								<a href="scheduler_day.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$d.'&m='.$m.'&y='.$y.'" class="b">Вернуться назад</a><br />
								<a href="own_scheduler.php" class="b">График работы врачей</a> 
								<a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'" class="b">График работы филиала</a><br /><br />';
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
							
					$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $kab);
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
								$back_color = 'background-color: rgba(255,255,0, .5);';
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
							$end_time_m = ($ZapisHereQueryToday[$z]['start_time']+$ZapisHereQueryToday[$z]['wt'])%60;
							if ($end_time_m < 10) $end_time_m = '0'.$end_time_m;
							echo 
										$start_time_h.':'.$start_time_m.' - '.$end_time_h.':'.$end_time_m;
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Пациент <br /><b>'.$ZapisHereQueryToday[$z]['patient'].'</b><br />'.$ZapisHereQueryToday[$z]['contacts'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										$filial[0]['name'];
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										$ZapisHereQueryToday[$z]['kab'].' кабинет';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Врач <br /><b>'.WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['worker'], 'user').'</b>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										$ZapisHereQueryToday[$z]['description'];
							echo '
									</div>';
							echo '
									<div class="cellRight">';
							echo 
										'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 1)">Пришёл</a><br />
										<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 9)">Не пришёл</a><br />
										<a href="#" onclick="alert(\'Временно не работает\')">Редактировать</a><br />
										<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 8)">Ошибка, отметить на удаление</a><br />
										<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 0)">Отменить все изменения</a><br />
										';
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


			echo '	
				<script>  
					function Ajax_TempZapis_edit_Enter(id, enter) {
						 
						$.ajax({
							//statbox:SettingsScheduler,
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "ajax_tempzapis_edit_enter_f.php",
							// какие данные будут переданы
							data: {
								id:id,
								enter:enter,
								datatable:"'.$datatable.'"
							},
							// действие, при ответе с сервера
							success: function(data){
								//document.getElementById("req").innerHTML=data;
								window.location.href = "scheduler_day_full.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$d.'&m='.$m.'&y='.$y.'&kab='.$_GET['kab'].'";
							}
						});						
										
										

					};
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>