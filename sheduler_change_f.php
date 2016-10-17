<?php 

//sheduler_change_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			//Максимально кол-во дней в месяц, где будем менять
			$max_days =	cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
			
			if ($_POST['day'] < 0){
				$day = 1;
			}elseif($_POST['day'] > $max_days){
				$day = $max_days;
			}else{
				$day = $_POST['day'];
			}
			
			$month = $_POST['month'];
			$year = $_POST['year'];
			
			//var_dump ($day);
			$canUpdate = TRUE;
			
			//получаем шаблон графика из базы
			$query = "SELECT `filial`, `day`, `smena`, `kab`, `worker`, `type` FROM `sheduler_template`";
			
			$shedTemplate = 0;
			
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
					$rez[$arr['day']][$arr['smena']][$arr['filial']][$arr['type']][$arr['kab']][$arr['worker']] = true;
				}
				$shedTemplate = $rez;
			}else{
				$shedTemplate = 0;
			}
			//var_dump($shedTemplate[5][1]);
			
			if ($shedTemplate != 0){
				for ($i=$day; $i<=$max_days; $i++){
					$month_stamp = mktime(0, 0, 0, $month, $i, $year);
					//Узнаем номер дня недели
					$weekday = date("N", $month_stamp);
					//var_dump($weekday);
					//var_dump($shedTemplate[$weekday]);
					
					//foreach ($shedTemplate as $dayW => $valueW){
					if (isset($shedTemplate[$weekday])){
						foreach ($shedTemplate[$weekday] as $smena => $valueS){
							foreach ($valueS as $filial => $valueF){
								foreach ($valueF as $type => $valueT){
									foreach ($valueT as $kab => $valueK){
										//Смотрим нет ли такой записи
										$workerHere = FALSE;
										$query = "SELECT `worker` FROM `scheduler` WHERE `day`='{$i}' AND `month`='{$month}' AND `year`='{$year}' AND `smena`='{$smena}' AND `filial`='{$filial}' AND `kab`='{$kab}' AND `type`='{$type}'";
										$res = mysql_query($query) or die(mysql_error().' -> '.$query);
										$number = mysql_num_rows($res);
										if ($number != 0){
											$workerHere = TRUE;
										}
										//var_dump($workerHere);
										
										if ($workerHere){
											if ($_POST['ignoreshed'] == 1){
												$query = "DELETE FROM `scheduler` WHERE `month`='{$month}' AND `day`>='{$day}'";
													
												mysql_query($query) or die($query.' -> '.mysql_error());
												
												foreach($valueK as $worker => $val){
													//Вставляем запись
													$query = "INSERT INTO `scheduler` (`year`, `month`, `day`, `filial`, `kab`, `smena`, `smena_t`, `worker`, `type`)
													VALUES 
													('{$year}', '{$month}', '{$i}', '{$filial}', '{$kab}', '{$smena}', NULL, '{$worker}', '{$type}')";
													
													mysql_query($query) or die($query.' -> '.mysql_error());
												}
											}else{
												$canUpdate = FALSE;
												break 5;
											}
										}else{
											foreach($valueK as $worker => $val){
												//Вставляем запись
												$query = "INSERT INTO `scheduler` (`year`, `month`, `day`, `filial`, `kab`, `smena`, `smena_t`, `worker`, `type`)
												VALUES 
												('{$year}', '{$month}', '{$i}', '{$filial}', '{$kab}', '{$smena}', NULL, '{$worker}', '{$type}')";
												
												mysql_query($query) or die($query.' -> '.mysql_error());
											}
										}
									}
								}
							}
						}
					}
					//}
				}
				mysql_close();
				
				if (!$canUpdate){
					echo '
						<div class="query_neok">
							График был заполнен ранее.<br><br>
						</div>';
				}else{
					echo '
						<div class="query_ok">
							График заполнен.<br><br>
						</div>';
						AddLog ('0', $_SESSION['id'], '', 'График заполнен пользователем. Год ['.$year.']. Месяц['.$month.']. С числа['.$day.'].');	
				}
			}
		}
	}
?>