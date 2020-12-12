<?php

//add_proizvcalendar.php
//Добавим производственный календарь

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($cosm['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

			//clear_dir('uploads');
			
			$post_data = '';
			$js_data = '';
			

            echo '
                <div id="status">
                    <header>
						<div class="nav">
							<a href="spr_proizvcalendar.php" class="b">Производственный календарь</a>
						</div>
                        <h2>Добавить/обновить производственный календарь</h2>
                        Файл качаем отсюда и подкидываем в программу<br>
                        https://data.gov.ru/opendata/7708660670-proizvcalendar
                    </header>';

            echo '
                    <div id="data">';
            echo '
                        <input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
                        <input type=\'button\' class="b" value=\'Применить\' onclick=update_proizvcalendar()>
                        ';
            echo '
				
                        <form id="upload" method="post" action="upload2.php" enctype="multipart/form-data">
                            <div id="drop">
                                Переместите сюда или нажмите Поиск

                                <a>Поиск</a>
                                <input type="file" name="upl" multiple />
                            </div>

                            <ul>
                                <!-- The file uploads will be shown here -->
                            </ul>

                        </form>
                        <div id="errror"></div>

                        <!-- JavaScript Includes -->
                        <script src="js/jquery.knob.js"></script>

                        <!-- jQuery File Upload Dependencies -->
                        <script src="js/jquery.ui.widget.js"></script>
                        <script src="js/jquery.iframe-transport.js"></script>
                        <script src="js/jquery.fileupload.js"></script>
                        
                        <!-- Our main JS file -->
                        <script src="js/script_up_proizvcalend.js"></script>			
                    </div>
                </div>';
					
					
					
				//Фунция JS для проверки не нажаты ли чекбоксы + AJAX
				
				echo '
					<script>  
						function update_proizvcalendar() {
							var link = "update_proizvcalendar_f.php";
							var reqData = {
							    name: $("#calend").val()
							};
							//console.log(reqData);
							
							$.ajax({
                                url: link,
                                global: false,
                                type: "POST",
                                //dataType: "JSON",
                                data: reqData,
                                cache: false,
                                beforeSend: function () {
                                    $(\'#errror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
                                },
                                success: function (res) {
//                                    console.log (res);
//                                    console.log (res.length);
                                    $("#errror").html(res);
                                }
                            })

						};  
						  
					</script> 
				';	
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>