<?php

//ajax_show_result_main_report_category2_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        $filials_j = getAllFilials(true, true, true);
        //var_dump($filials_j);

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
                $query .= "
                    SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS  invoice_summins, ji.status AS invoice_status
                    FROM `zapis` z
                    INNER JOIN `journal_invoice` ji ON z.id = ji.zapis_id
                    LEFT JOIN `journal_invoice_ex` jiex ON ji.id = jiex.invoice_id";

//                $query = "
//                            SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS  invoice_summins
//                            FROM `journal_invoice` ji
//                            LEFT JOIN `journal_invoice_ex` jiex
//                            ON ji.id = jiex.invoice_id";


                $data_temp_arr = explode(".", $_POST['datastart']);
                $datastart = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                $data_temp_arr = explode(".", $_POST['dataend']);
                $dataend = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                //Дата/время
                if ($_POST['all_time'] != 1) {
                    //$queryDop .= "`create_time` BETWEEN '" . strtotime($datastart) . "' AND '" . strtotime($dataend . " 23:59:59") . "'";

                    $queryDop .= "CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '{$datastart}' AND '{$dataend}'";
                    //$queryDop .= "ji.closed_time BETWEEN '{$datastart}' AND '{$dataend}'";
                    $queryDopExist = true;
                }
                //var_dump($queryDop);

                //Кто создал запись
                if ($creator != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.create_person = '" . $creator . "'";
                    $queryDopExist = true;
                }

                //К кому запись
                if ($worker != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.worker = '" . $worker . "'";
                    $queryDopExist = true;
                }

                //Клиент
                if ($client != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.patient = '" . $client . "'";
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
                if ($_POST['zapisAll'] != 0) {
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
                }

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


                //if ($queryDopExist) {
                    $query .= ' WHERE ' . $queryDop;

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

                    $query = $query . " AND ji.status <> '9' ORDER BY CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) DESC";
                    //$query = $query . "AND ji.status = '5'";
                    //echo($query);

                    $msql_cnnct = ConnectToDB();

                    $arr = array();
                    $rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            //array_push($journal, $arr);
                            $journal[$arr['id']] = $arr;
                        }
                    }
                    //var_dump($journal);

                    //Выводим результат (нет)
                    //Делаем рассчеты
                    if (!empty($journal)) {
                        //include_once 'functions.php';

                        //Категории процентов
                        $percent_cats_j = array();
                        //Для сортировки по названию
                        $percent_cats_j_names = array();
                        //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
                        $query = "SELECT `id`, `name` FROM `fl_spr_percents`";
                        //var_dump( $percent_cats_j);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $percent_cats_j[$arr['id']] = $arr['name'];
                                //array_push($percent_cats_j_names, $arr['name']);
                            }
                        }
                        //$temp_inv_array = array();

                        $temp_cat_closed_array = array();
                        $temp_cat_opened_array = array();
                        $temp_cat_all_array = array();

                        $opened_summ = 0;
                        $closed_summ = 0;
                        $all_summ = 0;

                        $ins_summ = 0;
                        $garantee_summ = 0;

                        $invoice_opened_wo_cat = array();
                        $invoice_closed_wo_cat = array();
                        $invoice_all_wo_cat = array();

                        $invoice_garantee = array();

                        $invoice_opened = array();
                        $invoice_closed = array();
                        $invoice_all = array();

                        foreach ($journal as $item){
//                            var_dump($item['invoice_status']);
//                            if ($item['invoice_status'] != 5) {
//                                var_dump($item['invoice_id']);
//                                var_dump($item['invoice_summ']);
//                            }

                            if ($item['guarantee'] == 0) {
                                if ($item['insure'] == 0) {
                                    //Если сумма не = 0
                                    if ($item['invoice_summ'] != 0) {
                                        //Все созданные наряды (откр+закр)
                                        if (!isset($temp_cat_all_array[$item['percent_cats']])) {
                                            $temp_cat_all_array[$item['percent_cats']] = $item['itog_price'];
                                            //var_dump($item['percent_cats']);
                                            //var_dump($temp_cat_close_array);

                                        } else {
                                            $temp_cat_all_array[$item['percent_cats']] += $item['itog_price'];
                                        }
                                        //Без категории
                                        if ($item['percent_cats'] == 0) {
                                            if (!in_array($item['invoice_id'], $invoice_all_wo_cat)) {
                                                array_push($invoice_all_wo_cat, $item['invoice_id']);
                                            }
                                        }

                                        if (!in_array($item['invoice_id'], $invoice_all)) {
                                            $invoice_all[$item['invoice_id']] = $item['invoice_summ'];
                                        }

                                        $all_summ += $item['itog_price'];


                                        //Если закрыты
                                        if ($item['invoice_status'] == 5) {
                                            if (!isset($temp_cat_closed_array[$item['percent_cats']])) {
                                                $temp_cat_closed_array[$item['percent_cats']] = $item['itog_price'];
                                                //var_dump($item['percent_cats']);
                                                //var_dump($temp_cat_close_array);

                                            } else {
                                                $temp_cat_closed_array[$item['percent_cats']] += $item['itog_price'];
                                            }
                                            //Без категории
                                            if ($item['percent_cats'] == 0) {
                                                if (!in_array($item['invoice_id'], $invoice_closed_wo_cat)) {
                                                    array_push($invoice_closed_wo_cat, $item['invoice_id']);
                                                }
                                            }

                                            if (!in_array($item['invoice_id'], $invoice_closed)) {
                                                $invoice_closed[$item['invoice_id']] = $item['invoice_summ'];
                                            }

                                            $closed_summ += $item['itog_price'];
                                        }else{
                                            //Если не закрыты
                                            if (!isset($temp_cat_opened_array[$item['percent_cats']])) {
                                                $temp_cat_opened_array[$item['percent_cats']] = $item['itog_price'];
                                                //var_dump($item['percent_cats']);
                                                //var_dump($temp_cat_close_array);

                                            } else {
                                                $temp_cat_opened_array[$item['percent_cats']] += $item['itog_price'];
                                            }
                                            //Без категории
                                            if ($item['percent_cats'] == 0) {
                                                if (!in_array($item['invoice_id'], $invoice_opened_wo_cat)) {
                                                    array_push($invoice_opened_wo_cat, $item['invoice_id']);
                                                }
                                            }

                                            if (!in_array($item['invoice_id'], $invoice_opened)) {
                                                $invoice_opened[$item['invoice_id']] = $item['invoice_summ'];
                                            }

                                            $opened_summ += $item['itog_price'];
                                        }

                                    }


                                }else{
                                    //Страховые
                                    $ins_summ += $item['itog_price'];
                                }
                            }else{
                                //Гарантийные
                                $garantee_summ += $item['itog_price'];
                                if (!in_array($item['invoice_id'], $invoice_garantee)) {
                                    array_push($invoice_garantee, $item['invoice_id']);
                                }
                            }
                        }
                        //var_dump($temp_cat_close_array);
                        //var_dump($temp_inv_array);

                        //Сортируем по значению
                        arsort($temp_cat_closed_array);
                        arsort($temp_cat_opened_array);


