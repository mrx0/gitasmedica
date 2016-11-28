<?php 

//edit_schedule_day_f.php
//Функция для редактирования записи

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
			$y = date("Y");
			$m = date("m");
			$d = date("d");
			
			if ($y > $_POST['year'] || $m > $_POST['month'] || $d > $_POST['day']){
				$data = '
					<div class="query_neok">
						Нельзя добавлять задним числом<br><br>
					</div>';
				echo json_encode(array('result' => 'error', 'data' => $data));
			}else{
			
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
											//запись в базу
											WriteToDB_EditZapis ('zapis', $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_SESSION['filial'], $_POST['kab'], $worker, $_POST['author'], $client, $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt'], $_POST['type']);
											
											$data = '
												<div class="query_ok">
													Запись добавлена<br><br>
												</div>';
											echo json_encode(array('result' => 'success', 'data' => $data));
											//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
											//add_client.php
										}else{
											$data = '
												<div class="query_neok">
													Филиал не определён<br><br>
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
										<a href="add_client.php" class="b">Добавить пациента</a><br>
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
			}
		}
	}
?>