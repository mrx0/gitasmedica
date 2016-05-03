<?php

//cosmet.php
//Косметология

	require_once 'header.php';
	//var_dump ($enter_ok);
	//var_dump ($god_mode);
	
	if ($enter_ok){
		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
				
			$arr = array();
			$rez = array();
		
			//$filials = SelDataFromDB('spr_office', '', '');
			
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
				<div class="no_print"> 
				<header style="margin-bottom: 5px;">
					<h1>Косметология расширенная статистика</h1>';
					
			if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
				echo '
						<a href="cosmet.php" class="b">В журнал</a>';
			}
			
			/*if (($cosm['see_all'] == 1) || $god_mode){
				echo '
						<a href="full_stat_cosmet.php" class="b">Статистика2</a>';
			}*/
				
			if (($cosm['see_all'] == 1) || $god_mode){
				echo '
						<a href="stat_cosm.php" class="b">Статистика</a>';
			}
			echo '
				</header>
				</div>';

			
			
				///////////////////
				if ($_GET){
					//echo 1;
					//var_dump ($_GET);
					$filter_rez = array();
					if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
						//echo 2;
						//операции со временем						
						$ttime = explode('_', $_GET['ttime']);			
						$month = $ttime[0];
						//echo $month.'<br />';
						$year = $ttime[1];
						//echo $year.'<br />';
						$datestart = strtotime('1.'.$month.'.'.$year);
						//echo $datestart.'<br />';
						//echo date('d.m.Y H:i', $datestart).'<br />';
						//нулевой день следующего месяца - это последний день предыдущего
						$lastday = mktime(0, 0, 0, $month+1, 0, $year);
						$datefinish = strtotime(strftime("%d", $lastday).'.'.$month.'.'.$year.' 23:59:59');
						//echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';			
						$_GET['datastart'] = date('d.m.Y', $datestart);
						$_GET['dataend'] = date('d.m.Y', $datefinish);
						$_GET['ended'] = 0;	
		
						$filter_rez = filterFunction ($_GET);
						$filter = TRUE;
						//var_dump ($filter_rez);
					}else{
							$sw .= '';
							$type = '';
					}
					
				}else{
					//echo 4;
					//операции со временем						
					$ttime = time();			
					$month = date('n', $ttime);		
					$year = date('Y', $ttime);
					$datestart = strtotime('1.'.$month.'.'.$year);
					//нулевой день следующего месяца - это последний день предыдущего
					$lastday = mktime(0, 0, 0, $month+1, 0, $year);
					$datefinish = strtotime(strftime("%d", $lastday).'.'.$month.'.'.$year.' 23:59:59');
					//echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';			
					$_GET['datastart'] = date('d.m.Y', $datestart);
					$_GET['dataend'] = date('d.m.Y', $datefinish);
					$_GET['ended'] = 0;				
					
					$filter_rez = filterFunction ($_GET);

				}
				
				//Тут мы создаем массив с месяцами и годами между самым первым посещением и последним
				$arr_temp = SelMINDataFromDB ('journal_cosmet1', 'create_time');
				$mintime = $arr_temp[0]['create_time'];
				//var_dump ($mintime[0]['create_time']);
				$arr_temp = SelMAXDataFromDB ('journal_cosmet1', 'create_time');
				$maxtime = $arr_temp[0]['create_time'];
				//echo date('d.m.y H:i', $maxtime);
				$month_mintime = date('n', $mintime);
				$month_maxtime = date('n', $maxtime);
				$year_mintime = date('Y', $mintime);
				$year_maxtime = date('Y', $maxtime);
			
				$Diff_Months = array();
				while (!(($year_maxtime == $year_mintime) && ($month_maxtime == $month_mintime))){
					//echo $month_mintime.'.'.$year_mintime.'x'.$month_maxtime.'.'.$year_maxtime.'<br />';
					array_push($Diff_Months, $month_mintime.'.'.$year_mintime);
					$month_mintime++;
					if ($month_mintime > 12){
						$year_mintime++;
						$month_mintime = 1;
					}
				}
				array_push($Diff_Months, $month_maxtime.'.'.$year_maxtime);
			
				$li_months = '';
				$arr_temp = array();
				$m = array(
					1 => 'Январь',
					2 => 'Февраль',
					3 => 'Март',
					4 => 'Апрель',
					5 => 'Май',
					6 => 'Июнь',
					7 => 'Июль',
					8 => 'Август',
					9 => 'Сентябрь',
					10 => 'Октябрь',
					11 => 'Ноябрь',
					12 => 'Декабрь',
				);
				for ($i=0; $i<count($Diff_Months); $i++){
					$arr_temp = explode('.', $Diff_Months[$i]);
					if (($year == $arr_temp[1]) && ($month == $arr_temp[0])){
						$selected = 'selected';
						$selected_date = $m[$arr_temp[0]].' '.$arr_temp[1];
					}else{
						$selected = '';
					}			
					$li_months .= '<option value="'.$arr_temp[0].'_'.$arr_temp[1].'" '.$selected.' >'.$m[$arr_temp[0]].' '.$arr_temp[1].'</option>';
				}
				
				//////////////////////////////////////////////////	

				//$offices = SelDataFromDB('spr_office', '', '');
				//var_dump ($offices);
				
				$workers = array();
				$filials = array();
				
				$sw = $filter_rez[1];
				if (($cosm['see_all'] == 1) || $god_mode){
					$query = "SELECT `worker`,`office` FROM `journal_cosmet1` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						//Всего посещений
						$vsego = $number;
						while ($arr = mysql_fetch_assoc($res)){
							array_push($workers, $arr['worker']);
							$workers = array_unique($workers);
							array_push($filials, $arr['office']);
							$filials = array_unique($filials);
						}
					}else{
						$vsego = 0;
					}
				}
				
				
				
				//var_dump ($workers);
				//var_dump ($filials);
				
				
				echo '
					<div class="no_print"> 
					<form action="stat_cosm.php" id="months_form" method="GET">
						<input type="hidden" name="filter" value="yes">
						<select name="ttime" onchange="this.form.submit()">'.
							$li_months
						.'</select>
					</form>
					</div>';


				/*echo '
					<b>Всего посещений:</b> '.$vsego.'
					<br />';*/
				

		
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');	
				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
				//return $rez;
				//var_dump ($actions_cosmet);
				
				$tabs_workers = '<ul>';
				$itog = array();

				//по работникам
				foreach ($workers as $value){
					$journal_w = array();
					/*echo '
						<div class="cellsBlock2">
							<div class="cellName">
								'.WriteSearchUser('spr_workers', $value, 'user').'
							</div>
						</div>';*/
						
					$tabs_workers .= '<li><a href="#tabs-'.$value.'">'.WriteSearchUser('spr_workers', $value, 'user').'</a></li>';
					$itog[$value]['name'] = WriteSearchUser('spr_workers', $value, 'user');
					
					$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} ORDER BY `create_time` DESC";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						//Всего посещений
						$w_vsego = $number;
						while ($arr = mysql_fetch_assoc($res)){
							//!!!Зачем мне этот массив тут???
							array_push($journal_w, $arr);
						}
						/*echo '
							<div class="cellsBlock2">
								<div class="cellName">
									Всего посещений: '.$w_vsego.'<br />
								</div>
							</div>';*/
						$itog[$value]['w_vsego'] = $w_vsego;
						//var_dump($journal);
					}else{
						$w_vsego = 0;
						$itog[$value]['w_vsego'] = 0;
					}
					
					//по филиалам
					foreach ($filials as $value1){
						$offices = SelDataFromDB('spr_office', $value1, 'offices');
						//var_dump ($actions_cosmet);
						$office = $offices[0]['name'];
						$f_journal = array();
						$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} ORDER BY `create_time` DESC";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							//Всего посещений
							$f_vsego = $number;
							while ($arr = mysql_fetch_assoc($res)){
								array_push($f_journal, $arr);
							}
							/*echo '
								<div class="cellsBlock2">
									<div class="cellName">
										В филиале <b>'.$office.'</b> посещений: <b>'.$f_vsego.'</b>
									</div>
								';*/
							$itog[$value]['office'][$value1]['name'] = $office;
							$itog[$value]['office'][$value1]['f_vsego'] = $f_vsego;
							
							//первичные все по филиалам
							$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} AND `c13`='1' ORDER BY `create_time` DESC";
							$res = mysql_query($query) or die($query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								$p_vsego = $number;
								/*echo '<div class="cellName">
										первичных: <b>'.$p_vsego.'</b>
									</div>
								</div>';*/
								$itog[$value]['office'][$value1]['p_vsego'] = $p_vsego;
								
							}else{
								$p_vsego = 0;
								$itog[$value]['office'][$value1]['p_vsego'] = 0;
								//echo 'В филиале '.$office.' посещений не было.<br />';
								//echo '</div>';
							}
							
							
							//по процедурам пошли
							for ($k = 0; $k < count($actions_cosmet)-2; $k++) { 
								if ($actions_cosmet[$k]['active'] != 0){
									//echo '<div class="cellCosmAct tooltip " style="text-align: center" title="'.$actions_cosmet[$k]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
							
									$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} AND `c{$actions_cosmet[$k]['id']}`='1' ORDER BY `create_time` DESC";
									$res = mysql_query($query) or die($query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										$с1 = $number;
										/*echo '
											<div class="cellsBlock2">
												<div class="cellCosmAct" style="text-align: center; background-color: '.$actions_cosmet[$k]['color'].';">
													'.$actions_cosmet[$k]['full_name'].'
												</div>
												<div class="cellCosmAct">
													'.$с1.'
												';*/
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['name'] = $actions_cosmet[$k]['name'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['full_name'] = $actions_cosmet[$k]['full_name'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['color'] = $actions_cosmet[$k]['color'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['vsego'] = $с1;
										
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][0] = 0;
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][1] = 0;
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][2] = 0;
										
										while ($arr = mysql_fetch_assoc($res)){
											//var_dump($arr['client']);
											$query1 = "SELECT `sex` FROM `spr_clients` WHERE `id`='{$arr['client']}'";
											$res1 = mysql_query($query1) or die($query1);
											$number1 = mysql_num_rows($res1);
											if ($number1 != 0){
												while ($arr1 = mysql_fetch_assoc($res1)){
													if ($arr1['sex'] == 1){
														$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][1]++;
													}elseif ($arr1['sex'] == 2){
														$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][2]++;
													}else{
														$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['sex'][0]++;
													}
														
													//echo 'HHH:'.$arr1['sex'].'<br />';
												}
											}
										}
										
									}else{
										$с1 = 0;
										/*echo '
											<div class="cellsBlock2">
												<div class="cellCosmAct" style="text-align: center; background-color: '.$actions_cosmet[$k]['color'].';">
													'.$actions_cosmet[$k]['full_name'].'
												</div>
												<div class="cellCosmAct">
													'.$с1.'
												';*/
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['name'] = $actions_cosmet[$k]['name'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['full_name'] = $actions_cosmet[$k]['full_name'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['color'] = $actions_cosmet[$k]['color'];
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['vsego'] = 0;
									}
									
									
									
									//по процедурам + первичка
	

									//'SELECT * FROM tab2 WHERE title NOT IN(SELECT title FROM tab1)'
									if ($actions_cosmet[$k]['id'] == 13){
										$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} AND `c13`='1' AND 
										`c1` <> '1' AND `c2` <> '1' AND `c3` <> '1' AND `c4` <> '1' AND `c5` <> '1' AND `c6` <> '1' AND `c7` <> '1' AND `c8` <> '1' AND `c9` <> '1' AND `c10` <> '1' AND 
										`c11` <> '1' AND `c12` <> '1' AND `c14` <> '1' AND `c15` <> '1' AND `c16` <> '1' AND `c17` <> '1' AND `c18` <> '1' AND `c19` <> '1' AND `c20` <> '1' AND `c21` <> '1' AND 
										`c22` <> '1' 
										ORDER BY `create_time` DESC";
									}else{
										$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} AND `c{$actions_cosmet[$k]['id']}`='1' AND `c13`='1' ORDER BY `create_time` DESC";
									}
									//var_dump ($query);
									$res = mysql_query($query) or die($query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										$с1 = $number;
										//echo ' (первичных: '.$с1.')</div>';
										
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['p_vsego'] = $с1;
									}else{
										$с1 = 0;
										$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['p_vsego'] = 0;
										//echo '</div>';
									}
									//echo '</div>';
									
									
									//Для КД не I
	

									//'SELECT * FROM tab2 WHERE title NOT IN(SELECT title FROM tab1)'
									if ($actions_cosmet[$k]['id'] == 12){
										$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} AND `c13` <> '1' AND `c12` = '1' ORDER BY `create_time` DESC";
										//var_dump ($query);
										$res = mysql_query($query) or die($query);
										$number = mysql_num_rows($res);
										if ($number != 0){
											$с1 = $number;
											$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['II_KD'] = $с1;
										}else{
											$с1 = 0;
											$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['II_KD'] = 0;
											//echo '</div>';
										}
										//echo '</div>';
									}

									
									
								}
							}
							
						}else{
							$f_vsego = 0;
							
							$itog[$value]['office'][$value1]['f_vsego'] = 0;
							//echo 'В филиале '.$office.' посещений не было.<br />';
						}
					}
					
				
					
					
				}
				$tabs_workers .= '</ul>';
				
				//var_dump ($itog[381]['office'][16]);	
				
				//Вывод в браузер

				echo '<div id="tabs_w">';
				
				echo '<div class="no_print">'.$tabs_workers.'</div>';
				
				foreach($itog as $key => $value){
					echo '<div id="tabs-'.$key.'">';
						echo '<div class="no_print"> 
							<div class="cellsBlock2">
								<div class="cellName">
									Всего сделано: '.$value['w_vsego'].'
								</div>
							</div>
							</div>';
					//echo $key;
					//var_dump ($value);
					foreach($value['office'] as $key1 => $value1){
						if (isset($value1['name']) && $value1['f_vsego'] > 0){
							echo '
								<div class="cellsBlock4">
									<div class="cellsBlock2">
										<div class="cellName">
											'.$value1['name'].'
										</div>
										<div class="cellName">
											Всего: '.$value1['f_vsego'].' ';
							if ($value1['p_vsego'] > 0){
								echo 'первичных: '.$value1['p_vsego'];
							}
							echo '
										</div>
									</div>
									<div class="no_print"> 
									<div class="cellsBlock2">
										<div class="cellName">';
							//var_dump ($value1);
							foreach ($value1['action'] as $k => $v){
								//var_dump ($v);
							
							
								echo '
									<div class="cellsBlock">
										<div class="cellPriority" style="background-color: '.$v['color'].';">
										</div>
										<div class="cellName">
											'.$v['full_name'].'
										</div>
										<div class="cellName" style="text-align: center; color: ', $v['vsego'] > 0 ? 'green': 'red' ,';">
											'.$v['vsego'].'/'.$v['p_vsego'], $v['vsego'] > 0 ? '/(М:'.$v['sex'][1].',Ж:'.$v['sex'][2].',-:'.$v['sex'][0].')' : '' ,'
										</div>
									</div>';

						
							}		
							echo '
										</div>
									</div>
									</div>';
							//ЭаЭд I II
							echo '
									<div class="cellsBlock2">
										<div class="cellName">
											<canvas id="clientOs_'.$key.'_'.$key1.'_epil" width="400" height="350"></canvas>
										</div>
									</div>';
									
							//I + КД		
							echo '
									<div class="cellsBlock2">
										<div class="cellName">
											<canvas id="clientOs_'.$key.'_'.$key1.'_KDI" width="400" height="350"></canvas>
										</div>
									</div>';
									
							//Все		
							echo '
									<div class="cellsBlock2">
										<div class="cellName">
											<canvas id="clientOs_'.$key.'_'.$key1.'_All" width="400" height="350"></canvas>
										</div>
									</div>';
							echo '

								</div>';
								
								
								
							//графики	
							//$key - сотрудник
							//$key1 - филиал
							
							/*$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`={$key} AND `office`={$key1} AND `c{$actions_cosmet[$k]['id']}`='1' AND `c13`='1' ORDER BY `create_time` DESC";
							$res = mysql_query($query) or die($query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								$с1 = $number;
								
							$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['p_vsego'] = $с1;
							}else{
								$с1 = 0;
								$itog[$value]['office'][$value1]['action'][$actions_cosmet[$k]['id']]['p_vsego'] = 0;
							}*/
							
							echo '		
									<script>
										/*var randomScalingFactor = function(){ return Math.round(Math.random()*100)};*/
										var barData = {
											labels: [\'Эа Эд I\', \'Эа Эд II\'],
											datasets: [
												{
													fillColor : "rgba(220,220,220,0.5)",
													strokeColor : "rgba(220,220,220,0.8)",
													highlightFill: "rgba(220,220,220,0.75)",
													highlightStroke: "rgba(220,220,220,1)",
													data: ['.$value1['action'][1]['p_vsego'].', '.($value1['action'][1]['vsego'] - $value1['action'][1]['p_vsego']).']
												},
												{
													fillColor : "rgba(151,187,205,0.5)",
													strokeColor : "rgba(151,187,205,0.8)",
													highlightFill : "rgba(151,187,205,0.75)",
													highlightStroke : "rgba(151,187,205,1)",
													data: ['.$value1['action'][22]['p_vsego'].', '.($value1['action'][22]['vsego'] - $value1['action'][22]['p_vsego']).']
												}
											]
										};

										var context = document.getElementById(\'clientOs_'.$key.'_'.$key1.'_epil\').getContext(\'2d\');
										var clientsChart = new Chart(context).Bar(barData);
									</script>
									';
									
							echo '		
									<script>
										var barData = {
											labels: [\'I\', \'I + КД\', \'II + КД\'],
											datasets: [
												{
													fillColor : "rgba(220,220,220,0.5)",
													strokeColor : "rgba(220,220,220,0.8)",
													highlightFill: "rgba(220,220,220,0.75)",
													highlightStroke: "rgba(220,220,220,1)",
													data: ['.$value1['p_vsego'].', '.$value1['action'][12]['p_vsego'].', '.$value1['action'][12]['II_KD'].']
												}
											]
										};

										var context = document.getElementById(\'clientOs_'.$key.'_'.$key1.'_KDI\').getContext(\'2d\');
										var clientsChart = new Chart(context).Bar(barData);
									</script>
									';
									
							echo '		
									<script>
										var barData = {
											labels: [';
							foreach ($value1['action'] as $key2 => $value2){
								echo '\''.$value2['full_name'].'\',';
							}
							echo 					'],
											datasets: [
												{
													fillColor : "rgba(151,187,205,0.5)",
													strokeColor : "rgba(151,187,205,0.8)",
													highlightFill : "rgba(151,187,205,0.75)",
													highlightStroke : "rgba(151,187,205,1)",
													data: 	[';
							foreach ($value1['action'] as $key2 => $value2){
								echo '\''.$value2['vsego'].'\',';
							}
							echo
															']
												}
											]
										};

										var context = document.getElementById(\'clientOs_'.$key.'_'.$key1.'_All\').getContext(\'2d\');
										var clientsChart = new Chart(context).Bar(barData);
									</script>
									';
						}
	
					
					}
					
						echo '</div>';
				}
				
				
				echo '</div>';
				
				
				
				
				

				
				
				
				mysql_close();

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>