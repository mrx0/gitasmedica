<?php

//filial_edit.php
//Редактировать филиал

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
		
				$filial_j = SelDataFromDB('spr_filials', $_GET['id'], 'id');
		
				if ($filial_j != 0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="filials.php" class="b">Филиалы</a>
                                </div>							
								<h2>Редактировать филиал <a href="filial.php?id='.$filial_j[0]['id'].'">'.$filial_j[0]['name'].'</a></h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';
					echo '
								<form action="labor_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<input type="text" name="name" id="name" value="'.htmlspecialchars($filial_j[0]['name']).'">
										</div>
									</div>
						
									<div class="cellsBlock2">
										<div class="cellLeft">Адрес</div>
										<div class="cellRight">
											<textarea name="address" id="address" cols="35" rows="5">'.$filial_j[0]['address'].'</textarea>
										</div>
									</div>
							
									<div class="cellsBlock2">
										<div class="cellLeft">Контакты</div>
										<div class="cellRight">
											<textarea name="contacts" id="contacts" cols="35" rows="5">'.$filial_j[0]['contacts'].'</textarea>
										</div>
									</div>

									<input type="button" class="b" value="Применить" onclick="Ajax_edit_filial('.$_GET['id'].')">
								</form>';	
					echo '
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