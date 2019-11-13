<?php 

//installment_status_change_f.php
//Функция для изменения статуса рассрочки

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){
		    if (isset($_POST['client_id']) && isset($_POST['installment_status_now'])) {

                $msql_cnnct = ConnectToDB();

                //$time = time();

                //Если ничего нет и не было, ставим, что теперь есть
                if ($_POST['installment_status_now'] == 0){
                    $status = 1;
                }
                //Если была рассрочка, ставим, что теперь нет
                if ($_POST['installment_status_now'] == 1){
                    $status = 7;
                }
                //Если была рассрочка, а теперь нет, ставим, что теперь снова есть
                if ($_POST['installment_status_now'] == 7){
                    $status = 1;
                }

                $query = "UPDATE `spr_clients` SET `installment`='$status' WHERE `id`='{$_POST['client_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success', 'data' => ''));
            }

		}

	}
	
?>