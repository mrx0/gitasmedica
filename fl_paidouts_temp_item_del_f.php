<?php 

//fl_paidouts_temp_item_del_f.php
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

            if (!isset($_POST['id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                //Проверки, проверочки
                include_once 'DBWork.php';

                //!!! @@@
                include_once 'ffun.php';

                $msql_cnnct = ConnectToDB2 ();

                //Удаляем из БД
                $query = "DELETE FROM `fl_journal_paidouts_temp` WHERE `id`='{$_POST['id']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //Обновим общий
                //calculateDebt($_POST['client_id']);
                //calculateBalance ($_POST['client_id']);

                echo json_encode(array('result' => 'success', 'data' => 'Документ удален'));

            }
		}
	}
	
?>