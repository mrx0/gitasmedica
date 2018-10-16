<?php 

//invoice_close_f.php
//Закрыть наряд, поставить статус 5

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['invoice_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $msql_cnnct = ConnectToDB ();

                $query = "UPDATE `journal_invoice` SET `status` = '0', `closed_time`='0' WHERE `id`='{$_POST['invoice_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                CloseDB ($msql_cnnct);

				echo json_encode(array('result' => 'success', 'data' => 'Ok'));
			}
		}
	}

?>