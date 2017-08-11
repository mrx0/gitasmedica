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

                    $query = "INSERT INTO `journal_laborder_ex` (`laborder_id`, `office_id`, `create_person`, `create_time`, `status`)
                        VALUES (
                        '{$_POST['lab_order_id']}', '{$_SESSION['filial']}', '{$_SESSION['id']}', '{$time}', '{$_POST['status']}')";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //Обновляем
                    $query = "UPDATE `journal_laborder` SET `status`='{$_POST['status']}' WHERE `id`='{$_POST['lab_order_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    echo json_encode(array('result' => 'success', 'data' => ''));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">У вас не определён филиал</div>'));
                }
			}
		}
	}
?>