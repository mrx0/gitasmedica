<?php

//client_edit_fio.php
//Редактирование карточки пациента ФИО

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
				//var_dump($_SESSION);
				if ($client !=0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать карточку пациента</h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';
					echo '
								<form action="client_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">
											Фамилия
										</div>
										<div class="cellRight">
											<input type="text" name="f" id="f" value="'.$client[0]['f'].'">
											<label id="fname_error" class="error"></label>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Имя
										</div>
										<div class="cellRight">
											<input type="text" name="i" id="i" value="'.$client[0]['i'].'">
											<label id="iname_error" class="error"></label>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Отчество
										</div>
										<div class="cellRight">
											<input type="text" name="o" id="o" value="'.$client[0]['o'].'">
											<label id="oname_error" class="error"></label>
										</div>
									</div>';
					echo '					
									<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
									<div id="errror"></div>
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_fio_client('.$_SESSION['id'].')">
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