<?php 

//fl_give_out_cash_edit_f.php
//

	session_start();
	
	$god_mode = FALSE;
	//var_dump ($_POST);
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else {
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            $temp_arr = array();

            if (!isset($_POST['summ']) || !isset($_POST['type']) || !isset($_POST['office_id']) || !isset($_POST['date_in']) || !isset($_POST['comment']) || !isset($_POST['additional_info']) || !isset($_POST['giveoutcash_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());
                $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'] . " 09:00:00"));

                //Если заднее число записи
                if (
                    ((date("Y", strtotime($_POST['date_in'] . " 09:00:00")) < date("Y")) ||
                        ((date("Y", strtotime($_POST['date_in'] . " 09:00:00")) == date("Y")) && (date("m", strtotime($_POST['date_in'] . " 09:00:00")) < date("m"))) ||
                        ((date("m", strtotime($_POST['date_in'] . " 09:00:00")) == date("m")) && (date("d", strtotime($_POST['date_in'] . " 09:00:00")) < date("d")))) &&
                    !(($finances['see_all'] == 1) || $god_mode)
                ) {

                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Нельзя редактировать ордеры задним числом</div>'));
                } else {


                    $comment = addslashes($_POST['comment']);
                    if ($_POST['type'] == 0) {
                        $additional_info = addslashes($_POST['additional_info']);
                    } else {
                        $additional_info = '';
                    }

                    //Обновляем
                    $query = "UPDATE `journal_giveoutcash`
                    SET `office_id`='{$_POST['office_id']}', `summ`='{$_POST['summ']}', `type`='{$_POST['type']}', `date_in`='{$date_in}', `comment`='{$comment}', `additional_info`='{$additional_info}', `last_edit_person`='{$_SESSION['id']}', `last_edit_time`='{$time}'
                    WHERE `id`='{$_POST['giveoutcash_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //ID новой позиции
                    //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                    //!!! @@@ Пересчет баланса
                    //include_once 'ffun.php';
                    //calculateBalance ($_POST['client_id']);

                    echo json_encode(array('result' => 'success', 'data' =>  ''));
                }
            }
        }
    }
	
?>