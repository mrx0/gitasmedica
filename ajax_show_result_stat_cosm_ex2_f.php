<?php 

//ajax_show_result_stat_cosm_ex2_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			$workerExist = false;
			$queryDopExist = false;
			$queryDopExExist = false;
			$queryDopClientExist = false;
			$query = '';
			$query4Effect = '';
			$query4Effect2 = '';
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';
			
			$queryConditionExist = false;
			$queryCondition = '';
			$queryEffectExist = false;
			$queryEffect = '';
			
			//количество посещений, выбранных по условию
			$journal_count_condition = 0;
			//количество клиентов, приходивших после условия на следственное
			$journal_count_clients_effect = 0;
			
			if ($_POST['worker'] != ''){
				include_once 'DBWork.php';
				$workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
				
				if ($workerSearch == 0){
					$workerExist = false;
				}else{
					$workerExist = true;
					$worker = $workerSearch[0]['id'];
				}
			}else{
				$workerExist = true;
				$worker = 0;
			}	
			
			if ($workerExist){
				$query .= "SELECT * FROM `journal_cosmet1`";
				$query4Effect .= "SELECT `client` FROM `journal_cosmet1`";
				$query4Effect2 .= "SELECT `id` FROM `journal_cosmet1`";
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				//$time = time();
				
				//Дата/время
				if ($_POST['all_time'] != 1){
					//$queryDop .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
					//$queryDopExist = true;
				}
				
				//!!! Тут возраст, пока не готово
				
				//Сотрудник
				if ($worker != 0){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`worker` = '".$worker."'";
					$queryDopExist = true;
				}
				
				//Филиал
				if ($_POST['filial'] != 99){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`office` = '".$_POST['filial']."'";
					$queryDopExist = true;
				}
				
				//Пол
				if ($_POST['sex'] != 0){
					if ($queryDopClientExist){
						$queryDopClient .= ' AND';
					}
					$queryDopClient .= "`sex` = '".$_POST['sex']."'";
					$queryDopClientExist = true;
					
					//Без пола
					if ($_POST['wo_sex'] == 1){
						if ($queryDopClientExist){
							$queryDopClient .= ' OR';
						}
						$queryDopClient .= "`sex` = '0'";
						$queryDopClient = "(".$queryDopClient.")";
						$queryDopClientExist = true;
					}
				}
				

				
				//Первичка
				/*if ($_POST['pervich'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['pervich'] == 1){
						$queryDopEx .= "`pervich` = '1'";
					}else{
						$queryDopEx .= "`pervich` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Страховые
				if ($_POST['insured'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['insured'] == 1){
						$queryDopEx .= "`insured` = '1'";
					}else{
						$queryDopEx .= "`insured` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Ночные
				if ($_POST['noch'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['noch'] == 1){
						$queryDopEx .= "`noch` = '1'";
					}else{
						$queryDopEx .= "`noch` <> '1'";
					}
					$queryDopExExist = true;
				}*/
				
				//По процедурам
				//Условие
				if (isset($_POST['condition'])){
					for($i=0; $i<count($_POST['condition']); $i++){
						$queryCondition .= "`c".$_POST['condition'][$i]."`='1'";
						if ($i < count($_POST['condition']) - 1){
							$queryCondition .= ' AND ';
						}
					}
					$queryConditionExist = true;
					
					$queryDopExExist = true;
				}
				
				//Следствие
				if (isset($_POST['effect'])){
					for($i=0; $i<count($_POST['effect']); $i++){
						$queryEffect .= "`c".$_POST['effect'][$i]."`='1'";
						if ($i < count($_POST['effect']) - 1){
							$queryEffect .= ' OR ';
						}
					}
					$queryEffect = "(".$queryEffect.")";
					$queryEffectExist = true;
					
					$queryDopExExist = true;
				}
				
				if (($queryConditionExist) || ($queryEffectExist) || ($queryDopClientExist) || ($queryDopExist)){
					$query .= ' WHERE '.$queryDop;
					$query4Effect .= ' WHERE '.$queryDop;
					$query4Effect2 .= ' WHERE '.$queryDop;

					/*if ($queryEffectExist){
						//var_dump($queryEffect);

						if ($queryDopExist){
							$query .= ' AND';
						}
						//$query .= $queryEffect;
						$queryDopExist = true;
					}*/
					
					if ($queryConditionExist){
						
						//var_dump($queryCondition);

						if ($queryDopExist){
							$query .= ' AND';
							$query4Effect .= ' AND';
							$query4Effect2 .= ' AND';
						}
						/*
						$queryCondition = "SELECT * FROM `journal_cosmet1` WHERE ".$queryCondition;
						$queryDopExist = true;*/
						//var_dump($queryDopExist);						
						
						//Дата/время
						if ($_POST['all_time'] != 1){
							if ($queryDopExExist){
								$queryCondition .= ' AND';
							}
							$queryCondition .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
							$queryDopExist = true;
						}
						//$query .= "`client` IN (".$queryCondition.")";
						$query .= $queryCondition;
						$query4Effect .= $queryCondition;
						$query4Effect2 .= $queryCondition;
						//var_dump($query);
					}
					
					if ($queryDopClientExist){
						$queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
						if ($queryDopExist || $queryConditionExist){
							$query .= ' AND';
							$query4Effect .= ' AND';
							$query4Effect2 .= ' AND';
						}
						$query .= "`client` IN (".$queryDopClient.")";
						$query4Effect .= "`client` IN (".$queryDopClient.")";
						$query4Effect2 .= "`client` IN (".$queryDopClient.")";
					}
					
					$query = $query." ORDER BY `create_time`, `client`";
					//$query4Effect = $query4Effect." ORDER BY `create_time`, `client`";
					//$query4Effect2 = $query4Effect2." ORDER BY `create_time`, `client`";
					//var_dump($query);
					//var_dump($queryEffect);
					//var_dump($queryCondition);
					
					
					if ($queryConditionExist){
					
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die('1: '.mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						
						$arr = array();
						$rez = array();
						
						$res = mysql_query($query) or die('2: '.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($rez, $arr);
							}
							$journal = $rez;
						}else{
							$journal = 0;
						}
						
						if ($queryEffectExist){
							//var_dump($queryEffect);

							/*if ($queryDopExist){
								$query .= ' AND';
							}*/
							//$query .= $queryEffect;
							//$queryDopExist = true;
							
							$query = "SELECT * FROM `journal_cosmet1` WHERE ".$queryEffect." AND `client` IN (".$query4Effect.") AND `id` NOT IN (".$query4Effect2.")";
						}
						
						//var_dump($query);
						
						$arr = array();
						$rez = array();
						
						$res = mysql_query($query) or die('3: '.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								if (isset($rez[$arr['client']])){
									array_push($rez[$arr['client']], $arr);
								}else{
									$rez[$arr['client']][0] = $arr;
								}
							}
							$journalEffect = $rez;
						}else{
							$journalEffect = 0;
						}
						
						//var_dump($journal);
						//var_dump($journalEffect);
						
						//Выводим результат
						if ($journal != 0){
							include_once 'functions.php';
							$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');	

							echo '
								<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
									<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';

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
								
								$journal_count_condition++;
								
								$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
								if ($clients != 0){
									$client = $clients[0]["name"];
								}else{
									$client = 'не указан';
								}
								echo '
									<li class="cellsBlock cellsBlockHover">
											<a href="task_cosmet.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
											<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$client.'</a>
											<div class="cellName 4filter" id="4filter" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user', true).'</div>';
					
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

									
								//var_dump ($journal[$i]['client']);
								$journal_count_clients_effect_status = false;							
								
								if (isset($journalEffect[$journal[$i]['client']])){

									$journalTemp = $journalEffect[$journal[$i]['client']];
									
									for ($j = 0; $j < count($journalTemp); $j++) {
										/*$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
										if ($clients != 0){
											$client = $clients[0]["name"];
										}else{
											$client = 'не указан';
										}*/
										
										//процедура следствие должна быть строго после процедуры условия
										if ($journalTemp[$j]['create_time'] > $journal[$i]['create_time']){
											
											$journal_count_clients_effect_status = true;
											
											echo '
												<li class="cellsBlock cellsBlockHover">
														<div class="cellCosmAct" style="text-align: center; color: red; font-size: 120%;"><i class="fa fa-chevron-circle-right"></i></i></div>
														<a href="task_cosmet.php?id='.$journalTemp[$j]['id'].'" class="cellName ahref" title="'.$journalTemp[$j]['id'].'" style="width: 89px; min-width: 89px; font-size: 85%;', isFired($journalTemp[$j]['worker']) ? 'background-color: rgba(161,161,161,1);"' : '' ,'">'.date('d.m.y H:i', $journalTemp[$j]['create_time']).'</a>
														<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" ', isFired($journalTemp[$j]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$client.'</a>
														<div class="cellName 4filter" id="4filter" ', isFired($journalTemp[$j]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journalTemp[$j]['worker'], 'user', true).'</div>';
								
											$decription = array();
											$decription_temp_arr = array();
											$decription_temp = '';
											
											/*!!!Лайфхак для посещений из-за переделки структуры бд*/
											foreach($journalTemp[$j] as $key => $value){
												if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
													$decription_temp_arr[mb_substr($key, 1)] = $value;
												}
											}
											
											//var_dump ($decription_temp_arr);
											
											$decription = $decription_temp_arr;
										
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
														<div class="cellText" ', isFired($journalTemp[$j]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$journalTemp[$j]['comment'].'</div>
												</li>';
										}
									}
									if ($journal_count_clients_effect_status) $journal_count_clients_effect++;				
								}
							}
							echo '
								<li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
									Всего<br>
									Посещений по указанному условию: '.$journal_count_condition.'<br>
									Пациентов по указанному следствию: '.$journal_count_clients_effect.'<br>
								</li>';
							
							echo '
									</ul>
								</div>';
								
						}else{
							echo '<span style="color: red;">Ничего не найдено</span>';
						}				
					}else{
						echo '<span style="color: red;">Не выбрано условие</span>';
					}		
				}else{
					echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
				}
				
				//var_dump($query);
				//var_dump($queryDopEx);
				//var_dump($queryDopClient);
				
				mysql_close();
			}else{
				echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
			}
		}
	}
?>