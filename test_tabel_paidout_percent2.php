<?php
	
//test_tabel_paidout_percent.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';
    include_once 'functions.php';

    require 'variables.php';


    $tabel_id = 988;
    $worker_id = 288;

    $filials_j = getAllFilials(false, false, false);
    //var_dump($filials_j);

    $msql_cnnct = ConnectToDB ();

    $invoices_j = array();

    //Итоговый массив, куда соберем общие суммы по филиалам
    $itog_filials_summ = array();
    //Итоговый массив, куда соберем суммы по филиалам по тем работам,
    // которые мы хотим и должны оплатить другому человеку
    $itog_filials_summ_not4tou = array();

    //Наряды
    $query = "
            SELECT jcalc.invoice_id, ji.office_id AS filial_id, ji.status AS status
            FROM `fl_journal_calculate` jcalc
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }'
            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
            WHERE jtabex.calculate_id = jcalc.id
            GROUP BY jcalc.invoice_id";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            $invoices_j[$arr['invoice_id']]['filial_id'] = $arr['filial_id'];
            $invoices_j[$arr['invoice_id']]['status'] = $arr['status'];
        }
    }

    echo '

    <div id="fact"></div>
    
    
    <span style="font-size: 85%;">Наряды (Филиал / статус)</span><br>';
    //var_dump($invoices_j);

    foreach ($invoices_j as $invoice_id => $invoice_item){
        //($invoice_item['status'] == 5)  - работа закрыта

        echo '<div style="width: 190px; margin-bottom: 5px; border: 1px solid rgba(191, 188, 181, 0.53);">';

        //Рисуем кнопку-ссылку на наряд
        echo '
            <div style="margin: 2px 0;">
                <a href="invoice.php?id='.$invoice_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;">
                    '.$invoice_id.' ('.$filials_j[$invoice_item['filial_id']]['name2'].' / '.$invoice_item['status'].')
                </a>';
        if ($invoice_item['status'] == 5){
            echo '<i class="fa fa-plus" style="color: green; font-size: 120%;"></i>';
        }
        echo '
            </div>';

        //Позиции, которые прошли как подарки пациентам,
        //но нам же надо дать ЗП за них,
        //НЕ разбитые по филиалам, на которых был сделан наряд
        $gift_invoice_ex = array();
        //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
        $gift_invoice_summ = 0;

        //Получаем позиции, которые прошли как подарки пациентам
        $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `gift` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //var_dump($arr);

                //Сразу добавляем в итоговый массив,
                //предварительно добавив в массив элемент с ID филиала, если его не было
                if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                    $itog_filials_summ[$invoice_item['filial_id']] = 0;
                }
                $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];


//                array_push($gift_invoice_ex, $arr);
//                if (!isset($gift_invoice_summ[$invoice_item['filial_id']])){
//                    $gift_invoice_summ[$filial_id] = 0;
//                }

                $gift_invoice_summ += $arr['itog_price'];
            }
        }
