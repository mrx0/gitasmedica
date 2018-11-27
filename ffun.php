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
    function updateBalance ($id, $client_id, $Summ, $debited){

        $msql_cnnct = ConnectToDB2 ();

        $query = "UPDATE `journal_balance` SET `summ`='$Summ', `debited`='$debited'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    }

    //Обновим долг контрагента
    function updateDebt ($id, $client_id, $Summ){

        $msql_cnnct = ConnectToDB2 ();

        $query = "UPDATE `journal_debt` SET `summ`='$Summ'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    }

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
        $arr = array();

        //Соберем все (неудаленные) ордеры
        $query = "SELECT * FROM `journal_order` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
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
            $rezult['debited'] = calculatePayment($client_id);

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
    function takePrices ($item, $insure){

        $msql_cnnct = ConnectToDB2 ();

        $prices_j = array();
        $arr = array();

        $time = time();

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
    function calculateResult($summ, $koeffW, $koeffM){

        $result = 0;

        $result = ($summ - ($summ / 100 * $koeffM)) / 100 * $koeffW;

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
                $query = "SELECT `id`, `work_percent`, `material_percent`, `name` FROM `fl_spr_percents` WHERE `id`='" . $percent_cat . "' AND `type`='" . $type . "' LIMIT 1";
            }else{
                $query = "SELECT `id`, `work_percent`, `material_percent`, `name` FROM `fl_spr_percents` WHERE `type`='" . $type . "' LIMIT 1";
            }

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            //var_dump($number);

            if ($number != 0){
                $boolean = true;
            }else{
                $query = "SELECT `id`, `work_percent`, `material_percent`, `name` FROM `fl_spr_percents` WHERE `type`='" . $type . "' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    $boolean = true;
                }
            }

            if ($boolean){

                while ($arr = mysqli_fetch_assoc($res)){
                    $percents[$percent_cat]['category'] = $arr['id'];
                    $percents[$percent_cat]['name'] = $arr['name'];
                    $percents[$percent_cat]['work_percent'] = $arr['work_percent'];
                    $percents[$percent_cat]['material_percent'] =  $arr['material_percent'];
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
                    }
                }else{

                }
            }

        }

        $result = $percents;
        //var_dump($result);

        return $result;
    }

    //Обновить сумму баланса Табеля
    function updateTabelBalance($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summCalcs`  FROM `fl_journal_calculate` WHERE `id` IN (SELECT `calculate_id` FROM `fl_journal_tabels_ex` WHERE `tabel_id`='{$tabel_id}');";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `summ` = '".round($arr['summCalcs'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }

    //Обновить сумму баланса Вычетов табеля
    function updateTabelDeductionsSumm($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summCalcs`  FROM `fl_journal_deductions` WHERE `tabel_id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `deduction` = '".round($arr['summCalcs'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }

    //Обновить сумму баланса Надбавок табеля
    function updateTabelSurchargesSumm($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summCalcs`  FROM `fl_journal_surcharges` WHERE `tabel_id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `surcharge` = '".round($arr['summCalcs'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }

    //Обновить сумму баланса Выплат табеля
    function updateTabelPaidoutSumm($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summCalcs`  FROM `fl_journal_paidouts` WHERE `tabel_id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `paidout` = '".round($arr['summCalcs'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }

    //Обновить сумму баланса Надбавок по ночным сменам табеля
    function updateTabelNightSmensSumm($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summNS`  FROM `fl_journal_tabel_nightsmens` WHERE `tabel_id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `night_smena` = '".round($arr['summNS'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }


    //Обновить сумму баланса Надбавок по пустым сменам табеля
    function updateTabelEmptySmensSumm($tabel_id){

        $msql_cnnct = ConnectToDB2();

        $query = "SELECT SUM(`summ`) AS `summES`  FROM `fl_journal_tabel_emptysmens` WHERE `tabel_id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $arr = mysqli_fetch_assoc($res);

        $query = "UPDATE `fl_journal_tabels` SET `empty_smena` = '".round($arr['summES'], 2)."' WHERE `id`='{$tabel_id}';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        CloseDB ($msql_cnnct);

    }


    //Обновление РЛ
    function fl_updateCalculatesData ($invoice_id, $mat_cons_j_ex, $deleteMark){

        //$query = '';

        $msql_cnnct = ConnectToDB2();

        $calculate_data_array = array();

        $query_dop_array = array();
        $query_dop = '';

        if (!empty($mat_cons_j_ex)){

            $kr=true;

            if (!empty($mat_cons_j_ex['data'])) {
                /*foreach ($mat_cons_j_ex['data'] as $inv_pos_id => $mat_cons_summ){
                    array_push($query_dop_array, " `inv_pos_id`='".$inv_pos_id."'");
                }
                if (!empty($query_dop_array)) {
                    $query_dop = ' AND ('.implode(' OR ', $query_dop_array).')';
                }*/

                $query = "SELECT `calculate_id` FROM `fl_journal_calculate_ex` WHERE `calculate_id` IN (SELECT `id` FROM `fl_journal_calculate` WHERE `invoice_id`='$invoice_id')".$query_dop." GROUP BY `calculate_id`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($calculate_data_array, $arr);
                    }
                }else{

                }

                if (!empty($calculate_data_array)){
                    foreach($calculate_data_array as $data){

                        $calculateInvSumm = 0;
                        $calculateCalcSumm = 0;

                        $calculate_ex_j = array();

                        $calculate_j = SelDataFromDB('fl_journal_calculate', $data['calculate_id'], 'id');

                        if ($calculate_j != 0){

                            $query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `calculate_id`='".$calculate_j[0]['id']."';";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    if (!isset($calculate_ex_j[$arr['ind']])){
                                        $calculate_ex_j[$arr['ind']] = array();
                                        array_push($calculate_ex_j[$arr['ind']], $arr);
                                    }else{
                                        array_push($calculate_ex_j[$arr['ind']], $arr);
                                    }
                                }
                            }else {
                                //$calculate_ex_j = 0;
                                //var_dump ($calculate_ex_j);
                            }

                            //сортируем зубы по порядку
                            if ($calculate_ex_j != 0){
                                ksort($calculate_ex_j);
                            }

                            if (!empty($calculate_ex_j)) {
                                foreach ($calculate_ex_j as $ind => $calculate_data) {

                                    if (!empty($calculate_data)) {
                                        //if ($_POST['invoice_type'] == 5) {
                                            foreach ($calculate_data as $key => $items) {

                                                /*$pos_id = $items['inv_pos_id'];
                                                $price_id = $items['price_id'];
                                                $quantity = $items['quantity'];
                                                $insure = $items['insure'];
                                                $insure_approve = $items['insure_approve'];*/
                                                $price = $items['price'];

                                                /*$itog_price = 0;

                                                $guarantee = $items['guarantee'];
                                                $spec_koeff = $items['spec_koeff'];
                                                $discount = $items['discount'];*/

                                                $percent_cats = $items['percent_cats'];
                                                $work_percent = $items['work_percent'];
                                                $material_percent = $items['material_percent'];

                                                /*if ($itog_price == 0) {
                                                    $itog_price_add = $price;
                                                } else {
                                                    $itog_price_add = $itog_price;
                                                }*/


                                                /*if (!empty($mat_cons_j_ex['data'])){
                                                    if (isset($mat_cons_j_ex['data'][$pos_id])){
                                                        $itog_price_add = $itog_price_add - $mat_cons_j_ex['data'][$pos_id];
                                                    }else{
                                                    }
                                                }else{
                                                }*/


                                                /*if ($guarantee != 0) {
                                                    $itog_price_add = 0;
                                                }*/

                                                //Добавляем в базу
                                                /*$query = "INSERT INTO `fl_journal_calculate_ex` (`calculate_id`, `ind`, `price_id`, `inv_pos_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`, `percent_cats`, `work_percent`, `material_percent`)
                                                VALUES (
                                                '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$pos_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}', '{$percent_cats}', '{$work_percent}', '{$material_percent}')";*/

                                                //Обновляем
                                                /*$query = "UPDATE `fl_journal_calculate_ex` SET
                                                `paid`='$payed', `status`='0', `closed_time`='0'  WHERE `id`='{$_POST['invoice_id']}'";*/

                                                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                                /*$price = $price * $quantity;

                                                $price = ($price - ($price * $discount / 100));

                                                if ($itog_price == 0) {
                                                    $itog_price = $price;
                                                }

                                                if ($guarantee != 0) {
                                                    $itog_price = 0;
                                                }*/

                                                $itog_price = $price;

                                                //$calculateInvSumm +=  round($price);
                                                $calculateInvSumm += $itog_price;

                                                if (!$deleteMark) {
                                                    if (!empty($mat_cons_j_ex['data'])) {
                                                        if (isset($mat_cons_j_ex['data'][$items['inv_pos_id']])) {
                                                            $itog_price = $itog_price - $mat_cons_j_ex['data'][$items['inv_pos_id']];
                                                        } else {
                                                        }
                                                    } else {
                                                    }
                                                }

                                                if ($itog_price < 0) $itog_price = 0;

                                                //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
                                                $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);
                                            }

                                            /*if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
                                                $mkb_data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
                                                foreach ($mkb_data as $mkb_id){
                                                    //Добавляем в базу МКБ
                                                    $query = "INSERT INTO `journal_invoice_ex_mkb` (`invoice_id`, `ind`, `mkb_id`)
                                                    VALUES (
                                                    '{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";

                                                    mysql_query($query) or die(mysql_error().' -> '.$query);
                                                }
                                            }*/

                                        //}

                                        /*if ($_POST['invoice_type'] == 6) {

                                            $price_id = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price_id'];
                                            $quantity = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['quantity'];
                                            $insure = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure'];
                                            $insure_approve = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure_approve'];
                                            $price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price'];

                                            $itog_price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['itog_price'];

                                            $guarantee = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['guarantee'];
                                            $spec_koeff = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['spec_koeff'];
                                            $discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['discount'];

                                            $percent_cats = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['percent_cats'];
                                            $work_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['work_percent'];
                                            $material_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['material_percent'];

                                            if ($itog_price == 0){
                                                $itog_price_add = $price;
                                            }

                                            //Добавляем в базу
                                            $query = "INSERT INTO `fl_journal_calculate_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`)
                                            VALUES (
                                            '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}')";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            $price = $price*$quantity;

                                            $price =  ($price - ($price * $discount / 100));

                                            //$calculateInvSumm +=  round($price);
                                            $calculateInvSumm += $itog_price;

                                            //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
                                            $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);

                                        }*/
                                        //unset($_SESSION['calculate_data']);
                                    }
                                }
                            }


                        }



                        /*$query = "SELECT `calculate_id` FROM `fl_journal_calculate_ex` WHERE `calculate_id` IN (SELECT `id` FROM `fl_journal_calculate` WHERE `invoice_id`='$invoice_id')".$query_dop." GROUP BY `calculate_id`";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($calculate_data_array, $arr);
                            }
                        }else{

                        }*/


                        //Обновим сумму в расчете
                        if ($calculateInvSumm > 0) {
                            $query = "UPDATE `fl_journal_calculate` SET `summ_inv`='{$calculateInvSumm}', `summ`='{$calculateCalcSumm}' WHERE `id`='{$data['calculate_id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        }

                    }
                }


            }

        }/*else{

            //sleep (10);

            $kr=false;

            $mat_cons_j_ex = array();

            //Затраты на материалы
            $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                                LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                                ON jimc.id = jimcex.inv_mat_cons_id
                                WHERE jimc.invoice_id = '".$invoice_id."';";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {

                    //array_push($mat_cons_j, $arr);

                    if (!isset($mat_cons_j_ex['data'])){
                        $mat_cons_j_ex['data'] = array();
                    }

                    if (!isset($mat_cons_j_ex['data'][$arr['inv_pos_id']])){
                        $mat_cons_j_ex['data'][$arr['inv_pos_id']] = $arr['summ'];
                    }

                    $mat_cons_j_ex['create_person'] = $arr['create_person'];
                    $mat_cons_j_ex['create_time'] = $arr['create_time'];
                    $mat_cons_j_ex['all_summ'] = $arr['all_summ'];
                    $mat_cons_j_ex['descr'] = $arr['descr'];
                    $mat_cons_j_ex['id'] = $arr['mc_id'];
                }
            } else {

            }

            if (!empty($mat_cons_j_ex['data'])) {
                /*foreach ($mat_cons_j_ex['data'] as $inv_pos_id => $mat_cons_summ){
                    array_push($query_dop_array, " `inv_pos_id`='".$inv_pos_id."'");
                }
                if (!empty($query_dop_array)) {
                    $query_dop = ' AND ('.implode(' OR ', $query_dop_array).')';
                }*/

        /*        $query = "SELECT `calculate_id` FROM `fl_journal_calculate_ex` WHERE `calculate_id` IN (SELECT `id` FROM `fl_journal_calculate` WHERE `invoice_id`='$invoice_id')".$query_dop." GROUP BY `calculate_id`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($calculate_data_array, $arr);
                    }
                }else{

                }

                if (!empty($calculate_data_array)){
                    foreach($calculate_data_array as $data){

                        $calculateInvSumm = 0;
                        $calculateCalcSumm = 0;

                        $calculate_ex_j = array();

                        $calculate_j = SelDataFromDB('fl_journal_calculate', $data['calculate_id'], 'id');

                        if ($calculate_j != 0){

                            $query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `calculate_id`='".$calculate_j[0]['id']."';";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    if (!isset($calculate_ex_j[$arr['ind']])){
                                        $calculate_ex_j[$arr['ind']] = array();
                                        array_push($calculate_ex_j[$arr['ind']], $arr);
                                    }else{
                                        array_push($calculate_ex_j[$arr['ind']], $arr);
                                    }
                                }
                            }else {
                                //$calculate_ex_j = 0;
                                //var_dump ($calculate_ex_j);
                            }

                            //сортируем зубы по порядку
                            if ($calculate_ex_j != 0){
                                ksort($calculate_ex_j);
                            }

                            if (!empty($calculate_ex_j)) {
                                foreach ($calculate_ex_j as $ind => $calculate_data) {

                                    if (!empty($calculate_data)) {
                                        //if ($_POST['invoice_type'] == 5) {
                                        foreach ($calculate_data as $key => $items) {

                                            /*$pos_id = $items['inv_pos_id'];
                                            $price_id = $items['price_id'];
                                            $quantity = $items['quantity'];
                                            $insure = $items['insure'];
                                            $insure_approve = $items['insure_approve'];*/

        /*                                    $price = $items['price'];

                                            /*$itog_price = 0;

                                            $guarantee = $items['guarantee'];
                                            $spec_koeff = $items['spec_koeff'];
                                            $discount = $items['discount'];*/

        /*                                    $percent_cats = $items['percent_cats'];
                                            $work_percent = $items['work_percent'];
                                            $material_percent = $items['material_percent'];

                                            /*if ($itog_price == 0) {
                                                $itog_price_add = $price;
                                            } else {
                                                $itog_price_add = $itog_price;
                                            }*/


                                            /*if (!empty($mat_cons_j_ex['data'])){
                                                if (isset($mat_cons_j_ex['data'][$pos_id])){
                                                    $itog_price_add = $itog_price_add - $mat_cons_j_ex['data'][$pos_id];
                                                }else{
                                                }
                                            }else{
                                            }*/


                                            /*if ($guarantee != 0) {
                                                $itog_price_add = 0;
                                            }*/

                                            //Добавляем в базу
                                            /*$query = "INSERT INTO `fl_journal_calculate_ex` (`calculate_id`, `ind`, `price_id`, `inv_pos_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`, `percent_cats`, `work_percent`, `material_percent`)
                                            VALUES (
                                            '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$pos_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}', '{$percent_cats}', '{$work_percent}', '{$material_percent}')";*/

                                            //Обновляем
                                            /*$query = "UPDATE `fl_journal_calculate_ex` SET
                                            `paid`='$payed', `status`='0', `closed_time`='0'  WHERE `id`='{$_POST['invoice_id']}'";*/

                                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            /*$price = $price * $quantity;

                                            $price = ($price - ($price * $discount / 100));

                                            if ($itog_price == 0) {
                                                $itog_price = $price;
                                            }

                                            if ($guarantee != 0) {
                                                $itog_price = 0;
                                            }*/

        /*                                    $itog_price = $price;

                                            //$calculateInvSumm +=  round($price);
                                            $calculateInvSumm += $itog_price;

                                            /*if (!empty($mat_cons_j_ex['data'])) {
                                                if (isset($mat_cons_j_ex['data'][$pos_id])) {
                                                    $itog_price = $itog_price - $mat_cons_j_ex['data'][$pos_id];
                                                } else {
                                                }
                                            } else {
                                            }*/

                                            /*if ($itog_price < 0) $itog_price = 0;*/

                                            //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
        /*                                    $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);
                                        }

                                        /*if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
                                            $mkb_data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
                                            foreach ($mkb_data as $mkb_id){
                                                //Добавляем в базу МКБ
                                                $query = "INSERT INTO `journal_invoice_ex_mkb` (`invoice_id`, `ind`, `mkb_id`)
                                                VALUES (
                                                '{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";

                                                mysql_query($query) or die(mysql_error().' -> '.$query);
                                            }
                                        }*/

                                        //}

                                        /*if ($_POST['invoice_type'] == 6) {

                                            $price_id = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price_id'];
                                            $quantity = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['quantity'];
                                            $insure = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure'];
                                            $insure_approve = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure_approve'];
                                            $price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price'];

                                            $itog_price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['itog_price'];

                                            $guarantee = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['guarantee'];
                                            $spec_koeff = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['spec_koeff'];
                                            $discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['discount'];

                                            $percent_cats = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['percent_cats'];
                                            $work_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['work_percent'];
                                            $material_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['material_percent'];

                                            if ($itog_price == 0){
                                                $itog_price_add = $price;
                                            }

                                            //Добавляем в базу
                                            $query = "INSERT INTO `fl_journal_calculate_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`)
                                            VALUES (
                                            '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}')";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            $price = $price*$quantity;

                                            $price =  ($price - ($price * $discount / 100));

                                            //$calculateInvSumm +=  round($price);
                                            $calculateInvSumm += $itog_price;

                                            //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
                                            $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);

                                        }*/
                                        //unset($_SESSION['calculate_data']);
        /*                            }
                                }
                            }


                        }



                        /*$query = "SELECT `calculate_id` FROM `fl_journal_calculate_ex` WHERE `calculate_id` IN (SELECT `id` FROM `fl_journal_calculate` WHERE `invoice_id`='$invoice_id')".$query_dop." GROUP BY `calculate_id`";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($calculate_data_array, $arr);
                            }
                        }else{

                        }*/


                        //Обновим сумму в расчете
        /*                if ($calculateInvSumm > 0) {
                            $query = "UPDATE `fl_journal_calculate` SET `summ_inv`='{$calculateInvSumm}', `summ`='{$calculateCalcSumm}' WHERE `id`='{$data['calculate_id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        }

                    }
                }


            }

        }*/



        CloseDB ($msql_cnnct);

        return;
    }



    //функция для создания шаблона табеля (оасчетного листа) для печати
    function tabelPrintTemplate ($tabel_id, $month, $year, $worker_fio, $filial, $countSmen, $tabel_summ, $tabel_deductions_j2, $tabel_surcharges_j2, $tabel_deductions_j3, $tabel_surcharges_j3, $tabel_deductions_j4, $tabel_surcharges_j1, $tabel_deductions_j5, $emptySmenaCount, $emptySmenaPrice, $emptySmenaSumm, $tabel_paidouts_j1, $tabel_paidouts_j4, $tabel_paidouts_j2, $nightSmenaCount, $nightSmenaPrice, $nightSmenaSumm, $tabel_paidouts_j3){

        $rezult = '
                <div class="rezult_item" style="font-size: 95%; margin: 15px;" fio="'.$worker_fio.'">				
                    <div class="filterBlock" style="width: 650px; border-bottom: 1px dotted grey;">
                        <div class="filtercellLeft" style="width: 400px; text-align: center; border: none; padding-bottom: 2px;">
                            <a href="fl_tabel.php?id='.$tabel_id.'" class="ahref">Расчетный листок '.$tabel_id.'</a> за '.$month.' '.$year.'
                        </div>
                    </div>
                    
                    <div class="filterBlock" style="width: 650px; ">
                        <div class="filtercellLeft" style="border: none; width: auto; min-width: auto; max-width: auto; padding: 2px 2px 1px;">
                            <div style="padding: 2px 0 3px; font-size: 115%; font-weight: bold;">
                                <i>'.$worker_fio.'</i>
                            </div>
                            <div style="background-color: rgba(144,247,95, 0.4); font-size: 130%; padding: 5px 5px 2px;">
                                <div style="display: inline;">К выплате:</div>
                                <div style="float: right; display: inline; text-align: right; font-size: 110%;"><b><i><div class="pay_must_'.$tabel_id.'" style="display: inline;">0</div> р.</i></b></div>
                            </div>
                            
                        </div>
                        <div class="filtercellRight" style="border: none; width: 290px; min-width: 290px; max-width: 290px; padding: 2px 2px 1px;">
                            <div style="border-bottom: 1px dotted grey;">
                                <div style="display: inline;">Подразделение</div>
                                <div style="float: right; display: inline; text-align: right;"><b>'.$filial.'</b></div>
                            </div>
                            <div style="border-bottom: 1px dotted grey;">
                                <div style="display: inline;">Норма смен/дней</div>
                                <div style="float: right; display: inline; text-align: right;">-</div>
                            </div>
                            <div style="border-bottom: 1px dotted grey;">
                                <div style="display: inline;">Часов в смене</div>
                                <div style="float: right; display: inline; text-align: right;">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filterBlock" style="width: 650px;">
                    
                        <table width="100%" style="border: 2px solid #525252; border-collapse: collapse;">
                            <tr>
                                <td rowspan="2" style="width: 100px; text-align: center; border: 2px solid #525252;">
                                    Вид
                                </td>
                                <td rowspan="2" style="width: 60px; text-align: center; border: 2px solid #525252;">
                                    тариф
                                </td>
                                <td colspan="2" style="width: 70px; text-align: center; border: 2px solid #525252;">
                                    период
                                </td>
                                <td rowspan="2" style="width: 80px; text-align: center; border: 2px solid #525252;">
                                    Сумма
                                </td>
                                <td rowspan="2" style="width: 100px; text-align: center; border: 2px solid #525252;">
                                    Вид
                                </td>
                                <td style="width: 70px; text-align: center; border: 2px solid #525252;">
                                    период
                                </td>
                                <td rowspan="2" style="width: 80px; text-align: center; border: 2px solid #525252;">
                                    Сумма
                                </td>
                            </tr>
                            
                            
                            <tr>
                                <td style="width: 35px; text-align: center; border: 1px solid #BFBCB5; font-size: 80%;">
                                    дней
                                </td>
                                <td style="width: 35px; text-align: center; border: 1px solid #BFBCB5; font-size: 80%;">
                                    часов
                                </td>
                                    <td style="text-align: center; border: 1px solid #BFBCB5;">
                                    дней
                                </td>
                            </tr>  
                             
                            <tr>
                                <td colspan="5" style="text-align: left; border: 2px solid #525252; padding: 3px 0 3px 3px;">
                                    <b>1. Начислено</b>
                                </td>
                                <td colspan="3" style="text-align: left; border: 2px solid #525252; padding: 3px 0 3px 3px;">
                                    <b>2. Удержано</b>
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    з/п
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    '.$countSmen.'
                                </td>
                                <td  class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_summ.'
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    налог
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_deductions_j2.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    отпускные
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_surcharges_j2.'
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    штраф
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_deductions_j3.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    больничный
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_surcharges_j3.'
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    ссуда
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_deductions_j4.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    премия
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_surcharges_j1.'
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    обучение
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part1_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_deductions_j5.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr style="border: 2px solid #525252;">
                                <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 2px 0 1px 3px;">
                                    <b><i>Всего начислено</i></b>
                                </td>
                                <td style="text-align: right; border: 1px solid #BFBCB5; padding-right: 3px; font-size: 110%;">
                                    <b><div class="pay_plus1_'.$tabel_id.'" style="display: inline;">0</div> р.</b>
                                </td>
                                <td colspan="2" style="text-align: left; border-left: 2px solid #525252; padding: 2px 0 1px 3px;">
                                    <b><i>Всего удержано</i></b>
                                </td>
                                <td style="text-align: right; border: 1px solid #BFBCB5; padding: 2px 0 1px 3px; font-size: 110%;">
                                    <b><div class="pay_minus1_'.$tabel_id.'" style="display: inline;">0</div> р.</b>
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="5" style="text-align: left; border: 2px solid #525252; padding: 3px 0 3px 3px;">
                                    <b>3. Прочее</b>
                                </td>
                                <td colspan="3" style="text-align: left; border: 2px solid #525252; padding: 3px 0 3px 3px;">
                                    <b>4. Выплачено</b>
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    пустые смены
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">';

        if ($emptySmenaCount != 0){
            $rezult .= $emptySmenaPrice.' р.';
        }

        $rezult .= '
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">';

        if ($emptySmenaCount != 0){
            $rezult .= $emptySmenaCount;
        }

        $rezult .= '
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part2_'.$tabel_id.'" style="display: inline;">';

        //if ($emptySmenaCount != 0){
            $rezult .= $emptySmenaSumm;
        //}

        $rezult .= '
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    аванс
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part2_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_paidouts_j1.'
                                    </div> р.
                                </td>
                            </tr>	
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    ночные смены
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">';

        if ($nightSmenaCount != 0){
            $rezult .= $nightSmenaPrice.' р.';
        }

        $rezult .= '
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">';

        if ($nightSmenaCount != 0){
            $rezult .= $nightSmenaCount;
        }

        $rezult .= '
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part2_'.$tabel_id.'" style="display: inline;">';

        //if ($nightSmenaCount != 0){
            $rezult .= $nightSmenaSumm;
        //}

        $rezult .= '
                                    </div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    отпускные
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part2_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_paidouts_j2.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part2_'.$tabel_id.'" style="display: inline;">0</div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    больничный
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part2_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_paidouts_j3.'
                                    </div> р.
                                </td>
                            </tr>
                             
                            <tr>
                                <td class="border_tabel_print" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_plus_part2_'.$tabel_id.'" style="display: inline;">0</div> р.
                                </td>
                                <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
                                    на карту
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    
                                </td>
                                <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
                                    <div class="pay_minus_part2_'.$tabel_id.'" style="display: inline;">
                                        '.$tabel_paidouts_j4.'
                                    </div> р.
                                </td>
                            </tr>
                            
                            <tr style="border: 2px solid #525252;">
                                <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 2px 0 1px 3px;">
                                    <b><i>Всего</i></b>
                                </td>
                                <td style="text-align: right; border: 1px solid #BFBCB5; padding-right: 3px; font-size: 110%;">
                                    <b><div class="pay_plus2_'.$tabel_id.'" style="display: inline;">0</div> р.</b>
                                </td>
                                <td colspan="2" style="text-align: left; border-left: 2px solid #525252; padding: 2px 0 1px 3px;">
                                    <b><i>Всего выплачено</i></b>
                                </td>
                                <td style="text-align: right; border: 1px solid #BFBCB5; padding-right: 3px; font-size: 110%;">
                                    <b><div class="pay_minus2_'.$tabel_id.'" style="display: inline;">0</div> р.</b>
                                </td>
                            </tr>
                             
                        </table>
                    
                    </div>						
                </div>';

        return $rezult;

    }

    //Рассчет РЛ и сохранение в БД
    function calculateCalculateSave (
        $data, $zapis_id, $invoice_id, $filial_id, $client_id, $worker_id, $invoice_type, $summ, $discount, $author
    ){

        $calculateInvSumm = 0;
        $calculateCalcSumm = 0;

        $msql_cnnct = ConnectToDB2();

        $time = date('Y-m-d H:i:s', time());

        //$discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['discount'];

        //Добавляем в базу
        $query = "INSERT INTO `fl_journal_calculate` (`zapis_id`, `invoice_id`, `office_id`, `client_id`, `worker_id`, `type`, `summ_inv`, `discount`, `summ`, `date_in`, `create_person`, `create_time`) 
                            VALUES (
                            '{$zapis_id}', '{$invoice_id}', '{$filial_id}', '{$client_id}', '{$worker_id}', '{$invoice_type}', '{$summ}', '{$discount}', '{$summ}', '{$time}', '{$author}', '{$time}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        //ID новой позиции
        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        //Затраты на материалы
        $mat_cons_j_ex = array();

        $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                                    LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                                    ON jimc.id = jimcex.inv_mat_cons_id
                                    WHERE jimc.invoice_id = '".$invoice_id."';";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {

                //array_push($mat_cons_j, $arr);

                if (!isset($mat_cons_j_ex['data'])){
                    $mat_cons_j_ex['data'] = array();
                }

                if (!isset($mat_cons_j_ex['data'][$arr['inv_pos_id']])){
                    $mat_cons_j_ex['data'][$arr['inv_pos_id']] = $arr['summ'];
                }

                $mat_cons_j_ex['create_person'] = $arr['create_person'];
                $mat_cons_j_ex['create_time'] = $arr['create_time'];
                $mat_cons_j_ex['all_summ'] = $arr['all_summ'];
                $mat_cons_j_ex['descr'] = $arr['descr'];
                $mat_cons_j_ex['id'] = $arr['mc_id'];
            }
        } else {

        }

        foreach ($data as $ind => $calculate_data) {

            if (!empty($calculate_data)) {
                if ($invoice_type == 5) {
                    foreach ($calculate_data as $key => $items) {

                        $pos_id = $items['id'];
                        $price_id = $items['price_id'];
                        $quantity = $items['quantity'];
                        $insure = $items['insure'];
                        $insure_approve = $items['insure_approve'];
                        $price = $items['price'];

                        if (isset($items['itog_price'])){
                            $itog_price = $items['itog_price'];
                        }else{
                            $itog_price = $price;
                        }

                        $guarantee = $items['guarantee'];
                        $spec_koeff = $items['spec_koeff'];
                        $discount = $items['discount'];

                        $percent_cats = $items['percent_cats'];
                        $work_percent = $items['work_percent'];
                        $material_percent = $items['material_percent'];

                        //Спрятали лишнее телодвижение
                        //
                        /*if ($itog_price == 0){
                            $itog_price_add = $price;
                        }else{
                            $itog_price_add = $itog_price;
                        }*/

                        $itog_price_add = $itog_price;

                        /*if (!empty($mat_cons_j_ex['data'])){
                            if (isset($mat_cons_j_ex['data'][$pos_id])){
                                $itog_price_add = $itog_price_add - $mat_cons_j_ex['data'][$pos_id];
                            }else{
                            }
                        }else{
                        }*/

                        //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                        /*if ($guarantee != 0){
                            $itog_price_add = 0;
                        }*/

                        if ($guarantee == 0) {

                            //Добавляем в базу
                            $query = "INSERT INTO `fl_journal_calculate_ex` (`calculate_id`, `ind`, `price_id`, `inv_pos_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`, `percent_cats`, `work_percent`, `material_percent`) 
                                                VALUES (
                                                '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$pos_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}', '{$percent_cats}', '{$work_percent}', '{$material_percent}')";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $price = $price * $quantity;

                            $price = ($price - ($price * $discount / 100));

                            if ($itog_price == 0) {
                                $itog_price = $price;
                            }

                            //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                            /*if ($guarantee != 0){
                                $itog_price = 0;
                            }*/

                            //$calculateInvSumm +=  round($price);
                            $calculateInvSumm += $itog_price;

                            if (!empty($mat_cons_j_ex['data'])) {
                                if (isset($mat_cons_j_ex['data'][$pos_id])) {
                                    $itog_price = $itog_price - $mat_cons_j_ex['data'][$pos_id];
                                } else {
                                }
                            } else {
                            }

                            if ($itog_price < 0) $itog_price = 0;

                            //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
                            $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);
                        }
                    }

                    /*if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
                        $mkb_data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
                        foreach ($mkb_data as $mkb_id){
                            //Добавляем в базу МКБ
                            $query = "INSERT INTO `journal_invoice_ex_mkb` (`invoice_id`, `ind`, `mkb_id`)
                            VALUES (
                            '{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";

                            mysql_query($query) or die(mysql_error().' -> '.$query);
                        }
                    }*/

                }

                if ($invoice_type == 6) {

                    $pos_id = $calculate_data['id'];
                    $price_id = $calculate_data['price_id'];
                    $quantity = $calculate_data['quantity'];
                    $insure = $calculate_data['insure'];
                    $insure_approve = $calculate_data['insure_approve'];
                    $price = $calculate_data['price'];

                    if (isset($calculate_data['itog_price'])){
                        $itog_price = $calculate_data['itog_price'];
                    }else{
                        $itog_price = $price;
                    }

                    $guarantee = $calculate_data['guarantee'];
                    $spec_koeff = $calculate_data['spec_koeff'];
                    $discount = $calculate_data['discount'];

                    $percent_cats = $calculate_data['percent_cats'];
                    $work_percent = $calculate_data['work_percent'];
                    $material_percent = $calculate_data['material_percent'];

                    $itog_price_add = $itog_price;


                    if ($guarantee == 0) {

                        //Добавляем в базу
                        $query = "INSERT INTO `fl_journal_calculate_ex` (`calculate_id`, `ind`, `price_id`, `inv_pos_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`, `percent_cats`, `work_percent`, `material_percent`) 
                                                VALUES (
                                                '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$pos_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$itog_price_add}', '{$guarantee}', '{$spec_koeff}', '{$discount}', '{$percent_cats}', '{$work_percent}', '{$material_percent}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $price = $price * $quantity;

                        $price = ($price - ($price * $discount / 100));

                        if ($itog_price == 0) {
                            $itog_price = $price;
                        }

                        //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                        /*if ($guarantee != 0){
                            $itog_price = 0;
                        }*/

                        //$calculateInvSumm +=  round($price);
                        $calculateInvSumm += $itog_price;

                        if (!empty($mat_cons_j_ex['data'])) {
                            if (isset($mat_cons_j_ex['data'][$pos_id])) {
                                $itog_price = $itog_price - $mat_cons_j_ex['data'][$pos_id];
                            } else {
                            }
                        } else {
                        }

                        if ($itog_price < 0) $itog_price = 0;

                        //$calculateCalcSumm += calculateResult(round($price), $work_percent, $material_percent);
                        $calculateCalcSumm += calculateResult($itog_price, $work_percent, $material_percent);
                    }


                }
                //unset($_SESSION['calculate_data']);
            }
        }

        //Обновим сумму в расчете
        if ($calculateInvSumm > 0) {
            $query = "UPDATE `fl_journal_calculate` SET `summ_inv`='{$calculateInvSumm}', `summ`='{$calculateCalcSumm}' WHERE `id`='{$mysql_insert_id}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
        }

        unset($_SESSION['calculate_data']);

        //!!! @@@ Пересчет долга
        //include_once 'ffun.php';
        //calculateDebt ($_POST['client']);

        return (array('result' => 'success', 'data' => $mysql_insert_id));
    }

    //Для отчета касса
    function ajaxShowResultCashbox ($datastart, $dataend, $filial, $summtype, $certificatesShow){
        //var_dump(func_get_args());

        $msql_cnnct = ConnectToDB2 ();

        $rezult = array();
        $rezult_cert = array();
        $arr = array();

        //Переменная для строчки запроса по филиалу и типу
        $queryFilial = '';
        $queryType = '';

        //Филиал
        if ($filial != 99){
            $queryFilial .= "AND `office_id` = '".$filial."'";
        }

        if ($summtype != 0){
            $queryType .= "AND `summ_type` = '".$summtype."'";
        }

        //Приход денег вытащим
        $query = "SELECT * FROM `journal_order` WHERE
                `date_in` BETWEEN 
                STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                AND 
                STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                ".$queryFilial.$queryType." AND `org_pay` <> '1'
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
                    ".$queryFilial.$queryType."
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

        $result['rezult'] = $rezult;
        $result['rezult_cert'] = $rezult_cert;

        return $result;

    }


?>