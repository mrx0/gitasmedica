<?php 

//fl_delete_in_bank_f.php
//Функция для Удаление

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
                //Ищем
                //$order_j = SelDataFromDB('journal_giveoutcash', $_POST['id'], 'id');

                //if ($order_j != 0) {

                    $msql_cnnct = ConnectToDB();

                    //$time = date('Y-m-d H:i:s', time());

                    $query = "DELETE FROM `fl_journal_in_bank` WHERE `id`='{$_POST['id']}';";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    CloseDB ($msql_cnnct);

                    echo json_encode(array('result' => 'success', 'data' => 'Документ удалён'));

//                }else{
//                    //echo json_encode(array('result' => 'success', 'data' => 'Чёт ошибка какая-то'));
//                }
            }
		}
	}
	
?>