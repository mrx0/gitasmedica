<?php 

//task_soft_add_worker.php
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
						
				$task = SelDataFromDB('journal_soft', $_GET['id'], 'task');
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
										url:"task_soft_add_worker_f.php",
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
				
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';
?>