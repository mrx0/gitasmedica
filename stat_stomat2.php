<?php

//stomat.php
//Стоматология


	//Санация



	require_once 'header.php';
	//var_dump ($enter_ok);
	//var_dump ($god_mode);
	
	if ($enter_ok){
		//var_dump($_SESSION);
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$filter = FALSE;
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>______________</h1>';
					
			//$user = SelDataFromDB('spr_workers', $_SESSION['id'], 'user');
			//var_dump ($user);
			//echo 'Польз: '.$user[0]['name'].'<br />';
			

				///////////////////
				if ($_GET){
					//echo 1;
					//var_dump ($_GET);
					$filter_rez = array();
					if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
						$_GET['sw'] = 'stat_stomat2';
						if (isset($_GET['ttime'])){
							//var_dump($_GET['ttime']);
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
							//2 месяца
							//$datefinish = time()-60*60*24*60;
							$datefinish = time();
							//echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';			
							$_GET['datastart'] = date('d.m.Y', $datestart);
							$_GET['dataend'] = date('d.m.Y', $datefinish);
							
							//var_dump((time()-$datestart).' <-> '.(time()-$datefinish).' = '.(180*60*60*24).' <-> '.(365*60*60*24));
							
							if((time()-$datestart >= 180*60*60*24) && (time()-$datestart < 365*60*60*24)){
								$selected1 = '';
								$selected2 = 'selected';
								$selected3 = '';
							}elseif(time()-$datestart >= 365*60*60*24){
								$selected1 = '';
								$selected2 = '';
								$selected3 = 'selected';
							}else{
								$selected1 = 'selected';
								$selected2 = '';
								$selected3 = '';
							}
							
						}else{
							$ttime = explode('.', $_GET['datastart']);			
							$month = $ttime[1];
							$year = $ttime[2];
									
							$selected1 = 'selected';
							$selected2 = '';
							$selected3 = '';
						}
						$_GET['ended'] = 0;	
						$_GET['datatable'] = 'journal_tooth_status';
		
						$filter_rez = filterFunction ($_GET);
						$filter = TRUE;
						//var_dump ($filter_rez);

					}else{
							$sw .= '';
							$type = '';
							
							$selected1 = 'selected';
							$selected2 = '';
							$selected3 = '';
					}
					
				}else{
					//echo 4;
					//операции со временем						
					$ttime = time();			
					$month = date('n', $ttime);		
					$year = date('Y', $ttime);
					//3 месяца
					$datestart = time()-60*60*24*91;
					//2 месяца
					//$datefinish = time()-60*60*24*60;
					$datefinish = time();
					//echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';			
					$_GET['datastart'] = date('d.m.Y', $datestart);
					$_GET['dataend'] = date('d.m.Y', $datefinish);
					$_GET['ended'] = 0;				
					
					$filter_rez = filterFunction ($_GET);
					
					$selected1 = 'selected';
					$selected2 = '';
					$selected3 = '';

				}
				
				//Тут мы создаем массив с месяцами и годами между самым первым посещением и последним
				$arr_temp = SelMINDataFromDB ('journal_tooth_status', 'create_time');
				$mintime = $arr_temp[0]['create_time'];
				//var_dump ($mintime[0]['create_time']);
				$arr_temp = SelMAXDataFromDB ('journal_tooth_status', 'create_time');
				$maxtime = $arr_temp[0]['create_time'];
				//echo date('d.m.y H:i', $maxtime);
				$month_mintime = date('n', $mintime);
				$month_maxtime = date('n', $maxtime);
				$year_mintime = date('Y', $mintime);
				$year_maxtime = date('Y', $maxtime);
				//echo $month_mintime.'*'.$month_maxtime.'*'.$year_mintime.'*'.$year_maxtime;
			
				//var_dump(date('n', time()-60*60*24*91));
			
				/*$Diff_Months = array();
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
				);*/
				
				//var_dump($Diff_Months);
				
				/*for ($i=0; $i<count($Diff_Months); $i++){
					$arr_temp = explode('.', $Diff_Months[$i]);
					
					if (($year == $arr_temp[1]) && ($month == $arr_temp[0])){
						$selected = 'selected';
						$selected_date = $m[$arr_temp[0]].' '.$arr_temp[1];
					}else{
						$selected = '';
					}			
					
					//$li_months .= '<option value="'.$arr_temp[0].'_'.$arr_temp[1].'" '.$selected.' >'.$m[$arr_temp[0]].' '.$arr_temp[1].'</option>';
				}*/
				//var_dump(date('n', time()-60*60*24*365).'_'.date('Y', time()-60*60*24*365));
				$li_months = '
					<option value="'.date('n', time()-60*60*24*90).'_'.date('Y', time()-60*60*24*90).'" '.$selected1.' >Последние 3 месяца</option>
					<option value="'.date('n', time()-60*60*24*180).'_'.date('Y', time()-60*60*24*180).'" '.$selected2.' >Последние полгода</option>
					<option value="'.date('n', time()-60*60*24*365).'_'.date('Y', time()-60*60*24*365).'" '.$selected3.' >Последний год</option>
				';
				//////////////////////////////////////////////////	
				$journal = 0;
			
				
				$sw = $filter_rez[1];
				
				if ($stom['see_own'] == 1){
					$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`='".$_SESSION['id']."' ORDER BY `create_time` DESC";
					//$query = "SELECT `id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker` FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`='".$_SESSION['id']."' ORDER BY `create_time` DESC";
				}
				if (($stom['see_all'] == 1) || $god_mode){
					$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
					//$query = "SELECT `id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker` FROM `journal_tooth_status` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
				}
				
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
						array_push($rez, $arr);
					}
					$journal = $rez;
				}else{
					$journal = 0;
				}
				//mysql_close();
				
				echo '
					<form action="stat_stomat2.php" id="months_form" method="GET">
						<input type="hidden" name="filter" value="yes">
						<select name="ttime" onchange="this.form.submit()">'.
							$li_months
						.'</select>
					</form>';	
					
					
					
				if (($stom['see_all'] == 1) || $god_mode){		
					if (!$filter){
						//echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
					}else{
						//echo $filter_rez[0];
					}
				}
			
				echo '
					</header>';
					
				DrawFilterOptions ('stat_stomat2', $it, $stom, $stom, $workers, $clients, $offices, $god_mode);
			
			
			if ($journal != 0){
				//var_dump ($journal);

				//Цвет результата
				$rez_color = '';
				
				if (($stom['see_all'] == 1) || $god_mode){	
					echo '
						<p style="margin: 5px 0; padding: 1px; font-size:80%;">
							Быстрый поиск по врачу: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
							
						</p>';
				
				}
				echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>';
				if (($stom['see_all'] == 1) || $god_mode){
					echo '<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';
				}

				echo '
								<div class="cellCosmAct" style="text-align: center">-</div>
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
				
				
				$all_clients_arr = array();
				
				//Пробежка по пациентам, собираем массив с последним посещением каждого
				foreach ($journal as $value){
					//var_dump($value);
					if (isset($all_clients_arr[$value['client']])){
						//var_dump($all_clients_arr[$value['client']]['client'].' = '.$all_clients_arr[$value['client']]['create_time'].' - '.$value['create_time']);
						if ($all_clients_arr[$value['client']]['create_time'] < $value['create_time']){
							$all_clients_arr[$value['client']] = $value;
						}
					}else{
						$all_clients_arr[$value['client']] = $value;
					}
					unset ($all_clients_arr[$value['client']]['client']);
				}
				
				//var_dump(count($all_clients_arr));
				//var_dump($all_clients_arr);
				
				//Минимальное кол-во времени между осмотром и работой 10 часов
				$min_work_time = 60*60*10;
				
				foreach($all_clients_arr as $cl_id => $value) {
					$kom_arr = array();
					$komm = '';
					$removes_me = false;
					//var_dump ($value);
						
					$min_work_time_rez = $value['create_time'] - $min_work_time;
					
					$next_rez = array();
					$only_one = true;
					$dop_img = '';
					$sanat_status = false;
					
					//var_dump($next_rez);
					
					//Выбрали все посещения пациента кроме последнего и того который следовал сразу после осмотра (если в один день был и осмотр и работа)
					$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$cl_id}' AND `id` <> '{$value['id']}' AND `create_time` < '{$min_work_time_rez}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					//var_dump($query);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($next_rez, $arr);
						}
						$only_one = false;
					}else{
						$only_one = true;
					}
					
					//var_dump ($next_rez);
					//array_push($kom_arr, $next_rez[$i]['id']);						
					
					/*if (!empty($next_rez)){
						for ($i=0; $i < count($next_rez); $i++){
							//var_dump ($next_rez[$i]);
							//Смотрим какие посещения были раньше текущего у этого пациента
							if ($next_rez[$i]['create_time'] < $value['create_time']){
								$komm .= 'Было '.$next_rez[$i]['id'].'; ';
							}
							/*if ($next_rez[$i]['create_time'] > $value['create_time']){
								$komm .= 'Будет '.$next_rez[$i]['id'].'; ';
							}*/
					/*	}
					}*/
					//Дополнительно
					$dop = array();
					/*$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$journal[$i]['id']}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($dop, $arr);
						}
						
					}
					//var_dump ($dop);
					if (!empty($dop)){
						if ($dop[0]['pervich'] == 1){
							$dop_img .= '<img src="img/pervich.png" title="Первичное">';
						}
						if ($dop[0]['noch'] == 1){
							$dop_img .= '<img src="img/night.png" title="Ночное">';
						}
					}*/
					
					//var_dump($kom_arr);
					
					//Если последнее посещение было 2 месяцев назад
					if ($value['create_time'] < time()-60*60*24*59){
						
						//Надо найти клиента
						$clients = SelDataFromDB ('spr_clients', $cl_id, 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
							if ($clients[0]["birthday"] != -1577934000){
								$cl_age = getyeardiff($clients[0]["birthday"]);
							}else{
								$cl_age = 0;
							}
						}else{
							$client = 'unknown';
							$cl_age = 0;
						}
						
						
						//ЗО и тд	
						$dop = array();							
						$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$value['id']}'";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}
												
						include_once 'tooth_status.php';						
						include_once 't_surface_name.php';
						include_once 't_surface_status.php';
						
						
						$arr = array();
						$decription = $value;
						
						//var_dump($decription);
						
						unset($decription['id']);
						unset($decription['office']);
						unset($decription['client']);
						unset($decription['create_time']);
						unset($decription['create_person']);
						unset($decription['last_edit_time']);
						unset($decription['last_edit_person']);
						unset($decription['worker']);
						
						unset($decription['comment']);
						
						$t_f_data = array();
						
						//собрали массив с зубами и статусами по поверхностям
						foreach ($decription as $key => $value3){
							$surfaces_temp = explode(',', $value3);
							//var_dump($surfaces_temp);
							foreach ($surfaces_temp as $key1 => $value1){
								///!!!Еба костыль
								if ($key1 < 13){
									$t_f_data[$key][$surfaces[$key1]] = $value1;
								}
							}
						}
						//var_dump ($t_f_data);
						if (!empty($dop[0])){
							//var_dump($dop[0]);
							unset($dop[0]['id']);
							//var_dump($dop[0]);
							foreach($dop[0] as $key => $value3){
								//var_dump($value);
								if ($value3 != '0'){
									//var_dump($value);
									$dop_arr = json_decode($value3, true);
									//var_dump($dop_arr);
									foreach ($dop_arr as $n_key => $n_value){
										if ($n_key == 'zo'){
											$t_f_data[$key]['zo'] = $n_value;
											//$t_f_data_draw[$key]['zo'] = $n_value;
										}
										if ($n_key == 'shinir'){
											$t_f_data[$key]['shinir'] = $n_value;
											//$t_f_data_draw[$key]['shinir'] = $n_value;
										}
										if ($n_key == 'podvizh'){
											$t_f_data[$key]['podvizh'] = $n_value;
											//$t_f_data_draw[$key]['podvizh'] = $n_value;
										}
									}
								}
							}
						}
						
						//var_dump ($t_f_data);		
						
						
						if (Sanation2($value['id'] ,$t_f_data, $cl_age)){
							$sanat_status = true;
							$rez_color = "style= 'background: rgba(87,223,63,0.7);'";
						}else{
							$rez_color = "style= 'background: rgba(255,39,119,0.7);'";
						}
					}
					//Если не было посещений позже указанного
					if ($only_one && ($value['create_time'] <= time()-60*60*24*59)){
						
						//посмотрим, было ли направление
						$removes = SelDataFromDB ('removes',$value['id'], 'task');
						
						//var_dump($removes);
						if ($removes != 0){
							//$removes_me = true;
							foreach($removes as $removes_value){
								$komm .= 'Направлен к '.WriteSearchUser('spr_workers', $removes_value['whom'], 'user').' ';
								if ($sanat_status){
									$rez_color = "style = 'background: rgba(55,127,223,0.7);'";
								}
							}
						}
						
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_stomat_inspection.php?id='.$value['id'].'" class="cellName ahref" title="'.$value['id'].'">'.date('d.m.y H:i', $value['create_time']).' '.$dop_img.'</a>
									<a href="client.php?id='.$cl_id.'" class="cellName ahref">'.WriteSearchUser('spr_clients', $cl_id, 'user').'</a>';
						if (($stom['see_all'] == 1) || $god_mode){
							echo '<a href="user.php?id='.$value['worker'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_workers', $value['worker'], 'user').'</a>';
						}		

						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						
						$decription = $decription_temp_arr;

						echo '
									<div class="cellCosmAct">
										<a href="#" onclick="window.open(\'task_stomat_inspection_window.php?id='.$value['id'].'\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;">
											<img src="img/tooth_state/1.png">
										</a>	
									</div>';
						echo '
									<div class="cellText" '.$rez_color.'>'.$komm.'';
						//var_dump(Sanation2($value['id'] ,$value));
						echo 
									'</div>
							</li>';
					}
				}
				echo '
						</ul>
					</div>';
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>