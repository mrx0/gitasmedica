<?php

//fl_main_report2.php
//Финальный отчет v2.0

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';
            require 'variables.php';

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();

            //Дата
            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }
            $day = date("d");

            //Или если мы смотрим другой месяц
//            if (isset($_GET['m'])) {
//                $m = $_GET['m'];
//            }
//            if (isset($_GET['y'])) {
//                $y = $_GET['y'];
//            }

            //Филиал
            if (isset($_GET['filial_id'])) {
                $filial_id = $_GET['filial_id'];
            }else{
                $filial_id = 15;
            }

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <!--<a href="stat_cashbox.php" class="b">Касса</a>-->
                        </div>
                        <h2 style="padding: 0;">Отчёт '.$filials_j[$filial_id]['name2'].' / '.$monthsName[$month].' '.$year.'</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            //Выбор филиала
            echo '
                        <div class="no_print" style="font-size: 90%; ">
                            Филиал: ';

            //if (($finances['see_all'] == 1) || $god_mode) {

                echo '
                            <select name="SelectFilial" id="SelectFilial">';

                foreach ($filials_j as $filial_item) {

                    $selected = '';

                    if ($filial_id == $filial_item['id']) {
                        $selected = 'selected';
                    }

                    echo '
                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                }

                echo '
                            </select>';
