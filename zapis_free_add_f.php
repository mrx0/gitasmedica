<?php 

//zapis_free_add_f.php
//Функция для Добавления записи пациенту "с улицы"

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){

			$_time = time();
			$start_day = mktime(0, 0, 0, date("m", $_time), date("d", $_time), date("y", $_time));
			$time_post = strtotime($_POST['day'].'.'.$_POST['month'].'.'.$_POST['year']);

            $worker = $_POST['worker'];
            $client = 1;
            $_POST['contacts'] = '000';

            $pervich = $_POST['pervich'];

            //Страховые
            if ($_POST['insured'] != 0){
                $insured = 1;
            }else{
                $insured = 0;
            }
            //Ночные
            if ($_POST['noch'] != 0){
                $noch = 1;
            }else{
                $noch = 0;
            }

            //запись в базу
            $zapis_id = WriteToDB_EditZapis ('zapis', $_POST['year'], $_POST['month'], $_POST['day'], $_POST['office'], $_POST['office'], $_POST['kab'], $worker, $_SESSION['id'], $client, $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt'], $_POST['type'], $pervich, $insured, $noch, 6);

            echo json_encode(array('result' => 'success', 'data' => $zapis_id));

		}
	}
?>