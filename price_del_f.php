<?php 

//price_del_f.php
//Функция для Удаление(блокирование) 

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		//include_once 'DBWork.php';

        include_once('DBWorkPDO.php');

		include_once 'functions.php';

		if ($_POST){

            if (!isset($_POST['id']) || !isset($_POST['insure'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $db = new DB();

                $args = [
                    'id' => $_POST['id']
                ];

                //Удаляем из БД
                if ($_POST['insure'] == 'true'){
                    $query = "DELETE FROM `spr_priceprices_insure` WHERE `id`= :id";
                }else {
                    $query = "DELETE FROM `spr_priceprices` WHERE `id`= :id";
                }

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
		}
	}
	
?>