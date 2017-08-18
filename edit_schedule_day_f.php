<?php 

//edit_schedule_day_f.php
//Функция для Добавления записи

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			/*if ($_POST['type'] == 5){
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}elseif ($_POST['type'] == 6){
				$who = '&who=cosm';
				$datatable = 'zapis_cosm';
			}else{
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}*/
			/*$y = date("Y");
			$m = date("m");
			$d = date("d");*/
			
			$_time = time();
			$start_day = mktime(0, 0, 0, date("m", $_time), date("d", $_time), date("y", $_time));
			$time_post = strtotime($_POST['day'].'.'.$_POST['month'].'.'.$_POST['year']);
			
			//if ((($y <= $_POST['year']) && ($m <= $_POST['month']) && ($d <= $_POST['day'])) || ($_SESSION['permissions'] == '777')){
			//if (($start_day - $time_post <= 24*60*60) || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9)){
				
				if (isset($_POST['worker'])){
					$therapists = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
					if ($therapists != 0){
						$worker = $therapists[0]['id'];
						if ($_POST['patient'] != ''){
							//Ищем Пациента
							$clients = SelDataFromDB ('spr_clients', $_POST['patient'], 'client_full_name');
							//var_dump($clients);
							if ($clients != 0){
								$client = $clients[0]["id"];
								if ($_POST['contacts'] != ''){
									if ($_POST['description'] != ''){
										if (isset($_SESSION['filial'])){
											
											//Первичка
											if ($_POST['pervich'] != 0){
												$pervich = 1;
											}else{
												$pervich = 0;
											}
											//Страховые
											if ($_POST['insured'] != 0){
												$insured = 1;
											}else{
												$insured = 0;
											}
											//Ночные
											if ($_POST['noch'] != 0){
												$noch = 1;
											}else{
												$noch = 0;
											}
											
											//запись в базу
											WriteToDB_EditZapis ('zapis', $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_SESSION['filial'], $_POST['kab'], $worker, $_POST['author'], $client, $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt'], $_POST['type'], $pervich, $insured, $noch);
											
											$data = '
												<div class="query_ok">
													Запись добавлена<br><br>
												</div>';
											echo json_encode(array('result' => 'success', 'data' => $data));
											//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
											//client_add.php
										}else{
											$data = '
												<div class="query_neok">
													Ваш филиал не определён<br><br><a href="user.php?id='.$_SESSION['id'].'" class="ahref">определить</a>
												</div>';
											echo json_encode(array('result' => 'error', 'data' => $data));
										}
									}else{
										$data = '
											<div class="query_neok">
												Не указано описание<br><br>
											</div>';
										echo json_encode(array('result' => 'error', 'data' => $data));
									}
								}else{
									$data = '
										<div class="query_neok">
											Не указали контакты<br><br>
										</div>';
									echo json_encode(array('result' => 'error', 'data' => $data));
								}
							}else{
								$data = '
									<div class="query_neok">
										Не нашли в базе пациента<br>
										<a href="client_add.php" class="b">Добавить пациента</a><br>
									</div>';
								echo json_encode(array('result' => 'error', 'data' => $data));
							}
						}else{
							$data = '
								<div class="query_neok">
									Не указали пациента<br><br>
								</div>';
							echo json_encode(array('result' => 'error', 'data' => $data));
						}
					}else{
						$data = '
							<div class="query_neok">
								Нет такого врача<br><br>
							</div>';
						echo json_encode(array('result' => 'error', 'data' => $data));
					}
				}else{
					$data = '
						<div class="query_neok">
							Не выбрали врача<br><br>
						</div>';
					echo json_encode(array('result' => 'error', 'data' => $data));
				}
			/*}else{
				$data = '
					<div class="query_neok">
						Нельзя добавлять задним числом<br><br>
					</div>';
				echo json_encode(array('result' => 'error', 'data' => $data));
			}*/
		}
	}
?>