<?php

//zapis_full.php
//Вся щапись на день 

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($stom);
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			$offices = SelDataFromDB('spr_office', '', '');
			//var_dump ($offices);

            require 'variables.php';

            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

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
					if (($_GET['who'] == 'stom') || ($_GET['who'] == 5)){
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
					}elseif(($_GET['who'] == 'cosm') || ($_GET['who'] == 6)){
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
                    }elseif(($_GET['who'] == 'somat') || ($_GET['who'] == 10)){
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
						
					$stom_color = 'background-color: #fff261;';
					$cosm_color = '';
                    $somat_color = '';
				}
				
				/*$month_names=array(
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
				); */
				
				/*if (isset($_GET['y']))
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
					$d = date("d");*/
				
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
				
				
				if (isset($_GET['kab'])){
					$kab = $_GET['kab'];
				}else{
					$kab = 1;
				}
				
				//$month_stamp = mktime(0, 0, 0, $m, 1, $y);
				//$day_count = date("t",$month_stamp);
				//$weekday = date("w", $month_stamp);
				/*if ($weekday == 0)
					$weekday = 7;
				$start = -($weekday-2);
				$last = ($day_count + $weekday - 1) % 7;
				if ($last == 0) 
					$end = $day_count; 
				else 
					$end = $day_count + 7 - $last;
				$today = date("Y-m-d");
				$go_today = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, date("m"), date("d"), date("Y"))); 
				*/
				/*$prev = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, $m, $d-1, $y));  
				$next = date('?\d=d&\m=m&\y=Y', mktime (0, 0, 0, $m, $d+1, $y));
				if(isset($_GET['filial'])){
					$prev .= '&filial='.$_GET['filial']; 
					$next .= '&filial='.$_GET['filial'];
					$go_today .= '&filial='.$_GET['filial'];
					
					$selected_fil = $_GET['filial'];
				}
				$i = 0;*/
				
				foreach ($_GET as $key => $value){
					if (($key == 'd') || ($key == 'm') || ($key == 'y'))
						$dopDate  .= '&'.$key.'='.$value;
					if ($key == 'filial')
						$dopFilial .= '&'.$key.'='.$value;
					if ($key == 'who')
						$dopWho .= '&'.$key.'='.$value;
				}
				
				
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
					
				//переменная, чтоб вкл/откл редактирование
				echo '
					<script>
						var iCanManage = true;
					</script>';
				
				if ($filial != 0){
					
					echo '
						<div id="status">
							<header>
								<div class="nav">
									<a href="zapis.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'" class="b">Запись</a>
									<a href="scheduler.php?filial='.$_GET['filial'].''.$who.'" class="b">График</a>
									<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
								</div>
							
								<h2>Запись '.$day.' ',$monthsName[$month],' ',$year,' <small>(подробное описание)</small></h2>
								<b>Филиал</b> '.$filial[0]['name'].'<br>
								<b>Кабинет '.$kab.'</b><br>
								<span style="color: green; font-size: 120%; font-weight: bold;">'.$whose.'</span><br>
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
					if (!isset($_SESSION['filial'])){
						echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';							
					}
	
					/*echo '
							<div id="data">';*/
							
					echo '		
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?'.$dopFilial.$dopDate.'&who=stom&kab=1" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="?'.$dopFilial.$dopDate.'&who=cosm&kab=1" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="?'.$dopFilial.$dopDate.'&who=somat&kab=1" class="b" style="'.$somat_color.'">Специалисты</a>
							</li>';
							
					$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $year, $month, $day, $_GET['filial'], $kab, $type);
					//var_dump($ZapisHereQueryToday);
					
					
					//Календарик	
					echo '
	
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
									<div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?'.$dopFilial.$dopWho.'&kab='.$kab.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
									<div>
										<span style="color: rgb(125, 125, 125);">
											Изменить дату:
											<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
											<span style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'zapis_full.php?&kab='.$kab.$dopFilial.$dopWho.'\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
										</span>
									</div>
								</li>';
					
					
					
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
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите кабинет</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">';

						for ($k = 1; $k <= count($kabsInFilial); $k++){
							$kab_color = '';
							if ($k == $kab){
								$kab_color = ' background-color: #fff261;';
							}

                            if (isset($Work_Today_arr[$k][1])){
                                //var_dump($Kab_work_today_smena1);
                                $worker = '<i>1 см: '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][1]['worker'], 'user', false).'</i><br>';
                            }else{
                                $worker = '1 см: <span style="font-weight: normal;">никого</span><br>';
                            }
                            if (isset($Work_Today_arr[$k][2])){
                                //var_dump($Kab_work_today_smena1);
                                $worker .= '<i>2 см: '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][2]['worker'], 'user', false).'</i>';
                            }else{
                                $worker .= '2 см: <span style="font-weight: normal;">никого</span>';
                            }

							echo '		
								<a href="?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="b" style="'.$kab_color.' text-align: center;"><span style="font-size: 120%">каб '.$k.'</span><br>'.$worker.'</a>';
						}
						echo '</li>';
					}
					
					if ($ZapisHereQueryToday != 0){

                        // !!! **** тест с записью
                        include_once 'showZapisRezult.php';

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

                        echo showZapisRezult($ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type, true, true);
					}else{
						echo 'В этом кабинете нет записи<br>Смотрите остальные';
					}

				}
			}else{
				echo '
					<div id="status">
						<header>';
				echo '			
				</header>';
			}

			echo '
					</div>
				</div>';


					
			echo '	
						
					</div>
					<div id="doc_title">Подробная запись '.$whose.'/'.$day.' ',$monthsName[$month],' ',$year,'/'.$filial[0]['name'].' - Асмедика</div>';
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			echo '
					<script>';
					
			if (($zapis['add_new'] == 1) || $god_mode){
				echo '		
					function ShowSettingsAddTempZapis(filial, filial_name, kab, year, month, day, smena, time, period, worker_id, worker_name, patient_name, description, insured, pervich, noch, id){
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
						document.getElementById("zapis_id").value=id;

						if (worker_id == 0){
							document.getElementById("search_client2").value = "";
						}else{
							document.getElementById("search_client2").value = worker_name;
						}
						
						document.getElementById("search_client").value=patient_name;
						
						document.getElementById("description").value=description;
												
						document.getElementById("filial_name").innerHTML=filial_name;
						document.getElementById("kab").innerHTML=kab;
						document.getElementById("month_date").innerHTML=day+\'.\'+month+\'.\'+year;
						document.getElementById("month_date_smena").innerHTML=smena
						
						//alert(insured);
						//alert(pervich);
						//alert(noch);
						
						var pervich_checkbox = document.getElementById("pervich");
						var insured_checkbox = document.getElementById("insured");
						var noch_checkbox = document.getElementById("noch");
						//if (pervich == 1) pervich_checkbox.attr("checked",true);
						
						if (pervich == 1) pervich_checkbox.checked = true;
						if (insured == 1) insured_checkbox.checked = true;
						if (noch == 1) noch_checkbox.checked = true;

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
                                    id: id,
                    
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
						
						//Поправка времени и вставка в форму
						document.getElementById("change_hours").value = period/60|0;
						document.getElementById("change_minutes").value = period%60;
						
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
						
						var pervich_checkbox = document.getElementById("pervich");
						var insured_checkbox = document.getElementById("insured");
						var noch_checkbox = document.getElementById("noch");
						
						pervich_checkbox.checked = false;
						insured_checkbox.checked = false;
						noch_checkbox.checked = false;
						
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