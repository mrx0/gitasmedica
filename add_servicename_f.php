<?php 

// !!!!!! Доделать !!! add_servicename_f.php
//Функция для добавления новой услуги

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['servicename'] == ''){
				echo '
					<div class="query_neok">
						Что-то не заполнено.<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';

				//$name = trim($_POST['servicename']);
				
				$name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['servicename']))));
				
				//Проверяем есть ли такая услуга
				$rezult = SelDataFromDB('spr_services', $name, 'name');
				//var_dump($rezult);
				
				/*if ($rezult == 0){
					//WriteServiceToDB_Update ($name, $_SESSION['id'], $_POST['id']);
					echo '
						<div class="query_ok">
							Услуга добавлена в базу.<br><br>
						</div>';
				}else{
					echo '
						<div class="query_neok">
							Такая услуга уже есть.<br><br>
						</div>';
				}*/

			}
		}
	}
?>