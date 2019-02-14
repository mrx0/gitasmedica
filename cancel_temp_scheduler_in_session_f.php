<?php 

//cancel_temp_scheduler_in_session_f.php
//Чистим переменную временного графика scheduler3.php

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		//if ($_POST){

            unset($_SESSION['scheduler3']);

            echo json_encode(array('result' => 'success', 'data' => 'OK'));

		//}
	}
?>