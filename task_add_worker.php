<?php 

//task_add_worker.php
//Добавить/изменить исполнителя IT

	require_once 'header.php';
	
	if ($enter_ok){
		if (($it['add_worker'] == 1) || $god_mode){
			include_once 'DBWork.php';
			//var_dump ($_GET);
			
			if ($_GET){
				echo '
					<div id="status">
						<header>
							<h2>Выбор исполнителя</h2>
							Заполните поля
						</header>';

				echo '
						<div id="data">';
						
				$task = SelDataFromDB('journal_it', $_GET['id'], 'task');
				//var_dump($task);
				if ($task != 0){
					if ($task[0]['worker'] != 0){
						echo '
							У задачи уже назначен исполнитель: ';
							$user = SelDataFromDB('spr_workers', $task[0]['worker'], 'user');
							//var_dump($user);
							if ($user != 0){
								echo $user[0]['name'].'<br /><br />';
							}
					}
				}		
				echo '
							<form action="task_add_worker_f.php">
								<div class="cellsBlock3">
									<div class="cellLeft">Исполнитель</div>
									<div class="cellRight">
										<input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2"  autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</div>
								
								<input type=\'button\' class="b" value=\'Назначить/Изменить\' onclick=\'
									ajax({
										url:"task_add_worker_f.php",
										statbox:"status",
										method:"POST",
										data:
										{	
											worker:document.getElementById("search_client2").value,
											id:'.$_GET['id'].',	
										},
										success:function(data){document.getElementById("status").innerHTML=data;}
									})\'
								>
							</form>';	
				
				/*
				if ($_POST['id'] == ''){
					echo '
						Не выбрали пациента. Давайте еще разок =)<br /><br />
						<a href="add_task_cosmet.php" class="b">Добавить</a>
						<a href="index.php" class="b">На главную</a>';
				}else{
					$arr = array();
					$rezult = '';
					
					foreach ($_POST as $key => $value){
						if (($key != 'author') && ($key != 'client') && ($key != 'ajax') && ($key != 'comment')){
							//array_push ($arr, $value);
							$key = str_replace('action', '', $key);
							//echo $key.'<br />';
							$arr[$key] = $value;
						}				
					}
					//var_dump ($arr);
					$rezult = json_encode($arr);
					//echo $rezult.'<br />';
					//echo strlen($rezult);
					
					include_once 'DBWork.php';
					//Ищем ID клиента
					$clients = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
					if ($clients != 0){
						$client = $clients[0]["id"];
					}else{
						$client = '0';
					}
				
					WriteToDB_EditCosmet ('16', $client, $rezult, time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], $_POST['comment']);
				
					echo '
						Добавлено в журнал.
						<br /><br />
						<a href="add_task_cosmet.php" class="b">Добавить ещё</a>
						<a href="index.php" class="b">На главную</a>
						';
				}*/
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';
?>