//                        var_dump($_POST['worker']);
//                        var_dump($workerExist);
                        if  ($workerExist && $worker != 0) {
                            //var_dump($workerSearch[0]['name']);
                            echo 'Данные указаны для сотрудника: <i><b>'.$workerSearch[0]['name'].'</b></i><br>';
                        }

                        if ($_POST['filial'] != 99) {
                           echo 'Для филиала: <i>'.$filials_j[$_POST['filial']]['name'].'</i><br>';
                        }

                        echo 'с '.$_POST['datastart'].' по '.$_POST['dataend'].'<br>';

                        echo '
                                <div style="padding: 2px 4px;">
                                    Общая сумма: <b>' . number_format($ins_summ+$all_summ, 0, ',', ' ') . ' руб.</b>
                                </div>
                                <div style="padding: 2px 4px;">
                                    Сумма от страховых: <b>' . number_format($ins_summ, 0, ',', ' ') . ' руб.</b>
                                </div>
                                <!--<div style="padding: 2px 4px;">
                                    Сумма по закрытым работам: <b>' . number_format($closed_summ, 0, ',', ' ') . ' руб.</b>
                                </div>-->
                                <!--<div style="padding: 2px 4px;">
                                    Сумма по не закрытым работам: <b>' . number_format($opened_summ, 0, ',', ' ') . ' руб.</b>
                                </div>-->';


                        echo '
                                <div>
                                    <div style="display: inline-block; vertical-align: top;">';

                        //По всем
                        if (!empty($temp_cat_all_array)){
                            foreach ($temp_cat_all_array as $item_id => $item_summ) {

                                $percent_from_all_summ = $item_summ * 100 / $all_summ;
                                $percent_from_all_summ = number_format($percent_from_all_summ, 2, ',', '');

                                if ($item_id != 0) {
                                    echo '
                                        <li>
                                            <div class="cellOrder">
                                                '.$percent_cats_j[$item_id].'
                                            </div>
                                            <div class="cellName" style="text-align: right;">
                                                ' . number_format($item_summ, 0, ',', ' ') . ' руб.
                                            </div>
                                            <div class="cellName categoryItem" percentCat="'.$percent_from_all_summ.'" nameCat="'.$percent_cats_j[$item_id].'"  style="text-align: right;">
                                                ' . $percent_from_all_summ . ' %
                                            </div>
                                        </li>';
                                }else{
                                    echo '
                                        <li>
                                            <div class="cellOrder" style="color: red;">
                                                '.'Не указана категория ' . '
                                            </div>
                                            <div class="cellName" style="text-align: right; color: red;">
                                                ' . number_format($item_summ, 0, ',', ' ')   . ' руб.
                                            </div>
                                            <div class="cellName" style="text-align: right; color: red;">
                                                ' . $percent_from_all_summ . ' %
                                            </div>
                                        </li>';
                                }

                            }
                        }

                        echo '<br><br><br><br>';

                        //По закрытым
