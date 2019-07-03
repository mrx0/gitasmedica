<?php
	
//test_tabel_paidout_percent4.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';
    include_once 'functions.php';

    require 'variables.php';

    //ID табеля
    $tabel_id = 1101;

    $tabel_j = array();

    $filials_j = getAllFilials(false, false, false);
    //var_dump($filials_j);

    $msql_cnnct = ConnectToDB ();

    //Наряды с данными
    $invoices_j = array();
    //Позиции в нарядах
    $invoices_j_ex = array();
    //ID нарядов
    $invoices_ids_arr = array();
    //Оплаты
    $payments_j = array();
    //Итоговый массив, куда соберем общие суммы по филиалам
    $itog_filials_summ = array();

    //Позиции, которые прошли как подарки пациентам,
    //но нам же надо дать ЗП за них
    //НЕ разбиваем по филиалам
    //Филиал мы присвоим от филиала наряда, в котором был подарок
    $gift_invoice_ex = array();
    //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
    //просто чтоб отследить отдельно эту сумму
    $gift_invoice_summ = 0;
    //Страховые позиции в наряде
    //возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
    $filial_insure_invoice_ex = array();
    //Итоговый массив, куда соберем суммы по филиалам по тем работам,
    // которые мы хотим и должны оплатить другому человеку
    $itog_filials_summ_not4tou = array();


    //Получаем табель
    //!!!по сути нам это надо только для того, чтоб получить id worker'a
    //!!!в будущем надо убрать и получать id через POST, как и id табеля
    $query = "SELECT `worker_id` FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        $arr = mysqli_fetch_assoc($res);

        $tabel_j = $arr;
    }
    //var_dump($tabel_j);

    $worker_id = $tabel_j['worker_id'];


    //Наряды с позициями в нарядах + статус (открыт/закрыт) наряда, + филиал
    $query = "
            SELECT ji_ex.*, ji.office_id AS filial_id, ji.status AS status
            FROM `fl_journal_calculate` jcalc
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }'
            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
            RIGHT JOIn `journal_invoice_ex` ji_ex ON ji_ex.invoice_id = ji.id  
            WHERE jtabex.calculate_id = jcalc.id
            ORDER BY `ji_ex`.`invoice_id` ASC";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($invoices_j_ex, $arr);

            array_push($invoices_ids_arr, "`invoice_id`='" . $arr['invoice_id'] . "'");
        }
    }
    //var_dump($invoices_j_ex);.
    //var_dump($invoices_ids_arr);

    //Оставим только уникальные ID
    $invoices_ids_arr = array_unique($invoices_ids_arr);
    //var_dump($invoices_ids_arr);

    //Строчка для следующего запроса
    $invoices_ids_str = implode(' OR ', $invoices_ids_arr);

    //Получаем все оплаты по всем нарядам
    $query = "
            SELECT *
            FROM `journal_payment`
            WHERE ({$invoices_ids_str})";
    //var_dump($query);

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($payments_j, $arr);

            //Раскидаем суммы оплат сразу по филиалам
            //предварительно добавив в массив элемент с ID филиала, если его не было
            if (!isset($itog_filials_summ[$arr['filial_id']])){
                $itog_filials_summ[$arr['filial_id']] = 0;
            }
            $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

        }
    }
    //var_dump($payments_j);
    var_dump($itog_filials_summ);

//    var_dump($invoices_ids_str);
//    var_dump(str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str));

    //Получаем позиции, которые прошли как подарки пациентам
    $query = "
            SELECT ji_ex.*, ji.office_id
            FROM `journal_invoice_ex` ji_ex
            LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
            WHERE (".str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str).")
            AND ji_ex.gift = '1'";
    //echo ($query);

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            //var_dump($arr);

            //Сразу добавляем в итоговый массив,
            //предварительно добавив в массив элемент с ID филиала, если его не было
            if (!isset($itog_filials_summ[$arr['office_id']])){
                $itog_filials_summ[$arr['office_id']] = 0;
            }
            $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

            //Просто чтоб отследить отдельно эту сумму
            $gift_invoice_summ += $arr['itog_price'];
        }
    }
    var_dump($itog_filials_summ);


    //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
    //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
    $query = "
            SELECT ji_ex.*, ji.office_id
            FROM `journal_invoice_ex` ji_ex
            LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
            WHERE (".str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str).")
            AND ji_ex.insure <> '0' AND ji_ex.insure_approve = '1'";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    //Если что-то нашли страхового
    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){

            //Сразу добавляем в итоговый массив,
            //предварительно добавив в массив элемент с ID филиала, если его не было
            if (!isset($itog_filials_summ[$arr['office_id']])){
                $itog_filials_summ[$$arr['office_id']] = 0;
            }
            $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

        }
    }
    var_dump($itog_filials_summ);

    //А теперь выберем РЛ, которые были не этому исполнителю.
    //Ибо может быть так, что наряд один, а исполнителей больше.
    //Выберем их и вычтем суммы из общих
    //... а может потом и не придется, заставим админов делать отдельные наряды
    $query = "
          SELECT * 
          FROM `fl_journal_calculate` 
          WHERE ({$invoices_ids_str}) 
          AND `worker_id` <> '{$worker_id}'";
    //echo($query);

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
            if (!isset($itog_filials_summ_not4tou[$arr['office_id']])){
                $itog_filials_summ_not4tou[$arr['office_id']] = 0;
            }
            $itog_filials_summ_not4tou[$arr['office_id']] += $arr['summ_inv'];
        }
    }
    var_dump($itog_filials_summ_not4tou);

    //Вычтем с филиалов суммы, которые уйдут в зп другому человеку
    //!!!перенести это потом
    foreach ($itog_filials_summ_not4tou as $filial_id => $summ){
        if (isset($itog_filials_summ[$filial_id])){
            $itog_filials_summ[$filial_id] -= $summ;
        }
    }


require_once 'footer.php';
	
?>