<?php 

//payment_del_f.php
//Функция для Удаление(блокирование) 

	session_start();

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
                $payment_j = SelDataFromDB('journal_payment', $_POST['id'], 'id');

                //разбираемся с правами
                $god_mode = FALSE;
                require_once 'permissions.php';

                if ($payment_j != 0) {

                    //Ищем наряд
                    $invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');

                    if ($invoice_j != 0) {

                        $fl_calculate_j = array();

                        $msql_cnnct = ConnectToDB ();

                        $query2 = "SELECT `id` FROM `fl_journal_calculate` WHERE `invoice_id`='".$invoice_j[0]['id']."' ORDER BY `create_time` DESC";
                        //var_dump($query);
                        $res = mysqli_query($msql_cnnct, $query2) or die(mysqli_error($msql_cnnct).' -> '.$query2);

                        $number = mysqli_num_rows($res);

                        if (($number == 0) || ($finances['see_all']) || $god_mode){

                            //Нет расчетов

                            //пересчитаем долги и баланс еще разок
                            //!!! @@@
                            //Баланс контрагента
                            include_once 'ffun.php';
                            $client_balance = json_decode(calculateBalance($_POST['client_id']), true);
                            //Долг контрагента
                            $client_debt = json_decode(calculateDebt($_POST['client_id']), true);


                            //Ну вроде все норм, поехали всё обновлять/сохранять
                            $msql_cnnct = ConnectToDB2();

                            $payed = $invoice_j[0]['paid'] - $payment_j[0]['summ'];

                            //Обновим цифру оплаты в наряде
                            $query = "UPDATE `journal_invoice` SET `paid`='$payed' WHERE `id`='{$_POST['invoice_id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            if ($payment_j[0]['type'] != 1) {
                                $debited = $client_balance['debited'] - $payment_j[0]['summ'];
                            } else {
                                //Вернем в работу сертификат
                                $time = date('Y-m-d H:i:s', time());

                                if ($payment_j[0]['cert_id'] != 0) {
                                    //Ищем сертификат
                                    $cert_j = SelDataFromDB('journal_cert', $payment_j[0]['cert_id'], 'id');
                                    if ($cert_j != 0) {
                                        //Обновим потраченное в балансе
                                        $query = "UPDATE `journal_cert` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `debited`='" . ($cert_j[0]['debited'] - $payment_j[0]['summ']) . "', `closed_time`='0000-00-00 00:00:00', `status`='7'  WHERE `id`='{$payment_j[0]['cert_id']}'";
                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                    }
                                }
                            }
                            if ($payment_j[0]['type'] != 1) {
                                //Обновим потраченное в балансе
                                $query = "UPDATE `journal_balance` SET `debited`='$debited'  WHERE `client_id`='{$_POST['client_id']}'";
                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                            }

                            //Удаляем оплату из БД
                            $query = "DELETE FROM `journal_payment` WHERE `id`='{$_POST['id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            //Обновим общий
                            calculateDebt($_POST['client_id']);
                            calculateBalance($_POST['client_id']);

                            echo json_encode(array('result' => 'success', 'data' => $query2));

                        }else {

                            //есть расчеты
                            echo json_encode(array('result' => 'error', 'data' => 'К наряду выписаны расчетные листы. Отменить оплату нельзя.'));

                        }
                    }
                }
            }
		}
	}
	
?>