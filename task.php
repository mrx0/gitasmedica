<?php

//task.php
//Описание задачи IT

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($it['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_it', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_filials', $task[0]['office'], 'offices');
						//var_dump ($offices);
						$office = $offices[0]['name'];
					}	
					echo '
						<div id="status">
							<header>
								<h2>Задача #'.$task[0]['id'].'';
					if (!$closed){
						if (($it['edit'] == 1) || $god_mode){
							
							echo '
									<a href="task_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
					}
					echo '
								</h2>
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
					echo '
								<form>
									<div class="cellsBlock2">
										<div class="cellLeft">Филиал</div>
										<div class="cellRight">'.$office.'</div>
									</div>

									<div class="cellsBlock2">
										<div class="cellLeft">Описание</div>
										<div class="cellRight">'.$task[0]['description'].'</div>
									</div>
								
									<div class="cellsBlock2">
										<div class="cellLeft">Исполнитель</div>
										<div class="cellRight">', $task[0]['worker'] == 0 ? 'не определён' : WriteSearchUser('spr_workers', $task[0]['worker'], 'user', true) ,'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Закрыта</div>
										<div class="cellRight">'.$ended.'</div>
									</div>								
									
									<div class="cellsBlock2">
										<span style="font-size: 80%; color: #999;">
											Создан: '.date('d.m.y H:i', $task[0]['create_time']).' пользователем
											'.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user', true).'';
					if ((($task[0]['last_edit_time'] != 0) || ($task[0]['last_edit_person'] !=0)) && (($task[0]['create_time'] != $task[0]['last_edit_time']))){
						echo '
											<br>
											Редактировался: '.date('d.m.y H:i', $task[0]['last_edit_time']).' пользователем
											'.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user', true).'';
					}
					echo '
										</span>
									</div>
									
									<input type="hidden" id="ended" name="ended" value="'.$task[0]['end_time'].'">
									<input type="hidden" id="task_id" name="task_id" value="'.$_GET['id'].'">
									<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';
					if (!$closed){
						if (($it['add_worker'] == 1) || $god_mode){
							echo '
									<a href="task_add_worker.php?id='.$_GET['id'].'" class="b">Назначить исполнителя</a>';
						}
					}

					if ($closed){
						if (($it['reopen'] == 1) || $god_mode){
							echo '
									<input type=\'button\' class="b" value=\'Вернуть в работу\' onclick=\'
										ajax({
											url:"task_reopen_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
						}
					}else{
						if (($it['close'] == 1) || $god_mode){
							echo '
									<input type=\'button\' class="b" value=\'Завершить\' onclick=\'
										ajax({
											url:"task_close_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
						}
					}
					echo '
								</form>';	
								
					//оставить комментарий
					if ((($it['add_own'] == 1) || $god_mode) && (!$closed)){
						echo '
								<form>

									<div class="cellsBlock2">
										<div class="cellLeft">Оставить комментарий</div>
										<div class="cellRight"><textarea name="t_s_comment" id="t_s_comment" cols="35" rows="5"></textarea></div>
									</div>
									<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
									<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
									<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
										ajax({
											url:"add_comment_task_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												t_s_comment:encodeURIComponent(document.getElementById("t_s_comment").value),
												author:document.getElementById("author").value,
												id:document.getElementById("id").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>
								</form>';
					}
					//отобразить комментарии
					if (($it['see_all'] == 1) || $god_mode){
						$comments = SelDataFromDB('comments', 'it'.':'.$_GET['id'], 'parrent');
						//var_dump ($comments);	
						if ($comments != 0){
							echo '
								<div class="cellsBlock3">
									<div class="cellRight">Все комментарии</div>
								</div>';
							foreach ($comments as $value){
								echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											'.WriteSearchUser('spr_workers',$value['create_person'], 'user', true).'<br />
											<span style="font-size:80%;">'.date('d.m.y H:i', $value['create_time']).'</span>
										</div>
										<div class="cellRight">'.nl2br($value['description']).'</div>
									</div>';
							}
						}
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