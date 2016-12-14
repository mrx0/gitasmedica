<?php 

//client_del_f.php
//Функция для Удаление(блокирование) карточки пациента

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WriteClientToDB_Delete ($_POST['session_id'], $_POST['id']);

			echo '
				<div class="query_ok">
					<h3>Карточка пациента удалена (заблокирована).</h3>
				</div>';	
		}

	}
	
?>