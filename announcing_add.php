<?php

//announcing_add.php
//Добавить объявление

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$offices = SelDataFromDB('spr_filials', '', '');
            //Получили список прав
            $permissions = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions);
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';

			echo '
						<form action="announcing_add_f.php">';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Тип</div>
							<div class="cellRight">
								<select name="announcing_type" id="announcing_type">
								    <option value="1" selected>Объявление</option>
								    <option value="2">Обновление</option>
								    <option value="3">Инструкция</option>
								</select>
							</div>
						</div>';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">
							    Тема сообщения<br>
							    <span style="font-size: 70%;">Не обязательно. Максимум 20 знаков</span>
							</div>
							<div class="cellRight">
								<input type="text" size="30" name="theme" id="theme" value="" placeholder="" style="padding: 5px;">
							</div>
						</div>';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Текст сообщения</div>
							<div class="cellRight">
								<textarea name="comment" id="comment" cols="60" rows="10"></textarea>
							</div>
						</div>';

            echo '
							
							<div class="cellsBlock3">
								<div class="cellLeft">
								    Для кого из сотрудников<br><span style="font-size:80%;  color: #555;">(по умолчанию видно всем)</span>
								<!--<span style="font-size: 70%;">Если не выбрано, то для всех</span>-->
								</div>
								<div class="cellRight">
								    <span style="font-size:80%;  color: #555;">Кому не видно</span>
									<select multiple="multiple" name="workers_type[]" id="workers_type">';
			if ($permissions != 0){
			    for ($i=0; $i<count($permissions); $i++){
											echo "<option value='".$permissions[$i]['id']."' selected>".$permissions[$i]['name']."</option>";
				}
			}
			echo '
									</select>
									<label id="workers_type" class="workers_type">
								</div>
							</div>';



            echo '		
							<div class="cellsBlock3">
								<div class="cellLeft">
								    Для какого филиала<br><span style="font-size:80%;  color: #555;">(по умолчанию видно всем)</span>
								    <!--<span style="font-size: 70%;">Если не выбрано, то для всех</span>-->
								</div>
								<div class="cellRight">
								    <span style="font-size:80%;  color: #555;">Кому не видно</span>
									<select multiple="multiple" name="filial[]" id="filial">';
            if ($offices != 0){
                for ($i=0;$i<count($offices);$i++){
                    echo "<option value='".$offices[$i]['id']."' selected>".$offices[$i]['name']."</option>";
                }
            }
            echo '
									</select>
									<label id="filial_error" class="error">
								</div>
							</div>';

			echo '
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick=Ajax_add_announcing(\'add\')>
						</form>';	
				
			echo '
					</div>
				</div>';

			
			echo '
				<script>  
				    $("#filial").multiSelect()
				    $("#workers_type").multiSelect()
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>