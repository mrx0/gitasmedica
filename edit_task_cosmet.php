<?php

//edit_task_cosmet.php
//Редактирование посещения косметолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($cosm['edit'] == 1) || $god_mode){
			if ($_GET){
				//include_once 'DBWork.php';
                include_once('DBWorkPDO.php');

				include_once 'functions.php';

                require 'variables.php';

                include_once 'showZapisRezult.php';
				
				$task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				
				if ($task !=0){
//					if ($task[0]['office'] == 99){
//						$office = 'Во всех';
//					}else{
//						$offices = SelDataFromDB('spr_filials', '', '');
//						//var_dump ($offices);
//						//$office = $offices[0]['name'];
//					}

                    $zapis_id = $task[0]['zapis_id'];

                    $args = [
                        'zapis_id' => $zapis_id
                    ];

                    //Получаем данные по записи
                    //Получаем данные по записи
                    $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";

                    //Выбрать все
                    $sheduler_zapis = $db::getRows($query, $args);
                    //var_dump($sheduler_zapis);

                    //Если запись есть (а она должна быть, если ЗФ не очень старая, ведь раньше записи не было)
                    if (!empty($sheduler_zapis)) {
                        $client_id = $sheduler_zapis[0]['patient'];
                        $filial_id = $sheduler_zapis[0]['office'];
                    }else{
                        $client_id = $task[0]['client'];
                        $filial_id = $task[0]['office'];
                    }

					echo '
						<div id="status">
							<header>
								<h2>Редактировать посещение <a href="task_cosmet.php?id='.$task[0]['id'].'" class="ahref">#'.$task[0]['id'].'</a></h2>
							</header>';

                    //Показываем карточку записи
                    echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);

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
									<form action="edit_task_cosmet_f.php">';
//                        echo '
//										<div class="cellsBlock2">
//											<div class="cellLeft">
//												Время посещения<br>
//												<span style="font-size:70%;">
//													Согласно записи
//												</span>
//											</div>
//											<div class="cellRight">';
//						if ($task[0]['zapis_date'] != 0){
//								echo date('d.m.y H:i', $task[0]['zapis_date']);
//						}else{
//							echo 'не было привязано к записи';
//						}
//						echo '
//											</div>
//										</div>';

//						echo '
//										<div class="cellsBlock2" style="display: none">
//											<div class="cellLeft">Дата посещения</div>
//											<div class="cellRight">';
//				if ($task[0]['create_time'] != 0){
//					//print_r  (getdate($client[0]['birthday']));
//					$bdate = getdate($task[0]['create_time']);
//					echo '
//						<input type="hidden" id="sel_seconds" name="sel_seconds" value="'.$bdate['seconds'].'">
//						<input type="hidden" id="sel_minutes" name="sel_minutes" value="'.$bdate['minutes'].'">
//						<input type="hidden" id="sel_hours" name="sel_hours" value="'.$bdate['hours'].'">';
//				}else{
//					$bdate = 0;
//				}
//				echo '<select name="sel_date" id="sel_date">';
//				$i = 1;
//				while ($i <= 31) {
//					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
//					$i++;
//				}
//				echo "</select>";
//				// Месяц
//				echo "<select name='sel_month' id='sel_month'>";
//				$month = array(
//					"Январь",
//					"Февраль",
//					"Март",
//					"Апрель",
//					"Май",
//					"Июнь",
//					"Июль",
//					"Август",
//					"Сентябрь",
//					"Октябрь",
//					"Ноябрь",
//					"Декабрь"
//				);
//				foreach ($month as $m => $n) {
//					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
//				}
//				echo "</select>";
//				// Год
//				echo "<select name='sel_year' id='sel_year'>";
//				$j = 1920;
//				while ($j <= 2020) {
//					echo "<option value='" . $j . "'", $bdate['year'] == $j ? ' selected':'' ,">$j</option>";
//					$j++;
//				}
//				echo '</select>
//
//
//											</div>
//										</div>
//
//										<div class="cellsBlock2">
//											<div class="cellLeft">Врач</div>
//											<div class="cellRight">'.WriteSearchUser('spr_workers',$task[0]['worker'], 'user', true).'</div>
//										</div>
//
//										<div class="cellsBlock2">
//											<div class="cellLeft">Пациент</div>
//											<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user', true).'</div>
//										</div>';

				/*echo '
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
										</div>';*/

//                        echo '
//										<div class="cellsBlock2">
//											<div class="cellLeft">Филиал</div>
//											<div class="cellRight">';
//
//                        $offices_j = SelDataFromDB('spr_filials', $task[0]['office'] , 'offices');
//
//                        echo $offices_j[0]['name'].'<input type="hidden" id="filial" name="filial" value="'.$task[0]['office'] .'">';
//
//                        echo '
//											</div>
//										</div>';

                        echo '
				
										<div class="cellsBlock2">
											<div class="cellLeft">Описание</div>
											<div class="cellRight">
											</div>
										</div>
										';
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
						//var_dump($actions_cosmet);
						
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
				    //var_dump($actions_cosmet[$i]['id']);
				    //var_dump($decription[$actions_cosmet[$i]['id']]);

                    $checked = '';

                    //!!! 2020-11-28 не знаю, зачем я тут это делал, но теперь убрал, посмотрим, на что повлияет
					//if (isset ($decription[$i+1])){
						if ($decription[$actions_cosmet[$i]['id']] == 1){
							$checked = 'checked';
						}/*else{
							$checked = '';
						}*/
					//}
                    //var_dump($checked);
					
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
						<div class="cellsBlock2">
							<div class="cellLeft" style=" width: auto; font-size: 80%; background-color: '.$actions_cosmet[$i]['color'].'">
								<span style="float: left;">'.$actions_cosmet[$i]['full_name'].'</span>
								<span style="float: right;"><input type="checkbox" name="action'.$actions_cosmet[$i]['id'].'" id="action'.$actions_cosmet[$i]['id'].'" '.$checked.'></span>
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
										</div>';

                        //Напоминания / особые отметки
                        $notes = SelDataFromDB ('notes', $_GET['id'], 'task');

                        if ($notes != 0){
                            echo '
							<div class="cellsBlock2">
								<div class="cellRight">
									Напоминание уже создано
								</div>
							</div>';
                        }else{
                            echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Создать напоминание
									<input type="checkbox" name="add_notes_show" id="add_notes_show" value="1" onclick="Add_notes_stomat_show(this)">
								</div>
								
								<div class="cellRight">
									<table id="add_notes_here" style="display:block; display:none;">
										<tr>
											<td colspan="2">
										
												<select name="add_notes_type" id="add_notes_type">
													<option value="0" selected>Выберите</option>';
                            foreach ($for_notes[6] as $for_notes_id =>  $for_notes_descr){
                                echo '<option value="'.$for_notes_id.'">'.$for_notes_descr.'</option>';
                            }

                            echo '
												</select>
											
											</td>
										</tr>
										<tr>
											<td>Месяцев</td>
											<td>Дней</td>
										</tr>
										<tr>
											<td>
												<input type="number" size="2" name="add_notes_months" id="add_notes_months" min="0" max="12" value="0">
											</td>
											<td>
												<input type="number" size="2" name="add_notes_days" id="add_notes_days" min="0" max="31" value="0">
											</td>
										</tr>
										<!--<tr>
											<td>
												<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_notes_stomat()>
											</td>
										</tr>-->
									</table>
									
								</div>
							</div>
							';
                        }

			            echo '

										<div class="cellsBlock2">
											<div class="cellLeft">Подтвердить редактирование</div>
											<div class="cellRight">
												<input type="checkbox" name="change_true">
											</div>
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
										<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
										<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
										<input type="hidden" id="client_id" name="client_id" value="'.$client_id.'">
										<input type="hidden" id="client" name="client" value="'.$client_id.'">
                                        
										<input type="button" class="b" value="Применить" onclick="Ajax_edit_task_cosmet()">';
                    echo '
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
				
				
					if (document.getElementById("#add_notes_show")){
						document.getElementById(\'add_notes_show\').checked=false;
					}
//					document.getElementById(\'add_remove_show\').checked=false;
					
					function Add_notes_stomat_show(box) {
						var vis = (box.checked) ? "block" : "none";
						document.getElementById(\'add_notes_here\').style.display = vis;
					}
				

					$("input").change(function() {
						let $input = $(this);';
			echo $js_data;
			echo '
					});
				
                    function Ajax_edit_task_cosmet() {
                    
                        let link = "edit_task_cosmet_f.php";
                    
                        let add_notes_type = 0;
                        let add_notes_months = 0;
                        let add_notes_days = 0;
                
                        let notes_val = 0;
//                        let remove_val = 0;
                        
                        if ($("#add_notes_show")){
                            if ($("#add_notes_show").prop("checked")){
                                notes_val = 1;
                                add_notes_type = $("#add_notes_type").val();
                                add_notes_months = $("#add_notes_months").val();
                                add_notes_days = $("#add_notes_days").val();
                            }
                        }
                        
//                        if ($("#add_remove_show").prop("checked")){
//                            remove_val = 1;
//                        }
                
//                        var arrayRemoveAct = new Array();
//                        var arrayRemoveWorker = new Array();
//                
//                        $(".remove_add_search").each(function() {
//                            if (($(this).attr("id")).indexOf("td_title") != -1){
//                                var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
//                                arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
//                            }
//                            if (($(this).attr("id")).indexOf("td_worker") != -1){
//                                var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
//                                arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
//                            }
//                        });
                    
                        let reqData = {';

            echo $post_data;

            echo '  
                            id: $("#id").val(),
                            client_id: $("#client").val(),
                
//                            complaints: $("#complaints").val(),
//                            objectively: $("#objectively").val(),
//                            diagnosis: $("#diagnosis").val(),
//                            therapy: $("#therapy").val(),
//                            recommended: $("#recommended").val(),
                
                            comment: $("#comment").val(),
                
//                            sel_date: $("#sel_date").val(),
//                            sel_month: $("#sel_month").val(),
//                            sel_year: $("#sel_year").val(),
//                
//                            sel_seconds: $("#sel_seconds").val(),
//                            sel_minutes: $("#sel_minutes").val(),
//                            sel_hours: $("#sel_hours").val(),
//                
                            notes: notes_val,
//                            remove: remove_val,
//                
//                            removeAct: JSON.stringify(arrayRemoveAct),
//                            removeWork: JSON.stringify(arrayRemoveWorker),
//                
                            add_notes_type: add_notes_type,
                            add_notes_months: add_notes_months,
                            add_notes_days: add_notes_days,
//                
//                            client: $("#client").val()
                        };
                        
//                        console.log(reqData);
                                        
                        $.ajax({
                            url: link,
                            global: false,
                            type: "POST",
                            //dataType: "JSON",
                            data: reqData,
                            cache: false,
                            beforeSend: function () {
                
                            },
                            success: function (res) {
                                //console.log (res);
                
                                $("#status").html(res);
                            }
                        })
                    }

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