<?php

//zub.php
//Не используем нигде, удаляю 2018-08-31

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($stom['add_own'] == 1) || ($stom['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$post_data = '';
			$js_data = '';
			
			//Если у нас по GET передали ID
			if (isset($_GET['id']) && ($_GET['id'] != '')){
				$etap = SelDataFromDB('journal_etaps', $_GET['id'], 'id');
				//var_dump($etap);
				if ($etap != 0){
					$client = SelDataFromDB('spr_clients', $etap[0]['client_id'], 'user');
					//var_dump($client);
					if ($client !=0){
						$get_client = $client[0]['full_name'];
						
						echo '
							<div id="status">
								<header>
									<h2>Исследование</h2>

								</header>';
								
						echo '
								<div id="data">';
						echo '
										<div class="cellsBlock3">
											<div class="cellLeft">Пациент</div>
											<div class="cellRight">
												<a href="client.php?id='.$etap[0]['client_id'].'" class="ahref">'.$get_client.'</a>
											</div>
										</div>';
										
						$etap_items = SelDataFromDB('journal_etaps_img', $_GET['id'], 'etap');
						
						if ($etap_items !=0){
							//var_dump($etap_items);
							echo '
								<div style=" margin-bottom: 30px;">';
							for($i = 0; $i < count($etap_items); $i++){
								echo '
									<div style="display: inline-block; border: 1px solid #ccc; vertical-align: top;">
										<div style=" border: 1px solid #eee;">'.date('d.m.y H:i', $etap_items[$i]['uptime']).'</div>
									';
								echo '									
										<div>';		
								if (file_exists ('etaps/'.$etap_items[$i]['id'].'.jpg')){
									echo '<img src="etaps/'.$etap_items[$i]['id'].'.jpg" width="400" class="jLoupe" />';
								}elseif (file_exists ('etaps/'.$etap_items[$i]['id'].'.png')){
									echo '<img src="etaps/'.$etap_items[$i]['id'].'.png" width="400" class="jLoupe" />';								
								}else{
									echo 'Ошибка изображения '.$etap_items[$i]['id'];
								}
								echo '
										</div>
									</div>';
							}
							echo '
								</div>';
						}else{
							echo '
								<h3>Не добавлено ни одного изображения</h3>
							';
						}
							
						echo '
										<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
										<input type=\'button\' class="b" value=\'Добавить изображения\' onclick=fin_upload()>
										';	
						echo '
						
										<form id="upload" method="post" action="upload_etap.php" enctype="multipart/form-data">
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
										<script src="js/script_up.js"></script>';
							
						echo '
								</div>';
								
								
						//Фунция JS для проверки не нажаты ли чекбоксы + AJAX
						
						echo '
							<script>  
								var idd = "";
								function fin_upload() {
									var img_arr = [];
									var imgs = $(".img_z");

									
									
									$.each(imgs, function(){
										//alert($(this).attr("value"));
										img_arr[img_arr.length] = $(this).attr("value");
									});
									
									//alert(img_arr);
									//alert(JSON.stringify(img_arr));
									
									ajax({
										url:"fin_upload_etap.php",
										statbox:"status",
										method:"POST",
										data:
										{
											etap:'.$_GET['id'].',
											imgs: img_arr';
						echo '
										},
										success:function(data){
											document.getElementById("status").innerHTML=data;
										}
									})
								};  
								  
							</script> 
							
							
							<script type="text/javascript" src="js/jquery.jloupe.js"></script>
							<script type="text/javascript">
								$(function(){ 
									// Image 1 and 2 use built-in jLoupe selector

									// Image 3
									$(\'#custom\').jloupe({
										radiusLT: 100,
										margin: 12,
										borderColor: false,
										image: \'img\loupe-trans.png\'
									});

									// Image 4
									$(\'#shape\').jloupe({
										radiusLT: 0,
										radiusRT: 10,
										radiusRB: 0,
										radiusLB: 10,
										width: 300,
										height: 150,
										borderColor: \'#f2730b\',
										backgroundColor: \'#000\',
										fade: false
									});
								});
							</script>
						';
								
					}else{
						echo '<h1>Ошибка.</h1><a href="index.php">На главную</a>';
					}
				}else{
					echo '<h1>Нет такого исследования</h1><a href="index.php">На главную</a>';
				}
	
			}else{
				echo '<h1>Ошибка.</h1><a href="index.php">На главную</a>';
			}

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>