//        var_dump($gift_invoice_ex);
//        var_dump($gift_invoice_summ);

        //Если были подарочные позиции по наряду
        if ($gift_invoice_summ > 0){
            //Нарисуем полученные оплаты
            //foreach ($payments_j as $filial_id => $payment_item) {
                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(255, 235, 64, 0.42);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $gift_invoice_summ . '</b></span></i></div>';
            //}
        }


        //Оплаты
        $payments_j = array();

        //Получаем все оплаты по текущему наряду
        $query = "
            SELECT *
            FROM `journal_payment`
            WHERE `invoice_id` = '{$invoice_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //array_push($payments_j, $arr);
                //Раскидаем суммы оплат сразу по филиалам
                if (!isset($payments_j[$arr['filial_id']])){
                    $payments_j[$arr['filial_id']]['summ'] = 0;
                }
                $payments_j[$arr['filial_id']]['summ'] += $arr['summ'];

                //Сразу добавляем в итоговый массив,
                //предварительно добавив в массив элемент с ID филиала, если его не было
                if (!isset($itog_filials_summ[$arr['filial_id']])){
                    $itog_filials_summ[$arr['filial_id']] = 0;
                }
                $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

            }
        }

        //echo '<span style="font-size: 85%;">Оплаты: Филиал -> Сумма</span><br>';
        //var_dump($payments_j);
        //Если оплаты оп наряду есть
        if (!empty($payments_j)) {
            //Нарисуем полученные оплаты
            foreach ($payments_j as $filial_id => $payment_item) {
                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 220, 14, 0.2);">' . $filials_j[$filial_id]['name2'] . ' ->  <b style="font-size: 105%;">' . $payment_item['summ'] . '</b></span></i></div>';
            }
        }else{

            //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
            //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд

            $filial_insure_invoice_ex = array();

            $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `insure` <> '0' AND `insure_approve` = '1'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            //Если что-то нашли страхового
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    if (!isset($filial_insure_invoice_ex[$invoice_item['filial_id']])){
                        $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] = 0;
                    }
                    $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] += $arr['itog_price'];

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                        $itog_filials_summ[$invoice_item['filial_id']] = 0;
                    }
                    $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];

                }

                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 175, 220, 0.2);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] . '</b></span></i></div>';

            }else{

                if ($gift_invoice_summ > 0){
                    //Уж даже и не страховой наряд и нет там подарков
                   echo '<i><span style="color: red; font-size: 80%;">нет оплат (наряд скорее всего "нулевой", РЛ к нему можно было бы и не создавать)</span></i>';
                }/*else{
                    echo '<i><span style="color: red; font-size: 80%;">Ошибка #54. Требуется тщательная проверка.</span></i>';
                }*/
            }
        }

        //А теперь выберем РЛ, которые были не этому исполнителю.
        //Ибо может быть так, что наряд один, а исполнителей больше.
        //Выберем их и вычтем суммы из общих
        //... а может потом и не придется, заставим админов делать отдельные наряды
        $query = "SELECT * FROM `fl_journal_calculate` WHERE  `invoice_id` = '{$invoice_id}'  AND `worker_id` <> '{$worker_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        //Если нашли РЛ, которые не принадлежат указанному в табеле исполнителю
        //... и тут на самом деле жопа, потому что денег могли принести в одном, а работу сделают на другом
        //... и ппц
        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {

                //Сразу добавляем в итоговый массив,
                //предварительно добавив в массив элемент с ID филиала, если его не было
                //... Тут у нас опять филиал берётся по наряду, что наверное не есть хорошо,
                //... но пока так
                if (!isset($itog_filials_summ_not4tou[$invoice_item['filial_id']])){
                    $itog_filials_summ_not4tou[$invoice_item['filial_id']] = 0;
                }
                $itog_filials_summ_not4tou[$invoice_item['filial_id']] += $arr['summ_inv'];
            }
        }


        echo '</div>';
    }

    //Сумма на всех филиалах
    $itog_all_filial_summ = 0;

    //Итог сумм, с которых надо выдать ЗП, по филиалам
    arsort($itog_filials_summ);
    var_dump($itog_filials_summ);

    $itog_all_filial_summ = array_sum($itog_filials_summ);
    var_dump($itog_all_filial_summ);

    //Нам нужен (?) последний ключ массива для
    //дальнейшей работы с ним
//    end($itog_filials_summ);
//    $last_key = key($itog_filials_summ);
//    var_dump($last_key);

    //Массив с процентами по филиалам
    $itog_filials_percents = array();

    //Вычислим процентное соотношение
    foreach ($itog_filials_summ as $filial_id => $summ){

        $percent_value = 0;

        //предварительно добавляем в массив элемент с ID филиала, если его не было
        //!!! потом сделать это выше, когда суммы собираем
        if (!isset($itog_filials_percents[$filial_id])){
            $itog_filials_percents[$filial_id] = 0;
        }

        $percent_value = (100* $summ) / $itog_all_filial_summ;

        $itog_filials_percents[$filial_id] = $percent_value;
    }
    var_dump($itog_filials_percents);
    var_dump(array_sum($itog_filials_percents));




