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
						$whose = 'Стоматологи ';
						$selected_stom = ' selected';
						$selected_cosm = ' ';
						$datatable = 'scheduler_stom';
						$kabsForDoctor = 'stom';
						$type = 5;
					}elseif($_GET['who'] == 'cosm'){
						$who = '&who=cosm';
						$whose = 'Косметологи ';
						$selected_stom = ' ';
						$selected_cosm = ' selected';
						$datatable = 'scheduler_cosm';
						$kabsForDoctor = 'cosm';
						$type = 6;
					}else{
						$who = '&who=stom';
						$whose = 'Стоматологи ';
						$selected_stom = ' selected';
						$selected_cosm = ' ';
						$datatable = 'scheduler_stom';
						$kabsForDoctor = 'stom';
						$type = 5;
					}
				}else{
					$who = '&who=stom';
					$whose = 'Стоматологи ';
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
								<h2>Запись '.$d.' ',$month_names[$m-1],' ',$y,'</h2>
								<b>Филиал</b> '.$filial[0]['name'].'<br>
								<b>Кабинет '.$kab.'</b><br>
								<span style="color: green; font-size: 120%; font-weight: bold;">'.$whose.'</span><br>
								<a href="zapis.php?filial='.$_GET['filial'].''.$who.'&d='.$d.'&m='.$m.'&y='.$y.'" class="b">Запись</a>
								<a href="scheduler.php?filial='.$_GET['filial'].''.$who.'" class="b">График</a>
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
							
					echo '		
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?filial='.$_GET['filial'].'&who=stom&d='.$d.'&m='.$m.'&y='.$y.'&kab='.$kab.'" class="b">Стоматологи</a>
								<a href="?filial='.$_GET['filial'].'&who=cosm&d='.$d.'&m='.$m.'&y='.$y.'&kab='.$kab.'" class="b">Косметологи</a>
							</li>';
							
					$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $kab, $type);
					//var_dump($ZapisHereQueryToday);
					
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
					if ($kabsInFilialExist){
						echo '		
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите кабинет</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">';
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							$kab_color = '';
							if ($k == $kab){
								$kab_color = ' background-color: #fff261;';
							}
							echo '		
								<a href="?filial='.$_GET['filial'].''.$who.'&d='.$d.'&m='.$m.'&y='.$y.'&kab='.$k.'" class="b" style="'.$kab_color.'">каб '.$k.'</a>';
						}
						echo '</li>';
					}
					
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
							
							$dop_img = '';
							
							if ($ZapisHereQueryToday[$z]['insured'] == 1){
								$dop_img .= '<img src="img/insured.png" title="Страховое"> ';
							}
							if ($ZapisHereQueryToday[$z]['pervich'] == 1){
								$dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
							}
							if ($ZapisHereQueryToday[$z]['noch'] == 1){
								$dop_img .= '<img src="img/night.png" title="Ночное"> ';
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
									<div class="cellName" style="position: relative; '.$back_color.'">';
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
										<div style="position: absolute; top: 1px; right: 1px;">'.$dop_img.'</div>';
							echo '
									</div>';
							echo '
									<div class="cellName">';
							echo 
										'Пациент <br /><b>'.WriteSearchUser('spr_clients', $ZapisHereQueryToday[$z]['patient'], 'user', true).'</b>';
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
										if($ZapisHereQueryToday[$z]['enter'] != 8){
											echo '<a href="#" onclick="Ajax_TempZapis_edit_OK('.$ZapisHereQueryToday[$z]['id'].', '.$ZapisHereQueryToday[$z]['office'].')">Подтвердить</a><br />';
										}
									}
									if($ZapisHereQueryToday[$z]['office'] == $ZapisHereQueryToday[$z]['add_from']){
										if($ZapisHereQueryToday[$z]['enter'] != 8){
											echo 
													'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 1)">Пришёл</a><br />';
											echo 
													'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 9)">Не пришёл</a><br />';
											/*echo 
													'<a href="#" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$y.', '.$m.','.$d.', 1, '.$ZapisHereQueryToday[$z]['start_time'].', '.$ZapisHereQueryToday[$z]['wt'].', '.$ZapisHereQueryToday[$z]['worker'].', \''.WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['worker'], 'user_full', false).'\')">Редактировать</a><br />';
											*/
											//var_dump($ZapisHereQueryToday[$z]['create_time']);
											//var_dump(time());
											$zapisDate = strtotime($ZapisHereQueryToday[$z]['day'].'.'.$ZapisHereQueryToday[$z]['month'].'.'.$ZapisHereQueryToday[$z]['year']);
											if (time() < $zapisDate + 60*60*24){
												echo 
													'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 8)">Ошибка, удалить из записи</a><br>';
											}
										}
										echo 
													'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 0)">Отменить все изменения</a><br>';
									}
								}else{
									echo 
										'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 8)">Ошибка, удалить из записи</a><br>';
									echo 
										'<a href="#" onclick="Ajax_TempZapis_edit_Enter('.$ZapisHereQueryToday[$z]['id'].', 0)">Отменить все изменения</a><br>';
								}
							}
							
			echo '
					<div id="ShowSettingsAddTempZapis" style="position: absolute; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; z-index:105; padding:10px;">
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
										<input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО врача" value="" class="who2"  autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</div>

								<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Пациент</div>
									<div class="cellRight">
										<input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off"> <a href="add_client.php" class="ahref"><i class="fa fa-plus-square" title="Добавить пациента" style="color: green; font-size: 120%;"></i></a>
										<ul id="search_result" class="search_result"></ul><br />
									</div>
								</div>
								<!--<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Телефон</div>
									<div class="cellRight" style="">
										<input type="text" size="30" name="contacts" id="contacts" placeholder="Введите телефон" value="" autocomplete="off">
									</div>
								</div>-->
								<div class="cellsBlock2" style="font-size:80%; width:400px;">
									<div class="cellLeft" style="font-weight: bold;">Описание</div>
									<div class="cellRight" style="">
										<textarea name="description" id="description" style="width:90%; overflow:auto; height: 100px;"></textarea>
									</div>
								</div>		
							</div>';
			echo '
							<div style="display:inline-block; vertical-align: top; width: 360px; border: 1px solid #C1C1C1;">
								<div id="ShowTimeSettingsHere">
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Время начала</div>
									<div class="cellRight">
										<!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
										
										<input type="number" size="2" name="work_time_h" id="work_time_h" min="0" max="23" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
										<input type="number" size="2" name="work_time_m" id="work_time_m" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Длительность</div>
									<div class="cellRight">
										<!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->

										<input type="number" size="2" name="change_hours" id="change_hours" min="0" max="11" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
										<input type="number" size="2" name="change_minutes" id="change_minutes" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
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
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellRight">
										<div id="errror"></div>
									</div>
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
						<!--<input type="button" class="b" value="Добавить" id="Ajax_add_TempZapis" onclick="Ajax_add_TempZapis('.$type.')">-->
						<input type="button" class="b" value="OK" onclick="if (iCanManage) Ajax_add_TempZapis('.$type.')" id="Ajax_add_TempZapis">
						<input type="button" class="b" value="Отмена" onclick="HideSettingsAddTempZapis()">
					</div>';	
							
							
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
					<script>';
					
			if (($zapis['add_new'] == 1) || $god_mode){
				echo '		
					function ShowSettingsAddTempZapis(filial, filial_name, kab, year, month, day, smena, time, period, worker_id, worker_name){
						document.getElementById("errror").innerHTML="";
						//alert(period);
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
						document.getElementById("wt").value=period;
						document.getElementById("worker_id").value=worker_id;
						
						document.getElementById("filial_name").innerHTML=filial_name;
						if (worker_id == 0){
							document.getElementById("search_client2").value = "";
						}else{
							document.getElementById("search_client2").value = worker_name;
						}
						document.getElementById("kab").innerHTML=kab;
						document.getElementById("month_date").innerHTML=day+\'.\'+month+\'.\'+year;
						document.getElementById("month_date_smena").innerHTML=smena
						
						
						document.getElementById("change_minutes").value = period;
						
						var real_time_h = time/60|0;
						var real_time_m = time%60;
						if (real_time_m < 10) real_time_m = "0"+real_time_m;
						
						var real_time_h_end = (time+period)/60|0;
						if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
						var real_time_m_end = (time+period)%60;
						if (real_time_m_end < 10) real_time_m_end = \'0\'+real_time_m_end;
						
						//document.getElementById("work_time_h").innerHTML=real_time_h;
						//document.getElementById("work_time_m").innerHTML=real_time_m;

						document.getElementById("work_time_h").value=real_time_h;
						document.getElementById("work_time_m").value=real_time_m;
						
						document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
						document.getElementById("work_time_m_end").innerHTML=real_time_m_end;
						
						var next_time_start_rez = 0;
						
						$.ajax({
								dataType: "json",
								async: false,
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "get_next_zapis.php",
								// какие данные будут переданы
								data: {
									day:day,
									month:month,
									year:year,
									
									filial:filial,
									kab:kab,
									
									start_time:time,
									
									datatable:"zapis"
								},
								// действие, при ответе с сервера
								success: function(next_zapis_data){
									//alert (next_zapis_data.next_time_start);
									//document.getElementById("kab").innerHTML=nex_zapis_data;
									next_time_start_rez = next_zapis_data.next_time_start;
									next_time_end_rez = next_zapis_data.next_time_end;
									//next_zapis_data;
									
								}
						});
						
						//alert(next_time_start_rez);
						
						if (next_time_start_rez != 0){
						
							//if ((time+period > next_time_start_rez) || (time == next_time_start_rez)){
							if (((time+period > next_time_start_rez) && (time+period < next_time_end_rez)) || ((time >= next_time_start_rez) && (time < next_time_end_rez))){
								//document.getElementById("exist_zapis").innerHTML=\'<span style="color: red">Дальше есть запись</span>\';
								
								var raznica_vremeni = Math.abs(next_time_start_rez - time);
								
								document.getElementById("change_hours").value = raznica_vremeni/60|0;
								document.getElementById("change_minutes").value = raznica_vremeni%60;
								
								change_hours = raznica_vremeni/60|0;
								change_minutes = raznica_vremeni%60;
								
								var end_time = time+change_hours*60+change_minutes;
								
						
								var real_time_h_end = end_time/60|0;
								if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
								var real_time_m_end = end_time%60;
								if (real_time_m_end < 10) real_time_m_end = "0"+real_time_m_end;
								
								document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
								document.getElementById("work_time_m_end").innerHTML=real_time_m_end;
								
								document.getElementById("wt").value=change_hours*60+change_minutes;
								
								document.getElementById("Ajax_add_TempZapis").disabled = true; 
							}else{
							//if (time+period < next_time_start_rez){
								document.getElementById("exist_zapis").innerHTML="";
								document.getElementById("Ajax_add_TempZapis").disabled = false; 
							}
						}else{
							document.getElementById("exist_zapis").innerHTML="";
							document.getElementById("Ajax_add_TempZapis").disabled = false; 
						}
						

						
					}
					
					function HideSettingsAddTempZapis(){
						$(\'#ShowSettingsAddTempZapis\').hide();
						$(\'#overlay\').hide();
						document.getElementById("wt").value = 0;
						document.getElementById("change_hours").value = 0;
						document.getElementById("change_minutes").value = 30;
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
			}	
			echo '	
				</script>';
			
			
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>