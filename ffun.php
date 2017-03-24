<?php 

//ffun.php
//Различные функции

	include_once 'DBWork.php';

	//собственно коннект
    function connectDB (){
        require 'config.php';
        mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
        mysql_select_db($dbName) or die(mysql_error().' -> '.$query);
        mysql_query("SET NAMES 'utf8'");
    }

    //добавляем клиенту новую запись с балансом
    function addClientBalanceNew ($client_id, $balance){

        connectDB();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса пациента
        $query = "INSERT INTO `journal_balance` (
						`client_id`, `summ`, `create_time`, `create_person`)
						VALUES (
							'{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        mysql_query($query) or die(mysql_error().' -> '.$query);

    }

    //добавляем клиенту новую запись с долгом
    function addClientDebtNew ($client_id, $balance){

        connectDB();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса пациента
        $query = "INSERT INTO `journal_debt` (
						`client_id`, `summ`, `create_time`, `create_person`)
						VALUES (
							'{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        mysql_query($query) or die(mysql_error().' -> '.$query);

    }

    //Обновим баланс контрагента
    function updateBalance ($id, $client_id, $Summ, $debited){

        connectDB();

        $query = "UPDATE `journal_balance` SET `summ`='$Summ', `debited`='$debited'  WHERE `id`='$id'";

        mysql_query($query) or die(mysql_error().' -> '.$query);
    }

    //Обновим долг контрагента
    function updateDebt ($id, $client_id, $Summ){

        connectDB();

        $query = "UPDATE `journal_debt` SET `summ`='$Summ'  WHERE `id`='$id'";

        mysql_query($query) or die(mysql_error().' -> '.$query);
    }

    //Смотрим баланс
    function watchBalance ($client_id, $Summ){

        connectDB();

        $clientBalance = array();
        $arr = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_balance` WHERE `client_id`='$client_id'";

        $res = mysql_query($query) or die($query);
        $number = mysql_num_rows($res);
        if ($number != 0){
            while ($arr = mysql_fetch_assoc($res)){
                array_push($clientBalance, $arr);
            }
        }else{
            addClientBalanceNew ($client_id, $Summ);
        }

        return($clientBalance);
    }

   //Смотрим долг
    function watchDebt ($client_id, $Summ){

        connectDB();

        $clientDebt = array();
        $arr = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_debt` WHERE `client_id`='$client_id'";

        $res = mysql_query($query) or die(mysql_error().' -> '.$query);
        $number = mysql_num_rows($res);
        if ($number != 0){
            while ($arr = mysql_fetch_assoc($res)){
                array_push($clientDebt, $arr);
            }
        }else{
            addClientDebtNew ($client_id, $Summ);
        }

        return($clientDebt);
    }

    //считаем по ордерам, сколько внесено и обновляем
    function calculateBalance ($client_id){

        $rezult = array();

        connectDB();

        $clientOrders = array();
        $arr = array();

        //Соберем все (неудаленные) ордеры
        $query = "SELECT * FROM `journal_order` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysql_query($query) or die(mysql_error().' -> '.$query);
        $number = mysql_num_rows($res);
        if ($number != 0){
            while ($arr = mysql_fetch_assoc($res)){
                array_push($clientOrders, $arr);
            }
        }else{
            $clientOrders = 0;
        }
        //return ($clientOrders);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то ордеры
        if ( $clientOrders != 0) {
            //Посчитаем сумму
            foreach ($clientOrders as $orders) {
                $Summ += $orders['summ'];
            }
        }

        //Смотрим есть ли баланс вообще
        $clientBalance = watchBalance ($client_id, $Summ);
        //var_dump($clientBalance);

        //Если че та там есть с балансом
        if (!empty($clientBalance)){
            $rezult['summ'] = $Summ;
            $rezult['debited'] = $clientBalance[0]['debited'];

            //Обновим баланс контрагента
            updateBalance ($clientBalance[0]['id'], $client_id, $Summ, $rezult['debited']);
        }else {
            $rezult['summ'] = $Summ;
            $rezult['debited'] = 0;
        }

        return (json_encode($rezult, true));

        //return ($Summ);
    }
	
    //считаем по нарядам, сколько выставлено и обновляем
    function calculateDebt ($client_id){

        $rezult = array();

        connectDB();

        $clientInvoices = array();
        $arr = array();

        //Соберем все (неудаленные) наряды, где общая сумма не равна оплаченной
        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysql_query($query) or die(mysql_error().' -> '.$query);
        $number = mysql_num_rows($res);
        if ($number != 0){
            while ($arr = mysql_fetch_assoc($res)){
                array_push($clientInvoices, $arr);
            }
        }else{
            $clientInvoices = 0;
        }
        //return ($clientInvoices);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то наряды
        if ( $clientInvoices != 0) {
            //Посчитаем сумму
            foreach ($clientInvoices as $invoices) {
                $Summ += $invoices['summ'];
            }
        }

        //Смотрим есть ли долг в базе вообще
        $clientDebt = watchDebt ($client_id, $Summ);
        //var_dump($clientBalance);

        //Если че та там есть с долгом
        if (!empty($clientDebt)){
            $rezult['summ'] = $Summ;

            //Обновим баланс контрагента
            updateDebt ($clientDebt[0]['id'], $client_id, $Summ);
        }else {
            $rezult['summ'] = $Summ;
        }

        return (json_encode($rezult, true));

        //return ($Summ);
    }

?>