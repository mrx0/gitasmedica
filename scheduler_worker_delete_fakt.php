<?php 

//scheduler_worker_delete_fakt.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';

		//var_dump ($_POST);
		
		if ($_POST){

            $time = time();

            $msql_cnnct = ConnectToDB ();

            $query = "DELETE FROM `scheduler` WHERE `worker`='{$_POST['worker']}' AND
			`filial`='{$_POST['filial']}' AND `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}' AND 
			`smena`='{$_POST['smena']}' AND `kab`='{$_POST['kab']}' AND 
			`type`='{$_POST['type']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            CloseDB ($msql_cnnct);
			
			//логирование
			AddLog ('0', $_SESSION['id'], '', 'Сотрудник ['.$_POST['worker'].'] удален из Фактической смены ['.$_POST['smena'].']. Филиал ['.$_POST['filial'].']. Кабинет ['.$_POST['kab'].']. День ['.$_POST['day'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']');	
		
			echo '<span style="color: green;">Удаление прошло успешно</span>';
		}else{
			echo '<span style="color: red;">Что-то пошло не так</span>';
		}
	}
?>