//                        if (!empty($temp_cat_closed_array)){
//                            foreach ($temp_cat_closed_array as $item_id => $item_summ) {
//
//                                $percent_from_all_summ = $item_summ * 100 / $closed_summ;
//                                $percent_from_all_summ = number_format($percent_from_all_summ, 2, ',', '');
//
//                                if ($item_id != 0) {
//                                    echo '
//                                        <li>
//                                            <div class="cellOrder">
//                                                '.$percent_cats_j[$item_id].'
//                                            </div>
//                                            <div class="cellName" style="text-align: right;">
//                                                ' . number_format($item_summ, 0, ',', ' ') . ' руб.
//                                            </div>
//                                            <div class="cellName categoryItem" percentCat="'.$percent_from_all_summ.'" nameCat="'.$percent_cats_j[$item_id].'"  style="text-align: right;">
//                                                ' . $percent_from_all_summ . ' %
//                                            </div>
//                                        </li>';
//                                }else{
//                                    echo '
//                                        <li>
//                                            <div class="cellOrder" style="color: red;">
//                                                '.'Не указана категория ' . '
//                                            </div><div class="cellName" style="text-align: right; color: red;">
//                                                ' . number_format($item_summ, 0, ',', ' ')   . ' руб.
//                                            </div>
//                                            <div class="cellName" style="text-align: right; color: red;">
//                                                ' . $percent_from_all_summ . ' %
//                                            </div>
//                                        </li>';
//                                }
//
//                            }
//                        }

                        echo '
                                    </div>';

                        //Сумма сделанного по гарантии
                        if ($garantee_summ > 0){

                            echo '
                                    <div style="padding: 10px 4px 2px;">
                                        Сделано по гарантии на сумму: <b style="color: red;">' . number_format($garantee_summ, 0, ',', ' ') . ' руб.</b>
                                    </div>';
                            if (!empty($invoice_garantee)){
                                echo '<div style="padding: 10px 4px 2px;">Наряды по гарантии:';

                                for($i=0; $i < count($invoice_garantee); $i++){
                                    echo '<a href="invoice.php?id='.$invoice_garantee[$i].'" class="ahref button_tiny" style="margin: 0 3px;" target="_blank" rel="nofollow noopener">'.$invoice_garantee[$i].'</a>';
                                }
                                echo '</div>';
                            }
                        }

                        //Наряды без категорий
                        if (!empty($invoice_opened_wo_cat) || !empty($invoice_closed_wo_cat)){

                            echo '<div style="padding: 10px 4px 2px;">Наряды без категорий:';

                            if (!empty($invoice_opened_wo_cat)) {
                                for ($i = 0; $i < count($invoice_opened_wo_cat); $i++) {
                                    echo '<a href="invoice.php?id=' . $invoice_opened_wo_cat[$i] . '" class="ahref button_tiny" style="margin: 0 3px;">' . $invoice_opened_wo_cat[$i] . '</a>';
                                }
                            }
                            if (!empty($invoice_closed_wo_cat)){
                                for($i=0; $i < count($invoice_closed_wo_cat); $i++){
                                    echo '<a href="invoice.php?id='.$invoice_closed_wo_cat[$i].'" class="ahref button_tiny" style="margin: 0 3px;">'.$invoice_closed_wo_cat[$i].'</a>';
                                }
                            }

                            echo '</div>';
                        }

                        echo '
                                    <!--<div id="canvas-holder" style="display: inline-block; vertical-align: top; /*border: 1px dotted #CCC;*/ width: 450px;">
                                        <canvas id="chart-area"></canvas>
                                    </div>-->
                                </div>';

//                        //var_dump($invoice_all);
//                        echo '<br><br>';
//                        //arsort($invoice_all);
//                        foreach($invoice_all as $invoice_id => $invoice_summ){
//                            echo '<br><a href="invoice.php?id='.$invoice_id.'" class="ahref button_tiny" style="margin: 0 3px;">#'.$invoice_id.' => '.$invoice_summ.'</a>';
//                        }

                    } else {
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

            }else {
                echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден пациент.</span>'));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>'));
        }
    }
}
?>