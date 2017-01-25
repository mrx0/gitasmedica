<?php 

//add_task_cosmet_f.php
//Функция для добавления задачи косметологов в журнал

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		
		if ($_POST){
			$workerFounded = TRUE;
			
			if ($_POST['client'] == ''){
				echo '
					Не выбран пациент.<br><br>';
			}else{
				//Ищем клиента
				$clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
				//var_dump($clients);
				
				if ($clients != 0){
					$client = $clients[0]["id"];
					if ($clients[0]['therapist2'] == 0){
						UpdateTherapist($_SESSION['id'], $clients[0]["id"], $_SESSION['id'], '2');
					}
					
					
					if ($_POST['filial'] != 0){
						//Исполнитель
						if (isset($_POST['worker'])){
						
							$workers = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
							if ($workers != 0){
								$workerFounded = TRUE;
								$worker = $workers[0]["id"];
							}else{
								$workerFounded = FALSE;
							}
						}else{
							$worker = $_SESSION['id'];
						}
						
						
						if ($workerFounded){
							
							//Отметки процедур
							$arr = array();
							$rezult = '';
							
							foreach ($_POST as $key => $value){
								if (mb_strstr($key, 'action') != FALSE){
									//array_push ($arr, $value);
									$key = str_replace('action', 'c', $key);
									//echo $key.'<br />';
									$arr[$key] = $value;
								}				
							}
							
							//var_dump ($arr);
							//$rezult = json_encode($arr);
							//echo $rezult.'<br />';
							//echo strlen($rezult);
							
							//новая отметка о первичке
							if ($_POST['pervich'] == 1){
								$pervich_status = 1;
							}else{
								$pervich_status = 0;
							}
							
							
							$task = WriteToDB_EditCosmet ($_POST['filial'], $client, $arr, time(), $_SESSION['id'], $worker, $_POST['comment'], $pervich_status, $_POST['zapis_date'], $_POST['zapis_id']);
						
							echo '
								<a href="task_cosmet.php?id='.$task.'" class="ahref">Посещение #'.$task.'</a> добавлено в журнал.
								<br><br>
								<header>
									<span style= "color: rgba(255,39,39,0.7); padding: 2px;">
										Напоминание: Если вы что-то забыли или необходимо внести изменения,<br />
										посещение можно <a href="edit_task_cosmet.php?id='.$task.'" class="ahref">отредактировать</a>.
									</span>
								</header>
								<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>';
						}else{
							echo '
								Указанный вами исполнитель отсутствует в нашей базе
								<br><br>
								<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>';
						}
					}else{
						echo '
							Вы не выбрали филиал
							<br><br>
							<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>';
					}
				}else{
					echo '
						В нашей базе нет такого пациента
						<br><br>';
				}
			}
		}
	}
?>