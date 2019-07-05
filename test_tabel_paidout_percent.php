<?php
	
//test_tabel_paidout_percent.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';

    $msql_cnnct = ConnectToDB ();

//    $payments_j = array();
//
//    //Проведенные оплаты
//    $query = "
//            SELECT jp.filial_id, jp.summ, jcalc.invoice_id/*,
//            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats*/
//            FROM `fl_journal_calculate` jcalc
//            /*LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id*/
//            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '988'
//            LEFT JOIN `journal_payment` jp ON jp.invoice_id = jcalc.invoice_id
//            LEFT JOIN `journal_payment` jp ON jp.invoice_id = jcalc.invoice_id
//            WHERE jtabex.calculate_id = jcalc.id
//            GROUP BY jcalc.invoice_id";
//
////    $query = "
////    SELECT jp.*
////    FROM `journal_payment` jp
////    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
////    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
////    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
////    WHERE jp.invoice_id = ji.id";
//
//    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//    $number = mysqli_num_rows($res);
//
//    if ($number != 0){
//        while ($arr = mysqli_fetch_assoc($res)){
//            array_push($payments_j, $arr);
//        }
//    }
//
//    var_dump($payments_j);

    $invoices_j = array();

    //Наряды
    $query = "
            SELECT jcalc.invoice_id, ji.office_id AS filial_id
            FROM `fl_journal_calculate` jcalc
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '988'
            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
            WHERE jtabex.calculate_id = jcalc.id AND jtabex.noch = '0'
            GROUP BY jcalc.invoice_id";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            $invoices_j[$arr['invoice_id']] = $arr['filial_id'];
        }
    }

    echo 'Наряды';
    var_dump($invoices_j);

    //Оплаты
    $payments_j = array();
    //Все рассчетные листы
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

        //Получаем все оплаты
        $query = "
            SELECT *
            FROM `journal_payment`
            WHERE `invoice_id` = '{$invoice_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($payments_j, $arr);
            }
        }

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

    echo 'Оплаты';
    var_dump($payments_j);

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
    var_dump($filial_insure_calculate_summ);



require_once 'footer.php';
	
?>