<?php

//task_edit_cosmet.php
//Редактирование посещения косметолога

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_office', '', '');
						//var_dump ($offices);
						//$office = $offices[0]['name'];
					}	
					echo '
						<div id="status">
							<header>
								<h2>Редактировать посещение</h2>
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
					if (!$closed){
					
						echo '
									<form action="edit_task_cosmet_f.php">
										
										<div class="cellsBlock2">
											<div class="cellLeft">Дата посещения</div>
											<div class="cellRight">';
				if ($task[0]['create_time'] != 0){
					//print_r  (getdate($client[0]['birthday']));
					$bdate = getdate($task[0]['create_time']);
					echo '
						<input type="hidden" id="sel_seconds" name="sel_seconds" value="'.$bdate['seconds'].'">
						<input type="hidden" id="sel_minutes" name="sel_minutes" value="'.$bdate['minutes'].'">
						<input type="hidden" id="sel_hours" name="sel_hours" value="'.$bdate['hours'].'">';
				}else{
					$bdate = 0;
				}
				echo '<select name="sel_date" id="sel_date">';
				$i = 1;
				while ($i <= 31) {
					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
					$i++;
				}
				echo "</select>";
				// Месяц
				echo "<select name='sel_month' id='sel_month'>";
				$month = array(
					"Январь",
					"Февраль",
					"Март",
					"Апрель",
					"Май",
					"Июнь",
					"Июль",
					"Август",
					"Сентябрь",
					"Октябрь",
					"Ноябрь",
					"Декабрь"
				);
				foreach ($month as $m => $n) {
					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
				}
				echo "</select>";
				// Год
				echo "<select name='sel_year' id='sel_year'>";
				$j = 1920;
				while ($j <= 2020) {
					echo "<option value='" . $j . "'", $bdate['year'] == $j ? ' selected':'' ,">$j</option>";
					$j++;
				}
				echo '</select>

											
											</div>
										</div>
																				
										<div class="cellsBlock2">
											<div class="cellLeft">Врач</div>
											<div class="cellRight">'.WriteSearchUser('spr_workers',$task[0]['worker'], 'user').'</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Пациент</div>
											<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user').'</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Филиал</div>
											<div class="cellRight">
												<select name="filial" id="filial">
													<option value="0" selected>Выберите филиал</option>';
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
											</div>
										</div>
										';
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
						
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
						
						/*$decription = array();
						$decription = json_decode($task[0]['description'], true);*/
						
						//var_dump ($decription);	


				//отсортируем по nomer

				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
						
						
						//!!!Без возможности редактирования
						/*for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
							$action = '';
							if (isset($decription[$j])){
								if ($decription[$j] != 0){
									$action = $actions_cosmet[$j-1]['full_name'].'<br />';
								}else{
									$action = '';
								}
								echo $action;
							}else{
								echo '';
							}
						}*/

			//!!!С возможностью редактирования			
			$post_data = '';
			$js_data = '';			
			if ($actions_cosmet != 0){
				
				for ($i = 0; $i < count($actions_cosmet)-2; $i++){
					
					if (isset ($decription[$i+1])){
						if ($decription[$actions_cosmet[$i]['id']] == 1){
							$checked = 'checked';
						}else{
							$checked = '';
						}
					}
					
					$js_data .= '
						if ($("#action'.$actions_cosmet[$i]['id'].'").prop("checked")){
							action_value'.$actions_cosmet[$i]['id'].' = 1;
						}else{
							action_value'.$actions_cosmet[$i]['id'].' = 0;
						}
					
					';
					$post_data .= '
									action'.$actions_cosmet[$i]['id'].':action_value'.$actions_cosmet[$i]['id'].',';
					echo '
						<div class="cellsBlock3">
							<div class="cellLeft">'.$actions_cosmet[$i]['full_name'].'</div>
							<div class="cellRight">
								<input type="checkbox" name="action'.$actions_cosmet[$i]['id'].'" id="action'.$actions_cosmet[$i]['id'].'" '.$checked.'>
							</div>
						</div>';
				}
			}			

						echo '
											<!--</div>
										</div>-->
										
										<div class="cellsBlock2">
											<div class="cellLeft">Комментарий</div>
											<div class="cellRight">
												<textarea name="comment" id="comment" cols="35" rows="10" style="vertical-align:top; text-align:left;">'
												.$task[0]['comment'].
												'</textarea>
											</div>
										</div>	

										<div class="cellsBlock2">
											<div class="cellLeft">Подтвердить редактирование</div>
											<div class="cellRight">
												<input type="checkbox" name="change_true">
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
												url:"edit_task_cosmet_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													id:document.getElementById("id").value,
													filial:document.getElementById("filial").value,
													comment:document.getElementById("comment").value,
													
													
													sel_date:document.getElementById("sel_date").value,
													sel_month:document.getElementById("sel_month").value,
													sel_year:document.getElementById("sel_year").value,
													
													sel_seconds:document.getElementById("sel_seconds").value,
													sel_minutes:document.getElementById("sel_minutes").value,
													sel_hours:document.getElementById("sel_hours").value,';
													
					echo $post_data;
					echo							'
												},
												success:function(data){document.getElementById("status").innerHTML=data;}
											})\'
										>
									</form>';	
					}else{
						echo '<h1>Какая-то ошибка</h1>';
					}
					echo '
							</div>
						</div>';
						
			//Фунция JS для проверки не нажаты ли чекбоксы
			echo '
				<script type="text/javascript">
					$("input").change(function() {
						var $input = $(this);';
			echo $js_data;
			echo '
					});
				</script>
			';
						
						
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