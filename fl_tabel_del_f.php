<?php 

//fl_tabel_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
        include_once('DBWorkPDO.php');
    	include_once 'functions.php';

		if ($_POST){

            $db = new DB();

            if (!isset($_POST['id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                //Ищем
                //$tabel_j = SelDataFromDB('fl_journal_tabels', $_POST['id'], 'id');

                $args = [
                    'tabel_id' => $_POST['id']
                ];
                $query = "SELECT `id` FROM `fl_journal_tabels` WHERE `id`=:tabel_id LIMIT 1;";

                $tabel_id = $db::getValue($query, $args);

                if ($tabel_id > 0) {

                    //$msql_cnnct = ConnectToDB();



                    //Выплаты
                    $query = "SELECT COUNT(`id`) AS total FROM `fl_journal_paidouts` WHERE `tabel_id`=:tabel_id;";

                    $total = $db::getValue($query, $args);

                    if ($total > 0){
                        echo json_encode(array('result' => 'error', 'data' => 'Табель нельзя удалить. Есть выплаты'));
                    }else {


                        //Удаляем из БД связки РЛ и табелей
                        $query = "DELETE FROM `fl_journal_tabels_ex` WHERE `tabel_id`=:tabel_id;";
                        //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        $db::sql($query, $args);

                        $time = date('Y-m-d H:i:s', time());

                        $query = "UPDATE `fl_journal_tabels` SET `summ`='0', `status`='9', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`=:tabel_id;";
                        //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        $db::sql($query, $args);

                        //Обновим общий
                        //calculateDebt($_POST['client_id']);
                        //calculateBalance ($_POST['client_id']);

                        //CloseDB ($msql_cnnct);

                        echo json_encode(array('result' => 'success', 'data' => 'Табель удален'));

                        //логирование
                        AddLog(GetRealIp(), $_SESSION['id'], '', 'Удалён табель: #' . $_POST['id']);

                    }
                }else{
                    //echo json_encode(array('result' => 'success', 'data' => 'Чёт ошибка какая-то'));
                }
            }
		}
	}
	
?>