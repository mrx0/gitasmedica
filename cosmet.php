<?php

//cosmet.php
//Косметология

	require_once 'header.php';
	//var_dump ($enter_ok);
	//var_dump ($god_mode);
	
	if ($enter_ok){
		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$filter = FALSE;
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Косметология</h1>';
					
			//$user = SelDataFromDB('spr_workers', $_SESSION['id'], 'user');
			//var_dump ($user);
			//echo 'Польз: '.$user[0]['name'].'<br />';
			
			if (($cosm['add_own'] == 1) || $god_mode){
				echo '
						<a href="add_task_cosmet.php" class="b">Добавить</a>';
			}
				
			if (($cosm['see_all'] == 1) || $god_mode){
				echo '
						<a href="stat_cosm.php" class="b">Статистика</a>';
			}
			if (($cosm['see_all'] == 1) || $god_mode){
				echo '
						<a href="stat_cosm_ex.php" class="b">Статистика с фильтром</a>';
			}
				///////////////////
				if ($_GET){
					//echo 1;
					//var_dump ($_GET);
					$filter_rez = array();
					if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
						if (isset($_GET['ttime'])){
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
					}else{
						$ttime = explode('.', $_GET['datastart']);			
						$month = $ttime[1];
						$year = $ttime[2];
					}
						$_GET['ended'] = 0;	
						$_GET['datatable'] = 'journal_cosmet1';
		
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
				//echo $month_mintime.'*'.$month_maxtime.'*'.$year_mintime.'*'.$year_maxtime;
			
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
			$journal = 0;
			
				
				$sw = $filter_rez[1];
				if ($cosm['see_own'] == 1){
					$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} AND `worker`='".$_SESSION['id']."' ORDER BY `create_time` DESC";
				}
				if (($cosm['see_all'] == 1) || $god_mode){
					$query = "SELECT * FROM `journal_cosmet1` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
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
				mysql_close();
				
				echo '
					<form action="cosmet.php" id="months_form" method="GET">
						<input type="hidden" name="filter" value="yes">
						<select name="ttime" onchange="this.form.submit()">'.
							$li_months
						.'</select>
					</form>';	
					
			if (($cosm['see_all'] == 1) || $god_mode){		
				if (!$filter){
					echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
				}else{
					echo $filter_rez[0];
				}
			}
			echo '
				</header>';
				
			DrawFilterOptions ('cosmet', $it, $cosm, $stom, $workers, $clients, $offices, $god_mode);
					
			if ($journal != 0){
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
				
				echo '
					<p style="margin: 5px 0; padding: 1px; font-size:80%;">
						Быстрый поиск по врачу: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>';
				if (($cosm['see_all'] == 1) || $god_mode){
					echo '<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';
				}

				//отсортируем по nomer

				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
				//return $rez;
				//var_dump ($actions_cosmet);
				
				for ($i = 0; $i < count($actions_cosmet)-2; $i++) { 
					if ($actions_cosmet[$i]['active'] != 0){
						echo '<div class="cellCosmAct tooltip " style="text-align: center; background-color:#FEFEFE;" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
					}
				}
				echo '
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
	
				for ($i = 0; $i < count($journal); $i++) {
					
					//if (($journal[$i]['create_time'] >= $datestart)  && ($journal[$i]['create_time'] <= $datefinish)){
						//Надо найти имя клиента
						$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
						}else{
							$client = 'unknown';
						}
						//, isFired ? 'style="background-color: rgba(161,161,161,1);"' : '' ,
						//echo $journal[$i]['worker'];
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_cosmet.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$client.'</a>';
						if (($cosm['see_all'] == 1) || $god_mode){
							echo '<a href="user.php?id='.$journal[$i]['worker'].'" class="cellName ahref" id="4filter" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user').'</a>';
						}		
						
						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						
						/*!!!Лайфхак для посещений из-за переделки структуры бд*/
						foreach($journal[$i] as $key => $value){
							if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
								$decription_temp_arr[mb_substr($key, 1)] = $value;
							}
						}
						
						//var_dump ($decription_temp_arr);
						
						$decription = $decription_temp_arr;

						//array_multisort($data_nomer, SORT_NUMERIC, $decription);
						
						//var_dump ($decription);		
						//var_dump ($actions_cosmet);		
						
						//for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						foreach ($actions_cosmet as $key => $value) { 
							$cell_color = '#FFFFFF';
							$action = '';
							if ($value['active'] != 0){
								if (isset($decription[$value['id']])){
									if ($decription[$value['id']] != 0){
										$cell_color = $value['color'];
										$action = 'V';
									}
									echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'</div>';
								}else{
									echo '<div class="cellCosmAct" style="text-align: center"></div>';
								}
							}
						}
						
						echo '
									<div class="cellText" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$journal[$i]['comment'].'</div>
							</li>';
					//}
				}
				echo '
						</ul>
					</div>';
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>