//            } else {
//
//                echo $filials_j[$_SESSION['filial']]['name'] . '<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $_SESSION['filial'] . '">';
//
//            }

            //Выбор месяц и год
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: ';
            echo '
			                <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == (int)$month){
                    $selected = 'selected';
                }
                echo '
				                <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
			                </select>
			                <select name="iWantThisYear" id="iWantThisYear">';
            for ($i = 2017; $i <= (int)date('Y')+2; $i++){
                $selected = '';
                if ($i == (int)date('Y')){
                    $selected = 'selected';
                }
                echo '
				                <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
			                </select>
			                <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisDate(\'fl_main_report2.php?filial_id='. $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
			                <div style="font-size: 90%; color: rgb(125, 125, 125); float: right;">Сегодня: <a href="fl_main_report2.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
			            </div>';

            //Количество дней в месяце
            $month_stamp = mktime(0, 0, 0, $month, 1, $year);
            $day_count = date("t", $month_stamp);

            //или так
            //$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $msql_cnnct = ConnectToDB ();

            //Соберём все категории процентов (справочник)
            // по типу
            $percents_j = array();
            // по id
            $percents_j2 = array();

            $query = "SELECT `id`, `name`, `type` FROM  `fl_spr_percents`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    if (!isset($percents_j[$arr['type']])){
                        $percents_j[$arr['type']] = array();
                    }
                    $percents_j[$arr['type']][$arr['id']]['name'] = $arr['name'];

                    if (!isset($percents_j2[$arr['id']])){
                        $percents_j2[$arr['id']] = array();
                    }

                    $percents_j2[$arr['id']]['name'] = $arr['name'];


                }
            }
            //var_dump($percents_j);
            //var_dump($percents_j2);

            //Типы расходов
            $give_out_cash_types_j = array();

            $query = "SELECT `id`,`name` FROM `spr_cashout_types`";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $give_out_cash_types_j[$arr['id']] = $arr['name'];
                }
            }



            //Типы посещений - первичка/нет (количество)
            //Памятка
            //1 - Посещение для пациента первое без работы
            //2 - Посещение для пациента первое с работой
            //3 - Посещение для пациента не первое
            //4 - Посещение для пациента не первое, но был более полугода назад
            //5 - Продолжение работы
            $pervich_summ_arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

            //Получаем данные по записи за месяц
            $zapis_j = array();
            $zapis_j_noch = array();
            //Не пришло
            $zapis_not_enter = 0;
            //ID записей
            $zapis_ids = array();

            //Кроме тех, которые удалены или не пришли
            $query = "SELECT `id`, `type`, `patient`, `pervich`, `insured`, `noch`, `enter`  FROM `zapis` WHERE `office` = '{$filial_id}' AND `year` = '{$year}' AND `month` = '{$month}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `day` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Если ночь
                    if ($arr['noch'] == 1){
                        array_push($zapis_j_noch, $arr);
                    }else {
                        if (!isset($zapis_j[$arr['type']])) {
                            $zapis_j[$arr['type']] = array();
                            $zapis_j[$arr['type']]['pervich_summ_arr'] = $pervich_summ_arr;
                            $zapis_j[$arr['type']]['insured'] = 0;
                        }
                        //array_push($zapis_j[$arr['type']], $arr);
                        //array_push($zapis_j, $arr);

                        //Если страховое
                        //var_dump($arr['insured']);
                        if ($arr['insured'] == 1) {
                            //Если пришёл
                            //var_dump($arr['enter']);
                            //первичка/нет
                            if (($arr['enter'] == 1) || ($arr['enter'] == 6)) {
                                $zapis_j[$arr['type']]['insured']++;

                                array_push($zapis_ids, $arr['id']);
                            } else {
                            }

                        //Не страховой
                        }else{
                            //Если пришёл
                            //var_dump($arr['enter']);
                            //первичка/нет
                            if (($arr['enter'] == 1) || ($arr['enter'] == 6)) {
                                $zapis_j[$arr['type']]['pervich_summ_arr'][$arr['pervich']]++;

                                array_push($zapis_ids, $arr['id']);
                            } else {
                                if ($arr['enter'] == 0) {
                                    $zapis_not_enter++;
                                }
                            }
                        }
                    }
                }
            }
            //Ночь
            //var_dump($zapis_j_noch);
            //День
            //var_dump($zapis_j);
            //var_dump($zapis_j[7]);
            //Не пришли
            //var_dump($zapis_not_enter);
            //var_dump($zapis_ids);

            //Преобразуем массив ID записей для запросов - !!! не используем
            //$zapis_ids_str = implode("','", $zapis_ids);
            //var_dump($zapis_ids_str);
            //$query=mysqli_query($conn, "SELECT name FROM users WHERE id IN ('".zapis_ids_str."')");

            //Выберем наряды по записям
            $invoices_j = array();
            $invoices_j2 = array();
            $invoices_notinsure_ids = array();

            $query = "
                    SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.status AS invoice_status, ji.type AS type, z.enter AS enter, z.pervich AS pervich, sc.birthday2 AS birthday
                    FROM `zapis` z
                    INNER JOIN `journal_invoice` ji ON z.id = ji.zapis_id AND 
                    z.office = '{$filial_id}' AND z.year = '{$year}' AND z.month = '{$month}' AND (z.enter = '1' OR z.enter = '6')
                    LEFT JOIN `journal_invoice_ex` jiex ON ji.id = jiex.invoice_id
                    LEFT JOIN `spr_clients` sc ON ji.client_id = sc.id 
                    WHERE ji.status <> '9'";
            //var_dump($query);
            //echo ($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($invoices_j2, $arr);

                    //Пришел/не пришел/с улицы
                    if (!isset($invoices_j[$arr['enter']])){
                        $invoices_j[$arr['enter']] = array();
                    }
                    //тип стом, косм, ...
                    if (!isset($invoices_j[$arr['enter']][$arr['type']])){
                        $invoices_j[$arr['enter']][$arr['type']] = array();
                        $invoices_j[$arr['enter']][$arr['type']]['data'] = array();
                        $invoices_j[$arr['enter']][$arr['type']]['insure_data'] = array();
                        $invoices_j[$arr['enter']][$arr['type']]['child_stom_summ'] = 0;
                    }
                    //Если страховой
                    if ($arr['insure'] == 1){
                        if (!isset($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']])) {
                            $invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']] = array();
                        }
                        array_push($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']], $arr);

                    }else{
                        if (!isset($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']])){
                            $invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']] = array();
                        }
                        array_push($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']], $arr);

                    }
            //категории работ
                }
            }
            //сортируем по основным ключам
            ksort($invoices_j);

            foreach ($invoices_j as $id => $data){
                //сортируем по ключам, которые тип стом, косм,...
                ksort($invoices_j[$id]);
            }
            //var_dump($invoices_j);
            //var_dump($invoices_j[1][5]['data']);
            //var_dump($invoices_j2);

            //Итоговый массив для хранения по типам стом/косм/...
            $rezult_arr = array();
            //для хранения страховые и нет
            //$rezult_arr['data'] = array();
            //$rezult_arr['insure_data'] = array();
            //суммы по категориям
            //$rezult_arr['data'][ID категории]['category_summ'] = 0;

            //Костыль для типа 7
            $rezult_arr[7]['data'] = array();

            //переменная для строки, где будут ссылки на наряды, если с ними что-то не так
            $warn_str_percent_cats = '';

            foreach ($invoices_j as $enter => $enter_data){
                //Если пришел к врачу
                if (($enter == 1) || ($enter == 6)){
                    foreach ($enter_data as $type => $type_data){

                        if (!isset($rezult_arr[$type])){
                            $rezult_arr[$type] = array();
                            $rezult_arr[$type]['child_stom_summ'] = 0;
                        }

                        //Если стоматолог
                        //if ($type == 5){
                            //Проход по нарядам
                            //не страховые
                            foreach ($type_data['data'] as $invoice_id => $invoice_data){
//                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                var_dump($invoice_id);

                                if (!isset($rezult_arr[$type]['data'])){
                                    $rezult_arr[$type]['data'] = array();
                                }

//                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                var_dump($invoice_id);

                                $invoice_summ = 0;
                                $invoice_summins = 0;

                                $invoice_summ_pos = 0;

                                $pervich_status = 0;

                                //Дети стоматология
                                $child_stom_summ = 0;

                                //Пороход по данным наряда (позиции)
                                foreach ($invoice_data as $data){
                                    //var_dump($data);
                                    //var_dump($data['percent_cats']);

                                    //debug
//                                    var_dump(strlen($data['percent_cats']));
//                                    if (strlen($data['percent_cats']) == 0){
//                                        var_dump($invoice_id);
//                                    }

                                    if (!isset($percents_j[$type][$data['percent_cats']])){
                                        if (strlen($data['percent_cats']) > 0) {
                                            //var_dump($invoice_id.' ['.$type.'] => '.$data['percent_cats']);
                                            $warn_str_percent_cats .= '<a href="invoice.php?id=' . $invoice_id . '" class="ahref button_tiny" style="margin: 0 2px; font-size: 80%;">#' . $invoice_id . '</a>';
                                        }
                                    }

                                    $invoice_summ = $data['invoice_summ'];
                                    $invoice_summins = $data['invoice_summins'];
                                    //var_dump($data['itog_price']);

                                    $invoice_summ_pos += $data['itog_price'];

                                    //$pervich_status = $data['pervich'];

                                    //Если не продолжение работы
                                    if ($data['pervich'] != 5) {
//                                        var_dump($data['percent_cats']);

                                        //Дети стоматология
                                        //var_dump(getyeardiff(strtotime($data['birthday']), 0));
                                        if (($type == 5) && (getyeardiff(strtotime($data['birthday']), 0) <= 14)) {
                                            $child_stom_summ += $data['itog_price'];
                                            //var_dump($invoice_id);
                                        }else {
                                           //Костыль для категории 7 (ассистенты)
                                            if (!in_array($data['percent_cats'], [58, 59, 61, 62])) {
                                                if (!isset($rezult_arr[$type]['data'][$data['percent_cats']])) {
                                                    $rezult_arr[$type]['data'][$data['percent_cats']] = 0;
                                                }
                                                $rezult_arr[$type]['data'][$data['percent_cats']] += $data['itog_price'];
                                            } else {
                                                if (!isset($rezult_arr[7]['data'][$data['percent_cats']])) {
                                                    $rezult_arr[7]['data'][$data['percent_cats']] = 0;
                                                }
                                                $rezult_arr[7]['data'][$data['percent_cats']] += $data['itog_price'];
                                            }
                                        }
                                    }

                                }
                                if ($type == 5){
                                    $rezult_arr[$type]['child_stom_summ'] += $child_stom_summ;
                                }
//                                var_dump('_____________________________');
//                                if ($pervich_status  == 5){
//                                    var_dump('***___***___***___***');
//                                }
//                                var_dump('$pervich_status');
//                                var_dump($pervich_status);
//                                var_dump($invoice_summ_pos);
//                                var_dump($invoice_summ);
//                                var_dump($invoice_summ == $invoice_summ_pos);
//                                var_dump($invoice_summins);
//                                var_dump($invoice_summins == $invoice_summ_pos);

                            }

                            if (!empty($type_data['insure_data'])) {
                                //страховые
                                foreach ($type_data['insure_data'] as $invoice_id => $invoice_data) {

                                    if (!isset($rezult_arr[$type]['insure_data'])) {
                                        $rezult_arr[$type]['insure_data'] = array();
                                    }

//                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                var_dump($invoice_id);

                                    $invoice_summ = 0;
                                    $invoice_summins = 0;

                                    $invoice_summ_pos = 0;

                                    $pervich_status = 0;

                                    //Проход по данным наряда (позиции)
                                    foreach ($invoice_data as $data) {
                                        //var_dump($data['itog_price']);

                                        $invoice_summ = $data['invoice_summ'];
                                        $invoice_summins = $data['invoice_summins'];

                                        $invoice_summ_pos += $data['itog_price'];

                                        //$pervich_status = $data['pervich'];

                                        //Если не продолжениении работы
                                        if ($data['pervich'] != 5) {
                                            if (!isset($rezult_arr[$type]['insure_data'][$data['percent_cats']])) {
                                                $rezult_arr[$type]['insure_data'][$data['percent_cats']] = 0;
                                            }
                                            $rezult_arr[$type]['insure_data'][$data['percent_cats']] += $data['itog_price'];
                                        }
                                    }

//                                var_dump('_____________________________');
//                                if ($pervich_status  == 5){
//                                    var_dump('***___***___***___***');
//                                }
//                                var_dump($invoice_summ_pos);
//                                var_dump($invoice_summ);
//                                var_dump($invoice_summ == $invoice_summ_pos);
//                                var_dump($invoice_summins);
//                                var_dump($invoice_summins == $invoice_summ_pos);

                                }
                            }

                        //}
                    }
                }
            }
//            var_dump($rezult_arr);

            //Если какие-то косяки с категориями процентов
//            echo '<div class="no_print" style="font-size: 110%;">';
//
//            if (strlen($warn_str_percent_cats) > 0) {
//                echo '<li class="filterBlock" style="color: red;">Наряды, требуют дополнительной проверки указанных категорий</li>';
//                echo '<li class="filterBlock">'.$warn_str_percent_cats.'</li>';
//            }
//
//            echo '</div>';


            //Расходы, выдано из кассы
            $giveoutcash_j = array();

            //Даты от и до
            $datastart = $year.'-'.$month.'-'.$day.' 00:00:00';
            $dataend = $year.'-'.$month.'-'.$day.' 23:59:59';

            //Поехали собирать расходные ордера
            $query = "SELECT * FROM `journal_giveoutcash` WHERE
                    MONTH(`date_in`) = '".dateTransformation ($month)."' AND YEAR(`date_in`) = '{$year}' 
                    AND `office_id`='{$filial_id}' AND `status` <> '9' 
                    ORDER BY `type` DESC";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($giveoutcash_j, $arr);
                }
            }
            //var_dump($giveoutcash_j);



            //Сертификаты проданные
            $certificates_j = array();
            //Сумма общая, за которую продали
            $certificates_summSell = 0;

            $query = "SELECT `cell_price` FROM  `journal_cert` WHERE `office_id` = '{$filial_id}' AND (`status`='7' OR `status`='5') AND MONTH(`cell_time`) = '".dateTransformation ($month)."' AND YEAR(`cell_time`) = '{$year}' ";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($certificates_j, $arr);
                    $certificates_summSell += $arr['cell_price'];
                }
            }
            //var_dump($certificates_j);
            //var_dump($certificates_summSell);








            //Получаем данные из сводного отчета за месяц
                $reports_j = array();

                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month' AND `status` = '7'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($reports_j, $arr);
                    }
                }
                //var_dump($reports_j);


                //$report_header = '';

                echo '
			        <div id="report" class="report" style="margin-top: 10px;">';

                $cashbox_nal = 0;
                $beznal = 0;
                $arenda = 0;
                $rashod = 0;
                //$ostatok = 0;

                $temp_solar_beznal = 0;
                $temp_solar_nal = 0;

                foreach ($reports_j as $report){
                    $cashbox_nal += $report['nal'];
                    $beznal += $report['beznal'];
                    $arenda += $report['arenda'];
                    $rashod += $report['temp_giveoutcash'];

                    $temp_solar_nal += $report['temp_solar_nal'];
                    $temp_solar_beznal += $report['temp_solar_beznal'];
                }

