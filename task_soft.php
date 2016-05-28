<?php

//task.php
//Описание задачи IT

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
					if ($god_mode || ($soft['see_all'] == 1) || ($_SESSION['id'] == $task[0]['create_person'])){
						if ($task[0]['end_time'] != 0) {
							$closed = TRUE; 
						}
						
						echo '
							<div id="status">
								<header>
									<h2>Задача</h2>
								</header>';

						echo '
								<div id="data">';
								
						if ($task[0]['end_time'] == 0){
							if ($task[0]['inwork'] == 0){
								$ended = 'Рассмотрение';
								$background_style2 = '
									background: rgba(231,55,71, 0.9);
									color:#fff;
									';
							}
							if ($task[0]['inwork'] == 1){
								$ended = 'В работе';
								$background_style2 = '
									background: rgba(231,175,55, 0.8);
									color:#fff;
									';
							}
							if ($task[0]['inwork'] == 2){
								$ended = 'Отказ';
								$background_style2 = '
									background: rgba(185,145,255, 0.9);
									color:#fff;
									';
							}
						}else{
							$ended = date('d.m.y H:i', $task[0]['end_time']);
							$background_style2 = '
								background: rgba(144,247,95, 0.5);
								';
						}
						
						
						echo '
									<form name="formname">

										<div class="cellsBlock2">
											<div class="cellLeft">Краткое писание</div>
											<div class="cellRight">'.$task[0]['description'].'</div>
										</div>
		
										<div class="cellsBlock2">
											<div class="cellLeft">Описание</div>
											<div class="cellRight">'.nl2br($task[0]['full_description']).'</div>
										</div>
									
										<div class="cellsBlock2">
											<div class="cellLeft">Исполнитель</div>
											<div class="cellRight">'.WriteSearchUser('spr_workers', $task[0]['worker'], 'user').'</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Статус</div>
											<div class="cellRight" style="'.$background_style2.'">'.$ended.'</div>
										</div>';		
							if ($god_mode || ($soft['see_all'] == 1)){
								echo '				
										<div class="cellsBlock2">
											<div class="cellLeft">Сменить статус</div>
											<div class="cellRight">
												<input id="change_status" name="change_status" value="0" type="radio" checked>Не менять<br />
												<input id="change_status" name="change_status" value="1" type="radio">В работу<br />
												<input id="change_status" name="change_status" value="2" type="radio">Отказ<br />
												<input id="change_status" name="change_status" value="3" type="radio">Вернуть в рассмотрение<br />
											</div>
										</div>
										<div id="change_status_req"></div>';
							}			
							echo '			
										<div class="cellsBlock2">
											<span style="font-size:80%;">
												Создана: '.date('d.m.y H:i', $task[0]['create_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user').'<br />
												Последний раз редактировалось: '.date('d.m.y H:i', $task[0]['last_edit_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user').'
											</span>
										</div>
										
										<input type="hidden" id="ended" name="ended" value="'.$task[0]['end_time'].'">
										<input type="hidden" id="task_id" name="task_id" value="'.$_GET['id'].'">
										<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';
						if (!$closed){
							if (($soft['add_worker'] == 1) || $god_mode){
								echo '
										<a href="task_soft_add_worker.php?id='.$_GET['id'].'" class="b">Назначить исполнителя</a>';
							}
						}
						if (!$closed){
							if (($soft['edit'] == 1) || $god_mode){
								echo '
										<a href="task_soft_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
							}
						}
						if ($closed){
							if (($soft['reopen'] == 1) || $god_mode){
								echo '
										<input type=\'button\' class="b" value=\'Вернуть в работу\' onclick=\'
											ajax({
												url:"task_soft_reopen_f.php",
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
							if ((($soft['close'] == 1) || $god_mode) && !$closed){
								echo '
										<input type=\'button\' class="b" value=\'Завершить\' onclick=\'
											ajax({
												url:"task_soft_close_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													ended:document.getElementById("ended").value,
													task_id:'.$_GET['id'].',
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
						if (($soft['edit'] == 1) || $god_mode){
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
												url:"add_comment_task_soft_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													t_s_comment:document.getElementById("t_s_comment").value,
													author:document.getElementById("author").value,
													id:document.getElementById("id").value,
												},
												success:function(data){document.getElementById("status").innerHTML=data;}
											})\'
										>
									</form>';
						}
						//отобразить комментарии
						if (($soft['see_all'] == 1) || ($soft['see_own'] == 1) || $god_mode){
							$comments = SelDataFromDB('comments', 'soft'.':'.$_GET['id'], 'parrent');
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
												'.WriteSearchUser('spr_workers',$value['create_person'], 'user').'<br />
												<span style="font-size:80%;">'.date('d.m.y H:i', $value['create_time']).'</span>
											</div>
											<div class="cellRight">'.nl2br($value['description']).'</div>
										</div>';
								}
							}
						}
						echo '
								</div>
							</div>
							
							<script>
								$(function() {
									var change_status_val = 0;
									$(\'input:radio[name=change_status]\').change(function(){
										change_status_val = $(this).val();
										//alert(change_status_val);
										ajax({
											url:"change_status_soft_task.php",
											method:"POST",
											data:
											{
												change_status_val:change_status_val,
												id:'.$_GET['id'].',
											},
											success:function(data){
												//document.getElementById("change_status_req").innerHTML=data;
												alert(data);
											}
										})
									});
								});

							
							</script>
							
							
							';
					}else{
						echo '<h1>Вы не можете смотреть не ваше</h1><a href="soft.php">В журнал</a>';
					}
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