<?php

//ajax_show_result_main_report_category_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        //include_once 'DBWork.php';

        include_once('DBWorkPDO.php');

        include_once 'functions.php';

        //разбираемся с правами
        $god_mode = FALSE;

        require_once 'permissions.php';

        $creatorExist = false;
        $workerExist = false;
        $clientExist = false;
        $queryDopExist = false;
        $queryDopExExist = false;
        $queryDopEx2Exist = false;
        $queryDopClientExist = false;
        $query = '';
        $queryDop = '';
        $queryDopEx = '';
        $queryDopEx2 = '';
        $queryDopClient = '';

        $dop = array();

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        $datastart_temp_arr = array();

        //Дополнительные настройки, чтобы передать их дальше
        $dop['zapis']['fullAll'] = $_POST['fullAll'];
        $dop['zapis']['fullWOInvoice'] = $_POST['fullWOInvoice'];
        $dop['zapis']['fullWOTask'] = $_POST['fullWOTask'];
        $dop['zapis']['fullOk'] = $_POST['fullOk'];

        $dop['invoice']['invoiceAll'] = $_POST['invoiceAll'];
        $dop['invoice']['invoicePaid'] = $_POST['invoicePaid'];
        $dop['invoice']['invoiceNotPaid'] = $_POST['invoiceNotPaid'];
        $dop['invoice']['invoiceInsure'] = $_POST['invoiceInsure'];

        $dop['patientUnic'] = $_POST['patientUnic'];
        $dop['withFIO'] = $_POST['withFIO'];


        $db = new DB();

        if (isset($_POST['percent_cat'])){
            if ($_POST['percent_cat'] != 0){
                //Категории
        //        $percents_j = array();
        //
        //        $query = "SELECT `id`, `name`, `type` FROM  `fl_spr_percents`";
        //
        //        $args = [
        //        ];
        //
        //        //Выбрать все
        //        $percents_j = $db::getRows($query, $args);
        //        //var_dump($percents_j);
        //
        //        /*if (!empty($percents_j)) {
        //            if (!isset($percents_j[$arr['type']])){
        //                $percents_j[$arr['type']] = array();
        //            }
        //            $percents_j[$arr['type']][$arr['id']]['name'] = $arr['name'];
        //
        //            if (!isset($percents_j2[$arr['id']])){
        //                $percents_j2[$arr['id']] = array();
        //            }
        //
        //            $percents_j2[$arr['id']]['name'] = $arr['name'];
        //
        //
        //        }*/



                //Кто создал запись
                if ($_POST['creator'] != ''){
                    include_once 'DBWork.php';
                    $creatorSearch = SelDataFromDB ('spr_workers', $_POST['creator'], 'worker_full_name');

                    if ($creatorSearch == 0){
                        $creatorExist = false;
                    }else{
                        $creatorExist = true;
                        $creator = $creatorSearch[0]['id'];
                    }
                }else{
                    $creatorExist = true;
                    $creator = 0;
                }

                //К кому запись
                if ($_POST['worker'] != ''){
                    include_once 'DBWork.php';
                    $workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

                    if ($workerSearch == 0){
                        $workerExist = false;
                    }else{
                        $workerExist = true;
                        $worker = $workerSearch[0]['id'];
                    }
                }else{
                    $workerExist = true;
                    $worker = 0;
                }

                //Клиент
                if ($_POST['client'] != ''){
                    include_once 'DBWork.php';
                    $clientSearch = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');

                    if ($clientSearch == 0){
                        $clientExist = false;
                    }else{
                        $clientExist = true;
                        $client = $clientSearch[0]['id'];
                    }
                }else{
                    $clientExist = true;
                    $client = 0;
                }

                if ($creatorExist && $workerExist) {
                    if ($clientExist) {
                        //$query .= "SELECT `id`, `year`, `month`, `day`, `office`,`worker`, `create_person`, `patient`, `type`, `pervich`, `insured`, `noch`, `enter` FROM `zapis` z";
        //                $query .= "
        //                    SELECT jcalcex.* FROM `zapis` z
        //                    INNER JOIN `fl_journal_calculate` jcalc ON z.id = jcalc.zapis_id
        //                    LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id";

        //                $query = "
        //                            SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS  invoice_summins
        //                            FROM `journal_invoice` ji
        //                            LEFT JOIN `journal_invoice_ex` jiex
        //                            ON ji.id = jiex.invoice_id";

                        $query =
                            "SELECT jiex.invoice_id FROM `journal_invoice_ex` jiex
                            INNER JOIN `journal_invoice` ji
                            ON ji.id = jiex.invoice_id";


                        $data_temp_arr = explode(".", $_POST['datastart']);
                        $_POST['datastart'] = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                        $data_temp_arr = explode(".", $_POST['dataend']);
                        $_POST['dataend'] = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                        //Дата/время
                        if ($_POST['all_time'] != 1) {
                            //$queryDop .= "`create_time` BETWEEN '" . strtotime($_POST['datastart']) . "' AND '" . strtotime($_POST['dataend'] . " 23:59:59") . "'";

                            //$queryDop .= "CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}'";
                            $queryDop .= "ji.closed_time BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}'";
                            $queryDopExist = true;
                        }
                        //var_dump($queryDop);

                        //Кто создал запись
                        if ($creator != 0) {
                            if ($queryDopExist) {
                                $queryDop .= ' AND';
                            }
                            $queryDop .= " ji.create_person = '" . $creator . "'";
                            $queryDopExist = true;
                        }

                        //К кому запись
                        if ($worker != 0) {
                            if ($queryDopExist) {
                                $queryDop .= ' AND';
                            }
                            $queryDop .= " ji.worker_id = '" . $worker . "'";
                            $queryDopExist = true;
                        }

                        //Клиент
                        if ($client != 0) {
                            if ($queryDopExist) {
                                $queryDop .= ' AND';
                            }
                            $queryDop .= " ji.client_id = '" . $client . "'";
                            $queryDopExist = true;
                        }

                        //Филиал
                        if ($_POST['filial'] != 99) {
                            if ($queryDopExist) {
                                $queryDop .= ' AND';
                            }
                            $queryDop .= " ji.office_id = '" . $_POST['filial'] . "'";
                            $queryDopExist = true;
                        }

                        //Все записи
                        /*if ($_POST['zapisAll'] != 0) {
                            //ничего
                        } else {
                            //Пришёл
                            if ($_POST['zapisArrive'] != 0) {
                                if ($queryDopExExist) {
                                    $queryDopEx .= ' OR';
                                }
                                if ($_POST['zapisArrive'] == 1) {
                                    $queryDopEx .= "z.enter = '1'";
                                    $queryDopExExist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Не пришёл
                            if ($_POST['zapisNotArrive'] != 0) {
                                if ($queryDopExExist) {
                                    $queryDopEx .= ' OR';
                                }
                                if ($_POST['zapisNotArrive'] == 1) {
                                    $queryDopEx .= " z.enter = '9'";
                                    $queryDopExExist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Не отмеченные
                            if ($_POST['zapisNull'] != 0) {
                                if ($queryDopExExist) {
                                    $queryDopEx .= ' OR';
                                }
                                if ($_POST['zapisNull'] == 1) {
                                    $queryDopEx .= " z.enter = '0'";
                                    $queryDopExExist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Ошибочные
                            if ($_POST['zapisError'] != 0) {
                                if ($queryDopExExist) {
                                    $queryDopEx .= ' OR';
                                }
                                if ($_POST['zapisError'] == 1) {
                                    $queryDopEx .= " z.enter = '8'";
                                    $queryDopExExist = true;
                                }
                                //$queryDopExExist = true;
                            }
                        }*/

                        //Тип
                        if ($_POST['typeW'] != 0) {
                            if ($queryDopExist) {
                                $queryDop .= ' AND';
                            }
                            $queryDop .= " ji.type = '" . $_POST['typeW'] . "'";
                            $queryDopExist = true;
                        }


                        //Первичный ночной страховой
                        if ($_POST['statusAll'] != 0) {
                            //ничего
                        } else {
                            //Первичные
                            if ($_POST['statusPervich'] != 0) {
                                if ($queryDopEx2Exist) {
                                    $queryDopEx2 .= ' OR';
                                }
                                if ($_POST['statusPervich'] == 1) {
                                    $queryDopEx2 .= " z.pervich = '1'";
                                    $queryDopEx2Exist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Страховые
                            if ($_POST['statusInsure'] != 0) {
                                if ($queryDopEx2Exist) {
                                    $queryDopEx2 .= ' OR';
                                }
                                if ($_POST['statusInsure'] == 1) {
                                    $queryDopEx2 .= " z.insured = '1'";
                                    $queryDopEx2Exist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Ночные
                            if ($_POST['statusNight'] != 0) {
                                if ($queryDopEx2Exist) {
                                    $queryDopEx2 .= ' OR';
                                }
                                if ($_POST['statusNight'] == 1) {
                                    $queryDopEx2 .= " z.noch = '1'";
                                    $queryDopEx2Exist = true;
                                }
                                //$queryDopExExist = true;
                            }

                            //Все остальные
                            if ($_POST['statusAnother'] != 0) {
                                if ($queryDopEx2Exist) {
                                    $queryDopEx2 .= ' OR';
                                }
                                if ($_POST['statusAnother'] == 1) {
                                    $queryDopEx2 .= " z.pervich = '0' AND z.insured = '0' AND z.noch = '0'";
                                    $queryDopEx2Exist = true;
                                }
                                //$queryDopExExist = true;
                            }
                        }

                        $journal = array();
                        $invoice_ids = array();


                        //if ($queryDopExist) {
                            $query .= ' WHERE jiex.percent_cats = \''.$_POST['percent_cat'].'\' AND ' . $queryDop;

                            if ($queryDopExExist) {
                                $query .= ' AND (' . $queryDopEx . ')';
                            }

                            if ($queryDopEx2Exist) {
                                $query .= ' AND (' . $queryDopEx2 . ')';
                            }

                            /*if ($queryDopClientExist){
                                $queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
                                if ($queryDopExist){
                                    $query .= ' AND';
                                }
                                $query .= "`client` IN (".$queryDopClient.")";
                            }*/

                            //$query = $query . "AND ji.status = '5' ORDER BY CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) ASC";
                            $query = $query . "AND ji.status = '5' GROUP BY jiex.invoice_id";
//                            var_dump($query);

        //                    var_dump($_POST['percent_cat']);
        //
        //                    "SELECT jiex.invoice_id FROM `journal_invoice_ex` jiex
        //                    INNER JOIN `journal_invoice` ji
        //                    ON ji.id = jiex.invoice_id
        //                    WHERE jiex.percent_cats = '{$_POST['percent_cat']}' AND ji.closed_time BETWEEN '2020-11-01' AND '2020-11-22'
        //                    GROUP BY jiex.invoice_id";
        //
        //                    $msql_cnnct = ConnectToDB();
        //
        //                    $arr = array();
        //                    $rez = array();
        //
        //                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
        //
        //                    $number = mysqli_num_rows($res);

//                            if ($number != 0) {
//                                while ($arr = mysqli_fetch_assoc($res)) {
//                                    //array_push($journal, $arr);
//
//                                    if (!isset($journal[$arr['invoice_id']])){
//                                        $journal[$arr['invoice_id']] = array();
//                                    }
//                                    array_push($journal[$arr['invoice_id']], $arr);
//                                }
//                            }

                            $args = [

                            ];

                            $invoice_ids = $db::getColumn($query, $args);

                            //var_dump($invoice_ids);


                            $invoice_ids_str = implode("','", $invoice_ids);
    //                        var_dump($zapis_ids_str);

                            $query = "
                                SELECT ji.*, sc.full_name FROM `journal_invoice` ji
                                LEFT JOIN `spr_clients` sc ON sc.id = ji.client_id
                                WHERE ji.id IN ('".$invoice_ids_str."')";
//                            var_dump($query);

                            $show_client = false;

                            if ($dop['patientUnic'] == 1){
                                $query .=  " GROUP BY sc.full_name ORDER BY sc.full_name";
                            }

                            if ($dop['withFIO'] == 1){
                                $query .=  " ORDER BY ji.client_id, ji.create_time";
                            }

                            if (($dop['patientUnic'] == 1) || ($dop['withFIO'] == 1)){
                                $show_client = true;
                            }

                            $journal = $db::getRows($query, $args);
//                            var_dump($journal);


        //                    $query = "SELECT * FROM `journal_invoice` ji WHERE ji.closed_time BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}' AND ji.office_id = '15' AND ji.type = '5' AND ji.status = '5'";
        //
        //                    $journal2 = array();
        //                    $rez = array();
        //
        //                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
        //
        //                    $number = mysqli_num_rows($res);
        //
        //                    if ($number != 0) {
        //                        while ($arr = mysqli_fetch_assoc($res)) {
        //                            //array_push($journal, $arr);
        //                            $journal2[$arr['id']] = $arr;
        //                        }
        //                    }
        //                    //var_dump($journal2);
        //
        //                    $query = "SELECT * FROM `journal_invoice` ji WHERE ji.closed_time BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}' AND ji.office_id = '15' AND ji.type = '6' AND ji.status = '5'";
        //
        //                    $journal3 = array();
        //                    $rez = array();
        //
        //                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
        //
        //                    $number = mysqli_num_rows($res);
        //
        //                    if ($number != 0) {
        //                        while ($arr = mysqli_fetch_assoc($res)) {
        //                            //array_push($journal, $arr);
        //                            $journal3[$arr['id']] = $arr;
        //                        }
        //                    }
        //                    //var_dump($journal3);

                            //echo json_encode(array('result' => 'success', 'data' => $journal, 'query' => $query));

                            //Выводим результат (нет)
                            //Делаем рассчеты
                            if (!empty($journal)) {
                                //include_once 'functions.php';

                                echo '
                                    <li class="cellsBlock" style="margin-bottom: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                        Всего нарядов: ' . count($journal) . '<br>
                                    </li>
                                ';

//                                var_dump($journal);



                                echo showInvoiceDivRezult($journal, false, false, true, true, true, false, $show_client)['data'];

                                //Категории процентов
//                                $percent_cats_j = array();
//                                //Для сортировки по названию
//                                $percent_cats_j_names = array();
//                                //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
//                                $query = "SELECT `id`, `name` FROM `fl_spr_percents`";
//                                //var_dump( $percent_cats_j);
//
//                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                                $number = mysqli_num_rows($res);
//                                if ($number != 0){
//                                    while ($arr = mysqli_fetch_assoc($res)){
//                                        $percent_cats_j[$arr['id']] = $arr['name'];
//                                        //array_push($percent_cats_j_names, $arr['name']);
//                                    }
//                                }
//
//                                //var_dump($temp_cat_array);
//
//                                //$temp_inv_array = array();
//
//                                $temp_cat_array = array();
//                                $all_summ = 0;
//                                $ins_summ = 0;
//                                $garantee_summ = 0;
//
//                                $invoice_wo_cat = array();
//                                $invoice_garantee = array();
//
//                                foreach ($journal as $item){
//                                    //var_dump($item);
//                                    //var_dump($item['id']);
//                                    //var_dump($item['price']);
//                                    //echo $item['id'].'<br>';
//
//        //                            if (!isset($temp_inv_array[$item['invoice_id']])){
//        //                                $temp_inv_array[$item['invoice_id']] = array();
//        //                                $temp_inv_array[$item['invoice_id']]['summ'] = $item['invoice_summ'];
//        //                                $temp_inv_array[$item['invoice_id']]['summins'] = $item['invoice_summins'];
//        //                                $temp_inv_array[$item['invoice_id']]['summ_poss'] = 0;
//        //                            }
//        //                            $temp_inv_array[$item['invoice_id']]['summ_poss'] += $item['itog_price'];
//
//
//
//                                    if ($item['guarantee'] == 0) {
//                                        if ($item['insure'] == 0) {
//                                            if ($item['id'] != NULL) {
//                                                if (!isset($temp_cat_array[$item['percent_cats']])) {
//                                                    $temp_cat_array[$item['percent_cats']] = $item['itog_price'];
//                                                    //var_dump($item['percent_cats']);
//                                                    //var_dump($temp_cat_array);
//
//                                                } else {
//                                                    $temp_cat_array[$item['percent_cats']] += $item['itog_price'];
//                                                }
//                                                if ($item['percent_cats'] == 0) {
//                                                    if (!in_array($item['invoice_id'], $invoice_wo_cat)) {
//                                                        array_push($invoice_wo_cat, $item['invoice_id']);
//                                                    }
//                                                }
//                                            }
//                                            //}
//                                            $all_summ += $item['itog_price'];
//                                        }else{
//                                            $ins_summ += $item['itog_price'];
//                                        }
//                                    }else{
//                                        $garantee_summ += $item['itog_price'];
//                                        if (!in_array($item['invoice_id'], $invoice_garantee)) {
//                                            array_push($invoice_garantee, $item['invoice_id']);
//                                        }
//                                    }
//                                }
//                                //var_dump($temp_cat_array);
//                                //var_dump($temp_inv_array);
//
//                                //Сортируем по значению
//                                arsort($temp_cat_array);
//
//                                echo '
//                                        <div style="padding: 2px 4px 5px;">
//                                            Общая сумма по выполненным (закрытым) работам: : <b>' . number_format($ins_summ+$all_summ, 0, ',', ' ') . ' руб.</b>
//                                        </div>
//                                        <div style="padding: 2px 4px;">
//                                            Приход: <b>' . number_format($all_summ, 0, ',', ' ') . ' руб.</b>
//                                        </div>
//                                        <div style="padding: 2px 4px;">
//                                            Сумма от страховых: <b>' . number_format($ins_summ, 0, ',', ' ') . ' руб.</b>
//                                        </div>
//                                        <div>
//                                            <div style="display: inline-block; vertical-align: top;">';
//
//                                if (!empty($temp_cat_array)){
//                                    foreach ($temp_cat_array as $item_id => $item_summ) {
//
//                                        $percent_from_all_summ = $item_summ * 100 / $all_summ;
//                                        $percent_from_all_summ = number_format($percent_from_all_summ, 2, ',', '');
//
//                                        if ($item_id != 0) {
//                                            echo '<li><div class="cellOrder">'.$percent_cats_j[$item_id].'</div><div class="cellName" style="text-align: right;">' . number_format($item_summ, 0, ',', ' ') . ' руб.</b></div><div class="cellName categoryItem" percentCat="'.$percent_from_all_summ.'" nameCat="'.$percent_cats_j[$item_id].'"  style="text-align: right;">' . $percent_from_all_summ . ' %</div></li>';
//                                        }else{
//                                            echo '<li><div class="cellOrder" style="color: red;">'.'Не указана категория ' . '</div><div class="cellName" style="text-align: right; color: red;">' . number_format($item_summ, 0, ',', ' ')   . ' руб.</b></div><div class="cellName" style="text-align: right; color: red;">' . $percent_from_all_summ . ' %</div></li>';
//                                        }
//
//                                    }
//                                }
//
//                                echo '
//                                            </div>';
//                                if ($garantee_summ > 0){
//
//                                    echo '
//                                            <div style="padding: 10px 4px 2px;">
//                                                Сделано по гарантии на сумму: <b style="color: red;">' . number_format($garantee_summ, 0, ',', ' ') . ' руб.</b>
//                                            </div>';
//                                    if (!empty($invoice_garantee)){
//                                        echo '<div style="padding: 10px 4px 2px;">Наряды по гарантии:';
//
//                                        for($i=0; $i < count($invoice_garantee); $i++){
//                                            echo '<a href="invoice.php?id='.$invoice_garantee[$i].'" class="ahref button_tiny" style="margin: 0 3px;" target="_blank" rel="nofollow noopener">'.$invoice_garantee[$i].'</a>';
//                                        }
//                                        echo '</div>';
//                                    }
//                                }
//
//                                //Наряды без категорий
//                                if (!empty($invoice_wo_cat)){
//                                    echo '<div style="padding: 10px 4px 2px;">Наряды без категорий:';
//
//                                    for($i=0; $i < count($invoice_wo_cat); $i++){
//                                        echo '<a href="invoice.php?id='.$invoice_wo_cat[$i].'" class="ahref button_tiny" style="margin: 0 3px;">'.$invoice_wo_cat[$i].'</a>';
//                                    }
//                                    echo '</div>';
//                                }
//                                echo '
//                                            <!--<div id="canvas-holder" style="display: inline-block; vertical-align: top; /*border: 1px dotted #CCC;*/ width: 450px;">
//                                                <canvas id="chart-area"></canvas>
//                                            </div>-->
//                                        </div>';



                            } else {
                                echo '<span style="color: red;">Ничего не найдено</span>';
                            }

        //                    $summ2 = 0;
        //
        //                    if (!empty($journal2)) {
        //                        foreach ($journal2 as $item){
        //                            $summ2 += $item['summ']+$item['summins'];
        //                        }
        //                    }
        //                    var_dump($summ2);
        //
        //                    $summ3 = 0;
        //
        //                    if (!empty($journal3)) {
        //                        foreach ($journal3 as $item){
        //                            $summ3 += $item['summ']+$item['summins'];
        //                        }
        //                    }
        //                    var_dump($summ3);
        //
        //                    var_dump($summ2+$summ3);

        //                foreach ($temp_inv_array as $inv_id => $item){
        //                    //var_dump($item);
        //
        //                    if (($item['summ'] != $item['summ_poss']) && ( $item['summins'] != $item['summ_poss'])){
        //                        var_dump($inv_id);
        //                        var_dump($item['summ_poss']);
        //                        var_dump($item['summ']);
        //                        var_dump($item['summins']);
        //                        var_dump("------");
        //                    }
        //                }

                        /*} else {
                            echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
                        }*/

                        //var_dump($query);
                        //var_dump($queryDopEx);
                        //var_dump($queryDopClient);

                        //mysql_close();
                    }else {
//                        echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден пациент.</span>'));
                        echo '<span style="color: red;">Не найден пациент.</span>';
                    }
                }else{
//                    echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>'));
                    echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
                }
            }else{
//                echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Категория не выбрана.</span>'));
                echo '<span style="color: red;">Категория не выбрана.</span>';
            }
        }else{
//            echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Категория не выбрана.</span>'));
            echo '<span style="color: red;">Категория не выбрана.</span>';
        }
    }
}
?>