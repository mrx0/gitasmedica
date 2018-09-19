<?php 

//Change_notes_stomat_f.php
//Обновить изменить напоминалку

	session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){

			$date = date_create(date('Y-m-d', time()));
			$dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['change_notes_months'].' months'));
			$dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['change_notes_days'].' days'))) + 60*60*8;
			
			//Добавим данные в базу
            $time = time();

            $msql_cnnct = ConnectToDB ();

            $query = "UPDATE `notes` SET `dead_line` = '{$dead_line}', `description` = '{$_POST['change_notes_type']}' WHERE `id`='{$_POST['id']}'";
			//echo $query.'<br />';

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            CloseDB ($msql_cnnct);

            echo json_encode(array('result' => 'success', 'data' => 'Ok'));
		}
	}
?>