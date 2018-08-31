<?php

//zub_photo.php
//

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
				$task_stomat = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');
				//var_dump($task_stomat);
				
				if ($task_stomat != 0){
					$client = SelDataFromDB('spr_clients', $task_stomat[0]['client'], 'user');
					//var_dump($client);
					if ($client !=0){
						$get_client = $client[0]['full_name'];
						
						echo '
							<div id="status">
								<header>
									<h2>Добавить фото</h2>

								</header>';
								
						echo '
								<div id="data">';
						echo '
										<div class="cellsBlock3">
											<div class="cellLeft">Пациент</div>
											<div class="cellRight">
												<a href="client.php?id='.$task_stomat[0]['client'].'" class="ahref">'.$get_client.'</a>
											</div>
										</div>
										<div class="cellsBlock3">
											<div class="cellLeft">Формула</div>
											<div class="cellRight">
												<a href="task_stomat_inspection.php?id='.$task_stomat[0]['id'].'" class="ahref">#'.$task_stomat[0]['id'].'</a>
											</div>
										</div>';
										
									
					//Фотки			

					$arr = array();
					$rez = array();
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");

					$query = "SELECT * FROM `journal_zub_img` WHERE `task`='{$_GET['id']}'";			
					
					$res = mysql_query($query) or die(mysql_error().' -> '.$query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
						$rezult = $rez;
					}else{
						$rezult = 0;
					}
					
					$price = 0;
					
					if ($rezult != 0){
						//var_dump($rezult);
						echo '
							<div style=" margin-bottom: 30px; margin-top: 30px;">
								<div style="border: 1px solid #ccc; width: 400px; margin: 0 0 2px 0; padding: 2px;">
									Снимки с внутриротовой камеры
								</div>';
						for($i = 0; $i < count($rezult); $i++){
							echo '
								<div style="display: inline-block; border: 1px solid #ccc; vertical-align: top;">
									<div style=" border: 1px solid #eee;">'.date('d.m.y H:i', $rezult[$i]['uptime']).'</div>
								';
							echo '									
									<div>';		
							if (file_exists ('zub_photo/'.$rezult[$i]['id'].'.jpg')){
								echo '
									<a href="#" onclick="window.open(\'zub_photo/'.$rezult[$i]['id'].'.jpg\',\'Image'.$rezult[$i]['id'].'\',\'width=700,height=350,status=no,resizable=yes,top=200,left=200\');">
										<img src="zub_photo/'.$rezult[$i]['id'].'.jpg" width="200">
									</a>';
							}elseif (file_exists ('zub_photo/'.$rezult[$i]['id'].'.png')){
								echo '
								<a href="#" onclick="window.open(\'zub_photo/'.$rezult[$i]['id'].'.png\',\'Image'.$rezult[$i]['id'].'\',\'width=700,height=350,status=no,resizable=yes,top=200,left=200\');">
									<img src="zub_photo/'.$rezult[$i]['id'].'.png" width="200">
								</a>';									
									
							}else{
								echo 'Ошибка изображения '.$rezult[$i]['id'];
							}
							echo '
									</div>
								</div>';
						}
						echo '
							</div>';
					}else{
						echo '<h3>Не добавлено ни одного изображения</h3>';
					}
/*										
						$zub_photo_items = SelDataFromDB('journal_zub_img', $_GET['id'], 'task');
						
						if ($zub_photo_items !=0){
							//var_dump($zub_photo_items);
							
							echo '
								<div style=" margin-bottom: 30px;">';
							for($i = 0; $i < count($zub_photo_items); $i++){
								echo '
									<div style="display: inline-block; border: 1px solid #ccc; vertical-align: top;">
										<div style=" border: 1px solid #eee;">'.date('d.m.y H:i', $zub_photo_items[$i]['uptime']).'</div>
									';
								echo '									
										<div>';		
								if (file_exists ('zub_photo/'.$zub_photo_items[$i]['id'].'.jpg')){
									echo '<img src="zub_photo/'.$zub_photo_items[$i]['id'].'.jpg" width="400" class="jLoupe" />';
								}elseif (file_exists ('zub_photo/'.$zub_photo_items[$i]['id'].'.png')){
									echo '<img src="zub_photo/'.$zub_photo_items[$i]['id'].'.png" width="400" class="jLoupe" />';								
								}else{
									echo 'Ошибка изображения '.$zub_photo_items[$i]['id'];
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
						}*/
							
						echo '
										<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
										';	
						echo '
						
										<form id="upload" method="post" action="upload_zub.php" enctype="multipart/form-data" style="margin-bottom: 10px;">
											<div id="drop">
												Переместите сюда или нажмите Поиск

												<a>Поиск</a>
												<input type="file" name="upl" multiple />
											</div>

											<ul>
												<!-- The file uploads will be shown here -->
											</ul>

										</form>

										<input type="button" class="b" value="Применить" onclick=fin_upload()>
										
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
										url:"fin_upload_zub.php",
										statbox:"status",
										method:"POST",
										data:
										{
											task:'.$_GET['id'].',
											client:'.$task_stomat[0]['client'].',
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