<?php 

//labOrderStatusChange_f.php
//Функция изменения статуса заказа в лабораторию

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            //$god_mode = FALSE;

			if (!isset($_POST['lab_order_id']) || !isset($_POST['status'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                //Филиал
                if (isset($_SESSION['filial'])) {
                    $time = date('Y-m-d H:i:s', time());

                    $msql_cnnct = ConnectToDB();

                    if ($_POST['status'] == 2) {

                        $query = "DELETE FROM `journal_laborder_ex` WHERE `laborder_id`='{$_POST['lab_order_id']}' ORDER BY `id` DESC LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //Крайний статус
                        $query = "SELECT `status` FROM `journal_laborder_ex` WHERE `laborder_id`='{$_POST['lab_order_id']}' ORDER BY `id` DESC LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            /*while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($rez, $arr);
                            }*/

                            $arr = mysqli_fetch_assoc($res);

                            //Обновляем
                            $query = "UPDATE `journal_laborder` SET `status`='{$arr['status']}' WHERE `id`='{$_POST['lab_order_id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        }

                    } elseif ($_POST['status'] == 4) {

                    }else {

                        $query = "INSERT INTO `journal_laborder_ex` (`laborder_id`, `office_id`, `create_person`, `create_time`, `status`)
                            VALUES (
                            '{$_POST['lab_order_id']}', '{$_SESSION['filial']}', '{$_SESSION['id']}', '{$time}', '{$_POST['status']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //Обновляем
                        $query = "UPDATE `journal_laborder` SET `status`='{$_POST['status']}' WHERE `id`='{$_POST['lab_order_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    }

                    echo json_encode(array('result' => 'success', 'data' => ''));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">У вас не определён филиал</div>'));
                }
			}
		}
	}
?>