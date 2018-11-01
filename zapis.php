<?php

//zapis.php
//Запись на филиале

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($zapis['see_all'] == 1) || ($zapis['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
            include_once 'variables.php';

			$offices_j = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices_j);

            $office_j_arr = array();

            foreach ($offices_j as $office_item){
                $office_j_arr[$office_item['id']] = $office_item;
            }

			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			$dopWho = '';
			$dopDate = '';
			$dopFilial = '';
			
			$NextSmenaArr_Bool = FALSE;
			$NextSmenaArr_Zanimayu = 0;

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
			
			$zapis_times = array (
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
			);

			//Права для передачи в ф-ции
            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

            if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
                $finance_edit = true;
                $edit_options = true;
            }

            if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode){
                $stom_edit = true;
                $edit_options = true;
            }
            if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode){
                $cosm_edit = true;
                $edit_options = true;
            }

            if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                $admin_edit = true;
                $edit_options = true;
            }

            if (($scheduler['see_all'] == 1) || $god_mode){
                $upr_edit = true;
                $edit_options = true;
            }

            if (isset($_SESSION['filial'])) {
                $contexMenuZapisMain_filial = $_SESSION['filial'];
            }else{
                $contexMenuZapisMain_filial = 0;
            }

			//!!!!
			/*if(isset($_GET['filial'])){
				if ($_GET['filial'] == 0) $_GET['filial'] = 15;
				$selected_fil = $_GET['filial'];
			}*/
			
			if (!isset($_GET['filial'])){
				//Филиал	
				if (isset($_SESSION['filial'])){
					$_GET['filial'] = $_SESSION['filial'];
				}else{
					$_GET['filial'] = 15;
				}
			}
			
			/*$who = '&who=stom';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';*/
			
			//if ($_GET){
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
						
						$stom_color = 'background-color: #fff261;';
						$cosm_color = '';
                        $somat_color = '';
					}elseif($_GET['who'] == 'cosm'){
						$who = '&who=cosm';
						$whose = 'Косметологи ';
						$selected_stom = ' ';
						$selected_cosm = ' selected';
						$datatable = 'scheduler_cosm';
						$kabsForDoctor = 'cosm';
						$type = 6;
						
						$stom_color = '';
						$cosm_color = 'background-color: #fff261;';
                        $somat_color = '';
                    }elseif($_GET['who'] == 'somat'){
                        $who = '&who=somat';
                        $whose = 'Специалисты ';
                        $selected_stom = ' ';
                        $selected_cosm = ' selected';
                        $datatable = 'scheduler_somat';
                        $kabsForDoctor = 'somat';
                        $type = 10;

                        $stom_color = '';
                        $cosm_color = '';
                        $somat_color = 'background-color: #fff261;';
					}else{
						$who = '&who=stom';
						$whose = 'Стоматологи ';
						$selected_stom = ' selected';
						$selected_cosm = ' ';
						$datatable = 'scheduler_stom';
						$kabsForDoctor = 'stom';
						$type = 5;
						$_GET['who'] = 'stom';
						
						$stom_color = 'background-color: #fff261;';
						$cosm_color = '';
                        $somat_color = '';
					}
				}else{
					$who = '&who=stom';
					$whose = 'Стоматологи ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
					$_GET['who'] = 'stom';
						
					$stom_color = 'background-color: #fff261;';
					$cosm_color = '';
                    $somat_color = '';
				}
				
				if (isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
					//операции со временем						
					$day = $_GET['d'];
					$month = $_GET['m'];
					$year = $_GET['y'];
				}else{
					//операции со временем						
					$day = date('d');		
					$month = date('m');		
					$year = date('Y');
				}

				if (!isset($day) || $day < 1 || $day > 31)
					$day = date("d");				
				if (!isset($month) || $month < 1 || $month > 12)
					$month = date("m");
				if (!isset($year) || $year < 2010 || $year > 2037)
					$year = date("Y");

				/*$month_stamp = mktime(0, 0, 0, $month, 1, $year);
				
				$day_count = date("t",$month_stamp);
				
				$weekday = date("w", $month_stamp);
				if ($weekday == 0)
					$weekday = 7;
				$start = -($weekday-2);
				$last = ($day_count + $weekday - 1) % 7;
				if ($last == 0) 
					$end = $day_count; 
				else 
					$end = $day_count + 7 - $last;*/
				
			
			foreach ($_GET as $key => $value){
				if (($key == 'd') || ($key == 'm') || ($key == 'y'))
					$dopDate  .= '&'.$key.'='.$value;
				if ($key == 'filial')
					$dopFilial .= '&'.$key.'='.$value;
				if ($key == 'who')
					$dopWho .= '&'.$key.'='.$value;
			}
				
				/*$today = date("Y-m-d");
				$go_today = date('?\d=d&\m=m&\y=Y'.$dopFilial.$dopWho, mktime (0, 0, 0, date("m"), date("d"), date("Y"))); 
				
				/*$prev = date('?\d=d&\m=m&\y=Y'.$dopFilial.$dopWho, mktime (0, 0, 0, $month, $day-1, $year));  
				$next = date('?\d=d&\m=m&\y=Y'.$dopFilial.$dopWho, mktime (0, 0, 0, $month, $day+1, $year));*/
				
				/*if(isset($_GET['filial'])){
					$prev .= '&filial='.$_GET['filial']; 
					$next .= '&filial='.$_GET['filial'];
					$go_today .= '&filial='.$_GET['filial'];
					
					$selected_fil = $_GET['filial'];
				}
				$i = 0;*/
				
				
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
				
				//переменная, чтоб вкл/откл редактирование
				echo '
					<script>
						var iCanManage = true;
					</script>
				';
				
				//if ($filial != 0){
					echo '
						<div id="status">
							<header>
								<div class="nav">
									<a href="scheduler.php?'.$dopFilial.$dopWho.$dopDate.'" class="b">График</a>
									<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
								</div>
							
								<h2>Запись '.$day.' ',$monthsName[$month],' ',$year,'</h2>
								<b>Филиал</b> '.$filial[0]['name'].'<br>
								<span style="color: green; font-size: 120%; font-weight: bold;">'.$whose.'</span><br>
							</header>';

					echo '
							<div id="data" style="margin: 0">
								<ul style="margin-left: 6px; margin-bottom: 20px;">';
					if (($zapis['edit'] == 1) || $god_mode){
						echo '
									<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
										<div style="cursor: pointer;" onclick="manageZapis()">
											<!--<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>-->
										</div>
									</li>';
					}
					
					if (isset($_SESSION['filial'])){

                        $msql_cnnct = ConnectToDB();
						
						$arr = array();
						$arr2 = array();
						$rez = array();
						$rez2 = array();
						
						$query = "SELECT * FROM `zapis` WHERE `office` = '{$_SESSION['filial']}' AND `add_from` <> '{$_SESSION['filial']}' AND `enter` <> '8'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($rez, $arr);
							}
						}else{
							$rez = 0;
						}
						
						$query = "SELECT * FROM `zapis` WHERE `add_from` = '{$_SESSION['filial']}' AND `office` <> '{$_SESSION['filial']}' AND `enter` <> '8'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr2 = mysqli_fetch_assoc($res)){
								array_push($rez2, $arr2);
							}
						}else{
							$rez2 = 0;
						}
						
						//mysql_close();
						//var_dump($rez);
						if (($rez != 0) || ($rez2 != 0)){
							echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас есть неподтвёрждённые записи</span><br>';
						}
						if ($rez != 0){
							echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><b>Вам</b></span><br>';
							foreach ($rez as $val){
								if ($val['type'] == 5)
									$who = '&who=stom';
								if ($val['type'] == 6)
									$who = '&who=cosm';
								
								if ($val['day'] < 10) $val['day'] = '0'.$val['day'];
								if ($val['month'] < 10) $val['month'] = '0'.$val['month'];
								
								echo '
								<li class="cellsBlock" style="width: auto; margin-bottom: 5px;">
									<a href="zapis_full.php?filial='.$val['office'].''.$who.'&d='.$val['day'].'&m='.$val['month'].'&y='.$val['year'].'&kab='.$val['kab'].'" style="text-decoration: none; border-bottom: 1px dashed #000080;">'.$val['day'].'.'.$val['month'].'.'.$val['year'].' показать</a>
								</li>';							
							}
						}
						if ($rez2 != 0){
							echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><b>Ваши</b></span><br>';
							foreach ($rez2 as $val){
								if ($val['type'] == 5)
									$who = '&who=stom';
								if ($val['type'] == 6)
									$who = '&who=cosm';
								if ($val['type'] == 10)
									$who = '&who=somat';
								echo '
								<li class="cellsBlock" style="width: auto; margin-bottom: 5px;">
									<a href="zapis_full.php?filial='.$val['office'].''.$who.'&d='.$val['day'].'&m='.$val['month'].'&y='.$val['year'].'&kab='.$val['kab'].'" style="text-decoration: none; border-bottom: 1px dashed #000080;">'.$val['day'].'.'.$val['month'].'.'.$val['year'].' показать</a>
								</li>';							
							}
						}
					}else{
						echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
					}
					
			echo '			
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?'.$dopFilial.$dopDate.'&who=stom" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="?'.$dopFilial.$dopDate.'&who=cosm" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="?'.$dopFilial.$dopDate.'&who=somat" class="b" style="'.$somat_color.'">Специалисты</a>
							</li>
							<li class="cellsBlock" style="width: auto; margin-bottom: 20px;">
								<div style="display: inline-block; margin-right: 20px;">
									<div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
										Выберите филиал
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
										<a href="?'.$who.'" class="dotyel" style="font-size: 80%;">Сбросить</a>
									</div>
								</div>
							</li>';
					//Календарик	
					echo '
	
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
									<div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?'.$dopFilial.$dopWho.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
									<div>
										<span style="color: rgb(125, 125, 125);">
											Изменить дату:
											<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
											<span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'zapis.php?'.$dopFilial.$dopWho.'\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
										</span>
									</div>
								</li>';
							
					
					if ($kabsInFilialExist){
						
						$Work_Today_arr = array();
						
						$Work_Today = FilialWorker	($type, $year, $month, $day, $_GET['filial']);
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
						
						echo '
							<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
								<ul>
									<li><a href="#tabs-1" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-1\');">1 смена</a></li>
									<li><a href="#tabs-2" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-2\');">2 смена</a></li>
									<li><a href="#tabs-3" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-3\');">3 смена</a></li>
									<li><a href="#tabs-4" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-4\');">4 смена</a></li>
								</ul>';
						
						
						$NextSmenaArr = array();
						//$NextSmenaFill = FALSE;
						//$PrevSmenaZapis = array();
						
						//смена 1	
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;
						echo '
						<div id="tabs-1">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
							<td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
								<div class="" style="border: none; width: 100%;  height: 50px;">
									
								</div>
								<div style="position:relative; height: 720px;">';

						for ($wt=540; $wt < 900; $wt=$wt+30){
							
							echo '
								<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
									'.$zapis_times[$wt].'';
							//var_dump($NextFill);
							echo '
								</div>';
							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						echo '</div>
							</td>';
						
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';
							
							if (isset($Work_Today_arr[$k][1])){
								//var_dump($Kab_work_today_smena1);

								$worker = $Work_Today_arr[$k][1]['worker'];
								
								echo '
									<div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
										<div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
											1 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker, 'user', false).'</i>
											<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
										</div>
									</div>';
							}else{
								
								$worker = 0;
								
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
												1 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].''.$who.'">графику</a></span>
												<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
											</div>
										</div>';
							}
							
								echo '
									<div style="position:relative; height: 720px;">';
								//Выбрать записи пациентов, если есть
								//$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $k);
								//var_dump ($ZapisHereQueryToday);
								
								$NextTime = FALSE;
								$ThatTimeFree = TRUE;
								$PeredannNextTime = FALSE;
								$NextTime_val = 0;
								//сдвиг для блоков времени
								$cellZapisTime_TopSdvig = 0;
								$cellZapisValue_TopSdvig = 0;
								$PrevZapis = array();
								$NextFill = FALSE;

								for ($wt=540; $wt < 900; $wt=$wt+30){
									
									if (isset($Work_Today_arr[$k][1])){
										$bg_color = '';
									}else{
										$bg_color = ' background-color: #f0f0f0;';
									}
									
									$back_color = '';
									/*echo '
										<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;" onclick="window.location.href = \'zapis_full.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'\'">
											'.$zapis_times[$wt].'';
									//var_dump($NextFill);
									echo '
										</div>';*/
									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
									
									//Выбрать записи пациентов, если есть
									$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $_GET['filial'], $k, $wt, $type);
									//var_dump ($ZapisHereQueryToday);
									
									if ($ZapisHereQueryToday != 0){
										//Если тут записей больше 1
										if (count($ZapisHereQueryToday) > 1){
											foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
												if ($ZapisHereQueryToday_val['start_time'] < 900){
													//вычисляем время начала приёма
													$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
													$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
													//вычисляем время окончания приёма
													$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
													$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
													//Сдвиг для блока
													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-540)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
													//Высота блока
													$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
													//Если от начала работы окончание выходит за предел промежутка времени
													if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
														$NextFill = TRUE;
														//$PrevZapis = $ZapisHereQueryToday_val;
													}
													
													//Если перед первой работой от начала промежутка есть свободное время
													if ($Zapis_key == 0){
														if ($ZapisHereQueryToday_val['start_time'] > $wt){
															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
															$wt_start_FreeSpace = $wt;
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
															echo '
																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																	';
																//var_dump($Zapis_key);
															echo '
																</div>';
														}
													}else{
														//если последняя запись
														if ($Zapis_key == count($ZapisHereQueryToday)-1){
															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
															$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
															echo '
																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																	';
																//var_dump($Zapis_key);
															echo '
																</div>';
															//если есть свободное место после последней записи
															if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
																$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
																$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
															//Если залазит на следующий отрезок времени
															}else{
																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
																	$NextFill = TRUE;
																	//$PrevZapis = $ZapisHereQueryToday_val;
																}
															}	
															
														}else{
															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
															$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
															echo '
																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																	.';
																//var_dump($Zapis_key);
															echo '
																</div>';
														}
													}
													
													//Если время выполнения работы больше чем осталось до конца смены
													if ($wt == 870){
														if ($cellZapisValue_Height > 60){
															$cellZapisValue_Height = 60;
														}
													}
													//Если пришёл
													if ($ZapisHereQueryToday_val['enter'] == 1){
														$back_color = 'background-color: rgba(119, 255, 135, 1);';
													}else{
														//Если оформлено не на этом филиале
														if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
															$back_color = 'background-color: rgb(119, 255, 250);';
														}
													}

                                                    $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                                    $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
                                                    $title_descr = $ZapisHereQueryToday_val['description'];
                                                    $zapis_id = $ZapisHereQueryToday_val['id'];

                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                    //Контекстная менюшка
                                                    //echo contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false);

                                                    /*echo '
														</div>';*/
												}else{
												}
												//Передать предыдущую запись
												$PrevZapis = $ZapisHereQueryToday_val;
											}
										}elseif (count($ZapisHereQueryToday) == 1){
											if ($ZapisHereQueryToday[0]['start_time'] < 900){
												//вычисляем время начала приёма
												$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
												$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
												if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
												//вычисляем время окончания приёма
												$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
												if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
												$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
												if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
												//Сдвиг для блока
												$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-540)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-540)%30*2);
												//Высота блока
												$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
												//Если от начала работы окончание выходит за предел промежутка времени
												if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
													$NextFill = TRUE;
													//$PrevZapis = $ZapisHereQueryToday[0];
												}
												//Если работа закончится до окончания периода
												if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
													$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
													$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
													$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
													echo '
														<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
															';
														//var_dump($NextFill);
													echo '
														</div>';
												}
												//Если работа начнется не с начала периода
												if ($ZapisHereQueryToday[0]['start_time'] > $wt){
													if (!$NextFill){
														$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
														$wt_start_FreeSpace = $wt;
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
													}else{
														$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
														$wt_start_FreeSpace = $wt;
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
														
														/*if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
															$NextFill = FALSE;
															$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
															$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
															echo '
																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', 'add')">
																	4';
																//var_dump($NextFill);
															echo '
																</div>';
														}*/
														
													}
													echo '
														<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
															';
														//var_dump($NextFill);
													echo '
														</div>';
												}
												
												//Если время выполнения работы больше чем осталось до конца смены
												if ($wt == 870){
													if ($cellZapisValue_Height > 60){
														$cellZapisValue_Height = 60;
														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
													}
													//добавлено 2016.12.23
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > 900){
														$cellZapisValue_Height = (900-$ZapisHereQueryToday[0]['start_time'])*2;
														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
														if ($NextSmenaArr[$k]['OstatokVremeni'] > 720){
															$NextSmenaArr[$k]['OstatokVremeni'] = 720;
														}
													}
												}else{
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > 900){
														$cellZapisValue_Height = (900-$ZapisHereQueryToday[0]['start_time'])*2;
														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
														if ($NextSmenaArr[$k]['OstatokVremeni'] > 720){
															$NextSmenaArr[$k]['OstatokVremeni'] = 720;
														}
													}
												}
												if ($ZapisHereQueryToday[0]['enter'] == 1){
													$back_color = 'background-color: rgba(119, 255, 135, 1);';
												}else{
													//Если оформлено не на этом филиале
													if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
														$back_color = 'background-color: rgb(119, 255, 250);';
													}
												}

                                                $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                                $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
                                                $title_descr = $ZapisHereQueryToday[0]['description'];
                                                $zapis_id = $ZapisHereQueryToday[0]['id'];

                                                echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                //Контекстная менюшка
                                                //echo contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false);

                                                /*echo '
														</div>';*/
											}else{
											}
											//Передать предыдущую запись
											$PrevZapis = $ZapisHereQueryToday[0];
										}
									//если тут вообще нет записи
									}else{
										if (!$NextFill){
											$wt_FreeSpace = 30;
											$wt_start_FreeSpace = $wt;
											$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
											$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
											echo '
												<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
													';
												//var_dump($NextFill);
											echo '
												</div>';
										}
									}
									//Если предыдущее время залазит на это
									if ($NextFill){
										if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
											$NextFill = FALSE;
											$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
											$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
											$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
											$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
											echo '
												<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
													';
												//var_dump($NextFill);
											echo '
												</div>';
										}
									}
								}
								echo '</div>';
							/*}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none;">
												1 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">графику</a></span>
											</div>
										</div>';
							}*/

							echo '
									</td>';
						}
						echo '
									</tr>
								</table>
							</div>';
								
						//смена 2
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;
						echo '
						<div id="tabs-2">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
							<td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
								<div class="" style="border: none; width: 100%;  height: 50px;">
									
								</div>
								<div style="position:relative; height: 720px;">';

						for ($wt=900; $wt < 1260; $wt=$wt+30){
							
							echo '
								<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
									'.$zapis_times[$wt].'';
							//var_dump($NextFill);
							echo '
								</div>';
							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						echo '</div>
							</td>';
						
						
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';
							
							if (isset($Work_Today_arr[$k][2])){
								//var_dump($Kab_work_today_smena1);

								$worker = $Work_Today_arr[$k][2]['worker'];
								
								echo '
									<div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
										<div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
											2 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker, 'user', false).'</i>
											<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
										</div>
									</div>';
							}else{

								$worker = 0;
								
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
												2 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].''.$who.'">графику</a></span>
												<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
											</div>
										</div>';
							}
							echo'
										<div style="position:relative; height: 720px;">';
								//Выбрать записи пациентов, если есть
								//$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $k);
								//var_dump ($ZapisHereQueryToday);
								
								$NextTime = FALSE;
								$ThatTimeFree = TRUE;
								$PeredannNextTime = FALSE;
								$NextTime_val = 0;
								//сдвиг для блоков времени
								$cellZapisTime_TopSdvig = 0;
								$cellZapisValue_TopSdvig = 0;
								$PrevZapis = array();
								$NextFill = FALSE;
								
								for ($wt=900; $wt < 1260; $wt=$wt+30){
									if (isset($Work_Today_arr[$k][2])){
										$bg_color = '';	
									}else{
										$bg_color = ' background-color: #f0f0f0;';
									}
											
									$back_color = '';
									/*echo '
										<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;" onclick="window.location.href = \'zapis_full.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'\'">
											'.$zapis_times[$wt].'';
									//var_dump($NextFill);
									echo '
										</div>';*/
									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
									
									//Выбрать записи пациентов, если есть
									$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $_GET['filial'], $k, $wt, $type);
									//var_dump ($ZapisHereQueryToday);
									
									if (isset($NextSmenaArr[$k]['NextSmenaFill']) && !$NextSmenaArr_Bool){
										//var_dump(4);
										if($NextSmenaArr[$k]['NextSmenaFill']){
											//var_dump(5);
											//надо перескочить промежуток
											$NextSmenaArr_Bool = TRUE;
											$NextSmenaArr_Zanimayu = $NextSmenaArr[$k]['OstatokVremeni']/2;
											
											//$NextFill = TRUE;
											$PrevZapis = $NextSmenaArr[$k]['ZapisHereQueryToday'];
											
											//вычисляем время начала приёма
											$TempStartWorkTime_h = floor($PrevZapis['start_time']/60);
											$TempStartWorkTime_m = $PrevZapis['start_time']%60;
											if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
											//вычисляем время окончания приёма
											$TempEndWorkTime_h = floor(($PrevZapis['start_time']+$PrevZapis['wt'])/60);
											if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
											$TempEndWorkTime_m = ($PrevZapis['start_time']+$PrevZapis['wt'])%60;
											if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
											//Сдвиг для блока
											$cellZapisValue_TopSdvig = 0;
											//Высота блока
											$cellZapisValue_Height = $NextSmenaArr[$k]['OstatokVremeni'];
											if ($cellZapisValue_Height > 720)
												$cellZapisValue_Height = 720;
											
											if ($NextSmenaArr[$k]['ZapisHereQueryToday']['enter'] == 1){
												$back_color = 'background-color: rgba(119, 255, 135, 1);';
											}else{
												//Если оформлено не на этом филиале
												if($NextSmenaArr[$k]['ZapisHereQueryToday']['office'] != $NextSmenaArr[$k]['ZapisHereQueryToday']['add_from']){
													$back_color = 'background-color: rgb(119, 255, 250);';
												}
											}

                                            $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                            $title_client = WriteSearchUser('spr_clients', $NextSmenaArr[$k]['ZapisHereQueryToday']['patient'], 'user', false);
                                            $title_descr = $NextSmenaArr[$k]['ZapisHereQueryToday']['description'];
                                            $zapis_id = $NextSmenaArr[$k]['ZapisHereQueryToday']['id'];

                                            echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                            //Контекстная менюшка
                                            //echo contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false);

                                            /*echo '
														</div>';*/
										}
										//$NextSmenaArr = array();
									}
									//var_dump(6);
									//var_dump($PrevZapis);
									
									//!!! Доделать, если "рваная" запись залазит (не кратное 30 минутам)
									
									//var_dump(!$NextSmenaArr_Bool);
									//var_dump(((900+$NextSmenaArr_Zanimayu)-$wt)%30);
									//var_dump($NextSmenaArr_Zanimayu);
									//var_dump($ZapisHereQueryToday);

									//if (!$NextSmenaArr_Bool || ($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))){
										if ($ZapisHereQueryToday != 0){
											//var_dump(2);
											
											//Если тут записей больше 1
											if (count($ZapisHereQueryToday) > 1){
												foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
													if ($ZapisHereQueryToday_val['start_time'] < 1260){
														//вычисляем время начала приёма
														$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
														$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
														if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
														//вычисляем время окончания приёма
														$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
														if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
														$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
														if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
														//Сдвиг для блока
														$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-900)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
														//Высота блока
														$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
														//Если от начала работы окончание выходит за предел промежутка времени
														if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
															$NextFill = TRUE;
															//$PrevZapis = $ZapisHereQueryToday_val;
														}
														//Если перед первой работой от начала промежутка есть свободное время
														if ($Zapis_key == 0){
															if ($ZapisHereQueryToday_val['start_time'] > $wt){
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
																$wt_start_FreeSpace = $wt;
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
															}
														}else{
															//если последняя запись
															if ($Zapis_key == count($ZapisHereQueryToday)-1){
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
																//если есть свободное место после последней записи
																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
																	$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
																	$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
																	$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																	$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
																	echo '
																		<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', '.$type.', \'add\')">
																			';
																		//var_dump($Zapis_key);
																	echo '
																		</div>';
																//Если залазит на следующий отрезок времени
																}else{
																	if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
																		$NextFill = TRUE;
																		//$PrevZapis = $ZapisHereQueryToday_val;
																	}
																}	
																
															}else{
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
															}
														}
														
														//Если время выполнения работы больше чем осталось до конца смены
														if ($wt == 1230){
															if ($cellZapisValue_Height > 60){
																$cellZapisValue_Height = 60;
															}
														}else{
															if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1260){
																$cellZapisValue_Height = (1260-$ZapisHereQueryToday[0]['start_time'])*2;
																/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
																$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
																$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
																if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
																	$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
																}*/
															}
														}
														if ($ZapisHereQueryToday_val['enter'] == 1){
															$back_color = 'background-color: rgba(119, 255, 135, 1);';
														}else{
															//Если оформлено не на этом филиале
															if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
																$back_color = 'background-color: rgb(119, 255, 250);';
															}
														}

                                                        $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                                        $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
                                                        $title_descr = $ZapisHereQueryToday_val['description'];
                                                        $zapis_id = $ZapisHereQueryToday_val['id'];

                                                        echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                        //Контекстная менюшка
                                                        //echo contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false);

                                                        /*echo '
														       </div>';*/
													}else{
													}
													//Передать предыдущую запись
													$PrevZapis = $ZapisHereQueryToday_val;
												}
											}elseif (count($ZapisHereQueryToday) == 1){
												if ($ZapisHereQueryToday[0]['start_time'] < 1260){
													//вычисляем время начала приёма
													$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
													$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
													//вычисляем время окончания приёма
													$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
													$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
													//Сдвиг для блока
													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-900)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-900)%30*2);
													//Высота блока
													$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
													//Если от начала работы окончание выходит за предел промежутка времени
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
														$NextFill = TRUE;
														//$PrevZapis = $ZapisHereQueryToday[0];
													}
													//Если работа закончится до окончания периода
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
														$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
														$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
														echo '
															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																';
															//var_dump($NextFill);
														echo '
															</div>';
													}
													
													//Если работа начнется не с начала периода
													if ($ZapisHereQueryToday[0]['start_time'] > $wt){
														if (!$NextFill){
															$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
															$wt_start_FreeSpace = $wt;
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
														}else{
															//var_dump($PrevZapis);
															if (isset($PrevZapis['start_time'])){
																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - ($PrevZapis['start_time'] + $PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
															}else{
																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
																$wt_start_FreeSpace = $wt;
															}
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
														}
														echo '
															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																';
															//var_dump($NextSmenaArr);
														echo '
															</div>';
													}
													
													//Если время выполнения работы больше чем осталось до конца смены
													if ($wt == 1230){
														if ($cellZapisValue_Height > 60){
															$cellZapisValue_Height = 60;
														}
													}else{
														if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1260){
															$cellZapisValue_Height = (1260-$ZapisHereQueryToday[0]['start_time'])*2;
															/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
															$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
															$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
															if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
																$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
															}*/
														}
													}
													if ($ZapisHereQueryToday[0]['enter'] == 1){
														$back_color = 'background-color: rgba(119, 255, 135, 1);';
													}else{
														//Если оформлено не на этом филиале
														if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
															$back_color = 'background-color: rgb(119, 255, 250);';
														}
													}

													$title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
													$title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
													$title_descr = $ZapisHereQueryToday[0]['description'];
                                                    $zapis_id = $ZapisHereQueryToday[0]['id'];

                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                    //Контекстная менюшка
                                                    //echo contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false);

													/*echo '
														</div>';*/
												}else{
												}
												//Передать предыдущую запись
												$PrevZapis = $ZapisHereQueryToday[0];
											}
										//если тут вообще нет записи
										}else{
											//var_dump(1);
											$mark = '';
											if (!$NextFill){
												//($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))
												if (!$NextSmenaArr_Bool){
													$wt_FreeSpace = 30;
													$wt_start_FreeSpace = $wt;
													$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
													//$mark = 777;
													//var_dump($wt);
												}else{
													if ((900+$NextSmenaArr_Zanimayu)< $wt){
														$wt_FreeSpace = 30;
														$wt_start_FreeSpace = $wt;
														$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
														//$mark = 888;
													}else{
														$wt_FreeSpace = $wt+30-(900+$NextSmenaArr_Zanimayu);
														$wt_start_FreeSpace = 900+$NextSmenaArr_Zanimayu;
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
														//$mark = $NextSmenaArr_Zanimayu;
													}
												}
												echo '
													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
														'.$mark.'';
													//var_dump($NextFill);
												echo '
													</div>';
											}
										}
										//Если предыдущее время залазит на это
										if ($NextFill){
											//var_dump(3);
											if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
												$NextFill = FALSE;
												$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
												$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
												$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
												$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
												echo '
													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
														';
													//var_dump($NextFill);
												echo '
													</div>';
											}
										}
									//}
									
								}
								echo '</div>';
							/*}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none;">
												2 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">графику</a></span>
											</div>
										</div>';
							}*/

							echo '
									</td>';
							$NextSmenaArr[$k] = array();
							$NextSmenaArr_Bool = FALSE;
						}
						echo '
								</tr>
							</table>
						</div>';
						
						//смена 3
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;
						echo '
						<div id="tabs-3">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
							<td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
								<div class="" style="border: none; width: 100%;  height: 50px;">
									
								</div>
								<div style="position:relative; height: 720px;">';

						//for ($wt=1260; $wt < 1620; $wt=$wt+30){
						//до полуночи
						for ($wt=1260; $wt < 1440; $wt=$wt+30){	
							echo '
								<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
									'.$zapis_times[$wt].'';
							//var_dump($NextFill);
							echo '
								</div>';
							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						//после полуночи
						for ($wt=0; $wt < 180; $wt=$wt+30){	
							echo '
								<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
									'.$zapis_times[$wt].'';
							//var_dump($NextFill);
							echo '
								</div>';
							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						echo '</div>
							</td>';
						
						
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';
							
							if (isset($Work_Today_arr[$k][3])){
								//var_dump($Kab_work_today_smena1);

								$worker = $Work_Today_arr[$k][3]['worker'];
								
								echo '
									<div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
										<div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
											3 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker, 'user', false).'</i>
											<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
										</div>
									</div>';
							}else{

								$worker = 0;
								
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
												3 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].''.$who.'">графику</a></span>
												<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
											</div>
										</div>';
							}
							echo'
										<div style="position:relative; height: 720px;">';
								//Выбрать записи пациентов, если есть
								//$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $_GET['filial'], $k);
								//var_dump ($ZapisHereQueryToday);
								
								$NextTime = FALSE;
								$ThatTimeFree = TRUE;
								$PeredannNextTime = FALSE;
								$NextTime_val = 0;
								//сдвиг для блоков времени
								$cellZapisTime_TopSdvig = 0;
								$cellZapisValue_TopSdvig = 0;
								$PrevZapis = array();
								$NextFill = FALSE;
								
								//for ($wt=900; $wt < 1260; $wt=$wt+30){
								//for ($wt=1260; $wt < 1620; $wt=$wt+30){
									
								//до полуночи
								for ($wt=1260; $wt < 1440; $wt=$wt+30){										
									if (isset($Work_Today_arr[$k][3])){
										$bg_color = '';	
									}else{
										$bg_color = ' background-color: #f0f0f0;';
									}
											
									$back_color = '';
									/*echo '
										<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;" onclick="window.location.href = \'zapis_full.php?filial='.$_GET['filial'].'&who='.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'\'">
											'.$zapis_times[$wt].'';
									//var_dump($NextFill);
									echo '
										</div>';*/
									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
									
									//Выбрать записи пациентов, если есть
									$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $_GET['filial'], $k, $wt, $type);
									//var_dump ($ZapisHereQueryToday);
									
									if (isset($NextSmenaArr[$k]['NextSmenaFill']) && !$NextSmenaArr_Bool){
										//var_dump(4);
										if($NextSmenaArr[$k]['NextSmenaFill']){
											//var_dump(5);
											//надо перескочить промежуток
											$NextSmenaArr_Bool = TRUE;
											$NextSmenaArr_Zanimayu = $NextSmenaArr[$k]['OstatokVremeni']/2;
											
											//$NextFill = TRUE;
											$PrevZapis = $NextSmenaArr[$k]['ZapisHereQueryToday'];
											
											//вычисляем время начала приёма
											$TempStartWorkTime_h = floor($PrevZapis['start_time']/60);
											$TempStartWorkTime_m = $PrevZapis['start_time']%60;
											if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
											//вычисляем время окончания приёма
											$TempEndWorkTime_h = floor(($PrevZapis['start_time']+$PrevZapis['wt'])/60);
											if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
											$TempEndWorkTime_m = ($PrevZapis['start_time']+$PrevZapis['wt'])%60;
											if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
											//Сдвиг для блока
											$cellZapisValue_TopSdvig = 0;
											//Высота блока
											$cellZapisValue_Height = $NextSmenaArr[$k]['OstatokVremeni'];
											if ($cellZapisValue_Height > 720)
												$cellZapisValue_Height = 720;
											
											if ($NextSmenaArr[$k]['ZapisHereQueryToday']['enter'] == 1){
												$back_color = 'background-color: rgba(119, 255, 135, 1);';
											}else{
												//Если оформлено не на этом филиале
												if($NextSmenaArr[$k]['ZapisHereQueryToday']['office'] != $NextSmenaArr[$k]['ZapisHereQueryToday']['add_from']){
													$back_color = 'background-color: rgb(119, 255, 250);';
												}
											}

                                            $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                            $title_client = WriteSearchUser('spr_clients', $NextSmenaArr[$k]['ZapisHereQueryToday']['patient'], 'user', false);
                                            $title_descr = $NextSmenaArr[$k]['ZapisHereQueryToday']['description'];
                                            $zapis_id = $NextSmenaArr[$k]['ZapisHereQueryToday']['id'];

                                            echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                            //Контекстная менюшка
                                            //echo contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);

                                            /*echo '
														</div>';*/
										}
										//$NextSmenaArr = array();
									}
									//var_dump(6);
									//var_dump($PrevZapis);
									
									//!!! Доделать, если "рваная" запись залазит (не кратное 30 минутам)
									
									//var_dump(!$NextSmenaArr_Bool);
									//var_dump(((900+$NextSmenaArr_Zanimayu)-$wt)%30);
									//var_dump($NextSmenaArr_Zanimayu);
									//var_dump($ZapisHereQueryToday);

									//if (!$NextSmenaArr_Bool || ($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))){
										if ($ZapisHereQueryToday != 0){
											//var_dump(2);
											
											//Если тут записей больше 1
											if (count($ZapisHereQueryToday) > 1){
												foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
													if ($ZapisHereQueryToday_val['start_time'] < 1440){
														//вычисляем время начала приёма
														$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
														$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
														if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
														//вычисляем время окончания приёма
														$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
														if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
														$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
														if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
														//Сдвиг для блока
														$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-1260)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
														//Высота блока
														$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
														//Если от начала работы окончание выходит за предел промежутка времени
														if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
															$NextFill = TRUE;
															//$PrevZapis = $ZapisHereQueryToday_val;
														}
														//Если перед первой работой от начала промежутка есть свободное время
														if ($Zapis_key == 0){
															if ($ZapisHereQueryToday_val['start_time'] > $wt){
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
																$wt_start_FreeSpace = $wt;
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
															}
														}else{
															//если последняя запись
															if ($Zapis_key == count($ZapisHereQueryToday)-1){
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
																//если есть свободное место после последней записи
																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
																	$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
																	$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
																	$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																	$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
																	echo '
																		<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																			';
																		//var_dump($Zapis_key);
																	echo '
																		</div>';
																//Если залазит на следующий отрезок времени
																}else{
																	if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
																		$NextFill = TRUE;
																		//$PrevZapis = $ZapisHereQueryToday_val;
																	}
																}	
																
															}else{
																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
																echo '
																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																		';
																	//var_dump($Zapis_key);
																echo '
																	</div>';
															}
														}
														
														//Если время выполнения работы больше чем осталось до конца смены
														if ($wt == 1230){
															if ($cellZapisValue_Height > 60){
																$cellZapisValue_Height = 60;
															}
														}else{
															if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1440){
																$cellZapisValue_Height = (1440-$ZapisHereQueryToday[0]['start_time'])*2;
																/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
																$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
																$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
																if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
																	$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
																}*/
															}
														}
														if ($ZapisHereQueryToday_val['enter'] == 1){
															$back_color = 'background-color: rgba(119, 255, 135, 1);';
														}else{
															//Если оформлено не на этом филиале
															if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
																$back_color = 'background-color: rgb(119, 255, 250);';
															}
														}

                                                        $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                                        $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
                                                        $title_descr = $ZapisHereQueryToday_val['description'];
                                                        $zapis_id = $ZapisHereQueryToday_val['id'];

                                                        echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                        //Контекстная менюшка
                                                        //echo contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);

														/*echo '
														    </div>';*/
													}else{
													}
													//Передать предыдущую запись
													$PrevZapis = $ZapisHereQueryToday_val;
												}
											}elseif (count($ZapisHereQueryToday) == 1){
												//var_dump(2);
												//var_dump($ZapisHereQueryToday[0]['start_time']);
												//var_dump($ZapisHereQueryToday[0]['wt']);
												if ($ZapisHereQueryToday[0]['start_time'] < 1440){
													//вычисляем время начала приёма
													$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
													$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
													//вычисляем время окончания приёма
													$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
													$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;	
													//Сдвиг для блока
													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-1260)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-1260)%30*2);
													//Высота блока
													$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
													//Если от начала работы окончание выходит за предел промежутка времени
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
														$NextFill = TRUE;
														//$PrevZapis = $ZapisHereQueryToday[0];
													}
													//Если работа закончится до окончания периода
													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
														$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
														$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
														echo '
															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																';
															//var_dump($NextFill);
														echo '
															</div>';
													}
													
													//Если работа начнется не с начала периода
													if ($ZapisHereQueryToday[0]['start_time'] > $wt){
														if (!$NextFill){
															$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
															$wt_start_FreeSpace = $wt;
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
														}else{
															//var_dump($PrevZapis);
															if (isset($PrevZapis['start_time'])){
																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - ($PrevZapis['start_time'] + $PrevZapis['wt']);
																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
															}else{
																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
																$wt_start_FreeSpace = $wt;
															}
															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
														}
														echo '
															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
																';
															//var_dump($NextSmenaArr);
														echo '
															</div>';
													}
													
													//Если время выполнения работы больше чем осталось до конца смены
													if ($wt == 1230){
														if ($cellZapisValue_Height > 60){
															$cellZapisValue_Height = 60;
														}
													}else{
														if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1440){
															$cellZapisValue_Height = (1440-$ZapisHereQueryToday[0]['start_time'])*2;
															/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
															$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
															$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
															if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
																$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
															}*/
														}
													}
													if ($ZapisHereQueryToday[0]['enter'] == 1){
														$back_color = 'background-color: rgba(119, 255, 135, 1);';
													}else{
														//Если оформлено не на этом филиале
														if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
															$back_color = 'background-color: rgb(119, 255, 250);';
														}
													}

                                                    $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                                                    $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
                                                    $title_descr = $ZapisHereQueryToday[0]['description'];
                                                    $zapis_id = $ZapisHereQueryToday[0]['id'];

                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));

                                                    //Контекстная менюшка
                                                    //echo contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);

													/*echo '
														</div>';*/
												}else{
												}
												//Передать предыдущую запись
												$PrevZapis = $ZapisHereQueryToday[0];
											}
										//если тут вообще нет записи
										}else{
											//var_dump(1);
											$mark = '';
											if (!$NextFill){
												//var_dump(7);
												//($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))
												if (!$NextSmenaArr_Bool){
													//var_dump(8);
													$wt_FreeSpace = 30;
													$wt_start_FreeSpace = $wt;
													$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
													//$mark = 777;
													//var_dump($wt);
												}else{
													if ((1260+$NextSmenaArr_Zanimayu)< $wt){
														$wt_FreeSpace = 30;
														$wt_start_FreeSpace = $wt;
														$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
														//$mark = 888;
													}else{
														$wt_FreeSpace = $wt+30-(1260+$NextSmenaArr_Zanimayu);
														$wt_start_FreeSpace = 1260+$NextSmenaArr_Zanimayu;
														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
														//$mark = $NextSmenaArr_Zanimayu;
													}
												}
												echo '
													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
														'.$mark.'';
													//var_dump($NextFill);
												echo '
													</div>';
											}
										}
										//Если предыдущее время залазит на это
										if ($NextFill){
											//var_dump(3);
											if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
												$NextFill = FALSE;
												$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
												$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
												$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
												$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
												echo '
													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
														';
													//var_dump($NextFill);
												echo '
													</div>';
											}
										}
									//}
									
								}
								echo '</div>';
							/*}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none;">
												2 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$_GET['filial'].'&who='.$who.'">графику</a></span>
											</div>
										</div>';
							}*/

							echo '
									</td>';
							$NextSmenaArr[$k] = array();
							$NextSmenaArr_Bool = FALSE;
						}
						echo '
								</tr>
							</table>
						</div>';

						
						//смена 4
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;
						echo '
						<div id="tabs-4">
						</div>';
						
					}else{
						echo '<h1>В этом филиале нет кабинетов такого типа.</h1>';
					}
			//	}
			/*}else{
				echo '
					<div id="status">
						<header>
							<h2>График</h2>
							<a href="own_scheduler.php" class="b">График работы врачей</a><br /><br />';
				echo '
					<form>
						<select name="SelectFilial" id="SelectFilial">
							<option value="0" selected>Выберите филиал</option>';
				if ($offices_j != 0){
					for ($i=0;$i<count($offices_j);$i++){
						echo "<option value='".$offices_j[$i]['id']."'>".$offices_j[$i]['name']."</option>";
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
			}*/

			echo '
					</div>
				</div>';

            echo $block_show_settings_add_temp_zapis;

            echo '
                                    <div id="Ajax_add_TempZapis_button" style="display: inline;"></div>
						            <input type="button" class="b" value="Отмена" onclick="HideSettingsAddTempZapis()">
                 </div>';

			echo '
						<input type="hidden" id="zapis_id" name="zapis_id" value="0">
						
						<input type="hidden" id="day" name="day" value="0">
						<input type="hidden" id="month" name="month" value="0">
						<input type="hidden" id="year" name="year" value="0">
						<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
						<input type="hidden" id="filial" name="filial" value="0">
						<input type="hidden" id="start_time" name="start_time" value="0">
						<input type="hidden" id="wt" name="wt" value="0">
						<input type="hidden" id="worker_id" name="worker_id" value="0">
						<input type="hidden" id="type" name="type" value="'.$type.'">
						<!--<input type="button" class="b" value="Добавить" id="Ajax_add_TempZapis" onclick="Ajax_add_TempZapis('.$type.')">-->';


			echo '	
						
					</div>
					<div id="doc_title">Запись '.$whose.'/'.$day.' ',$monthsName[$month],' ',$year,'/'.$filial[0]['name'].' - Асмедика</div>';
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			/*echo '
			<div id="ShowDescrTempZapis" style="display: none; position: fixed; padding: 10px; margin: 10px; top: 40px; left: 20px; background-color: rgba(255,255,0, .7); border: 1px dotted red; font-size: 75%;">

			</div>
			';*/
			
			echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
								//var dayW = document.getElementById("SelectDayW").value;
								document.location.href = "?filial="+$(this).val()+"'.$who.'";
							});
							/*$("#SelectDayW").change(function(){
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
							});*/
						});
						
					</script>';
					
			/*echo '
					<script>';*/
			
			//если есть права или бог или костыль (ассисенты ночью)
			if (($zapis['add_new'] == 1) || $god_mode || (($_SESSION['permissions'] == 7) && (date("H", time()-60*60) > 16))){
				echo '
				<script src="js/zapis.js"></script>';

			}	
			echo '	
				<!--</script>-->
			
			
			
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
			if (($zapis['add_new'] == 1) || $god_mode){					
				echo '';
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