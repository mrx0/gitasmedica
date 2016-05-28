<?php

//task_edit.php
//Редактирование задачи IT

	require_once 'header.php';
	
	if ($enter_ok){
		if (($it['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_it', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				
				$offices = SelDataFromDB('spr_office', '', '');
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						//$offices = SelDataFromDB('spr_office', '', '');
						//var_dump ($offices);
						//$office = $offices[0]['name'];
					}	
					echo '
						<div id="status">
							<header>
								<h2>Редактировать задачу</h2>
							</header>';

					echo '
							<div id="data">';
							
					if ($task[0]['end_time'] == 0){
						$ended = 'Нет';
						$closed = FALSE;
					}else{
						$ended = date('d.m.y H:i', $task[0]['end_time']);
						$closed = TRUE;
					}
					if (!$closed){
					
						echo '
									<form action="task_edit_f.php">
										<div class="cellsBlock2">
											<div class="cellLeft">Филиал</div>
											<div class="cellRight">
												<select name="filial" id="filial">
													<option value="99" ', $task[0]['office'] == '99' ? 'selected' : '' ,'>Для всех</option>';
						if ($offices !=0){
							for ($i=0;$i<count($offices);$i++){
								echo "<option value='".$offices[$i]['id']."' ", $task[0]['office'] == $offices[$i]['id'] ? 'selected' : '' ,">".$offices[$i]['name']."</option>";
							}
						}
						echo '
												</select>
											</div>
										</div>

										<div class="cellsBlock2">
											<div class="cellLeft">Описание</div>
											<div class="cellRight">
												<textarea name="description" id="description" cols="35" rows="10">'.nl2br($task[0]['description']).'</textarea>
											</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Приоритет</div>
											<div class="cellRight">
												<select name="priority" id="priority">
													<option value="1" ', $task[0]['priority'] == 1 ? 'selected' : '' ,'>Низкий</option>
													<option value="2" ', $task[0]['priority'] == 2 ? 'selected' : '' ,'>Средний</option>
													<option value="3" ', $task[0]['priority'] == 3 ? 'selected' : '' ,'>Высокий</option>
												</select>
											</div>
										</div>
										
										<div class="cellsBlock2">
										<div class="cellsBlock2">
											<span style="font-size:80%;">
												Создана: '.date('d.m.y H:i', $task[0]['create_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user').'<br />
												Последний раз редактировалось: '.date('d.m.y H:i', $task[0]['last_edit_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user').'
											</span>
										</div>
										<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
										<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
										<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
											ajax({
												url:"task_edit_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													id:document.getElementById("id").value,
													filial:document.getElementById("filial").value,
													description:document.getElementById("description").value,
													priority:document.getElementById("priority").value,
												},
												success:function(data){document.getElementById("status").innerHTML=data;}
											})\'
										>
									</form>';	
					}else{
						echo '<h1>Задача закрыта. Редактировать нельзя</h1><a href="task.php?id='.$_GET['id'].'" class="b">Вернуться в заявку</a>';
					}
					echo '
							</div>
						</div>';
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>