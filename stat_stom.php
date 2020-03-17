<?php

//stat_stom.php
//Статистика Стоматология

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
		if (($stom['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';


//			require 'config.php';
//			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
//			mysql_select_db($dbName) or die(mysql_error());
//			mysql_query("SET NAMES 'utf8'");

            $msql_cnnct = ConnectToDB ();

			$arr = array();
			$rez = array();
		
			//$filials = SelDataFromDB('spr_filials', '', '');
			
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
				<div class="no_print"> 
				<header style="margin-bottom: 5px;">
					<h1>Стоматология статистика</h1>';
					
			if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
				echo '
						<a href="stomat.php" class="b">В журнал</a>';
			}
			
			/*if (($stom['see_all'] == 1) || $god_mode){
				echo '
						<a href="full_stat_cosmet.php" class="b">Статистика2</a>';
			}*/
				
			if (($stom['see_all'] == 1) || $god_mode){
				echo '
						<a href="stat_stom_ex.php" class="b">Статистика с фильтром</a>';
			}
			echo '
				</header>
				</div>
				<div style="font-size: 75%;">
					<!--Прим.: всего/первичных/(мужчины,женщины,пол не указан)-->
				</div><br />';

			
			
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

				//$offices = SelDataFromDB('spr_filials', '', '');
				//var_dump ($offices);
				
				$workers = array();
				$filials = array();
				
				$sw = $filter_rez[1];
				if (($stom['see_all'] == 1) || $god_mode){
					$query = "SELECT `worker`,`office` FROM `journal_tooth_status` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysql_num_rows($res);
					if ($number != 0){
						//Всего посещений
						$vsego = $number;
						while ($arr = mysqli_fetch_assoc($res)){
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
					<form action="stat_stom.php" id="months_form" method="GET">
						<input type="hidden" name="filter" value="yes">
						<select name="ttime" onchange="this.form.submit()">'.
							$li_months
						.'</select>
					</form>
					</div>';


				/*echo '
					<b>Всего посещений:</b> '.$vsego.'
					<br />';*/
				

		
				/*$actions_stomat = SelDataFromDB('actions_stomat', '', '');	
				foreach($actions_stomat as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_stomat);*/
				//return $rez;
				//var_dump ($actions_stomat);
				
				$tabs_workers = '<ul>';
				$itog = array();

				//по работникам
				foreach ($workers as $value){
					$journal_w = array();
					/*echo '
						<div class="cellsBlock2">
							<div class="cellName">
								'.WriteSearchUser('spr_workers', $value, 'user', false).'
							</div>
						</div>';*/
						
					$tabs_workers .= '<li><a href="#tabs-'.$value.'">'.WriteSearchUser('spr_workers', $value, 'user', false).'</a></li>';
					$itog[$value]['name'] = WriteSearchUser('spr_workers', $value, 'user', false);
					
					$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`={$value} ORDER BY `create_time` DESC";

					$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
					if ($number != 0){
						//Всего посещений
						$w_vsego = $number;
						while ($arr = mysqli_fetch_assoc($res)){
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
						$offices = SelDataFromDB('spr_filials', $value1, 'offices');
						//var_dump ($actions_cosmet);
						$office = $offices[0]['name'];
						$f_journal = array();
						$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`={$value} AND `office`={$value1} ORDER BY `create_time` DESC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

						if ($number != 0){
							//Всего посещений
							$f_vsego = $number;
							while ($arr = mysqli_fetch_assoc($res)){
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
							//if ($value1['p_vsego'] > 0){
							//	echo 'первичных: '.$value1['p_vsego'];
								echo 'первичных: -';
							//}
							echo '
										</div>
									</div>
									<div class="no_print"> 
									<div class="cellsBlock2">
										<div class="cellName">';
							//var_dump ($value1);

							echo '
										</div>
									</div>
									</div>';

							echo '

								</div>';
								
								
								

						}
	
					
					}
					
						echo '</div>';
				}
				
				
				echo '</div>';

            CloseDB ($msql_cnnct);

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>