<?php

//serviceitem_edit.php
//Редактирование карточки товара

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$items_j = SelDataFromDB('spr_services', $_GET['id'], 'id');
				//var_dump($items_j);
				
				if ($items_j !=0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать</h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';
					echo '
								<form action="serviceitem_edit_f.php">
					
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<textarea name="servicename" id="servicename" style="width:90%; overflow:auto; height: 50px;">'.$items_j[0]['name'].'</textarea>
											<label id="servicename_error" class="error"></label>
										</div>
									</div>
									
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_service('.$_SESSION['id'].')">
								</form>
							</div>
						</div>';
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