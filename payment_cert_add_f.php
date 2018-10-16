<?php 

//payment_cert_add_f.php
//оплата наряда сертификатом

//!!! доделать сравнение времени, учитывая месяц и тд и тп

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

			//$temp_arr = array();
			//переменная для дополнительного текста в запросе при обновлении наряда
            $query_invoice_dop = '';
            //...и сертификата
            $query_cert_dop = '';

			if (!isset($_POST['client_id']) || !isset($_POST['summ']) || !isset($_POST['cert_id']) || !isset($_POST['invoice_id']) || !isset($_POST['date_in'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                $time = date('Y-m-d H:i:s', time());
                $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 21:00:00"));

                $comment = addslashes($_POST['comment']);

                $res_data = '';

                //переменная для суммы оплаты
                $payed = 0;
                //переменная для потрачено с баланса
                $debited = 0;
                //переменная, можно ли добавить оплату (для сравнения по времени нескольких оплат)
                $canAddPayment = true;

                //Проверки, проверочки
                include_once 'DBWork.php';
                //Ищем наряд
                $invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');
               //Ищем сертификат
                $cert_j = SelDataFromDB('journal_cert', $_POST['cert_id'], 'id');

                if (($invoice_j != 0) && ($cert_j != 0)){

                    if($invoice_j[0]['status'] == 9) {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Наряд был удален. Оплатить нельзя</div>'));
                    }else{
                        //Ищем пациента
                        $client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');

                        if ($client_j != 0) {
                            //Если это был наряд того пациента
                            if ($client_j[0]['id'] == $invoice_j[0]['client_id']) {
                                //ID клиента
                                $client_id = $client_j[0]['id'];
                                //ID наряда
                                $invoice_id = $_POST['invoice_id'];
                                //ID сертификата
                                $cert_id = $_POST['cert_id'];

                                //Если наряд оплачен !!! Доделать: Добавить кнопку для изменения статуса
                                if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {
                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Наряд уже оплачен</div>'));
                                    //Если сумма наряда меньше, чем уже оплачено
                                } elseif ($invoice_j[0]['summ'] < $invoice_j[0]['paid']) {
                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сумма наряда не может быть меньше общей внесённой суммы.</div>'));
                                    //Если сумма наряда больше, чем уже оплачено, он по факту не оплачен и вроде можно двигаться дальше
                                } elseif ($invoice_j[0]['summ'] > $invoice_j[0]['paid']) {
                                    //Если стоит метка, что наряд оплачен, надо разбираться
                                    /*if ($invoice_j[0]['status'] == 5) {
                                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! У наряда стоит статус <оплачен>.</div>'));
                                    } else*/if ($invoice_j[0]['status'] == 9) {
                                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Наряд удален/заблокирован. Операции с ним запрещены</div>'));
                                    } else {
                                        //Если мы вносим оплату задним числом
                                        //+2 суток
                                        //if (time() > strtotime($_POST['date_in'] . " 21:00:00") + 2 * 24 * 60 * 60) {
                                        if ((time() > strtotime($_POST['date_in'] . " 21:00:00") + 2 * 24 * 60 * 60) &&  ($finances['add_new'] != 1) && !$god_mode){
                                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Нельзя вносить задним числом</div>'));
                                        } else {
                                            //до того как был создан наряд
                                            //if (date("d", strtotime($invoice_j[0]['create_time'])) > date("d", strtotime($_POST['date_in'] . " 21:00:00"))) {
                                            if (strtotime($invoice_j[0]['create_time']) > strtotime($_POST['date_in'] . " 22:30:00")) {
                                                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Оплата не может быть сделана до того, как был создан наряд</div>'));
                                            } else {

                                                include_once 'ffun.php';
                                                $msql_cnnct = ConnectToDB2 ();

                                                //возьмем последнюю оплату этого наряда, если она есть
                                                $payments_j = array();
                                                $arr = array();

                                                $query = "SELECT * FROM `journal_payment` WHERE `invoice_id`='$invoice_id'  ORDER BY `create_time` DESC, `id` DESC LIMIT 1";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                                $number = mysqli_num_rows($res);
                                                if ($number != 0) {
                                                    while ($arr = mysqli_fetch_assoc($res)) {
                                                        array_push($payments_j, $arr);
                                                    }
                                                } else {
                                                    $canAddPayment = true;
                                                }

                                                //Если есть оплаты
                                                if (!empty($payments_j)) {
                                                    //Если время последней до этой оплаты выше чем мы хотим сейчас, то борода. Иначе поедет время закрытия наряда
                                                    //if (date("d", strtotime($payments_j[0]['date_in'])) > date("d", strtotime($_POST['date_in'] . " 21:00:00"))) {
                                                    if (strtotime($payments_j[0]['date_in']) > strtotime($_POST['date_in'] . " 21:00:00")) {
                                                        $canAddPayment = false;
                                                    } else {
                                                        $canAddPayment = true;
                                                    }
                                                } else {
                                                    $canAddPayment = true;
                                                }

                                                if (!$canAddPayment) {
                                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Оплата не может быть сделана до того, как была сделана предыдущая оплата</div>'));
                                                } else {

                                                    //пересчитаем долги и баланс еще разок
                                                    //!!! @@@
                                                    //Баланс контрагента (тут нам не интересен)
                                                    //$client_balance = json_decode(calculateBalance($client_j[0]['id']), true);
                                                    //Долг контрагента
                                                    $client_debt = json_decode(calculateDebt($client_j[0]['id']), true);

                                                    //Нет доступных средств на счету
                                                    /*if (($client_balance['summ'] <= 0) || ($client_balance['summ'] - $client_balance['debited'] <= 0)) {
                                                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Нет доступных средств на счету</div>'));
                                                    } else {*/

                                                        $payed = $invoice_j[0]['paid'] + $_POST['summ'];
                                                        //$debited = $client_balance['debited'] + $_POST['summ'];

                                                        //Если в итоге общая суммы оплаты больше чем требуемая
                                                        if ($payed > $invoice_j[0]['summ']) {
                                                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">После оплаты наряд будет переплачен. Измените сумму</div>'));
                                                        } else {
                                                            //Если в итоге общее потрачено будет больше денег на балансе
                                                            /*if ($debited > $client_balance['summ']) {
                                                                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">На балансе не хватит средств для оплаты</div>'));
                                                            } else {*/

                                                                //Ну вроде все норм, поехали всё обновлять/сохранять

                                                                //Вставим новую запись оплаты по наряду
                                                                $query = "INSERT INTO `journal_payment` (
                                                                  `client_id`, `invoice_id`, `summ`, `type`, `cert_id`, `date_in`, `comment`, `create_time`, `create_person`)
                                                                VALUES (
                                                                  '{$client_id}', '{$invoice_id}', '{$_POST['summ']}', '1', '{$cert_id}', '{$date_in}', '{$_POST['comment']}', '{$time}', '{$_SESSION['id']}')";
                                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                                //ID новой позиции
                                                                $mysqli_insert_id = mysqli_insert_id($msql_cnnct);

                                                                //Обновим наряд его сумму оплаты
                                                                //Если набралась сумма оплат равная общей суммы долга по наряду, то ставим статус - оплачено и дату date_in
                                                                if ($payed == $invoice_j[0]['summ']) {
                                                                    //$query_invoice_dop = ", `closed_time`='{$date_in}'";
                                                                    $query_invoice_dop = "";
                                                                }
                                                                $query = "UPDATE `journal_invoice` SET `paid`='$payed'$query_invoice_dop WHERE `id`='$invoice_id'";
                                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                                //Обновим потраченное в балансе
                                                                //$query = "UPDATE `journal_balance` SET `debited`='$debited'  WHERE `client_id`='$client_id'";
                                                                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


                                                                //Работа с сертификатом
                                                                $debited = $_POST['summ'] + $cert_j[0]['debited'];

                                                                //Обновим в сертификатие "потрачено" и Проверяем не закрыть ли нам сертификат
                                                                if ($debited >= $cert_j[0]['nominal']) {
                                                                    //$query_cert_dop = ", `closed_time`='{$date_in}'";
                                                                    $query_cert_dop = "";
                                                                }
                                                                $query = "UPDATE `journal_cert` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `debited`='$debited'$query_cert_dop WHERE `id`='$cert_id'";
                                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                                //Обновим общий долг
                                                                calculateDebt($client_id);
                                                                calculateBalance ($client_id);

                                                                /*$query = "UPDATE `journal_debt` SET `summ`='$debited'  WHERE `client_id`='$client_id'";
                                                                mysql_query($query) or die(mysql_error() . ' -> ' . $query);*/

                                                                if ($invoice_j[0]['status'] == 5) {
                                                                    $res_data = 'Оплата прошла успешно';
                                                                }else{
                                                                    $res_data = 'Оплата прошла успешно <input type="button" class="b" value="Закрыть работу" onclick="showInvoiceClose(' . $invoice_id . ')">';
                                                                }

                                                                echo json_encode(array('result' => 'success', 'data' => $res_data));
                                                            //}
                                                        }

                                                    //}
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //лишний else
                                    //echo json_encode(array('result' => 'success', 'data' => $invoice_j[0]['status'].'-'.$invoice_j[0]['summ']));
                                }
                            }
                        }
                    }
                }
			}
		}
	}
?>