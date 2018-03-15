<?php 

//ajax_tempzapis_edit_OK_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
            $time = time();

            $msql_cnnct = ConnectToDB ();

            $query = "UPDATE `zapis` SET `add_from`='{$_POST['office']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}'  WHERE `id`='{$_POST['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            CloseDB ($msql_cnnct);
			
			
		}
	}
?>