<?php

//specialization_add.php
//Добавить специализацию

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		include_once 'DBWork.php';
		
		echo '
			<div id="status">
				<header>
                    <div class="nav">
						<a href="specializations.php" class="b">Специализации</a>
					</div>
					<h2>Добавить специализацию</h2>
					Заполните поля
				</header>';

		echo '
				<div id="data">';
		echo '				
					<div id="errror"></div>';
		echo '
					<form action="specialization_add_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Название</div>
							<div class="cellRight">
								<input type="text" name="name" id="name" value="">
							</div>
						</div>

						<div class="cellsBlock2">
							<div class="cellLeft">Для сотрудников</div>
							<div class="cellRight">';

		//Типы сотрудников
        $arr_permissions = SelDataFromDB('spr_permissions', '', '');

        echo '
                                <select name="permissions" id="permissions">
                                    <option value="0">Выберите специализацию</option>';

        for ($i=0;$i<count($arr_permissions);$i++){
                echo "<option value='".$arr_permissions[$i]['id']."'>".$arr_permissions[$i]['name']."</option>";
        }
        echo '
                                </select>';

        echo '
							</div>
						</div>

						<input type="button" class="b" value="Добавить" onclick="Ajax_specialization_add(\'add\')">
					</form>';	
			
		echo '
				</div>
			</div>';
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>