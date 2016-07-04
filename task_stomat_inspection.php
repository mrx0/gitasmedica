<?php

//task_stomat_inspection.php
//Описание осмотра стоматолога

	require_once 'header.php';
	
	if ($enter_ok){
		//var_dump($permissions);
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'tooth_status.php';
				
				$task = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');
				//var_dump($task);
				
				$closed = FALSE;
				$dop = array();
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_office', $task[0]['office'], 'offices');
						//var_dump ($offices);
						$office = $offices[0]['name'];
						
						$actions_stomat = SelDataFromDB('actions_stomat', '', '');
						//var_dump ($actions_stomat);
					}	
					echo '
						<script src="js/init.js" type="text/javascript"></script>
						<div id="status">
							<header>
								<h2>Посещение #'.$task[0]['id'].'</h2>';
								
					//Дополнительно
		
					$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$task[0]['id']}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($dop, $arr);
						}
						
					}
					//var_dump ($dop);
					if (!empty($dop)){
						if ($dop[0]['insured'] == 1){
							echo 'Страховое<br />';
						}
						if ($dop[0]['pervich'] == 1){
							echo 'Первичное<br />';
						}
						if ($dop[0]['noch'] == 1){
							echo 'Ночное<br />';
						}
					}

					//Надо найти клиента
					$clients = SelDataFromDB ('spr_clients', $task[0]['client'], 'client_id');
					if ($clients != 0){
						$client = $clients[0]["name"];
						if ($clients[0]["birthday"] != -1577934000){
							$cl_age = getyeardiff($clients[0]["birthday"]);
						}else{
							$cl_age = 0;
						}
					}else{
						$client = 'unknown';
						$cl_age = 0;
					}
					
					//Перенесено сюда снизу
					
					//ЗО и тд	
					$dop = array();							
					$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$task[0]['id']}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($dop, $arr);
						}
						
					}
											
											
					include_once 't_surface_name.php';
					include_once 't_surface_status.php';
					
					
					$arr = array();
					$decription = $task[0];
					
					//var_dump($decription);
					
					unset($decription['id']);
					unset($decription['office']);
					unset($decription['client']);
					unset($decription['create_time']);
					unset($decription['create_person']);
					unset($decription['last_edit_time']);
					unset($decription['last_edit_person']);
					unset($decription['worker']);
					
					unset($decription['comment']);
					
					$t_f_data = array();
					
					//собрали массив с зубами и статусами по поверхностям
					foreach ($decription as $key => $value){
						$surfaces_temp = explode(',', $value);
						//var_dump($surfaces_temp);
						foreach ($surfaces_temp as $key1 => $value1){
							///!!!Еба костыль
							if ($key1 < 13){
								$t_f_data[$key][$surfaces[$key1]] = $value1;
							}
						}
					}
					//var_dump ($t_f_data);
					if (!empty($dop[0])){
						//var_dump($dop[0]);
						unset($dop[0]['id']);
						//var_dump($dop[0]);
						foreach($dop[0] as $key => $value){
							//var_dump($value);
							if ($value != '0'){
								//var_dump($value);
								$dop_arr = json_decode($value, true);
								//var_dump($dop_arr);
								foreach ($dop_arr as $n_key => $n_value){
									if ($n_key == 'zo'){
										$t_f_data[$key]['zo'] = $n_value;
										//$t_f_data_draw[$key]['zo'] = $n_value;
									}
									if ($n_key == 'shinir'){
										$t_f_data[$key]['shinir'] = $n_value;
										//$t_f_data_draw[$key]['shinir'] = $n_value;
									}
									if ($n_key == 'podvizh'){
										$t_f_data[$key]['podvizh'] = $n_value;
										//$t_f_data_draw[$key]['podvizh'] = $n_value;
									}
								}
							}
						}
					}
					
					//var_dump ($t_f_data);		
					
					
					
					if (Sanation2($task[0]['id'], $t_f_data, $cl_age)){
						echo '<span style= "background: rgba(87,223,63,0.7); padding: 2px;">Санирован (ТЕСТ)</span><br />';
					}else{
						echo '<span style= "background: rgba(255,39,119,0.7); padding: 2px;">Не санирован (ТЕСТ)</span><br />';
					}

					
					echo '			
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
								<form>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Филиал</div>
										<div class="cellRight">'.$office.'</div>
									</div>

									<div class="cellsBlock2">
										<div class="cellLeft">Пациент</div>
										<div class="cellRight"><a href="client.php?id='.$task[0]['client'].'" class="ahref">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user').'</a></div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">';
										
					//$t_f_data_db = SelDataFromDB('journal_tooth_status_temp', $_GET['stat_id'], 'id');
					
					//$t_f_data_temp = $task[0];
					
					//$stat_id = $t_f_data_temp['id'];
					
					//unset($t_f_data_temp['id']);
					//unset($t_f_data_temp['create_time']);	
					
					



					//рисуем зубную формулу						
					include_once 'teeth_map_svg.php';
					DrawTeethMap($t_f_data, 0, $tooth_status, $tooth_alien_status, $surfaces, '');
												
					echo '						
										</div>
									</div>
									<div class="cellsBlock2">
										<!--<div class="cellLeft">Описание</div>-->
										<div class="cellRight">';
					
						$z = 0;
						$descr_rez = '';
		
						echo '<div><a href="#open1" onclick="show(\'hidden_'.$z.'\',200,5)">Подробно</a></div>';	
						echo '<div id=hidden_'.$z.' style="display:none;">';		
						foreach($t_f_data as $key => $value){
							//var_dump ($value);
							foreach ($value as $key1 => $value1){
								
								if ($key1 == 'status'){
									//var_dump ($value1);	
									if ($value1 != 0){
										//$descr_rez .= 
										echo t_surface_name('t'.$key.'NONE', 1).' '.t_surface_status($value1, 0).'';
									}
								}elseif($key1 == 'pin'){
									if ($value1 != 0){
										echo t_surface_status(3, 0);
									}
								}elseif($key1 == 'alien'){
									
								}elseif($key1 == 'zo'){
									
								}else{
									if ($value1 != 0){
										echo t_surface_name('t'.$key.$key1, 1).' '.t_surface_status(0, $value1);
									}
								}
							}
				
						}
						echo '</div>';
					
					/*foreach ($decription as $key => $value){
						//var_dump ($value);
						$zub_value = explode(',', $value);
						foreach ($zub_value as $key1 => $value1){
							$t_f_data[$key][$surfaces[$key1]] = $value1;
						}
						echo $key.' зуб.<br />';
					}*/
					
					//$decription = array();
					//$decription = json_decode($task[0]['description'], true);
					//$decription = $arr;
					
					//var_dump ($decription);		
						
					/*for ($j = 1; $j <= count($actions_stomat)-2; $j++) { 
						$action = '';
						if (isset($decription[$j])){
							if ($decription[$j] != 0){
								$action = $actions_stomat[$j-1]['full_name'].'<br />';
							}else{
								$action = '';
							}
							echo $action;
						}else{
							echo '';
						}
					}*/
					
					echo '	
										</div>
									</div>
									

									<div class="cellsBlock2">
										<div class="cellLeft">Комментарий</div>
										<div class="cellRight">'.$task[0]['comment'].'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Врач</div>
										<div class="cellRight">'.WriteSearchUser('spr_workers', $task[0]['worker'], 'user').'</div>
									</div>
									
									<div class="cellsBlock2">
										<span style="font-size:80%;">
											Создана: '.date('d.m.y H:i', $task[0]['create_time']).'<br />
											Кем: '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user').'<br />
											Последний раз редактировалось: '.date('d.m.y H:i', $task[0]['last_edit_time']).'<br />
											Кем: '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user').'
										</span>
									</div>
									
									<!--<input type="hidden" id="ended" name="ended" value="">-->
									<input type="hidden" id="task_id" name="task_id" value="'.$_GET['id'].'">
									<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';
									
					if (!$closed){
						if (($stom['edit'] == 1) || $god_mode){
							echo '
									<a href="edit_task_stomat.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
						}
					}
									
					$notes = SelDataFromDB ('notes', $_GET['id'], 'task');
					include_once 'WriteNotes.php';
					echo WriteNotes($notes);
									
					$removes = SelDataFromDB ('removes', $_GET['id'], 'task');
					include_once 'WriteRemoves.php';
					echo WriteRemoves($removes);
									
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
			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
			 </script>
					';
					
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