<?php

//task_cosmet.php
//Описание задачи косметолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'task_cosmet');
				//var_dump($task);
				
				$closed = FALSE;
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_filials', $task[0]['office'], 'offices');
						//var_dump ($offices);
						$office = $offices[0]['name'];
						
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
						//var_dump ($actions_cosmet);
					}	
					echo '
						<div id="status">
							<header>
								<h2>Посещение #'.$task[0]['id'].'';
					if (!$closed){
						if (($cosm['edit'] == 1) || $god_mode){
							echo '
									<a href="edit_task_cosmet.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
					}
					echo '			
								</h2>
							</header>';

					echo '
							<div id="data">';
							
					/*if ($task[0]['end_time'] == 0){
						$ended = 'Нет';
						$closed = FALSE;
					}else{
						$ended = date('d.m.y H:i', $task[0]['end_time']);
						$closed = TRUE;
					}*/
					echo '
								<form>';
					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">
											Время посещения<br>
											<span style="font-size:70%;">
												Согласно записи
											</span>
										</div>
										<div class="cellRight">';
					if ($task[0]['zapis_date'] != 0){
							echo date('d.m.y H:i', $task[0]['zapis_date']);
					}else{
						echo 'не было привязано к записи';
					}
					echo '
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">Филиал</div>
										<div class="cellRight">'.$office.'</div>
									</div>

									<div class="cellsBlock2">
										<div class="cellLeft">Пациент</div>
										<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user', true).'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Описание</div>
										<div class="cellRight">';
					

					$arr = array();
					
					foreach ($task[0] as $key => $value){
						/*if (mb_strstr($key, 'c') != FALSE){
							//array_push ($arr, $value);
							$key = str_replace('c', '', $key);
							//echo $key.'<br />';
							$arr[$key] = $value;
						}	*/			
						//!!! Лайфхак
						if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && 
						($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
							$key = str_replace('c', '', $key);
							$arr[$key] = $value;
						}
					}
					
					$decription = array();
					//$decription = json_decode($task[0]['description'], true);
					$decription = $arr;
					
					//var_dump ($decription);		
						
					for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						$action = '';
						if (isset($decription[$j])){
							if ($decription[$j] != 0){
								$action = '<div style="margin: 2px; border: 1px solid #CCC; padding-left: 3px; background-color: '.$actions_cosmet[$j-1]['color'].'">'.$actions_cosmet[$j-1]['full_name'].'</div>';
							}else{
								$action = '';
							}
							echo $action;
						}else{
							echo '';
						}
					}
					
					echo '	
										</div>
									</div>
									

									<div class="cellsBlock2">
										<div class="cellLeft">Комментарий</div>
										<div class="cellRight">'.$task[0]['comment'].'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Врач</div>
										<div class="cellRight">'.WriteSearchUser('spr_workers', $task[0]['worker'], 'user', true).'</div>
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
									
									<!--<input type="hidden" id="ended" name="ended" value="">-->
									<input type="hidden" id="task_id" name="task_id" value="'.$_GET['id'].'">
									<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';
					/*if (!$closed){
						echo '
									<input type=\'button\' class="b" value=\'Назначить исполнителя\' onclick=\'
										ajax({
											url:"task_add_worker_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}*/

					/*if ($closed){
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
					}else{
						echo '
									<input type=\'button\' class="b" value=\'Закрыть\' onclick=\'
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
					}*/
					echo '
								</form>';	
						
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