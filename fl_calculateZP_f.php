<?php

//fl_calculateZP_f.php
//получаем выручку со всех филиалов (смотрим только закрытые работы)

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

//        if ($_POST['month'] < 10) {
//            $month = '0'.$_POST['month'];
//        }else{
//            $month = $_POST['month'];
//        }

        $month = dateTransformation ($_POST['month']);

        //$data_temp_arr = explode(".", $_POST['datastart']);
        $datastart = $_POST['year'].'-'.$month.'-01';

        //последний день мясяца (Дата)
        $d = new DateTime($datastart);
        //$day = $d->format('Y-m-t');

        //$data_temp_arr = explode(".", $_POST['dataend']);
        $dataend = $d->format('Y-m-t');

//        //Суммы нарядов, закрытых в указанном месяце
//        $query = "SELECT ji.office_id AS filial_id, ji.summ, z.noch
//        FROM `journal_invoice` ji
//        LEFT JOIN `zapis` z ON ji.zapis_id = z.id
//        WHERE MONTH(ji.closed_time) = '{$month}' AND YEAR(ji.closed_time) = '{$_POST['year']}'";
//
//        //Если ассистент, то только стоматология
//        if ($_POST['typeW'] == 7){
//            $query = "SELECT ji.office_id AS filial_id, ji.summ, z.noch
//            FROM `journal_invoice` ji
//            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
//            WHERE MONTH(ji.closed_time) = '{$month}' AND YEAR(ji.closed_time) = '{$_POST['year']}' AND ji.type = '5'";
//
//        }

        //До сентября 2019 считалось так
        //Смотрим оплаты по нарядам, которые администраторы внесли в указанном месяце
        //20190902 добавили  || TRUE и продолжили пока дальше так считать
        if ((($month < '08') AND ($_POST['year'] == 2019)) || ($_POST['year'] < 2019) || TRUE){
            $query = "SELECT jp.filial_id, jp.summ, z.noch
            FROM `journal_payment` jp
            LEFT JOIN `journal_invoice` ji ON ji.id = jp.invoice_id
            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
            WHERE MONTH(jp.date_in) = '{$month}' AND YEAR(jp.date_in) = '{$_POST['year']}'";

            //Если ассистент, то только стоматология
            if ($_POST['typeW'] == 7) {
                $query = "SELECT jp.filial_id, jp.summ, z.noch
                FROM `journal_payment` jp
                INNER JOIN `journal_invoice` ji ON ji.id = jp.invoice_id AND ji.type = '5'
                LEFT JOIN `zapis` z ON ji.zapis_id = z.id
                WHERE MONTH(jp.date_in) = '{$month}' AND YEAR(jp.date_in) = '{$_POST['year']}'";
            }
        }

        //var_dump($query);

        $journal = array();

        $msql_cnnct = ConnectToDB();

        $arr = array();

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                //Исключаем ночные
                if ($arr['noch'] != 1) {
//                    if (!isset($journal[$arr['office_id']])){
//                        $journal[$arr['office_id']] = array();
//                    }
//                    array_push($journal[$arr['office_id']], $arr);
                    if (!isset($journal[$arr['filial_id']])){
                        //$journal[$arr['filial_id']] = array();
                        $journal[$arr['filial_id']] = 0;
                    }
                    //array_push($journal[$arr['filial_id']], $arr);
                    $journal[$arr['filial_id']] += $arr['summ'];
                }
            }
        }
        //var_dump($journal);

        //Наряды по стоматологическим страховым работам
        $invoices_ins_j = array();
        //Сумма по наряды по закрытым в этом месяце стоматологическим страховым работам
        $invoices_ins_summ = 0;

        $query = "
            SELECT ji.office_id AS filial_id, ji.summins, z.noch
            FROM `journal_invoice` ji
            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
            WHERE ji.type='5' AND MONTH(ji.closed_time) = '{$month}' AND YEAR(ji.closed_time) = '{$_POST['year']}'
            AND ji.summins <> '0' AND ji.status = '5'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                //Исключаем ночные
                if ($arr['noch'] != 1) {
                    //array_push($invoices_ins_j, $arr);
                    if (!isset($journal[$arr['filial_id']])){
                        $journal[$arr['filial_id']] = 0;
                    }
                    $journal[$arr['filial_id']] += $arr['summins'];
                }
            }
        }

        $summ_arr = $journal;


        //Солярий
        $summ_solar = array();

        $query = "SELECT * FROM `journal_solar` WHERE
                     MONTH(`date_in`) = '{$month}' AND YEAR(`date_in`) = '{$_POST['year']}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                if (!isset($summ_solar[$arr['filial_id']])){
                    $summ_solar[$arr['filial_id']] = array();
                    $summ_solar[$arr['filial_id']]['solar'] = 0;
                    $summ_solar[$arr['filial_id']]['realiz'] = 0;
                    $summ_solar[$arr['filial_id']]['abon'] = 0;
                }
                $summ_solar[$arr['filial_id']]['solar'] += $arr['summ'];
            }
        }
        //Реализация
        $query = "SELECT * FROM `journal_realiz` WHERE
                     MONTH(`date_in`) = '{$month}' AND YEAR(`date_in`) = '{$_POST['year']}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                if (!isset($summ_solar[$arr['filial_id']])){
                    $summ_solar[$arr['filial_id']] = array();
                    $summ_solar[$arr['filial_id']]['solar'] = 0;
                    $summ_solar[$arr['filial_id']]['realiz'] = 0;
                    $summ_solar[$arr['filial_id']]['abon'] = 0;
                }
                $summ_solar[$arr['filial_id']]['realiz'] += $arr['summ'];
            }
        }
        //Абонементы
        $query = "SELECT * FROM `journal_abonement_solar` WHERE
                     MONTH(`cell_time`) = '{$month}' AND YEAR(`cell_time`) = '{$_POST['year']}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                if (!isset($summ_solar[$arr['filial_id']])){
                    $summ_solar[$arr['filial_id']] = array();
                    $summ_solar[$arr['filial_id']]['solar'] = 0;
                    $summ_solar[$arr['filial_id']]['realiz'] = 0;
                    $summ_solar[$arr['filial_id']]['abon'] = 0;
                }
                $summ_solar[$arr['filial_id']]['abon'] += $arr['summ'];
            }
        }

        //Делаем рассчеты
        //Выводим результат
        if (!empty($journal)) {

            echo json_encode(array('result' => 'success', 'data' => $summ_arr, 'data_solar' => $summ_solar, 'msg' => ''));

        } else {
            echo json_encode(array('result' => 'empty', 'data' => array(), 'data_solar' => array(), 'msg' => '<div class="query_neok">Ничего не найдено</div>'));
        }
    }
}
?>