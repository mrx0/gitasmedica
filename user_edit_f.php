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

			WriteWorkerToDB_Update ($_SESSION['id'], $_POST['worker_id'], $_POST['sel_date'], $_POST['sel_month'], $_POST['sel_year'], $_POST['org'], $_POST['permissions'], $_POST['specializations'], $_POST['category'], $_POST['filial'], $_POST['contacts'], $_POST['status'], $_POST['spec_oklad'], $_POST['spec_oklad_work'], $_POST['spec_prikaz8'], $_POST['spec_work_6days']);

			echo '
				<h1>Карточка отредактирована.</h1>
				<br>
				<a href="user.php?id='.$_POST['worker_id'].'" class="b">Вернуться в профиль</a>
			';			
		}

	}
	
?>