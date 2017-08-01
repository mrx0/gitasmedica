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

            if (!isset($_POST['specializations'])){
                $_POST['specializations'] = array();
            }

			WriteWorkerToDB_Update ($_SESSION['id'], $_POST['worker_id'], $_POST['org'], $_POST['permissions'], $_POST['specializations'], $_POST['contacts'], $_POST['fired']);

			echo '
				<h1>Карточка отредактирована.</h1>
				<br>
				<a href="user.php?id='.$_POST['worker_id'].'" class="b">Вернуться в профиль</a>
			';			
		}

	}
	
?>