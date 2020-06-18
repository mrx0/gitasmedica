<?php 

//sclad_prihod_cancel_f.php
//Очищаем сессию

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                unset($_SESSION['sclad']);

				echo json_encode(array('result' => 'success'));
			}
		}
	}
?>