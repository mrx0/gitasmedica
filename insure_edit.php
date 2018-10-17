<?php

//insure_edit.php
//Редактировать страховую

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
		
				$insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');
		
				if ($insure_j != 0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать Страховую компанию</h2>
							</header>
							<a href="insurcompany.php" class="b">Все страховые</a><br>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';
					echo '
								<form action="insure_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<input type="text" name="name" id="name" value="'.htmlspecialchars($insure_j[0]['name']).'">
										</div>
									</div>
						
									<div class="cellsBlock2">
										<div class="cellLeft">Договор</div>
										<div class="cellRight">
											<textarea name="contract" id="contract" cols="35" rows="5">'.$insure_j[0]['contract'].'</textarea>
										</div>
									</div>
							
									<div class="cellsBlock2">
										<div class="cellLeft">№ договора для док-ов </div>
										<div class="cellRight">
											<textarea name="contract2" id="contract2" cols="35" rows="5">'.$insure_j[0]['contract2'].'</textarea>
										</div>
									</div>
							
									<div class="cellsBlock2">
										<div class="cellLeft">Контакты</div>
										<div class="cellRight">
											<textarea name="contacts" id="contacts" cols="35" rows="5">'.$insure_j[0]['contacts'].'</textarea>
										</div>
									</div>

									<input type="button" class="b" value="Применить" onclick="Ajax_edit_insure('.$_GET['id'].')">
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