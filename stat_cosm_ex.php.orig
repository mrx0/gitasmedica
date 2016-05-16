<?php

//stat_cosm_ex.php
//Статистика Косметология Расширенная (?)

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
		
			$errors = array();
			$query = '';
			$journal = 0;
			
			$offices = SelDataFromDB('spr_office', '', '');
			$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');	
			foreach($actions_cosmet as $key=>$arr_temp){
				$data_nomer[$key] = $arr_temp['nomer'];
			}
			array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
			
			//var_dump ($actions_cosmet);
			//var_dump($offices);
			//print_r (pathinfo(__FILE__));

			echo '
				<style type="text/css">
					div.ZakazDemo { padding: 10px !important; width: 300px;}
					.ui-widget{font-size: 0.6em !important;}
				</style>';
			echo '
				<div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>Статистика с фильтром (Demo)</h1>';
				if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
					echo '
							<a href="cosmet.php" class="b">В журнал</a>';
				}
				if (($cosm['see_all'] == 1) || $god_mode){
					echo '
							<a href="stat_cosm.php" class="b">Статистика</a>';
				}

				echo '
					</header>
				</div>';
			if ($_GET){
				$arr_actions = array();
				
				echo '<div>';
				if (isset($_GET['filter']) && ($_GET['filter'] == 'yes')){
					echo '
						<span style="font-size:85%;">Включен фильтр.';
					echo '
						<a href="stat_cosm_ex.php" class="ahref_sort">Сбросить</a></span>';
				}
				//var_dump($_GET);
				
				echo '
					<div style="font-size: 75%;">Параметры фильтра:<br />';
				
				//Филиал
				if ($_GET['filial'] == '99'){
					//$arr_actions['filial'] = '';
					echo 'Во всех филиалах<br />';
				}else{
					$arr_actions['filial'] = '`office` = \''.$_GET['filial'].'\'';
					$filial = SelDataFromDB('spr_office', $_GET['filial'], 'offices');
					echo 'Филиал: '.$filial[0]['name'].'<br />';
				}
				
				//Врач
				if ($_GET['searchdata2'] == ''){
					//$arr_actions['searchdata2'] = '';
					echo 'По всем врачам<br />';
				}else{
					$workers = SelDataFromDB ('spr_workers', $_GET['searchdata2'], 'worker_full_name');
					if ($workers != 0){
						$arr_actions['searchdata2'] = '`worker` = \''.$workers[0]["id"].'\'';
						echo 'Врач: '.$_GET['searchdata2'].'<br />';
					}else{
						//$arr_actions['searchdata2'] = '';
						echo 'По всем врачам<br />';
					}
				}
				
				//Временной период
				$datastart = strtotime ($_GET['datastart']);
				$dataend = strtotime ($_GET['dataend']);
				
				if ((isset($_GET['all_time'])) && ($_GET['all_time'] == '1')){
					echo 'Временной период: за всё время<br />';
				}else{
					$arr_actions['data_start_end'] = '`create_time` BETWEEN \''.strtotime ($_GET['datastart']).'\' AND \''.strtotime ($_GET['dataend'].' 23:59:59').'\'';
					//$arr_actions['dataend'] = '`create_time` < \''.strtotime ($_GET['dataend']).'\'';
					echo 'Временной период: с '.$_GET['datastart'].' по '.$_GET['dataend'].'<br />';
				}
				//Если дата начала больше даты конца
				if ($datastart > $dataend){
					$errors['start_end'] = 'Время начала не может быть позже времени конца.';
				}
				//echo $datastart.'-'.$dataend;
				
				//Первичка
				if ($_GET['pervich'] == '1'){
					//$arr_actions['pervich'] = '';
				}elseif($_GET['pervich'] == '2'){
					$arr_actions['pervich'] = '`c13` = \'1\'';
				}elseif($_GET['pervich'] == '3'){
					$arr_actions['pervich'] = '`c13` <> \'1\'';
				}
				
				//Возрасты
				echo 'Возраст пациентов: от '.$_GET['start_age'].' до '.$_GET['finish_age'].' лет<br />';
				
				//Если возраст начала больше возраст конца
				if ($_GET['start_age'] > $_GET['finish_age']){
					$errors['age'] = 'Возраст пациентов <От> больше возраста <До>. Так не бывает.';
				}
			//	$birthstart = strtotime ($_GET['datastart']);
			//	$birthend = strtotime ($_GET['dataend']);
				
				//Пол
				if ($_GET['sex'] == '1'){
					echo 'Пол: М. ';
				}elseif ($_GET['sex'] == '2'){
					echo 'Пол: Ж. ';
				}elseif ($_GET['sex'] == '3'){
					echo 'Пол: М+Ж. ';
				}
				if ((isset($_GET['wo_sex'])) && ($_GET['wo_sex'] == '1')){
					echo 'Показывать пациентов, у которых не указан пол<br />';
				}else{
					echo '<br />';
				}
				
				//Процедуры
				foreach($_GET as $key => $value){
					if (mb_strstr($key, 'action') != FALSE){
						$key = str_replace('action', 'c', $key);
						if ($value == 1){
							//$arr_actions[$key] = '`'.$key.'` = \'1\'';
						}elseif ($value == 2){
							$arr_actions[$key] = '`'.$key.'` = \'1\'';
						}elseif ($value == 3){
							$arr_actions[$key] = '`'.$key.'` <> \'1\'';
						}
					}else{
						//
					}
				}
				echo '</div>';

				
				//var_dump ($arr_actions);
				
				//Запрос к БД
				if (!empty ($arr_actions)){
					$query = 'SELECT * FROM `journal_cosmet1` WHERE '.implode(' AND ', $arr_actions).' ORDER BY `client` DESC';
					//echo $query;
				}else{
					$query = 'SELECT * FROM `journal_cosmet1` ORDER BY `client` DESC';
				}
				
				//Если нет ошибок
				if (empty($errors)){
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
				
					//Выводим табличку
					if ($journal != 0){
						//var_dump ($journal);
						echo '
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>
								<div class="cellCosmAct" style="text-align: center">Пол</div>
								<div class="cellCosmAct" style="text-align: center">Лет</div>
								';
						for ($i = 0; $i < count($actions_cosmet)-2; $i++) { 
							if ($actions_cosmet[$i]['active'] != 0){
								echo '<div class="cellCosmAct tooltip " style="text-align: center; background-color:#FEFEFE;" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
							}
						}
						
						for ($i = 0; $i < count($journal); $i++) {
							//Надо найти клиента
							$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'user');
							if ((((isset($_GET['wo_sex'])) && ($_GET['wo_sex'] == '1')) && ($clients[0]['sex'] == '0'))
							|| 
							(($_GET['sex'] == '3') && (($clients[0]['sex'] == '1') || ($clients[0]['sex'] == '2')))
							||
							(($_GET['sex'] != '3') && ($clients[0]['sex'] == $_GET['sex']))){
								
								if ((getyeardiff($clients[0]['birthday']) > $_GET['start_age']) && (getyeardiff($clients[0]['birthday']) < $_GET['finish_age'])){
									if ($clients[0]['sex'] != 0){
										if ($clients[0]['sex'] == 1){
											$cl_sex = 'М';
										}
										if ($clients[0]['sex'] == 2){
											$cl_sex = 'Ж';
										}
									}else{
										$cl_sex = '-';
									}
									
									//, isFired ? 'style="background-color: rgba(161,161,161,1);"' : '' ,
									//echo $journal[$i]['worker'];
									$offices = SelDataFromDB('spr_office', $journal[$i]['office'], 'offices');
									//var_dump ($actions_cosmet);
									$office = $offices[0]['name'];

									echo '
										<li class="cellsBlock cellsBlockHover">
												<a href="task_cosmet.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
												<div class="cellName">'.$office.'</div>
												<a href="user.php?id='.$journal[$i]['worker'].'" class="cellName ahref" id="4filter" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user').'</a>
												<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$clients[0]["name"].'</a>
												<div class="cellCosmAct" style="text-align: center">'.$cl_sex.'</div>
												<div class="cellCosmAct" style="text-align: center">'.getyeardiff($clients[0]['birthday']).'</div>
												';

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
										
										</li>';
								}
							}
						}
					}else{
						echo 'По вашему запросу ничего не найдено';
					}
					
					
				}else{
					echo '<div style="color: red;">Ошибки!!!</div>';
					foreach ($errors as $err_val){
						echo '<div style="color: red;">'.$err_val.'</div>';
					}
				}
				
				echo '</div>';
			}else{
						
				echo '
					<div>';

				echo '		
								<form name="cl_form" action="stat_cosm_ex.php" method="GET" id="form">	
									<input type="hidden" name="filter" value="yes">
									<!--<input type="hidden" name="template" id="type" value="5">-->';

				echo '					
									<div class="filterBlock">
										<div class="filtercellLeft">
											Выберите период
										</div>
										<div class="filtercellRight">
											С <input name="datastart" class="date" value="'.date("01.m.Y").'"> &bull;По <input name="dataend" class="date" value="'.date("d.m.Y").'">
											<!--<input type="text" name="duration" id="duration" onchange="calc(this.value);" style="border:0; color:#f6931f; font-weight:bold; width:120px;" readonly />
											<div id="slider-range-max"></div>
											<input type="hidden" name="period" value="неделя" readonly="readonly" id="period">-->
											<br />';
				echo '						
											<span style="font-size:80%;">За всё время <input type="checkbox" name="all_time" value="1"></span>
										</div>
									</div>
									<div class="filterBlock">
										<div class="filtercellLeft">
											Врач<br />
											<span style="font-size:75%">Если не выбрано, то для всех</span>
										</div>
										<div class="filtercellRight">
											<input type="text" size="35" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2" autocomplete="off">
											<ul id="search_result2" class="search_result2"></ul><br />
										</div>
									</div>';
				echo '
									<div class="filterBlock">
										<div class="filtercellLeft">
											Филиал
										</div>
										<div class="filtercellRight">
											<div class="wrapper-demo">
												<select id="dd2" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
													<ul class="dropdown">
														<li><option value="99" selected>Все</option></li>';
															if ($offices !=0){
																for ($i=0;$i<count($offices);$i++){
																	echo '<li><option value="'.$offices[$i]['id'].'" class="icon-twitter icon-large">'.$offices[$i]['name'].'</option></li>';
																}
															}
												
				echo '
													</ul>
												</select>
											</div>
										</div>
									</div>';
				//Пол					
				echo '			
									<div class="filterBlock">
										<div class="filtercellLeft">
											Пол<br />
										</div>
										<div class="filtercellRight">
											<input id="sex" name="sex" value="1" type="radio">М<br />
											<input id="sex" name="sex" value="2" type="radio">Ж<br />
											<input id="sex" name="sex" value="3" checked type="radio">М+Ж<br />
											<span style="font-size:80%;">Показывать тех, кто без пола <input type="checkbox" name="wo_sex" checked value="1"></span>
										</div>
									</div>';
				
				//Первичка
				echo '			
									<div class="filterBlock">
										<div class="filtercellLeft">
											Первичные<br />
											<span style="font-size:80%;">На период выборки</span>
										</div>
										<div class="filtercellRight">
											<input id="pervich" name="pervich" value="1" checked type="radio"> Все<br />
											<input id="pervich" name="pervich" value="2" type="radio"> Только первичные<br />
											<input id="pervich" name="pervich" value="3" type="radio"> Только не первичные
										</div>
									</div>';
				
				
				//Возраст
				echo '			
									<div class="filterBlock">
										<div class="filtercellLeft">
											Возраст<br />
										</div>
										<div class="filtercellRight">
											От <input type="number" size="2" name="start_age" id="start_age" min="0" max="99" value="0" class="mod"> 
											до <input type="number" size="2" name="finish_age" id="finish_age" min="0" max="99" value="99" class="mod"> лет

										</div>
									</div>';
				
				
				//По процедурам
				echo '			
									<div class="filterBlock">
										<div class="filtercellLeft">
											Процедуры
										</div>
									</div>';

			
				for ($k = 0; $k < count($actions_cosmet)-2; $k++) {
					//Не надо отмечать первичную консультацию, для этого есть отметка о первичном посещении
					if ($k != 13){
						echo 
									'<div class="filterBlock">
										<div class="filtercellLeft" style="background-color:'.$actions_cosmet[$k]['color'].';">
											'.$actions_cosmet[$k]['full_name'].'
										</div>
										<div class="filtercellRight">
											<input id="action'.$actions_cosmet[$k]['id'].'" name="action'.$actions_cosmet[$k]['id'].'" value="1" checked type="radio"> Не учитывать<br />
											<input id="action'.$actions_cosmet[$k]['id'].'" name="action'.$actions_cosmet[$k]['id'].'" value="2" type="radio"> Делали<br />
											<input id="action'.$actions_cosmet[$k]['id'].'" name="action'.$actions_cosmet[$k]['id'].'" value="3" type="radio"> Не делали<br />
										</div>
									</div>';
					}
				}
				echo '
								<input type="submit" value="Применить">
								<!--<button  type="submit" form="form" formaction="stat_cosm_ex.php" formmethod="GET" class="b" style="float:left;">Применить</button>	-->
								</form>
								
								
														
							</div>';
							
							
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>