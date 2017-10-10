<?php

//stomat.php
//Стоматология

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'widget_calendar.php';
			
			$filter = FALSE;
			$dop = '';			
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Стоматология</h1>
					<a href="stom_instruction.php" style="font-size: 90%">Краткая инструкция для стоматологов по основным действиям <span style="color:red;">(обновлено 07.04.2016)</span></a>
				</header>';
				
							
			DrawFilterOptions ('stomat', $it, $stom, $stom, $workers, $clients, $offices, $god_mode);
				
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
						$_GET['datatable'] = 'journal_tooth_status';
		
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
				
				echo widget_calendar ($month, $year, 'stomat.php', 'filter=yes'.$dop);
				
				//////////////////////////////////////////////////	
				$journal = 0;
				$journal_ex = 0;
				
				$sw = $filter_rez[1];
				if ($stom['see_own'] == 1){
					$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} AND `worker`='".$_SESSION['id']."' ORDER BY `create_time` DESC";
				}
				if (($stom['see_all'] == 1) || $god_mode){
					$query = "SELECT * FROM `journal_tooth_status` WHERE {$filter_rez[1]} ORDER BY `create_time` DESC";
				}


                $msql_cnnct = ConnectToDB ();
				
				$arr = array();
				$rez = array();

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
				$number = mysqli_num_rows($res);
				if ($number != 0){
					while ($arr = mysqli_fetch_assoc($res)){
						array_push($rez, $arr);
					}
					$journal = $rez;
				}else{
					$journal = 0;
				}

				$arr = array();
				$rez = array();

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
				
				if (($stom['add_own'] == 1) || $god_mode){
					/*echo '
							<a href="add_error.php" class="b">Добавить</a>';*/
				}
				echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
				
				//if (($stom['see_all'] == 1) || $god_mode){	
					//echo '<a href="stat_stomat2.php" class="b">Пропавшая первичка</a>';
				//}
				
				echo '</li></ul>';

			
			if ($journal != 0){
				//var_dump ($journal);
				$actions_stomat = SelDataFromDB('actions_stomat', '', '');
				if (($stom['see_all'] == 1) || $god_mode){	
					$id4filter4worker = '';
					$id4filter4upr = 'id="4filter"';
				}else{
					$id4filter4worker = 'id="4filter"';
					$id4filter4upr = '';
				}
					echo '
						<p style="margin: 5px 0; padding: 1px; font-size:80%;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
							
						</p>';
				echo '
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>
								<div class="cellCosmAct" style="text-align: center">-</div>';
				if (($stom['see_all'] == 1) || $god_mode){
					echo '<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';
				}
				/*echo '
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Тип</div>';*/
				//отсортируем по nomer

				/*foreach($actions_stomat as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}*/
				//array_multisort($data_nomer, SORT_NUMERIC, $actions_stomat);
				//return $rez;
				//var_dump ($actions_stomat);
				
				/*for ($i = 0; $i < count($actions_stomat)-2; $i++) { 
					if ($actions_stomat[$i]['active'] != 0){
						echo '<div class="cellCosmAct tooltip " style="text-align: center; background-color:#FEFEFE;" title="'.$actions_stomat[$i]['full_name'].'">'.$actions_stomat[$i]['name'].'</div>';
					}
				}*/
				echo '
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
				
				//!!!!!!тест санации Sanation ($journal);
				
				for ($i = 0; $i < count($journal); $i++) {
					$rez_color = '';
					
					$journal_ex_bool = FALSE;
                    //var_dump($journal[$i]['id']);
					if ((isset($filter_rez['pervich'])) && ($filter_rez['pervich'] == true)){
						$query = "SELECT * FROM `journal_tooth_ex` WHERE `pervich` = 1 AND `id` = '{$journal[$i]['id']}' ORDER BY `id` DESC";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query.'*205');
						$number = mysqli_num_rows($res);
						if ($number != 0){
							$journal_ex_bool = true;
						}else{
						}
					}
					if ((isset($filter_rez['pervich']) && $journal_ex_bool) || (!isset($filter_rez['pervich']))){
					//if (($journal[$i]['create_time'] >= $datestart)  && ($journal[$i]['create_time'] <= $datefinish)){
						//Надо найти Пациента
						$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
							if (($clients[0]["birthday"] != -1577934000) || ($clients[0]["birthday"] == 0)){
								$cl_age = getyeardiff($clients[0]["birthday"], 0);
							}else{
								$cl_age = 0;
							}
						}else{
							$client = 'не указан';
							$cl_age = 0;
						}
						
						//Дополнительно
						$dop = array();
						$dop_img = '';

                        //$msql_cnnct = ConnectToDB ();

						$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$journal[$i]['id']}'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}
						//var_dump ($dop);
						if (!empty($dop)){
							if ($dop[0]['insured'] == 1){
								$dop_img .= '<img src="img/insured.png" title="Страховое">';
							}
							if ($dop[0]['pervich'] == 1){
								$dop_img .= '<img src="img/pervich.png" title="Первичное">';
							}
							if ($dop[0]['noch'] == 1){
								$dop_img .= '<img src="img/night.png" title="Ночное">';
							}
						}
						
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_stomat_inspection.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'">'.date('d.m.y H:i', $journal[$i]['create_time']).' '.$dop_img.'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" '.$id4filter4worker.'>'.$client.'</a>';
						
						///if (Sanation2($journal[$i]['id'], $journal[$i], $cl_age)){
						
						
						
						//ЗО и тд	
						$dop = array();							
						$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$journal[$i]['id']}'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}
												
						include_once 'tooth_status.php';						
						include_once 't_surface_name.php';
						include_once 't_surface_status.php';
						
						
						$arr = array();
						$decription = $journal[$i];
						
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
						unset($decription['zapis_date']);
						unset($decription['zapis_id']);
						
						$t_f_data = array();
						
						//собрали массив с зубами и статусами по поверхностям
						foreach ($decription as $key => $value){
							$surfaces_temp = explode(',', $value);
							//var_dump($surfaces_temp);
							foreach ($surfaces_temp as $key1 => $value1){
								///!!!Еба костыль
								if ($key1 < 13){
									$t_f_data[$key][$surfaces[$key1]] = $value1;
									//var_dump($t_f_data[$key][$surfaces[$key1]]);
								}
							}
						}
						//var_dump ($t_f_data);
						if (!empty($dop[0])){
							//var_dump($dop[0]);
							unset($dop[0]['id']);
							//var_dump($dop[0]);
							foreach($dop[0] as $key => $value){
								//var_dump($value);
								if ($value != '0'){
									//var_dump($value);
									$dop_arr = json_decode($value, true);
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
										if ($n_key == 'retein'){
											$t_f_data[$key]['retein'] = $n_value;
											//$t_f_data_draw[$key]['retein'] = $n_value;
										}
										if ($n_key == 'skomplect'){
											$t_f_data[$key]['skomplect'] = $n_value;
											//$t_f_data_draw[$key]['skomplect'] = $n_value;
										}
									}
								}
							}
						}
						
						//var_dump ($t_f_data);
						
						
						if (Sanation2($journal[$i]['id'], $t_f_data, $cl_age)){
							$rez_color = "style= 'background: rgba(87,223,63,0.7);'";
						}else{
							$rez_color = "style= 'background: rgba(255,39,119,0.7);'";
						}
						echo '
									<div class="cellCosmAct" '.$rez_color.'>
										<a href="#" onclick="window.open(\'task_stomat_inspection_window.php?id='.$journal[$i]['id'].'\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;">
											<img src="img/tooth_state/1.png">
										</a>	
									</div>';
									
						if (($stom['see_all'] == 1) || $god_mode){
							echo '<div class="cellName" '.$id4filter4upr.'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user', true).'</div>';
						}		
						
						/*echo '
								<div class="cellName">!!!ТИП</div>';*/
						
						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						
						/*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
						/*foreach($journal[$i] as $key => $value){
							if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
								$decription_temp_arr[mb_substr($key, 1)] = $value;
							}
						}*/
						
						//var_dump ($decription_temp_arr);
						
						$decription = $decription_temp_arr;

						//array_multisort($data_nomer, SORT_NUMERIC, $decription);
						
						//var_dump ($decription);		
						//var_dump ($actions_stomat);		
						
						//for ($j = 1; $j <= count($actions_stomat)-2; $j++) { 
						/*foreach ($actions_stomat as $key => $value) { 
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
						}*/
						
						echo '
									<div class="cellText">'.$journal[$i]['comment'].'</div>
							</li>';
					}
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