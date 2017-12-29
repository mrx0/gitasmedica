<?php

//cosmet.php
//Косметология

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'widget_calendar.php';
			
			$filter = FALSE;
			$dop = '';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Косметология</h1>
				</header>';	

			DrawFilterOptions ('cosmet', $it, $cosm, $stom, $workers, $clients, $offices, $god_mode);
			
			echo '
					<div id="data">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';

				///////////////////
				if ($_GET){
					$filter_rez = array();
				
					if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
						if (isset($_GET['m']) && isset($_GET['y'])){
							
							//операции со временем						
							$month = $_GET['m'];
							$year = $_GET['y'];
							$datestart = strtotime('01.'.$month.'.'.$year);
							
							//нулевой день следующего месяца - это последний день предыдущего
							$lastday = mktime(0, 0, 0, $month+1, 0, $year);
							$datefinish = strtotime(strftime("%d", $lastday).'.'.$month.'.'.$year.' 23:59:59');
							
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
						
						foreach ($_GET as $key => $value){
							$dop .= '&'.$key.'='.$value;
						}
						
					}else{
							$sw = '';
							$type = '';
					}
					
				}else{
					//операции со временем						
					$month = date('m');		
					$year = date('Y');
					$datestart = strtotime('01.'.$month.'.'.$year);
					
					//нулевой день следующего месяца - это последний день предыдущего
					$lastday = mktime(0, 0, 0, $month+1, 0, $year);
					$datefinish = strtotime(strftime("%d", $lastday).'.'.$month.'.'.$year.' 23:59:59');
					
					$_GET['datastart'] = date('d.m.Y', $datestart);
					$_GET['dataend'] = date('d.m.Y', $datefinish);
					$_GET['ended'] = 0;				
					
					$filter_rez = filterFunction ($_GET);
				}
				
				echo widget_calendar ($month, $year, 'cosmet.php', 'filter=yes'.$dop);
				
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
					
				if (($stom['see_all'] == 1) || $god_mode){	
					if ($filter){
						echo '<li class="cellsBlock" style="width: 400px; margin-top: 10px; border: 1px dotted green; background-color: rgba(158, 252, 158, 0.5); padding: 7px;">
								';
						echo $filter_rez[0];
						echo '
							</li>';
					}
				}
				
			echo '<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">';

			if (($cosm['add_own'] == 1) || $god_mode){
				/*echo '
						<a href="add_error.php" class="b">Добавить</a>';*/
			}
			
			echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';				
			
			echo '</li></ul>';
			
			
			if ($journal != 0){
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
				
				echo '
					<p style="margin: 5px 0; padding: 1px; font-size:80%;">
						Быстрый поиск по врачу: 
						<input type="text" class="filter filterInCosmet" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						Показано: <span class="countCosmBlocks">0</span>.
						
					</p>
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
							$client = 'не указан';
						}
						//, isFired ? 'style="background-color: rgba(161,161,161,1);"' : '' ,
						//echo $journal[$i]['worker'];
						echo '
							<li class="cellsBlock cellsBlockHover cosmBlock">
									<a href="task_cosmet.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$client.'</a>';
						if (($cosm['see_all'] == 1) || $god_mode){
							echo '<div class="cellName 4filter" id="4filter" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user', true).'</div>';
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
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
            echo '
						</ul>
						<div id="doc_title">Косметология - Асмедика</div>
					</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>