<?php 

//fl_tabel_del_f.php
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
                //Ищем
                $tabel_j = SelDataFromDB('fl_journal_tabels', $_POST['id'], 'id');

                if ($tabel_j != 0) {

                    $msql_cnnct = ConnectToDB();

                    //Удаляем из БД связки РЛ и табелей
                    $query = "DELETE FROM `fl_journal_tabels_ex` WHERE `tabel_id`='{$_POST['id']}';";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $time = date('Y-m-d H:i:s', time());

                    $query = "UPDATE `fl_journal_tabels` SET `summ`='0', `status`='9', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['id']}';";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    //Обновим общий
                    //calculateDebt($_POST['client_id']);
                    //calculateBalance ($_POST['client_id']);

                    CloseDB ($msql_cnnct);

                    echo json_encode(array('result' => 'success', 'data' => 'Табель удален'));

                }else{
                    //echo json_encode(array('result' => 'success', 'data' => 'Чёт ошибка какая-то'));
                }
            }
		}
	}
	
?>