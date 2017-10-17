<?php 

//change_pass_f.php
//

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'functions.php';
		//var_dump($_SESSION);
		
		if ($_POST) {

            $msql_cnnct = ConnectToDB ();

			$time = time();
			
			//Генератор пароля
			$password = PassGen();
				
			$query = "UPDATE `spr_workers` SET `password`='{$password}' WHERE `id`='{$_POST['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			echo 'Новый пароль: '.$password;
		}
	}
?>