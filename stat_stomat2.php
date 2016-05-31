<?php

//stomat.php
//Стоматология

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
					<h1>Стоматология</h1>';
					
			//$user = SelDataFromDB('spr_workers', $_SESSION['id'], 'user');
			//var_dump ($user);
			//echo 'Польз: '.$user[0]['name'].'<br />';
			
			if (($stom['add_own'] == 1) || $god_mode){
				echo '
						<!--<a href="add_task_stomat.php" class="b">Добавить</a>-->
						<a href="clients.php" class="b">Добавить</a>';
			}
			

				///////////////////
				if ($_GET){
					//echo 1;
					//var_dump ($_GET);
					$filter_rez = array();
					if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
						$_GET['sw'] = 'stat_stomat2';
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
						$_GET['datatable'] = 'journal_tooth_status';
		
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
				if ($stom['see_own'] == 1){
					$query = "SELECT `id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker` FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`='".$_SESSION['id']."' ORDER BY `create_time` DESC";
				}
				if (($stom['see_all'] == 1) || $god_mode){
					$query = "SELECT `id`, `office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker` FROM `journal_tooth_status` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
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
					<form action="stat_stomat2.php" id="months_form" method="GET">
						<input type="hidden" name="filter" value="yes">
						<select name="ttime" onchange="this.form.submit()">'.
							$li_months
						.'</select>
					</form>';	
					
					
					
			if (($stom['see_all'] == 1) || $god_mode){		
				if (!$filter){
					echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
				}else{
					echo $filter_rez[0];
				}
			}
			
			echo '
				</header>';
				
			DrawFilterOptions ('stat_stomat2', $it, $stom, $stom, $workers, $clients, $offices, $god_mode);
			
			
			if ($journal != 0){
				//var_dump ($journal);

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
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
				
				//!!!!!!тест санации Sanation ($journal);
				
				$all_clients_arr = array();
				
				foreach ($journal as $value){
					//var_dump($value);
					$all_clients_arr[$value['client']] = $value;
					unset ($all_clients_arr[$value['client']]['client']);
				}
				
				var_dump(count($all_clients_arr));
				
				foreach($all_clients_arr as $cl_id => $value) {
					
						//Надо найти имя клиента
						$clients = SelDataFromDB ('spr_clients', $cl_id, 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
						}else{
							$client = 'unknown';
						}
						
						//Дополнительно
						$dop = array();
						$dop_img = '';
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
						
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_stomat_inspection.php?id='.$value['id'].'" class="cellName ahref" title="'.$value['id'].'">'.date('d.m.y H:i', $value['create_time']).' '.$dop_img.'</a>
									<a href="client.php?id='.$cl_id.'" class="cellName ahref">'.$client.'</a>';
						if (($stom['see_all'] == 1) || $god_mode){
							echo '<a href="user.php?id='.$value['worker'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_workers', $value['worker'], 'user').'</a>';
						}		
						
						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						

						$decription = $decription_temp_arr;


						echo '
									<div class="cellText">_._</div>
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