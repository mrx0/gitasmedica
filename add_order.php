<?php

//add_order.php
//Приходный ордер


// !!! доделать

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$offices = SelDataFromDB('spr_office', '', '');

			//Если у нас по GET передали клиента
			$get_client = '';
			if (isset($_GET['client_id']) && ($_GET['client_id'] != '')){
				$client = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
				if ($client !=0){
					$get_client = $client[0]['full_name'];
				}
				
			}
			
			//Автоматизация выбора филиала
			if (isset($_SESSION['filial']) && !empty($_SESSION['filial'])){
				$selected_fil = $_SESSION['filial'];
			}else{
				$selected_fil = 0;
			}

			echo '
				<div id="status">
					<header>
						<h2>Новый приходный ордер</h2>
					</header>';

			echo '
					<div id="data">';

			echo '		
							<div class="cellsBlock3">
								<div class="cellLeft">
									Время посещения
									<span style="font-size:70%;">
										Согласно записи
									</span>
								</div>
								<div class="cellRight">
									'.$zapis_date.'
								</div>
							</div>';
							
			if (($cosm['add_new'] == 1) || $god_mode){
				if (isset($_GET['worker'])){
					$workerEcho = WriteSearchUser('spr_workers', $_GET['worker'], 'user_full', false);
				}else{
					$workerEcho = '';
				}
				echo '
							<div style="margin-bottom: 10px; color: #777; font-size: 90%;">Необходимо выбрать исполнителя</div>
							<div class="cellsBlock3" style="margin-bottom: 20px;">
								<div class="cellLeft">Исполнитель</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata2" id="search_worker" placeholder="Введите первые три буквы для поиска" value="'.$workerEcho.'" class="who2"  autocomplete="off">
									<ul id="search_result2" class="search_result2"></ul><br />
									<label id="worker_error" class="error"></label>
								</div>
							</div>';	
			}
							
			echo '		
							<div class="cellsBlock3">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">
									<select name="filial" id="filial">
										<option value="0" selected>Выберите филиал</option>';
									if ($offices != 0){
										for ($i=0;$i<count($offices);$i++){
											echo "<option value='".$offices[$i]['id']."' ", $selected_fil == $offices[$i]['id'] ? "selected" : "" ,">".$offices[$i]['name']."</option>";
										}
									}
									echo '
									</select>
									<label id="filial_error" class="error">
								</div>
							</div>

							<div class="cellsBlock3">
								<div class="cellLeft">Пациент</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata" id="search_client" placeholder="Введите первые три буквы для поиска" value="'.$get_client.'" class="who"  autocomplete="off">
									<ul id="search_result" class="search_result"></ul><br />
									<label id="client_error" class="error">
								</div>
							</div>


	<!--<script type="text/javascript">
		function showMe (box){
			var vis = (box.checked) ? "block" : "none";
			document.getElementById(\'div1\').style.display = vis;
		}
	</script>-->';
					
			$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
			//var_dump ($actions_cosmet);
			if ($actions_cosmet != 0){
				

			//отсортируем по nomer

			foreach($actions_cosmet as $key=>$arr_temp){
				$data_nomer[$key] = $arr_temp['nomer'];
			}
			//var_dump ($data_nomer);
			
			array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
			//var_dump ($actions_cosmet);

				for ($i = 0; $i < count($actions_cosmet)-2; $i++){
					/*$js_data .= '
						if ($("#action'.$actions_cosmet[$i]['id'].'").prop("checked")){
							action_value'.$actions_cosmet[$i]['id'].' = 1;
						}else{
							action_value'.$actions_cosmet[$i]['id'].' = 0;
						}
					
					';*/
					$js_data .= '
						var action_value'.$actions_cosmet[$i]['id'].' = $("input[name=action'.$actions_cosmet[$i]['id'].']:checked").val();
					';
					$post_data .= '
									action'.$actions_cosmet[$i]['id'].':action_value'.$actions_cosmet[$i]['id'].',';
									
					//отметка для первички (костыль от старого определения)
					if ($actions_cosmet[$i]['id'] == 13){
						if (isset($_GET['pervich']) && ($_GET['pervich'] == 1)){
							$pervich_cons_check = ' checked';
							
						}else{
							$pervich_cons_check = '';
						}
						$pervich_cons_check .= ' onclick="CheckPervich()"';
					}else{
						$pervich_cons_check = '';
					}

					echo '
						<div class="cellsBlock3" style="font-size:80%;">
							<div class="cellLeft">'.$actions_cosmet[$i]['full_name'].'</div>
							<div class="cellRight">
								<input type="checkbox" name="action'.$actions_cosmet[$i]['id'].'" id="action'.$actions_cosmet[$i]['id'].'" value="1" '.$pervich_cons_check.'>
							</div>
						</div>';
				}
				
				//Новая отметка для первички
				echo '
						<div class="cellsBlock3">
							<div class="cellLeft">
								Первичный<br>
								<span style="font-size: 70%;">Определяется из записи пациента</span>
							</div>
							<div class="cellRight">
								<input type="checkbox" name="pervich" id="pervich" value="1" '.$pervich_check.'> да
							</div>
						</div>';
				
				echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Комментарий</div>
							<div class="cellRight">
								<textarea name="comment" id="comment" cols="35" rows="10"></textarea>
							</div>
						</div>';
			}
			echo '
							<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
							<input type="hidden" id="zapis_date" name="zapis_date" value="'.$zapis_date_hidden.'">
							<input type="hidden" id="zapis_id" name="zapis_id" value="'.$zapis_id.'">
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick=Ajax_add_task_cosmet()>
						</form>';	
				
			echo '
					</div>
				</div>';
				
			//Фунция JS для проверки не нажаты ли чекбоксы + AJAX
			
			echo '
				<script>  

					function Ajax_add_task_cosmet() {
						// убираем класс ошибок с инпутов
						$(\'input\').each(function(){
							$(this).removeClass(\'error_input\');
						});
						// прячем текст ошибок
						$(\'.error\').hide();
						 
						// получение данных из полей
					   // var client = $(\'#search_client\').val();
						//var filial = $(\'#filial\').val();
						 
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "ajax_test.php",
							// какие данные будут переданы
							data: {
								client:document.getElementById("search_client").value,
								filial:document.getElementById("filial").value,';
			if (($cosm['add_new'] == 1) || $god_mode){
				echo '
								worker:document.getElementById("search_worker").value,';
			}
			echo '
								
							},
							// тип передачи данных
							dataType: "json",
							// действие, при ответе с сервера
							success: function(data){
								// в случае, когда пришло success. Отработало без ошибок
								if(data.result == \'success\'){   
									//alert(\'форма корректно заполнена\');
										'.$js_data.'
										
										if ($("#pervich").prop("checked")){
											pervich = 1;
										}else{
											pervich = 0;
										}
											
												ajax({
													url:"add_task_cosmet_f.php",
													statbox:"status",
													method:"POST",
													data:
													{
														author:document.getElementById("author").value,
														client:document.getElementById("search_client").value,
														filial:document.getElementById("filial").value,
														comment:document.getElementById("comment").value,
														
														zapis_date:document.getElementById("zapis_date").value,
														zapis_id:document.getElementById("zapis_id").value,
								
														pervich:pervich,';
														
							if (($cosm['add_new'] == 1) || $god_mode){
								echo '
														worker:document.getElementById("search_worker").value,';
							}
														
							echo $post_data;
							echo '
													},
													success:function(data){
														document.getElementById("status").innerHTML=data;
													}
												})
								// в случае ошибок в форме
								}else{
									// перебираем массив с ошибками
									for(var errorField in data.text_error){
										// выводим текст ошибок 
										$(\'#\'+errorField+\'_error\').html(data.text_error[errorField]);
										// показываем текст ошибок
										$(\'#\'+errorField+\'_error\').show();
										// обводим инпуты красным цветом
									   // $(\'#\'+errorField).addClass(\'error_input\');                      
									}
									document.getElementById("errror").innerHTML=\'<span style="color: red">Ошибка, что-то заполнено не так.</span>\'
								}
							}
						});						
					};  
					  
					function CheckPervich(){
						//alert(document.getElementById("pervich").checked);

						if(document.getElementById("action13").checked == true){
							document.getElementById("pervich").checked = true;
						}else{
							document.getElementById("pervich").checked = false;
						}
					}
					  
					  
					  
					  
				</script> 
			';	

			
			
			/*echo '
				<script type="text/javascript">
					$("input").change(function() {
						var $input = $(this);';
			echo $js_data;
			echo '
					});
				</script>
			';*/
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>