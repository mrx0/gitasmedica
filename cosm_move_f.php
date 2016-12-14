<?php 

//cosm_move_f.php
//Функция для Переноса посещений пациента к другому

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			echo '
				<div class="query_ok">
					<h3>Пока не работает</h3>
				</div>';	
		}
	}
	
?>