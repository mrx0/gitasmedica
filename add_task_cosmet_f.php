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
			if ($_POST['client'] == ''){
				echo '
					Не выбрали пациента. Давайте еще разок =)<br /><br />
					<a href="add_task_cosmet.php" class="b">Добавить запись</a>';
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
						
						WriteToDB_EditCosmet ($_POST['filial'], $client, $arr, time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], $_POST['comment']);
					
						echo '
							Добавлено в журнал.
							<br /><br />
							<a href="client.php?id='.$client.'" class="b">В карточку пациента</a>
							<a href="add_task_cosmet.php?client='.$client.'" class="b">Добавить посещение этому пациенту</a>
							<a href="add_task_cosmet.php" class="b">Добавить новое посещение</a>
							';
					}else{
						echo '
							Вы не выбрали филиал<br /><br />
							<a href="add_task_cosmet.php" class="b">Добавить запись</a>';
					}
				}else{
					echo '
						В нашей базе нет такого пациента :(<br /><br />
						<a href="add_task_cosmet.php" class="b">Добавить запись</a>
						<a href="add_client.php" class="b">Добавить пациента</a>';
				}
			}
		}
	}
?>