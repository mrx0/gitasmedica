<?php 

//insure_price_fill_f.php
//Функция для заполнения прайса

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['group']) && isset($_POST['id'])){
				$arr4fill = returnTree($_POST['group'], '', 'return', 0, TRUE, 0, FALSE, 'spr_pricelist_template', 0);
				//var_dump ($arr4fill);
		
				if (!empty($arr4fill)){
					//var_dump ($arr4fill);
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					
					$query = "DELETE FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['id']}'";
					mysql_query($query) or die(mysql_error().' -> '.$query);
					
					$query = "DELETE FROM `spr_priceprices_insure` WHERE `insure`='{$_POST['id']}'";
					mysql_query($query) or die(mysql_error().' -> '.$query);
					
					//Сегодня 09:00:00
					$fromdate = strtotime(date('d.m.Y', $time)." 09:00:00");
					
					foreach($arr4fill as $id => $price_arr){
                        $price = $price_arr['price'];
                        $price2 = $price_arr['price2'];
                        $price3 = $price_arr['price3'];

						//Добавляем в базу позицию прайса для страховой
						$query = "INSERT INTO `spr_pricelists_insure` (`item`, `insure`, `create_time`, `create_person`) 
						VALUES (
						'{$id}', '{$_POST['id']}', '{$time}', '{$_SESSION['id']}')";
						mysql_query($query) or die(mysql_error().' -> '.$query);
						
						//ID новой позиции
						$mysql_insert_id = mysql_insert_id();
						
						//Добавляем в базу цену позиции прайса для страховой
						$query = "INSERT INTO `spr_priceprices_insure` (
							`insure`, `item`, `price`, `price2`, `price3`, `date_from`, `create_time`, `create_person`) 
							VALUES (
						'{$_POST['id']}', '{$id}', '{$price}', '0', '0', '{$fromdate}', '{$time}', '{$_SESSION['id']}')";
						mysql_query($query) or die(mysql_error().' -> '.$query);
						
					}
					echo '
						<div class="query_ok">
							Прайс заполнен<br><br>
						</div>';
				}
				
			}else{
				echo '
					<div class="query_neok">
						Не выбран раздел<br><br>
					</div>';
			}
		}
		
		
		/*
		if ($_POST){
			if (isset($_POST['group'])){
				
				//
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
			}else{
				echo '
					<div class="query_neok">
						Не выбрали раздел<br><br>
					</div>';
			}
		}*/
	}
?>