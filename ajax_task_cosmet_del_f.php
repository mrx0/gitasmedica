<?php 

//ajax_task_cosmet_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){

		    $data = '';

            $msql_cnnct = ConnectToDB ();

			$time = time();

            $query = "UPDATE `journal_cosmet1` SET `status`='9', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}'  WHERE `id`='{$_POST['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $data = '<div class="query_ok" style="padding-bottom: 10px;"><h3>Удалено (заблокировано).</h3></div>';

            echo json_encode(array('result' => 'success', 'data' => $data));

		}

	}
	
?>