<?php 

//payment_del_f.php
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
                $payment_j = SelDataFromDB('journal_payment', $_POST['id'], 'id');

                if ($payment_j != 0) {
                    //Ищем наряд
                    $invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');

                    if ($invoice_j != 0) {

                        //пересчитаем долги и баланс еще разок
                        //!!! @@@
                        //Баланс контрагента
                        include_once 'ffun.php';
                        $client_balance = json_decode(calculateBalance($_POST['client_id']), true);
                        //Долг контрагента
                        $client_debt = json_decode(calculateDebt($_POST['client_id']), true);


                        //Ну вроде все норм, поехали всё обновлять/сохранять
                        connectDB();

                        $payed = $invoice_j[0]['paid'] - $payment_j[0]['summ'];

                        //Обновим цифру оплаты в наряде
                        $query = "UPDATE `journal_invoice` SET `paid`='$payed', `status`='0', `closed_time`='0'  WHERE `id`='{$_POST['invoice_id']}'";
                        mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                        $debited = $client_balance['debited'] - $payment_j[0]['summ'];

                        //Обновим потраченное в балансе
                        $query = "UPDATE `journal_balance` SET `debited`='$debited'  WHERE `client_id`='{$_POST['client_id']}'";
                        mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                        //Удаляем оплату из БД
                        $query = "DELETE FROM `journal_payment` WHERE `id`='{$_POST['id']}'";
                        mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                        //Обновим общий
                        calculateDebt($_POST['client_id']);
                        calculateBalance ($_POST['client_id']);

                        echo json_encode(array('result' => 'success', 'data' => 'Оплата удалена'));

                    }
                }
            }
		}
	}
	
?>