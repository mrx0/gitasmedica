<?php 

//change_payment_filial.php
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

			//$time = time();
			
			//Генератор пароля
			$password = PassGen();
				
			$query = "UPDATE `journal_payment` SET `filial_id`='{$_POST['filial_id']}' WHERE `id`='{$_POST['payment_id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            echo json_encode(array('result' => 'success', 'data' => 'Ok'));
		}
	}
?>