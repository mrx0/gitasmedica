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
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';
			
			$queryConditionExist = false;
			$queryCondition = '';
			$queryEffectExist = false;
			$queryEffect = '';
			
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
				if (isset($_POST['condition'])){
					for($i=0; $i<count($_POST['condition']); $i++){
						$queryCondition .= "`c".$_POST['condition'][$i]."`='1'";
						if ($i < count($_POST['condition']) - 1){
							$queryCondition .= ' AND ';
						}
					}
					$queryConditionExist = true;
				}
				
				if (isset($_POST['effect'])){
					for($i=0; $i<count($_POST['effect']); $i++){
						$queryEffect .= "`c".$_POST['effect'][$i]."`='1'";
						if ($i < count($_POST['effect']) - 1){
							$queryEffect .= ' OR ';
						}
					}
					$queryEffect = "(".$queryEffect.")";
					$queryEffectExist = true;
				}
				
				if (($queryConditionExist) || ($queryEffectExist) || ($queryDopClientExist) || ($queryDopExist)){
					$query .= ' WHERE '.$queryDop;

					if ($queryEffectExist){
						//var_dump($queryEffect);

						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= $queryEffect;
						$queryDopExist = true;
					}
					
					if ($queryConditionExist){
						//var_dump($queryCondition);

						if ($queryDopExist){
							$query .= ' AND';
						}
						$queryCondition = "SELECT `id` FROM `journal_cosmet1` WHERE ".$queryCondition;
						$queryDopExist = true;
						
						//Дата/время
						if ($_POST['all_time'] != 1){
							if ($queryDopExist){
								$queryCondition .= ' AND';
							}
							$queryCondition .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
							$queryDopExist = true;
						}
						$query .= "`id` IN (".$queryCondition.")";
					}
					
					if ($queryDopClientExist){
						$queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= "`client` IN (".$queryDopClient.")";
					}
					
					$query = $query." ORDER BY `create_time` DESC";
					//var_dump($query);
					//var_dump($queryEffect);
					//var_dump($queryCondition);
					
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
					//var_dump($journal);
					
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
										<a href="user.php?id='.$journal[$i]['worker'].'" class="cellName ahref" id="4filter" ', isFired($journal[$i]['worker']) ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user').'</a>';
				
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
										
						}
							
							
					}else{
						echo '<span style="color: red;">Ничего не найдено</span>';
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