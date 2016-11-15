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
			if ($_POST['type'] == 5){
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}elseif ($_POST['type'] == 6){
				$who = '&who=cosm';
				$datatable = 'zapis_cosm';
			}else{
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}
			if ($_POST['worker'] !=0){
				if ($_POST['patient'] != ''){
					if ($_POST['contacts'] != ''){
						if ($_POST['description'] != ''){
							//Ищем Пациента
							$clients = SelDataFromDB ('spr_clients', $_POST['patient'], 'client_full_name');
							//var_dump($clients);
							if ($clients != 0){
								$client = $clients[0]["id"];
								//запись в базу
								WriteToDB_EditZapis ('zapis', $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab'], $_POST['worker'], $_POST['author'], $client, $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt'], $_POST['type']);
								
								echo '
									<div class="query_ok">
										Запись добавлена<br><br>
									</div>';
								//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
								//add_client.php
							}else{
								echo '
									<div class="query_neok">
										Добавьте пациента в базу<br>
										<a href="add_client.php" class="b">Добавить пациента</a><br>
									</div>';
							}
						}else{
							echo '
								<div class="query_neok">
									Не указано описание<br><br>
								</div>';
						}
					}else{
						echo '
							<div class="query_neok">
								Не указали контакты<br><br>
							</div>';
					}
				}else{
					echo '
						<div class="query_neok">
							Не указали пациента<br><br>
						</div>';
				}
			}else{
				echo '
					<div class="query_neok">
						Не выбрали врача<br><br>
					</div>';
			}
		}
	}
?>