<?php 

//edit_order_f.php
//Функция редактирования ордера

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['order_id']) || !isset($_POST['client_id']) || !isset($_POST['summ']) || !isset($_POST['summtype']) || !isset($_POST['date_in']) || !isset($_POST['office_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
			    if ($_POST['summ'] < 1){
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сумма не может быть меньше 1 руб.</div>'));
                }else {
                    include_once 'DBWork.php';

                    $order_j = SelDataFromDB('journal_order', $_POST['order_id'], 'id');

                    if ($order_j != 0) {

                        $msql_cnnct = ConnectToDB();

                        $time = date('Y-m-d H:i:s', time());
                        $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'] . " 09:00:00"));

                        $comment = addslashes($_POST['comment']);

                        //Обновляем
                        $query = "UPDATE `journal_order` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `summ`='{$_POST['summ']}', `summ_type`='{$_POST['summtype']}', `date_in`='{$date_in}', `comment`='{$_POST['comment']}', `office_id`='{$_POST['office_id']}' WHERE `id`='{$_POST['order_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //ID старой позиции
                        $mysql_insert_id = $_POST['order_id'];

                        //!!! @@@ Пересчет баланса
                        include_once 'ffun.php';
                        calculateBalance($_POST['client_id']);

                        echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id));

                    } else {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                    }
                }
			}
		}
	}
?>