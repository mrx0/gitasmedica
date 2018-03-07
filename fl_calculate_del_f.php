<?php 

//fl_calculate_del_f.php
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

            if (!isset($_POST['client_id']) || !isset($_POST['invoice_id']) || !isset($_POST['id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                //Проверки, проверочки
                include_once 'DBWork.php';
                //Ищем оплату
                $calculate_j = SelDataFromDB('fl_journal_calculate', $_POST['id'], 'id');

                if ($calculate_j != 0) {
                    //Ищем наряд
                    $invoice_j = SelDataFromDB('journal_invoice', $calculate_j[0]['invoice_id'], 'id');

                    if ($invoice_j != 0) {

                        //!!! @@@
                        include_once 'ffun.php';

                        //Ну вроде все норм, поехали всё обновлять/сохранять
                        $msql_cnnct = ConnectToDB2 ();

                        //$payed = $invoice_j[0]['paid'] - $payment_j[0]['summ'];

                        //Обновим цифру оплаты в наряде
                        //$query = "UPDATE `journal_invoice` SET `paid`='$payed', `status`='0', `closed_time`='0'  WHERE `id`='{$_POST['invoice_id']}'";
                        //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        //if ($payment_j[0]['type'] != 1) {
                        //    $debited = $client_balance['debited'] - $payment_j[0]['summ'];
                        //}else{
                            //Вернем в работу сертификат
                            $time = date('Y-m-d H:i:s', time());

                        /*    if ($payment_j[0]['cert_id'] != 0) {
                                //Ищем сертификат
                                $cert_j = SelDataFromDB('journal_cert', $payment_j[0]['cert_id'], 'id');
                                if ($cert_j != 0) {
                                    //Обновим потраченное в балансе
                                    $query = "UPDATE `journal_cert` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `debited`='".($cert_j[0]['debited'] - $payment_j[0]['summ'])."', `closed_time`='0000-00-00 00:00:00', `status`='7'  WHERE `id`='{$payment_j[0]['cert_id']}'";
                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                }
                            }
                        }*/
                        /*if ($payment_j[0]['type'] != 1) {
                            //Обновим потраченное в балансе
                            $query = "UPDATE `journal_balance` SET `debited`='$debited'  WHERE `client_id`='{$_POST['client_id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        }*/

                        //Удаляем из БД
                        $query = "DELETE FROM `fl_journal_calculate` WHERE `id`='{$_POST['id']}'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $query = "DELETE FROM `fl_journal_calculate_ex` WHERE `calculate_id`='{$_POST['id']}'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        //Обновим общий
                        //calculateDebt($_POST['client_id']);
                        //calculateBalance ($_POST['client_id']);

                        echo json_encode(array('result' => 'success', 'data' => 'Расчёт удален'));

                    }
                }
            }
		}
	}
	
?>