//                var_dump('Итоги');
//                var_dump('---------------------------------');
//
//                var_dump('Нал');
//                var_dump($cashbox_nal);
//                var_dump('Безнал');
//                var_dump($beznal);
//                var_dump('Аренда');
//                var_dump($arenda);
//
//                var_dump('Выручка вся');
//                var_dump(number_format($cashbox_nal + $beznal + $arenda, 0, '.', ' '));
//
//                var_dump('/////////////////////////////////////////////');
//
//                var_dump('Наличные');
//                var_dump('---------------------------------');
//
//                var_dump('Нал');
//                var_dump($cashbox_nal);
//                var_dump('Аренда');
//                var_dump($arenda);
//                var_dump('Расход');
//                var_dump($rashod);
//                var_dump('Остаток');
//                var_dump(number_format($cashbox_nal + $arenda - $rashod, 0, '.', ' '));


            echo '<div style="display: inline-block; vertical-align: top;">';
//
//            echo '
//                    <li class="filterBlock">
//                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
//                           наличные касса
//                        </div>
//                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';
//
//            echo number_format($cashbox_nal, 0, '.', ' ');
//
//                echo '
//                        </div>
//                    </li>';


            echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                           <b>наличные касса</b>
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px;">
                            <div style="float:left;">'.number_format($cashbox_nal, 0, '.', ' ').'</div>
                        </div>
                    </li>';

            echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                           <b>аренда</b>
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px;">
                            <div style="float:left;">'.number_format($arenda, 0, '.', ' ').'</div>
                        </div>
                    </li>';


            echo '</div>';


                //не понял, что это и для чего, сравнивая с флешкой
