<?php 

//edit_schedule_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if ($_POST['worker'] != 0){
				//надо посмотреть, а не работает ли этот врач еще где-то в эту смену в этот день
				//!!!Выбираем врачей (не уволенные)
				//$query = "SELECT `worker` FROM `sheduler_template` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `kab`='{$_POST['kabN']}' AND `type`='{$_POST['type']}'";
				$query = "SELECT * FROM `spr_workers` WHERE `permissions` = '{$_POST['type']}' AND `fired` <> '1' AND `id`
				NOT IN (SELECT `worker` FROM `sheduler_template` WHERE `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `type`='{$_POST['type']}')
				ORDER BY `full_name` ASC";
				
				$workers = array();

				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$res = mysql_query($query) or die(mysql_error().' -> '.$query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($workers, $arr);
					}
				}else{
					$workers = 0;
				}
			
				if (!empty($arr) || ($smena != 0)){
					//надо посмотреть, а не работает ли этот врач еще где-то в эту смену в этот день
					//$offices = SelDataFromDB('spr_office', '', '');
					//var_dump ($offices);
					
					$WorkerFree = TRUE;
					//if ($offices != 0){
						//for ($off=0;$off<count($offices);$off++){
							$FilialSmenaWorkerFree = FilialSmenaWorkerFree ($_POST['datatable'], $_POST['year'], $_POST['month'], $_POST['day'], $smena, $_POST['worker']);
							//var_dump($FilialSmenaWorkerFree);
							if ($FilialSmenaWorkerFree != 0){
								//echo 'Врач уже работает где-то';
								$WorkerFree = FALSE;
								foreach($FilialSmenaWorkerFree as $value4delete){
									if ($value4delete['smena'] == '9'){
										if ($smena == $value4delete['smena']){
											WriteToDB_DeleteScheduler($_POST['datatable'], $value4delete['id']);
										}else{
											if ($smena == 1){
												WriteToDB_UpdateScheduler ($_POST['datatable'], $value4delete['id'], 2);
											}elseif ($smena == 2){
												WriteToDB_UpdateScheduler ($_POST['datatable'],$value4delete['id'], 1);
											}
										}
									}else{
										WriteToDB_DeleteScheduler($_POST['datatable'], $value4delete['id']);
									}
								}
							}
							//смотрим не занят ли кабинет в этот день, в эту смену и удаляем лишнее
							$Kab_work_today_free = FilialKabSmenaWorker($_POST['datatable'], $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab']);
							//var_dump($Kab_work_today_free);
							
							if ($Kab_work_today_free != 0){
								foreach($Kab_work_today_free as $value4delete){
									if ($value4delete['smena'] == '9'){
										if ($smena == $value4delete['smena']){
											WriteToDB_DeleteScheduler($_POST['datatable'], $value4delete['id']);
										}else{
											if ($smena == 1){
												WriteToDB_UpdateScheduler ($_POST['datatable'], $value4delete['id'], 2);
											}elseif ($smena == 2){
												WriteToDB_UpdateScheduler ($_POST['datatable'],$value4delete['id'], 1);
											}
										}
									}else{
										if ($smena == $value4delete['smena']){
											WriteToDB_DeleteScheduler($_POST['datatable'], $value4delete['id']);
										}else{
											if ($smena == 9){
												WriteToDB_DeleteScheduler($_POST['datatable'], $value4delete['id']);
											}
										}
									}
								}
							}
						//}
					//}
					//if (!$WorkerFree){
						//
					//}
					//запись в базу
					WriteToDB_EditScheduler ($_POST['datatable'], $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab'], $smena, json_encode($arr, true), $_POST['worker'], $_POST['author']);
					
					if (isset($_POST['DateForMove']) && ($_POST['DateForMove'] != 0)){
						$count = floor($_POST['DateForMove']/7);
						for ($i=1; $i<=$count; $i++){
							$DateForMove_week = date('j:n:Y', mktime (0, 0, 0, $_POST['month'], $_POST['day']+($i*7), $_POST['year']));
							$rez_arr = array();
							$rez_arr = explode(':', $DateForMove_week);
							WriteToDB_EditScheduler ($_POST['datatable'], $rez_arr[2], $rez_arr[1], $rez_arr[0], $_POST['filial'], $_POST['kab'], $smena, json_encode($arr, true), $_POST['worker'], $_POST['author']);
						}
					}
					
					if ($_POST['month'] < 10) $_POST['month'] = '0'.$_POST['month'];
					/*$location = 'scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'];
					header("location: ".$location);*/
					$link = 'scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'];
					echo '{"req": "ok", "text":"'.$link.'"}';
					
					
					
					//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
				}else{
					if ($_POST['month'] < 10) $_POST['month'] = '0'.$_POST['month'];
					
					echo '{"req": "9", "text":"<span style=\'color:red; font-size: 120%;\'>Не выбрали время</span><br /><br /><a href=\'scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'\' class=\'b\'>К расписанию</a>"}';
				}
			}else{
				echo '{"req": "error", "text":"<span style=\'color:red; font-size: 120%;\'>Не выбрали врача</span>"}';
			}
		}
	}
?>