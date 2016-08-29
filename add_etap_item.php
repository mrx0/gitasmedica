<?php

//
//Это пиздец, нах я на это подписался-то

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			/*$offices = SelDataFromDB('spr_office', '', '');*/
			
			//clear_dir('uploads');
			
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
								<input type=\'button\' class="b" value=\'Отправить изображения\' onclick=fin_upload()>
								';	
				echo '
				
								<form id="upload" method="post" action="upload.php" enctype="multipart/form-data">
									<div id="drop">
										Переместите сюда или нажмите Поиск

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
						var idd = "";
						function fin_upload() {
							var face = "";
							var graf= "";
							var imgs = $(".img_z");

							
							
							$.each(imgs, function(){
								//alert ($(this).attr("value"));
								if ($(this).attr("id") == "face")
									face = $(this).attr("value");
								if ($(this).attr("id") == "graf")
									if (face != $(this).attr("value"))
										graf = $(this).attr("value");
							});
							
							//alert(face);
							//alert(graf);
							
							ajax({
								url:"fin_upload.php",
								statbox:"status",
								method:"POST",
								data:
								{
									face:face,
									graf:graf,
									client:'.$_GET['client'].',';
				echo '
								},
								success:function(data){
									document.getElementById("status").innerHTML=data;
								}
							})
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