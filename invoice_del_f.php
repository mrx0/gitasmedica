<?php 

//invoice_del_f.php
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

		    $data = '';

            $msql_cnnct = ConnectToDB ();

			$time = time();

			//проверим, есть ли оплаты по этому наряду
            //Документы закрытия/оплаты нарядов списком
            $payment_j = array();

            $query = "SELECT * FROM `journal_payment` WHERE `invoice_id`='".$_POST['id']."' ORDER BY `date_in` DESC";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($payment_j, $arr);
                }
            }else{

            }

			//проверим, есть ли расчёты по этому наряду
            //Расчёты списком
            $calculate_j = array();

            $query = "SELECT * FROM `fl_journal_calculate` WHERE `invoice_id`='".$_POST['id']."' ORDER BY `date_in` DESC";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($calculate_j, $arr);
                }
            }else{

            }

            if (!empty($payment_j)) {
                $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>По наряду проходили оплаты. Удалить нельзя.</h3></div>';

                echo json_encode(array('result' => 'error', 'data' => $data));
            }else {
                if (!empty($calculate_j)) {
                    $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>По наряду созданы расчёты. Удалить нельзя.</h3></div>';

                    echo json_encode(array('result' => 'error', 'data' => $data));
                }else {

                    $query = "UPDATE `journal_invoice` SET `status`='9' WHERE `id`='{$_POST['id']}'";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //mysql_close();

                    //!!! @@@ Пересчет долга
                    include_once 'ffun.php';
                    calculateDebt($_POST['client_id']);

                    $data = '<div class="query_ok" style="padding-bottom: 10px;"><h3>Наряд удален (заблокирован).</h3></div>';

                    echo json_encode(array('result' => 'success', 'data' => $data));
                }
            }
		}

	}
	
?>