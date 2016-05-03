<?php 

//edit_schedule_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump($_POST);

		if ($_POST){
			if ($_POST['smena'] != 0){
				//!!!Выбираем врачей (не уволенные) (пока стоматологи только)
				$workers = array();
				if ($_POST['datatable'] == 'scheduler_stom'){
					$permissions = 5;
				}else{
					$permissions = 6;
				}
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$query = "SELECT * FROM `spr_workers` WHERE `permissions` = '$permissions' AND `fired` <> '1' ORDER BY `full_name` ASC";
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($workers, $arr);
					}
				}else
					$workers = 0;
				mysql_close();
				
				//var_dump($workers);

				if ($workers !=0){
					$works_today_arr = array();
					foreach($workers as $value){
						$works_today = FilialSmenaWorkerFree($_POST['datatable'], $_POST['year'], $_POST['month'], $_POST['day'], $_POST['smena'], $value['id']);
						if ($works_today == 0){
							echo '
								<div class="cellsBlock2" style="width:320px; font-size:80%;">
									<div class="cellRight">
										<input type="radio" name="worker" value="'.$value['id'].'"> '.$value['name'].'
									</div>
								</div>';
						}else{
							array_push($works_today_arr, $works_today[0]); 
							/*echo '
								<div class="cellsBlock2" style="width:320px; font-size:80%; background: red;">
									<div class="cellRight">
										<input type="radio" name="worker" value="'.$value['id'].'"> '.$value['name'].'
									</div>
								</div>';*/
						}
					}
					if (!empty($works_today_arr)){
						//var_dump($works_today_arr);
						echo '
							<div style="font-size:80%; background-color: #FF49E9; padding: 2px;">
								<a href="#open1" onclick="show(\'hidden_1\',200,5)" class="ahref">
									[+] Развернуть (уже в графике)
								</a>
							</div>';	
						echo '
							<div id="hidden_1" style="display:none; border: 1px solid red;">';	
						foreach($works_today_arr as $value){
							$filial = SelDataFromDB('spr_office', $value['office'], 'offices');
							//var_dump($filial);
							if ($value['smena'] == 1){
								$smn = 'смена 1';
							}elseif ($value['smena'] == 2){
								$smn = 'смена 2';
							}elseif ($value['smena'] == 9){
								$smn = 'смена 1+2';
							}else{
								$smn = 'unknown';
							}
							echo '
								<div class="cellsBlock2" style="width:320px; font-size:80%;">
									<div class="cellRight" style="background-color: rgba(255,83,75,.5);">
										<input type="radio" name="worker" value="'.$value['worker'].'"> '.WriteSearchUser('spr_workers', $value['worker'], 'user').'<br />
										<span style="font-size:80%;">Филиал '.$filial[0]['name'].'; кабинет '.$value['kab'].'; '.$smn.'</span>
									</div>
								</div>';
						}
						echo '
							</div>';
					}
				}else{
					echo '
						<div class="cellsBlock2" style="width:320px; font-size:80%;">
							<div class="cellRight">
								В базе нет сотрудников. Странно.
							</div>
						</div>';
				}
			}else{
				echo '
					<div class="cellsBlock2" style="width:320px; font-size:80%;">
						<div class="cellRight">
							Не выбрана смена
						</div>
					</div>
					';
			}
		}
	}
	
?>