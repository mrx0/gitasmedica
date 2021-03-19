<?php 

//ffun.php
//Различные функции

	include_once 'DBWork.php';

	//собственно коннект
    /*function connectDB (){
        require 'config.php';
        mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
        mysql_select_db($dbName) or die(mysql_error().' -> '.$query);
        mysql_query("SET NAMES 'utf8'");
    }*/

    //Подключение к БД MySQl
    function ConnectToDB2 () {
        require 'config.php';

        $msql_cnnct = mysqli_connect($hostname, $username, $db_pass, $dbName) or die("Не возможно создать соединение ");
        mysqli_query($msql_cnnct, "SET NAMES 'utf8'");

        return $msql_cnnct;
    }


    //добавляем клиенту новую запись с балансом
    function addClientBalanceNew ($client_id, $balance){

        $msql_cnnct = ConnectToDB2 ();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса пациента
        $query = "INSERT INTO `journal_balance` (
						`client_id`, `summ`, `create_time`, `create_person`)
						VALUES (
							'{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //добавляем клиенту новую запись с долгом
    function addClientDebtNew ($client_id, $balance){

        $msql_cnnct = ConnectToDB2 ();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса пациента
        $query = "INSERT INTO `journal_debt` (
						`client_id`, `summ`, `create_time`, `create_person`)
						VALUES (
							'{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //Обновим баланс контрагента
    function updateBalance ($id, $client_id, $Summ, $debited, $refund, $withdraw){

        $msql_cnnct = ConnectToDB2 ();

        $query = "UPDATE `journal_balance` SET `summ`='$Summ', `debited`='$debited', `refund`='$refund' , `withdraw`='$withdraw'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //Обновим долг контрагента
    function updateDebt ($id, $client_id, $Summ){

        $msql_cnnct = ConnectToDB2 ();

        $query = "UPDATE `journal_debt` SET `summ`='$Summ'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    }

    //Обновим возвраты контрагента
//    function updateRefund ($id, $client_id, $Summ){
//
//        $msql_cnnct = ConnectToDB2 ();
//
//        $query = "UPDATE `journal_debt` SET `summ`='$Summ'  WHERE `id`='$id'";
//
//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//    }

    //Смотрим баланс
    function watchBalance ($client_id, $Summ){

        $msql_cnnct = ConnectToDB2 ();

        $clientBalance = array();
        $arr = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_balance` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientBalance, $arr);
            }
        }else{
            addClientBalanceNew ($client_id, $Summ);
        }

        return($clientBalance);
    }

   //Смотрим долг
    function watchDebt ($client_id, $Summ){

        $msql_cnnct = ConnectToDB2 ();

        $clientDebt = array();
        $arr = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_debt` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
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

        $msql_cnnct = ConnectToDB2 ();

        $clientOrders = array();
        $nonClientOrders = array();

        //Переменная для суммы
        $Summ = 0;

        //Соберем все (неудаленные) ордеры
        $query = "SELECT * FROM `journal_order` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientOrders, $arr);
            }
        }/*else{
            $clientOrders = 0;
        }*/
        //return ($clientOrders);

        //Если были там какие-то ордеры
        //if ( $clientOrders != 0) {
        if (!empty($clientOrders)) {
            //Посчитаем сумму
            foreach ($clientOrders as $orders) {
                $Summ += $orders['summ'];
            }
        }

        //Соберем все (неудаленные) системные ордеры
        $query = "SELECT * FROM `journal_order_nonclient` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($nonClientOrders, $arr);
            }
        }

        //Если были там какие-то ордеры
        if (!empty($nonClientOrders)) {
            //Посчитаем сумму
            foreach ($nonClientOrders as $orders) {
                $Summ += $orders['summ'];
            }
        }

        //Смотрим есть ли баланс вообще
        $clientBalance = watchBalance ($client_id, $Summ);
        //var_dump($clientBalance);

        //Если че та там есть с балансом
        if (!empty($clientBalance)){
            $rezult['summ'] = $Summ;
            $rezult['debited'] = calculatePayment($client_id);
            $rezult['refund'] = calculateRefund($client_id);
            $rezult['withdraw'] = calculateWithdraw($client_id);

            //Обновим баланс контрагента
            updateBalance ($clientBalance[0]['id'], $client_id, $Summ, $rezult['debited'], $rezult['refund'], $rezult['withdraw']);
        }else {
            $rezult['summ'] = $Summ;
            $rezult['debited'] = 0;
            $rezult['refund'] = 0;
            $rezult['withdraw'] = 0;
        }

        return (json_encode($rezult, true));

        //return ($Summ);
    }
	
    //считаем по возвратам, сколько вернули
    function calculateRefund ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB2 ();

        $clientRefunds = array();
        $arr = array();

        //Соберем все возвраты
        $query = "SELECT * FROM `fl_journal_refund` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientRefunds, $arr);
            }
        }else{
            $clientRefunds = 0;
        }
        //return ($clientWithdraws);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то оплаты
        if ($clientRefunds != 0) {
            //Посчитаем сумму
            foreach ($clientRefunds as $refunds) {
                //if ($withdraw['type'] != 1) {
                    $Summ += $refunds['summ'];
                //}
            }
        }

        //$rezult['summ'] = $Summ;
        //return (json_encode($rezult, true));

        return ($Summ);
    }

    //считаем по выдачам, сколько выдали
    function calculateWithdraw ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB2 ();

        $clientWithdraws = array();
        $arr = array();

        //Соберем все возвраты
        $query = "SELECT * FROM `journal_withdraw` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientWithdraws, $arr);
            }
        }else{
            $clientWithdraws = 0;
        }
        //return ($clientWithdraws);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то оплаты
        if ($clientWithdraws != 0) {
            //Посчитаем сумму
            foreach ($clientWithdraws as $withdraws) {
                //if ($withdraw['type'] != 1) {
                    $Summ += $withdraws['summ'];
                //}
            }
        }

        //$rezult['summ'] = $Summ;
        //return (json_encode($rezult, true));

        return ($Summ);
    }

    //считаем по нарядам, сколько выставлено и обновляем
    function calculateDebt ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB2 ();

        $clientInvoices = array();
        $arr = array();

        //Соберем все (неудаленные) наряды, где общая сумма не равна оплаченной
        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='$client_id' AND `status` <> '9' AND `summ` <> `paid`";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
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
                $Summ += $invoices['summ'] - $invoices['paid'];
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

    //считаем по нарядам, сколько потрачено и обновляем
    function calculatePayment ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB2 ();

        $clientPayments = array();
        $arr = array();

        //Соберем все оплаты
        $query = "SELECT * FROM `journal_payment` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientPayments, $arr);
            }
        }else{
            $clientPayments = 0;
        }
        //return ($clientInvoices);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то оплаты
        if ( $clientPayments != 0) {
            //Посчитаем сумму
            foreach ($clientPayments as $payments) {
                if ($payments['type'] != 1) {
                    $Summ += $payments['summ'];
                }
            }
        }

        $rezult['summ'] = $Summ;

        //return (json_encode($rezult, true));

        return ($Summ);
    }

    //берем цены из прайса
    function takePrices ($item, $insure, $ttime){

        $msql_cnnct = ConnectToDB2 ();

        $prices_j = array();
        $arr = array();

        if ($ttime == 0) {
            $time = time();
        }else{
            $time = $ttime;
        }

        //Вытащим цены позиции
        $query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices` WHERE `item`='".$item."' AND $time > `date_from` ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

        //Если посещение страховое и у пациента прописана страховая
        if ($insure != 0){
            $query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices_insure` WHERE `item`='".$item."' AND `insure`='".$insure."' AND $time > `date_from` ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";
        }

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($prices_j, $arr);
            }
        }else{

        }

        return($prices_j);
    }

    //Вернём цену по коэффициенту
    function returnPriceWithKoeff ($koef, $prices_arr, $insure, $manual, $db_price){

        $price = $prices_arr[0]['price'];
        //$start_price = (int)$price;

        if ($insure == 0) {

                if ($koef === 'k1') {
                    if (isset($prices_arr[0]['price2'])) {
                        if ($prices_arr[0]['price2'] != 0) {
                            $price = $prices_arr[0]['price2'];
                        } else {
                            $price = $prices_arr[0]['price'] + $prices_arr[0]['price'] / 100 * 10;
                        }
                    }
                } elseif ($koef === 'k2') {
                    if (isset($prices_arr[0]['price3'])) {
                        if ($prices_arr[0]['price3'] != 0) {
                            $price = $prices_arr[0]['price3'];
                        } else {
                            $price = $prices_arr[0]['price'] + $prices_arr[0]['price'] / 100 * 20;
                        }
                    }
                } else {
                    if (is_numeric($koef)) {
                        $price = $prices_arr[0]['price'] + $prices_arr[0]['price'] / 100 * $koef;
                    } else {
                        $price = $prices_arr[0]['price'];
                    }
                }
                $start_price = (int)$price;


                if ($manual) {
                    $price = $db_price;
                }else{
                    //$price = round($price / 10) * 10;
                    //$price = round($price);
                    $price = floor($price);
                }
                //$start_price = $prices_arr[0]['start_price'];
                //$start_price = (int)$prices_arr[0]['price'];

        }else{
            $start_price = (int)$price;
        }

        //$price = round($price / 10) * 10;

        return(array('price' => $price, 'start_price' => $start_price));
    }



    //Результат расчета от процентов
    function calculateResult($summ, $koeffW, $koeffM, $SummSpec){

        //$result = 0;

        //Если есть спеццена
        if ($SummSpec > 0){
            $result = $SummSpec;
        //а иначе процент
        }else {
            $result = ($summ - ($summ / 100 * $koeffM)) / 100 * $koeffW;
        }

        return number_format($result, 2, '.', '');
    }

    //Собираем все категории процентов по типу
    function getAllPercentCats($type){
        $percents_j = array();

        $msql_cnnct = ConnectToDB ();

        $query = "SELECT * FROM `fl_spr_percents` WHERE `type`='{$type}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                $percents_j[$arr['id']] = $arr;
            }
        }

        return $percents_j;
    }


    //Получить категории процентов по сотруднику и категории
    function getPercents($worker_id, $percent_cat){

        $type = 0;
        $result = array();
        $percents = array();
        $percents_personal = array();
        $boolean = false;

        $msql_cnnct = ConnectToDB2 ();

        //Узнаем категорию сотрудника
        $query = "SELECT `id`, `permissions` AS `type` FROM `spr_workers` WHERE `id`='".$worker_id."' LIMIT 1";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        //var_dump($number);

        if ($number != 0){
            $arr = mysqli_fetch_assoc($res);

            $type = $arr['type'];

            //Вытащим общие процентовки
            if ($percent_cat > 0) {
                $query = "SELECT `id`, `work_percent`, `material_percent`, `summ_special`, `name` FROM `fl_spr_percents` WHERE `id`='" . $percent_cat . "' AND `type`='" . $type . "' LIMIT 1";
            }else{
                $query = "SELECT `id`, `work_percent`, `material_percent`, `summ_special`, `name` FROM `fl_spr_percents` WHERE `type`='" . $type . "' LIMIT 1";
            }

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            //var_dump($number);

            if ($number != 0){
                $boolean = true;
            }else{
                //Если вроде бы и категория есть, но она другого типа (пример: расчетный лист для ассистента из наряда стоматологического)
                $query = "SELECT `id`, `work_percent`, `material_percent`, `summ_special`, `name` FROM `fl_spr_percents` WHERE `id`='" . $percent_cat . "' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    $boolean = false;
                }
            }

            if ($boolean){

                while ($arr = mysqli_fetch_assoc($res)){
                    $percents[$percent_cat]['category'] = $arr['id'];
                    $percents[$percent_cat]['name'] = $arr['name'];
                    $percents[$percent_cat]['work_percent'] = $arr['work_percent'];
                    $percents[$percent_cat]['material_percent'] =  $arr['material_percent'];
                    $percents[$percent_cat]['summ_special'] =  $arr['summ_special'];
                }

                //Вытащим персональные процентовки
                $query = "SELECT * FROM `fl_spr_percents_personal` WHERE `worker_id`='".$worker_id."' AND `percent_cats`='".$percent_cat."'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if ($arr['type'] == 1){
                            $percents[$percent_cat]['work_percent'] = $arr['percent'];
                        }
                        if ($arr['type'] == 2){
                            $percents[$percent_cat]['material_percent'] = $arr['percent'];
                        }
                        if ($arr['type'] == 3){
                            $percents[$percent_cat]['summ_special'] = $arr['percent'];
                        }
                    }
                }else{

                }
            }else{
                while ($arr = mysqli_fetch_assoc($res)){
                    $percents[$percent_cat]['category'] = $arr['id'];
                    $percents[$percent_cat]['name'] = $arr['name'].'/ <span style="color: red;">Ошибка #33. Измените исполнителя.</span>';
                    $percents[$percent_cat]['work_percent'] = $arr['work_percent'];
                    $percents[$percent_cat]['material_percent'] =  $arr['material_percent'];
                    $percents[$percent_cat]['summ_special'] =  $arr['summ_special'];
                }
            }

        }

        $result = $percents;
        //var_dump($result);

        return $result;
    }



    //Для отчета касса
    function ajaxShowResultCashbox ($datastart, $dataend, $filial, $summtype, $certificatesShow, $show_deleted){
        //var_dump(func_get_args());

        $msql_cnnct = ConnectToDB2 ();

        $rezult = array();
        $rezult_cert = array();
        $rezult_abon = array();
        $rezult_solar = array();
        $rezult_realiz = array();
        $rezult_give_out_cash = array();
        $arr = array();

        //Переменная для строчки запроса по филиалу и типу
        $queryFilial = '';
        $queryFilial2 = '';
        $queryType = '';

        //Филиал
        if ($filial != 99){
            $queryFilial .= "AND `office_id` = '".$filial."'";
            $queryFilial2 .= "AND `filial_id` = '".$filial."'";
        }

        if ($summtype != 0){
            $queryType .= "AND `summ_type` = '".$summtype."'";
        }

        $show_deleted_str = '';
        if (!$show_deleted){
            $show_deleted_str = "AND `status` <> '9'";
        }

        //Приход денег вытащим
        $query = "SELECT * FROM `journal_order` WHERE
                `date_in` BETWEEN 
                STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                AND 
                STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                ".$queryFilial.$queryType." AND `org_pay` <> '1' ".$show_deleted_str."
                ORDER BY `date_in` DESC";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($rezult, $arr);
            }
        }else{
            //addClientBalanceNew ($client_id, $Summ);
        }

        //Приход денег за сертификаты вытащим
        if ($certificatesShow != 0){
            $query = "SELECT * FROM `journal_cert` WHERE
                    `cell_time` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    ".$queryFilial.$queryType.$show_deleted_str."
                    ORDER BY `cell_time` DESC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rezult_cert, $arr);
                }
            }else{
                //addClientBalanceNew ($client_id, $Summ);
            }
        }

        //Приход денег за абонементы вытащим
        if ($certificatesShow != 0){
            $query = "SELECT * FROM `journal_abonement_solar` WHERE
                    `cell_time` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    ".$queryFilial2.$queryType.$show_deleted_str."
                    ORDER BY `cell_time` DESC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rezult_abon, $arr);
                }
            }else{
                //addClientBalanceNew ($client_id, $Summ);
            }
        }

        //Приход денег за солярий
        if ($certificatesShow != 0){
            $query = "SELECT * FROM `journal_solar` WHERE
                    `date_in` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    AND (`summ_type`='1' OR `summ_type`='2')
                    ".$queryFilial2.$queryType.$show_deleted_str."
                    ORDER BY `date_in` DESC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rezult_solar, $arr);
                }
            }else{
                //addClientBalanceNew ($client_id, $Summ);
            }
        }

        //Приход денег за реализацию
        if ($certificatesShow != 0){
            $query = "SELECT * FROM `journal_realiz` WHERE
                    `date_in` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    ".$queryFilial2.$queryType.$show_deleted_str."
                    ORDER BY `date_in` DESC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rezult_realiz, $arr);
                }
            }else{
                //addClientBalanceNew ($client_id, $Summ);
            }
        }

        //Расходы с кассы  вытащим
        //if ($certificatesShow != 0){
            $query = "SELECT * FROM `journal_giveoutcash` WHERE
                    `date_in` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    AND `status` <> '9'
                    ".$queryFilial."
                    ORDER BY `date_in` DESC";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rezult_give_out_cash, $arr);
                }
            }else{
                //addClientBalanceNew ($client_id, $Summ);
            }
        //}

        $result['rezult'] = $rezult;
        $result['rezult_cert'] = $rezult_cert;
        $result['rezult_abon'] = $rezult_abon;
        $result['rezult_solar'] = $rezult_solar;
        $result['rezult_realiz'] = $rezult_realiz;
        $result['rezult_give_out_cash'] = $rezult_give_out_cash;

        return $result;

    }




?>