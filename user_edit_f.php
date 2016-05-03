<?php 

//user_edit_f.php
//Функция для редактирования карточки пользователя

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			WriteWorkerToDB_Update ($_POST['session_id'], $_POST['id'], $_POST['org'], $_POST['permissions'], $_POST['contacts'], $_POST['fired']);
			
			echo '
				<h1>Карточка отредактирована.</h1>
				<br /><a href="contacts.php" class="b">В журнал</a>
			';			
		}

	}
	
?>