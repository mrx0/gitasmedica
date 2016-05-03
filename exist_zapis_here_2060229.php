<?php 

//exist_zapis_here.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump($_POST);

		if ($_POST){
			//if ($_POST['smena'] != 0){
				//!!!Выбираем врачей (не уволенные) (пока стоматологи только)
				$zapis = array();
				if ($_POST['datatable'] == 'scheduler_stom'){
					$datatable = 'zapis_stom';
				}elseif ($_POST['datatable'] == 'scheduler_cosm'){
					$datatable = 'zapis_cosm';
				}else{
					$datatable = 'zapis_stom';
				}
				/*require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				//$query = "SELECT * FROM `$datatable` WHERE `office` = '".$_POST['filial']."' AND `kab` = '".$_POST['kab']."' AND `year` = '".$_POST['year']."' AND `month` = '".$_POST['month']."' AND `day` = '".$_POST['day']."' AND `start_time` >= '".$_POST['time']."' AND `start_time` < '".($_POST['time'] + 30)."'";
				$query = "SELECT * FROM `$datatable` WHERE `office` = '".$_POST['filial']."' AND `kab` = '".$_POST['kab']."' AND `year` = '".$_POST['year']."' AND `month` = '".$_POST['month']."' AND `day` = '".$_POST['day']."'";
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($zapis, $arr);
					}
				}else
					$zapis = 0;
				mysql_close();
				*/

				$zapis = FilialKabSmenaZapisToday($datatable, $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab']);
				//var_dump($zapis);
				$exist_zapis = FALSE;
				
				if ($zapis != 0){
					foreach ($zapis as $key => $zapis_val){
						if (($zapis_val['start_time'] >= $_POST['time']) && ($zapis_val['start_time'] < $_POST['time']+30)){
							//запись точно равна отрезку времени в расписании
							if ($zapis_val['start_time'] + $zapis_val['wt'] == $_POST['time']+30){
								echo '{"req":"full", "time_start":"0"}';
							//запись превышает отрезок времени в расписании
							}elseif ($zapis_val['start_time'] + $zapis_val['wt'] > $_POST['time']+30){
								echo '{"req":"hard", "time_start":"0"}';
							//запись меньше отрезка времени в расписании
							}elseif ($zapis_val['start_time'] + $zapis_val['wt'] < $_POST['time']+30){
								$start_time = $zapis_val['start_time'] + $zapis_val['wt'];
								echo '{"req":"light", "time_start":"'.$start_time.'"}';
								//echo '{"time":"123"}';
								
							}
							//echo 'тут есть';
							//var_dump($zapis_val);
							$exist_zapis = TRUE;
						}
					}
				}else{
					echo '{"req":"нет записей", "time_start":"0"}';
				}
				if (!$exist_zapis){
					echo '{"req":"нет записей", "time_start":"0"}';
				}
				
				/*
								$NextTime = FALSE;
								$ThatTimeFree = TRUE;
								$PeredannNextTime = FALSE;
								$NextTime_val = 0;
								for ($wt=540; $wt < 900; $wt=$wt+30){
									if ($zapis != 0){
										//var_dump ($zapis);
										$ZapisHere = FALSE;
										if ($NextTime){
											$PeredannNextTime = TRUE;
										}
										foreach ($zapis as $key => $zapis_val){
											//var_dump ($zapis[$key]);

											if ((($zapis[$key]['start_time']+$zapis[$key]['wt'] <= $wt+30) || ($zapis[$key]['start_time'] < $wt+30))
												&& ($zapis[$key]['start_time'] >= $wt)){
												
												//echo '000<br />';
												$ZapisHere = TRUE;
												//Если длительность приёма захватывает следующий промежуток
												if ($zapis[$key]['start_time']+$zapis[$key]['wt'] > $wt+30){
													//echo '001<br />';
													$ThatTimeFree = FALSE;
													$NextTime = TRUE;
													//Если время больше чем на два промежутка
													//if ($zapis[$key]['wt'] >= 60){
													$NextTime_val = $zapis[$key]['wt']-30;
													//}
												//Если длительность приёма НЕ полностью покрывает промежуток
												}elseif ($zapis[$key]['start_time']+$zapis[$key]['wt'] < $wt+30){
													//echo '002<br />';
													$ThatTimeFree = TRUE;
													$NextTime = FALSE;
												//Если длительность приёма полностью покрывает промежуток
												}elseif ($zapis[$key]['start_time']+$zapis[$key]['wt'] = $wt+30){
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
								
									/*echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%;">
											<div class="cellRight" id="month_date_worker" style="padding:0;">
												<div onclick="ShowSettingsAddTempZapis('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$y.', '.$m.','.$d.', 1, '.$wt.', '.$Work_Today_arr[$k][1]['worker'].', \''.WriteSearchUser('spr_workers', $Work_Today_arr[$k][1]['worker'], 'user').'\')" style="float:left; margin: 5px;">'.$sheduler_times[$wt].'</div>
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
									';*/
									//$wt = $wt+60;
								//}
				
				/*if ($zapis !=0){
					$zapis_arr = array();
					foreach($zapis as $value){
						if (($value['start_time']+$value['wt']) >= ($_POST['time'] + 30)){
							//echo $value['start_time'].'+'.$value['wt'].'<br />';
							echo '{"req":"Промежуток забит"}';
						}else{
							echo '{"req":"Есть свободное время"}';
						}
					}
				}else{
					echo '{"req":"Полностью свободная запись"}';
				}*/
			/*}else{
				echo '
					<div class="cellsBlock2" style="width:320px; font-size:80%;">
						<div class="cellRight">
							Не выбрана смена
						</div>
					</div>';*/
			//}
		}
	}
?>