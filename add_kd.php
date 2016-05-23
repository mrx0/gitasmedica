<?php

//add_task_cosmet.php 
//Добавить задачу косметологов

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			/*$offices = SelDataFromDB('spr_office', '', '');*/
			
			clear_dir('uploads');
			
			$post_data = '';
			$js_data = '';
			
			//Если у нас по GET передали клиента
			$get_client = '';
			if (isset($_GET['client']) && ($_GET['client'] != '')){
				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
				if ($client !=0){
					$get_client = $client[0]['full_name'];
				}
				
				echo '
					<div id="status">
						<header>
							<h2>Добавить</h2>

						</header>';

				echo '
						<div id="data">';
				echo '
								<div class="cellsBlock3">
									<div class="cellLeft">Пациент</div>
									<div class="cellRight">
										'.$get_client.'
									</div>
								</div>';
				echo '
								<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
								<input type=\'button\' class="b" value=\'Отправить изображения\' onclick=Ajax_add_task_cosmet()>
								';	
				echo '
				
								<form id="upload" method="post" action="upload.php" enctype="multipart/form-data">
									<div id="drop">
										Переместите сюда

										<a>Поиск</a>
										<input type="file" name="upl" multiple />
									</div>

									<ul>
										<!-- The file uploads will be shown here -->
									</ul>

								</form>

								<!-- JavaScript Includes -->
								<script src="js/jquery.knob.js"></script>

								<!-- jQuery File Upload Dependencies -->
								<script src="js/jquery.ui.widget.js"></script>
								<script src="js/jquery.iframe-transport.js"></script>
								<script src="js/jquery.fileupload.js"></script>
								
								<!-- Our main JS file -->
								<script src="js/script_up.js"></script>
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
			}else{
				echo '<h1>Не выбран пациент.</h1><a href="index.php">На главную</a>';
			}

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>