//            4 =>
//        array (size=1)
//          'name' => string 'Пародонтология' (length=28)
//
//            7 =>
//        array (size=1)
//          'name' => string 'Дополнительно' (length=26)


            //Пробуем вывести то, что получили

            echo '<div style="display: inline-block; vertical-align: top;">';
            //Стоматология
            if (isset($rezult_arr[5])){
                echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                           Стоматология
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                if (!empty($rezult_arr[5])){
                    if (!empty($rezult_arr[5]['data'])){
                        //arsort($rezult_arr[5]['data']);
                        echo number_format(array_sum($rezult_arr[5]['data']), 0, '.', ' ');
                    }else{
                        echo 'нет данных';
                    }
                }else{
                    echo 'нет данных';
                }

                echo '
                        </div>
                    </li>';
            }


            //Сумма за детскую стоматологию
            $child_stom_summ = 0;

            //Если за детскую стоматологию есть сумма
            if (isset($rezult_arr[5])){
                if (isset($rezult_arr[5]['child_stom_summ'])){
                    $child_stom_summ = $rezult_arr[5]['child_stom_summ'];
                }
            }


            if (isset($rezult_arr[5])){
                if (!empty($rezult_arr[5])){
                    if (!empty($rezult_arr[5]['data'])){
                        arsort($rezult_arr[5]['data']);
                        //var_dump($rezult_arr[5]['data']);

                        foreach($rezult_arr[5]['data'] as $percent_cat_id => $value) {

                            //$pervent_value = ;
                            //var_dump($percent_cat_id);

                            //Если все хорошо в массиве с данными
                            if ((strlen($percent_cat_id) > 0) && ($value != 0)) {
                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percents_j[5][$percent_cat_id]['name'] . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 245px; min-width: 245px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format((($value * 100) / (array_sum($rezult_arr[5]['data']) - $child_stom_summ)), 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                            }else{

                            }
                        }
                        //Детство отдельно
                        echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                   <b>Детство</b>
                                </div>
                                <div class="cellRight" style="width: 245px; min-width: 245px;">
                                    <div style="float:left;">'.number_format($child_stom_summ, 0, '.', ' ').'</div> <div style="float:right;">'.number_format((($child_stom_summ * 100)/ (array_sum($rezult_arr[5]['data']) - $child_stom_summ)), 2, '.', '').'%</div>
                                </div>
                            </li>';
                    }
                }
            }

            //Ассистенты
            if (isset($rezult_arr[7])){
                echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                           Ассистенты
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                if (!empty($rezult_arr[7])){
                    if (!empty($rezult_arr[7]['data'])){
                        //arsort($rezult_arr[10]['data']);
                        echo number_format(array_sum($rezult_arr[7]['data']), 0, '.', ' ');
                    }else{
                        echo 'нет данных';
                    }
                }else{
                    echo 'нет данных';
                }
                echo '
                        </div>
                    </li>';
            }




            if (isset($rezult_arr[7])){
                if (!empty($rezult_arr[7])){
                    if (!empty($rezult_arr[7]['data'])){
                        arsort($rezult_arr[7]['data']);

                        foreach($rezult_arr[7]['data'] as $percent_cat_id => $value) {

                            if (isset($percents_j[7][$percent_cat_id])){
                                $percent_cat_name = $percents_j[7][$percent_cat_id]['name'];
                            }else{
                                $percent_cat_name = $percents_j2[$percent_cat_id]['name'].'<i class="fa fa-warning" aria-hidden="true"></i>';
                            }

                            echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                   <b>'.$percent_cat_name.'</b>
                                </div>
                                <div class="cellRight" style="width: 245px; min-width: 245px;">
                                    <div style="float:left;">'.number_format($value, 0, '.', ' ').'</div> <div style="float:right;">'.number_format((($value * 100)/ array_sum($rezult_arr[7]['data'])), 2, '.', '').'%</div>
                                </div>
                            </li>';
                        }
                    }
                }
            }



            //Косметология
            if (isset($rezult_arr[6])){
                echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                           Косметология
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                if (!empty($rezult_arr[6])){
                    if (!empty($rezult_arr[6]['data'])){
                        //arsort($rezult_arr[5]['data']);
                        echo number_format(array_sum($rezult_arr[6]['data']), 0, '.', ' ');
                    }else{
                        echo 'нет данных';
                    }
                }else{
                    echo 'нет данных';
                }
                echo '
                        </div>
                    </li>';
            }


            if (isset($rezult_arr[6])){
                if (!empty($rezult_arr[6])){
                    if (!empty($rezult_arr[6]['data'])){
                        arsort($rezult_arr[6]['data']);

                        foreach($rezult_arr[6]['data'] as $percent_cat_id => $value) {

                            //$pervent_value = ;


                            echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                   <b>'.$percents_j[6][$percent_cat_id]['name'].'</b>
                                </div>
                                <div class="cellRight" style="width: 245px; min-width: 245px;">
                                    <div style="float:left;">'.number_format($value, 0, '.', ' ').'</div> <div style="float:right;">'.number_format((($value * 100)/ array_sum($rezult_arr[6]['data'])), 2, '.', '').'%</div>
                                </div>
                            </li>';
                        }
                    }
                }
            }

            if ($temp_solar_nal + $temp_solar_beznal != 0) {
                //Солярий
                echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                           Солярий
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                echo
                            number_format(($temp_solar_nal + $temp_solar_beznal), 0, '.', ' ');
                echo '
                        </div>
                    </li>';
            }

            //Специалисты
            if (isset($rezult_arr[10])){
                echo '
                    <li class="filterBlock">
                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                           Специалисты
                        </div>
                        <div class="cellRight" style="width: 245px; min-width: 245px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                if (!empty($rezult_arr[10])){
                    if (!empty($rezult_arr[10]['data'])){
                        //arsort($rezult_arr[10]['data']);
                        echo number_format(array_sum($rezult_arr[10]['data']), 0, '.', ' ');
                    }else{
                        echo 'нет данных';
                    }
                }else{
                    echo 'нет данных';
                }
                echo '
                        </div>
                    </li>';
            }




            if (isset($rezult_arr[10])){
                if (!empty($rezult_arr[10])){
                    if (!empty($rezult_arr[10]['data'])){
                        arsort($rezult_arr[10]['data']);

                        foreach($rezult_arr[10]['data'] as $percent_cat_id => $value) {

                            //$pervent_value = ;


                            echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                   <b>'.$percents_j[10][$percent_cat_id]['name'].'</b>
                                </div>
                                <div class="cellRight" style="width: 245px; min-width: 245px;">
                                    <div style="float:left;">'.number_format($value, 0, '.', ' ').'</div> <div style="float:right;">'.number_format((($value * 100)/ array_sum($rezult_arr[10]['data'])), 2, '.', '').'%</div>
                                </div>
                            </li>';
                        }
                    }
                }
            }



                echo '
                    <!--</ul>-->
			    </div>';

            echo '
                    </div>
                </div>
                <div id="doc_title">Отчёт - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
			echo '

				<script type="text/javascript">
				
				    //Изменили тут стили основные, чтоб умещалось
                    $(document).ready(function() {
                        $("#main").css({margin: \'0\', padding: \'10px 0 20px\'});                        
                        $("#header").css({"padding-left": \'10px\'});                        
                        $("#data").css({margin: \'10px \'});                        
                        $("#livefilter-list").css({width: \'min-content\'});                        
                    });

                    $(function() {
                        $("#SelectFilial").change(function(){
                            
                            blockWhileWaiting (true);
                            
                            var get_data_str = "";
                            
                            var params = window
                                .location
                                .search
                                .replace("?","")
                                .split("&")
                                .reduce(
                                    function(p,e){
                                        var a = e.split(\'=\');
                                        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                        return p;
                                    },
                                    {}
                                );
                            //console.log(params);
                                                            
                            for (key in params) {
                                if (key.indexOf("filial_id") == -1){
                                    get_data_str = get_data_str + "&" + key + "=" + params[key];
                                }
                            }
                            //console.log(get_data_str);
                            
                            document.location.href = "?filial_id="+$(this).val() + "&" + get_data_str;
                        });
                    });
                    
                    $(document).ready(function(){

                        var elems = $(".blockControl"), count = elems.length;

                        elems.each( function() {
                            
                            //fl_getDailyReports($(this));
                            
                            if (!--count){
                                //console.log(count);
                                
                                //fl_getDailyReportsSummAllMonth ();
                            }

                        });
/*                        
                        //Выделить в отдельную функцию?
                        $(".blockControl").each(function(){
                            //console.log(1);
                        
                            //Дата
                            //var date = ($(this).find(".reportDate").html());
                            //console.log(date);
                            
                            fl_getDailyReports($(this));
                            
                        }).promise().done( function(){ 
                            //Суммы за месяц
                            alert(1); 
                            fl_getDailyReportsSummAllMonth (); 
                        });*/
                        

                        

                        //Клик на дате
                        $("body").on("click", ".reportDate", function(event){

                            // Проверяем нажата ли именно левая кнопка мыши:
                            if (event.which === 1){
                                
                                // Получаем элемент на котором был совершен клик:
                                var target = $(event.target);
                                //console.log(target.attr(\'status\'));                            
                                
                                contextMenuShow(target.attr(\'report_id\'), target.attr(\'status\'), event, \'consRepAdm\');
                            }
                        });

                    });				
                
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>