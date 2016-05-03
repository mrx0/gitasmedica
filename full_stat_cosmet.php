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
			
			//$offices = SelDataFromDB('spr_office', '', '');
			
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
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
				</header>';
				
			$journal = 0;
			
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
					//var_dump ($_GET);
					//echo (time().'<br />');
					//echo date('d.m.Y H:i', strtotime($_GET['datastart'])).'<br />';
	
					$filter_rez = filterFunction ($_GET);
					$filter = TRUE;
					//var_dump ($filter_rez);
				}else{
					//echo 3;
					/*if  (!empty($_GET['sort_added'])){
					//Для сортировки по времени добавления
						$sw .= '';
						$type = 'sort_added';
					}elseif  (!empty($_GET['sort_filial'])){
					//Для сортировки по филиалу добавления
						$sw .= '';
						$type = 'sort_filial';
					}else{*/
						$sw .= '';
						$type = '';
					//}
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
				
				//var_dump ($filter_rez);
			}
				
			if ($filter){
				//echo 5;
				$sw = $filter_rez[1];
				//echo $sw;
				//var_dump ($filter_rez);
				//echo $filter_rez[0];
				//echo $filter_rez[1];
				if (($cosm['see_all'] == 1) || $god_mode){
					//echo 6;
					//echo $sw;
					$journal = SelDataFromDB('journal_cosmet1', $sw, 'filter');
				}
			}else{
				//echo 7;
				$sw = $filter_rez[1];
				if (($cosm['see_all'] == 1) || $god_mode){
					//echo 8;
					//echo $sw;
					$journal = SelDataFromDB('journal_cosmet1', $sw, 'filter');
				}	
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
						

			echo '
				<form action="full_stat_cosmet.php" id="months_form" method="GET">
					<input type="hidden" name="filter" value="yes">
					<select name="ttime" onchange="this.form.submit()">'.
						$li_months
					.'</select>
			</form>';


	
			if ($journal != 0){
				
				//var_dump ($journal);
				


	
				//Вот тут полный пиздец...  Заебался выделываться с этой хуйнёй
				//Выборка оригинальных пациентов с общим количеством процедур
				//1.11.2015 тут же соберем массивы для статистики
				
				$client = array();
				$description = array();
				$description_w = array();
				$description_summ = '';
				$description_w_summ = '';
				///////////////////////////////////////////////////////////////
				$office = array();
				$doctors = array();
				$worker = array();
				$temp_arr_poseschen = array();
				$pervich = 0;
				
				$pervich_arr_temp = array();
				$pervich_arr = array(
					//id сотрудника
					'worker' => array(
						//id процедуры
						'proc' => array(),
						//кол-во первичных
						//'perv' => array(),
					)
				);
				
				//var_dump($pervich_arr);

				for ($i=0; $i<count($journal); $i++){ 
					$client[$i] = $journal[$i]['client'];
					$description[$i] = $journal[$i]['description'];
					$description_w[$i] = $journal[$i]['description'];
					
					//****//***//**//*//**//***//****//
					$worker[$i] = $journal[$i]['worker'];
					//////////////////////////////////////////////////////////////
					
					//посещения по филиалам
					if (!array_key_exists($journal[$i]['office'], $office)){
						$office[$journal[$i]['office']] = 1;
					}else{
						$office[$journal[$i]['office']]++;
					}
					//посещения по докторам
					if (!array_key_exists($journal[$i]['worker'], $doctors)){
						$doctors[$journal[$i]['worker']] = 1;
					}else{
						$doctors[$journal[$i]['worker']]++;
					}
					
					$temp_arr_poseschen = json_decode($journal[$i]['description'], true);
					if ($temp_arr_poseschen[13] == 1){
						$pervich++;
					}
					
					
					$pervich_arr_temp = json_decode($journal[$i]['description'], true);
					//var_dump ($pervich_arr_temp);
					if ($pervich_arr_temp[13] != 0){
						//var_dump ($pervich_arr_temp);
						foreach ($pervich_arr_temp as $key => $value){
							if ($value > 0){
								if (!isset($pervich_arr[$journal[$i]['worker']])){
									$pervich_arr[$journal[$i]['worker']] = array();
								}
								if (!isset($pervich_arr[$journal[$i]['worker']][$key])){
									$pervich_arr[$journal[$i]['worker']][$key] = 0;
								}
								$pervich_arr[$journal[$i]['worker']][$key]++;
							}
						}
						
					}

					//echo $worker[$i].'X'.$description_w[$i].'<br />';
					
					
					
					
				}
				//var_dump ($pervich_arr);
				
				
				$client_rez = array();
				$sex = array(0 => 0, 1=> 0, 2=>0);
				
				
				//$client_rez = array_unique($client);
				$client_rez = $client;
				

				//var_dump($client);
				//var_dump($description);
				//var_dump($description_w);

				foreach($client_rez as $value){
					//echo $value.'.';
					$clients = SelDataFromDB ('spr_clients', $value, 'client_id');
					//var_dump ($clients[0]);
					if ($clients[0]['sex'] == 1){
						$sex[1]++;
					}elseif ($clients[0]['sex'] == 2){
						$sex[2]++;
					}else{
						$sex[0]++;
					}
				}
				
				
				//массив для посещений по работникам
				$temp_arr_w = array();
				
				
				for ($i=0; $i<count($worker); $i++){
					if (!array_key_exists($worker[$i], $temp_arr_w)){
						for ($j=$i+1; $j<count($worker); $j++){
							if ($worker[$i] == $worker[$j]){
								//echo $worker[$i].'<br />'.$description_w[$i].'<br />'.$description_w[$j].'<br /><br />';
								$description_w[$i] = json_encode(ArraySum(json_decode($description_w[$i], true), json_decode($description_w[$j], true)));
								//echo $description_w[$i].'<br /><br />';
							}
						}
						if (!array_key_exists($worker[$i], $temp_arr_w)){
							$temp_arr_w[$worker[$i]] = $description_w[$i];
						}else{
							$temp_arr_w[$worker[$i]] .= $description_w[$i];
						}
					}
				}
				
				//массив для посещений по клиентам
				$temp_arr = array();

				for ($i=0; $i<count($client); $i++){
					if (!array_key_exists($client[$i], $temp_arr)){
						//echo '<br />id: '.$client[$i].'<br />';
						for ($j=$i+1; $j<count($client); $j++){
							//echo '<br />i='.$i.'; j='.$j.'<br />';
							if ($client[$i] == $client[$j]){
								//echo '<b>+OK</b>.<br />';
								//echo $description[$i].'<br />';
								//echo $description[$j].'<br />';
								//var_dump (json_decode($description[$i], true));
								$description[$i] = json_encode(ArraySum(json_decode($description[$i], true), json_decode($description[$j], true)));
								//array_splice($journal , $i, 1);
								//echo $description[$i].'<br />';
							}//else{
								//echo '<b>-NOT</b>.<br />';
							//}
						}
						//echo '<br />id: '.$client[$i].'<br />';
						if (!array_key_exists($client[$i], $temp_arr)){
							$temp_arr[$client[$i]] = $description[$i];
						}else{
							$temp_arr[$client[$i]] .= $description[$i];
						}
						//echo '<br /><br />temp_arr:<br />';
						//var_dump($temp_arr);
						//echo '<br />///********************////***********************///<br />';
					}
				}

				
				//var_dump($temp_arr);
				//var_dump($temp_arr_w);
				//Кончился пиздец
				
				
				
				
			
				
				echo '

					<div id="data">
						'.$selected_date.'
						<br />';
				echo '
						<b>Всего посещений:</b> '.count($journal).'
						<br />';
				echo '
						<b>Первичных:</b> '.$pervich.'
						<br />';
				echo '
						<b>По филиалам:</b><br />';
				foreach ($office as $key => $value){
					$filial = SelDataFromDB('spr_office', $key, 'offices');
					echo $filial[0]['name'].': '.$value.'<br />';
				}
				echo '
						<b>По врачам:</b><br />';
				foreach ($doctors as $key => $value){
					//$filial = SelDataFromDB('spr_office', $key, 'offices');
					echo WriteSearchUser('spr_workers', $key, 'user').': '.$value.'<br />';
				}
				//var_dump($worker);
				echo '
						<b>По полу:</b><br />';
				echo 'Женщины: '.$sex[2].'<br />';
				echo 'Мужчины: '.$sex[1].'<br />';
				echo 'Пол не указан: '.$sex[0].'<br />';
							
							
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
				//var_dump ($actions_cosmet);
				
				//отсортируем по nomer

				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
				//return $rez;
				//var_dump ($actions_cosmet);
				
				
				
				//Ститистика по кол-ву процедур по работникам
				echo '	<!--<br />
						Быстрый поиск по пациенту: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						-->
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">
								<!--<div class="cellName" style="text-align: center">Дата</div>-->
								<div class="cellName" style="text-align: center">Врач</div>';				

				for ($i = 0; $i < count($actions_cosmet); $i++) { 
					if ($actions_cosmet[$i]['active'] != 0){
						
						echo '<div class="cellCosmAct" style="text-align: center" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
					}
				}
				echo '
								<!--<div class="cellText" style="text-align: center">Комментарий</div>-->
							</li>';
				foreach ($temp_arr_w as $key => $value) {
					//Надо найти имя работника
					$workers = SelDataFromDB ('spr_workers', $key, 'user');
					if ($workers != 0){
						$wworker = $workers[0]["name"];
					}else{
						$wworker = 'unknown';
					}
					echo '
						<li class="cellsBlock cellsBlockHover">
								<a href="user.php?id='.$key.'" class="cellName ahref" id="4filter">'.$wworker.'</a>';
								
					$description_w = array();
					$description_w = json_decode($value, true);
					
					//var_dump ($description_w);		
					
					foreach ($actions_cosmet as $key1 => $value1) { 
						$cell_color = '#FFFFFF';
						$action = '';
						$pervich_p = 0;
						if ($value1['active'] != 0){
							if (isset($description_w[$value1['id']])){
								if ($description_w[$value1['id']] != 0){
									$cell_color = $value1['color'];
									$action = $description_w[$value1['id']];
									
									if ($value1['id'] != 13){
										if (isset($pervich_arr[$key])){
											if (isset($pervich_arr[$key][$value1['id']])){
												$pervich_p = $pervich_arr[$key][$value1['id']];
											}
										}
									}	
								}
								echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'', ($pervich_p > 0) ? '('.$pervich_p.')' : '' ,'</div>';
							}else{
								echo '<div class="cellCosmAct" style="text-align: center"></div>';
							}
						}
					}
						echo '
							</li>';
				}
				echo '
						</ul>';
						
						
				
				
				//Ститистика по кол-ву процедур по пациентам
				echo '		
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">
								<!--<div class="cellName" style="text-align: center">Дата</div>-->
								<div class="cellName" style="text-align: center">Пациент</div>';				

				for ($i = 0; $i < count($actions_cosmet); $i++) { 
					if ($actions_cosmet[$i]['active'] != 0){
						echo '<div class="cellCosmAct" style="text-align: center" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
					}
				}
				echo '
								<!--<div class="cellText" style="text-align: center">Комментарий</div>-->
							</li>';
				foreach ($temp_arr as $key => $value) {
					//Надо найти имя клиента
					$clients = SelDataFromDB ('spr_clients', $key, 'client_id');
					if ($clients != 0){
						$client = $clients[0]["name"];
					}else{
						$client = 'unknown';
					}
					echo '
						<li class="cellsBlock cellsBlockHover">
								<a href="client.php?id='.$key.'" class="cellName ahref" id="4filter">'.$client.'</a>';
								
					$decription = array();
					$decription = json_decode($value, true);
					
					//var_dump ($decription);		
					
					foreach ($actions_cosmet as $key1 => $value1) { 
						$cell_color = '#FFFFFF';
						$action = '';
						if ($value1['active'] != 0){
							if (isset($decription[$value1['id']])){
								if ($decription[$value1['id']] != 0){
									$cell_color = $value1['color'];
									$action = $decription[$value1['id']];
								}
								echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'</div>';
							}else{
								echo '<div class="cellCosmAct" style="text-align: center"></div>';
							}
						}
					}
						echo '
							</li>';
				}
				echo '
						</ul>';
						
						
						
						
				echo '
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