/*    //Все рассчетные листы
    $calculates_j = array();
    //Позиции, которые прошли по страховой (денег в кассе нет, а зп выдать надо с этого филиала)
    $insure_invoice_ex = array();
    //Позиции, которые прошли как подарки пациентам,
    //но нам же надо дать ЗП за них,
    //разбитые по филиалам, на которых был сделан наряд
    $gift_invoice_ex = array();
    //Сумма позиций с подарками, разбитые по филиалам, на которых юыл сделан наряд
    $gift_invoice_summ = array();

    //Пройдем по всем нарядам
    foreach ($invoices_j as $invoice_id => $filial_id){



        //Получаем все рассчетные листы
        $query = "
            SELECT *
            FROM `fl_journal_calculate`
            WHERE `invoice_id` = '{$invoice_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($calculates_j, $arr);
            }
        }

        //Получаем позиции, которые прошли как подарки пациентам
        $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `gift` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                if (!isset($gift_invoice_ex[$filial_id])){
                    $gift_invoice_ex[$filial_id] = array();
                }
                array_push($gift_invoice_ex[$filial_id], $arr);
                if (!isset($gift_invoice_summ[$filial_id])){
                    $gift_invoice_summ[$filial_id] = 0;
                }
                $gift_invoice_summ[$filial_id] += $arr['price'];
            }
        }
    }

    echo 'Позиции, которые прошли как подарки пациентам (по филиалам)';
    var_dump($gift_invoice_ex);

    echo 'Суммы позиций, которые прошли по страховой (по филиалам)';
    var_dump($gift_invoice_summ);


    //На каких филиалах какие оплаты были произведены
    $filial_payments = array();

    //Пройдемся по оплатам
    foreach ($payments_j as $payment_item){
        if (!isset($filial_payments[$payment_item['filial_id']])){
            $filial_payments[$payment_item['filial_id']] = 0;
        }
        $filial_payments[$payment_item['filial_id']] += $payment_item['summ'];
    }

    echo 'Суммы оплат по филиалам';
    var_dump($filial_payments);

    echo 'Все рассчетные листы';
    var_dump($calculates_j);

    //Позиции в рассчетных листах, которые прошли по страховой,
    //распределённые по филиалам, где был сделан наряд
    $filial_insure_calculate_ex = array();
    //Суммы, по страховым нарядам, разбитые по филиалам...
    $filial_insure_calculate_summ = array();

    //Пройдемся по рассчетным листам, соберем позиции, где есть страховые
    //добавим сюда филиал, в котором был сделан наряд и далее будем считать, что
    //выплачиваем ЗП за эти страховые работы с того филиала, где сделали наряд
    foreach ($calculates_j as $calculate_item){
        $query = "
            SELECT *
            FROM `fl_journal_calculate_ex`
            WHERE `calculate_id` = '{$calculate_item['id']}'
            AND `insure` <> '0' AND `insure_approve` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                if (!isset($filial_insure_calculate_ex[$calculate_item['office_id']])){
                    $filial_insure_calculate_ex[$payment_item['filial_id']] = array();
                }
                array_push($filial_insure_calculate_ex[$payment_item['filial_id']], $arr);
                if (!isset($filial_insure_calculate_summ[$calculate_item['office_id']])){
                    $filial_insure_calculate_summ[$payment_item['filial_id']] = 0;
                }
                $filial_insure_calculate_summ[$payment_item['filial_id']] += $arr['price'];
            }
        }
    }

    echo 'Позиции в рассчетных листах, которые прошли по страховой (по филиалам)';
    var_dump($filial_insure_calculate_ex);

    echo 'Суммы в рассчетных листах, которые прошли по страховой (по филиалам)';
    var_dump($filial_insure_calculate_summ);*/


require_once 'footer.php';
	
?>