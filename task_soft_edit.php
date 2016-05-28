<?php

//task_soft_edit.php
//Редактирование задачи soft

	require_once 'header.php';
	
	if ($enter_ok){
		if (($soft['see_all'] == 1) || ($soft['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_soft', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				
				if ($task !=0){
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
									<form action="task_soft_edit_f.php">

										<div class="cellsBlock2">
											<div class="cellLeft">Краткое описание</div>
											<div class="cellRight">
												'.$task[0]['description'].'
											</div>
										</div>
		
										<div class="cellsBlock2">
											<div class="cellLeft">Описание</div>
											<div class="cellRight">
												<textarea name="description" id="description" cols="35" rows="10">'.nl2br($task[0]['full_description']).'</textarea>
											</div>
										</div>
										
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
												url:"task_soft_edit_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													id:document.getElementById("id").value,
													description:document.getElementById("description").value,
												},
												success:function(data){document.getElementById("status").innerHTML=data;}
											})\'
										>
									</form>';	
					}else{
						echo '<h1>Задача закрыта. Редактировать нельзя</h1><a href="task_soft.php?id='.$_GET['id'].'" class="b">Вернуться в заявку</a>';
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