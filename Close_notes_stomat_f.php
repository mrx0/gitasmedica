<?php 

//Close_notes_stomat_f.php
//Статус закрыт напоминалке врача

	session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);

		if ($_POST){
			//Добавим данные в базу
            $time = time();

            $msql_cnnct = ConnectToDB ();

            $query = "UPDATE `notes` SET `closed` = '1' WHERE `id`='{$_POST['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            CloseDB ($msql_cnnct);

            echo json_encode(array('result' => 'success', 'data' => 'Ok'));
		}
	}
?>