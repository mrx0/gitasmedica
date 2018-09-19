<?php 

//fl_updateTabelBalance_f.php
//Наитупейшее решение.
//Отдельный файл, который тупо передает tabel_id в updateTabelBalance()

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
            if (!isset($_POST['tabel_id'])){
                echo json_encode(array('result' => 'error', 'data' => 'Ошибка #14'));
            }else{
                //!!! @@@
                include_once 'ffun.php';

                //Обновим баланс табеля
                updateTabelBalance($_POST['tabel_id']);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }
		}
	}

?>