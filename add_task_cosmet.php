<?php

//add_task_cosmet.php 
//Добавить задачу косметологов

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			$offices = SelDataFromDB('spr_office', '', '');
			
			$post_data = '';
			$js_data = '';
			
			//Если у нас по GET передали клиента
			$get_client = '';
			if (isset($_GET['client']) && ($_GET['client'] != '')){
				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
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
						<h2>Добавить</h2>
						Заполните поля
					</header>';

			echo '
			
		<style>
.label_desc{
    display: block;
}
.error{
    display: none;
}
.error_input{
    border: 2px solid #FF0000; 
}
</style>	
			
			
					<div id="data">';

			echo '
						<form action="add_task_cosmet_f.php">
					
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
					echo '
						<div class="cellsBlock3" style="font-size:80%;">
							<div class="cellLeft">'.$actions_cosmet[$i]['full_name'].'</div>
							<div class="cellRight">
								<input type="checkbox" name="action'.$actions_cosmet[$i]['id'].'" id="action'.$actions_cosmet[$i]['id'].'" value="1">
							</div>
						</div>';
				}
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
							<div id="errror"></div>
							<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_task_cosmet()>
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
								filial:document.getElementById("filial").value,
							},
							// тип передачи данных
							dataType: "json",
							// действие, при ответе с сервера
							success: function(data){
								// в случае, когда пришло success. Отработало без ошибок
								if(data.result == \'success\'){   
									//alert(\'форма корректно заполнена\');
										'.$js_data.'
												ajax({
													url:"add_task_cosmet_f.php",
													statbox:"status",
													method:"POST",
													data:
													{
														author:document.getElementById("author").value,
														client:document.getElementById("search_client").value,
														filial:document.getElementById("filial").value,
														comment:document.getElementById("comment").value,';
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