<?php 

//search_user_f.php
//Функция поиска пользователя по ФИО

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

            $rezult = SelDataFromDB('spr_workers', $_POST['workerFIO'], 'full_name');

            if ($rezult != 0) {
                echo json_encode(array('result' => 'success', 'data' => $rezult[0]));
            }/*else {
                echo json_encode(array('result' => 'error', 'data' => 'Ошибка #12'));
            }*/

		}

	}
	
?>