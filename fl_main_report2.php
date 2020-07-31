<?php

//fl_main_report2.php
//Финальный отчет v2.0

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		//if (($finances['see_all'] == 1) || $god_mode){
        //if (($_SESSION['id'] == 270) || ($god_mode)){
        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'widget_calendar.php';
        include_once 'ffun.php';
        include_once 'fl_main_report2_f.php';
        require 'variables.php';

        //Опция доступа к филиалам конкретных сотрудников
        $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
        //var_dump($optionsWF);

        if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){


            //$permissions_sort_method = [5,6,10,7,4,13,14,15,9,12,11,777];

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            //!!! костыль для меня =)
            //array_push($permissions_j, array('id' => '777', 'name' => 'Сис.админ'));
            $permissions_j[777] = array('id' => '777', 'name' => 'Сис.админ');
            //var_dump($permissions_j);


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

            if (!$god_mode) {
                if (!in_array($filial_id, $optionsWF[$_SESSION['id']])) {
                    $filial_id = $optionsWF[$_SESSION['id']][0];
                }
            }


            //Пробуем получить данные (тест)
            $datas = fl_main_report2_f($month, $year, $filial_id);

            $rezult_arr = $datas['rezult_arr'];
            $cashbox_nal = $datas['cashbox_nal'];
            $arenda = $datas['arenda'];
            $money_from_outside = $datas['money_from_outside'];
            $beznal = $datas['beznal'];
            $giveoutcash_summ = $datas['giveoutcash_summ'];
            $subtractions_j = $datas['subtractions_j'];
            $subtractions_summ = $datas['subtractions_summ'];
            $paidouts_temp_j = $datas['paidouts_temp_j'];
            $paidouts_temp_summ = $datas['paidouts_temp_summ'];
            $giveoutcash_ex_j = $datas['giveoutcash_ex_j'];
            $bank_summ = $datas['bank_summ'];
            $director_summ = $datas['director_summ'];
            $temp_solar_beznal = $datas['temp_solar_beznal'];
            $temp_solar_nal = $datas['temp_solar_nal'];
            $percents_j = $datas['percents_j'];
            $giveoutcash_j = $datas['giveoutcash_j'];
            $give_out_cash_types_j = $datas['give_out_cash_types_j'];
            $prev_month_filial_summ_arr = $datas['prev_month_filial_summ_arr'];
            $zapis_j = $datas['zapis_j'];
            $pervich_summ_arr_new = $datas['pervich_summ_arr_new'];

            $dop = 'filial_id='.$filial_id;

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav never_print_it">
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'&m='.$month.'&y='.$year.'" class="b">Сводный отчёт по филиалу</a>
                            <a href="fl_money_from_outside_add.php" class="b">Добавить приход вручную</a>
                        </div>
                        <h2 style="padding: 0;">Отчёт '.$filials_j[$filial_id]['name2'].' / '.$monthsName[$month].' '.$year.'</h2>
                    </header>';



            echo '
                    <div id="data">';

//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, date('m', time()), date('Y', time()))));
//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, $month, $year)));
//            var_dump(time());

            if (TRUE) {
                echo '				
                            <div id="errrror"></div>';

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_main_report2.php', $dop);
                echo '</div><br>';

                //Выбор филиала
                echo '
                            <div class="no_print" style="font-size: 90%; ">
                                Филиал: ';

                //if (($finances['see_all'] == 1) || $god_mode) {

                echo '
                                <select name="SelectFilial" id="SelectFilial">';

                foreach ($filials_j as $filial_item) {

                    $selected = '';

                    if (in_array($filial_item['id'], $optionsWF[$_SESSION['id']]) || $god_mode) {
                        if ($filial_id == $filial_item['id']) {
                            $selected = 'selected';
                        }


                        echo '
                                    <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                    }
                }

                echo '
                                </select>';
                //            } else {
                //
                //                echo $filials_j[$_SESSION['filial']]['name'] . '<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $_SESSION['filial'] . '">';
                //
                //            }

                //Выбор месяц и год
//                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: ';
//                echo '
//                                <span id="getThisCalendar">
//                                    <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
//                foreach ($monthsName as $mNumber => $mName) {
//                    $selected = '';
//                    if ((int)$mNumber == (int)$month) {
//                        $selected = 'selected';
//                    }
//                    echo '
//                                        <option value="' . $mNumber . '" ' . $selected . '>' . $mName . '</option>';
//                }
//                echo '
//                                    </select>
//                                    <select name="iWantThisYear" id="iWantThisYear">';
//                for ($i = 2017; $i <= (int)date('Y') + 2; $i++) {
//                    $selected = '';
//                    if ($i == (int)$year) {
//                        $selected = 'selected';
//                    }
//                    echo '
//                                        <option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
//                }
//                echo '
//                                    </select>
//                                </span>
//                                <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisDate(\'fl_main_report2.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
//                                <div style="font-size: 90%; color: rgb(125, 125, 125); float: right;">Сегодня: <a href="fl_main_report2.php" class="ahref">' . date("d") . ' ' . $monthsName[date("m")] . ' ' . date("Y") . '</a></div>';
                echo '
                            </div>';

                //Количество дней в месяце
//                $month_stamp = mktime(0, 0, 0, $month, 1, $year);
//                $day_count = date("t", $month_stamp);

                //или так
                //$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//                $msql_cnnct = ConnectToDB();
//
//                //Соберём все категории процентов (справочник)
//                // по типу
//                $percents_j = array();
//                // по id
//                $percents_j2 = array();
//
//                $query = "SELECT `id`, `name`, `type` FROM  `fl_spr_percents`";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        if (!isset($percents_j[$arr['type']])) {
//                            $percents_j[$arr['type']] = array();
//                        }
//                        $percents_j[$arr['type']][$arr['id']]['name'] = $arr['name'];
//
//                        if (!isset($percents_j2[$arr['id']])) {
//                            $percents_j2[$arr['id']] = array();
//                        }
//
//                        $percents_j2[$arr['id']]['name'] = $arr['name'];
//
//
//                    }
//                }
//                //var_dump($percents_j);
//                //var_dump($percents_j2);
//
//                //Типы расходов (справочник)
//                $give_out_cash_types_j = array();
//
//                $query = "SELECT `id`,`name` FROM `spr_cashout_types`";
//                //var_dump($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        $give_out_cash_types_j[$arr['id']] = $arr['name'];
//                    }
//                }
//
//
//                //Типы посещений - первичка/нет (количество) (pervich)
//                //Памятка
//                //1 - Посещение для пациента первое без работы
//                //2 - Посещение для пациента первое с работой
//                //3 - Посещение для пациента не первое
//                //4 - Посещение для пациента не первое, но был более полугода назад
//                //5 - Продолжение работы
//                //6 - Без записи (enter)
//                $pervich_summ_arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
//
//                //Получаем данные по записи за месяц
//                $zapis_j = array();
//                $zapis_j_noch = array();
//                //Не пришло
//                $zapis_not_enter = 0;
//                //ID записей
//                $zapis_ids = array();
//
//                //Кроме тех, которые удалены или не пришли
//                $query = "SELECT `id`, `type`, `patient`, `pervich`, `insured`, `noch`, `enter`  FROM `zapis` WHERE `office` = '{$filial_id}' AND `year` = '{$year}' AND `month` = '{$month}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `day` ASC";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //Если ночь
//                        if ($arr['noch'] == 1) {
//                            array_push($zapis_j_noch, $arr);
//                        } else {
//                            if (!isset($zapis_j[$arr['type']])) {
//                                $zapis_j[$arr['type']] = array();
//                                $zapis_j[$arr['type']]['pervich_summ_arr'] = $pervich_summ_arr;
//                                $zapis_j[$arr['type']]['insured'] = 0;
//                            }
//                            //array_push($zapis_j[$arr['type']], $arr);
//                            //array_push($zapis_j, $arr);
//
//                            //Если страховое
//                            //var_dump($arr['insured']);
//                            if ($arr['insured'] == 1) {
//                                //Если пришёл
//                                //var_dump($arr['enter']);
//                                //первичка/нет
//                                if (($arr['enter'] == 1) || ($arr['enter'] == 6)) {
//                                    $zapis_j[$arr['type']]['insured']++;
//
//                                    array_push($zapis_ids, $arr['id']);
//                                } else {
//                                }
//
//                                //Не страховой
//                            } else {
//                                //Если пришёл
//                                //var_dump($arr['enter']);
//                                //первичка/нет
//                                if (($arr['enter'] == 1) || ($arr['enter'] == 6)) {
//                                    $zapis_j[$arr['type']]['pervich_summ_arr'][$arr['pervich']]++;
//
//                                    array_push($zapis_ids, $arr['id']);
//                                } else {
//                                    if ($arr['enter'] == 0) {
//                                        //не пришел
//                                        $zapis_not_enter++;
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//                //Ночь
//                //var_dump($zapis_j_noch);
//                //День
//                //var_dump($zapis_j);
//                //var_dump($zapis_j[7]);
//                //Не пришли
//                //var_dump($zapis_not_enter);
//                //var_dump($zapis_ids);
//
//                //Преобразуем массив ID записей для запросов - !!! не используем
//                //$zapis_ids_str = implode("','", $zapis_ids);
//                //var_dump($zapis_ids_str);
//                //$query=mysqli_query($conn, "SELECT name FROM users WHERE id IN ('".zapis_ids_str."')");
//
//                //Выберем наряды по записям
//                $invoices_j = array();
//                $invoices_j2 = array();
//                $invoices_notinsure_ids = array();
//
//                //Массив, где будем хранить суммы нарядов, чтобы потом определять первичное посещение или нет по сумме
//                $zapis_summ = array();
//
//                $query = "
//                        SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.status AS invoice_status, ji.type AS type, ji.zapis_id AS zapis_id, z.enter AS enter, z.pervich AS pervich, sc.birthday2 AS birthday
//                        FROM `zapis` z
//                        INNER JOIN `journal_invoice` ji ON z.id = ji.zapis_id AND
//                        z.office = '{$filial_id}' AND z.year = '{$year}' AND z.month = '{$month}' AND (z.enter = '1' OR z.enter = '6')
//                        LEFT JOIN `journal_invoice_ex` jiex ON ji.id = jiex.invoice_id
//                        LEFT JOIN `spr_clients` sc ON ji.client_id = sc.id
//                        WHERE ji.status <> '9'";
//                //var_dump($query);
//                //echo ($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //var_dump($arr);
//
//                        array_push($invoices_j2, $arr);
//
//                        //Пришел/не пришел/с улицы
//                        if (!isset($invoices_j[$arr['enter']])) {
//                            $invoices_j[$arr['enter']] = array();
//
//                        }
//                        //тип стом, косм, ...
//                        if (!isset($invoices_j[$arr['enter']][$arr['type']])) {
//                            $invoices_j[$arr['enter']][$arr['type']] = array();
//                            $invoices_j[$arr['enter']][$arr['type']]['data'] = array();
//                            $invoices_j[$arr['enter']][$arr['type']]['insure_data'] = array();
//                            $invoices_j[$arr['enter']][$arr['type']]['child_stom_summ'] = 0;
//                        }
//                        //Если страховой
//                        if ($arr['insure'] == 1) {
//                            if (!isset($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']])) {
//                                $invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']] = array();
//                            }
//                            array_push($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']], $arr);
//
//                        } else {
//                            if (!isset($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']])) {
//                                $invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']] = array();
//                            }
//                            array_push($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']], $arr);
//
//                        }
//
//
//                        //Теперь суммы нарядов
//                        //Пришел/не пришел/с улицы
//                        if (!isset($zapis_summ[$arr['type']])) {
//                            $zapis_summ[$arr['type']] = array();
//
//                        }
//                        //тип стом, косм, ...
//                        if (!isset($zapis_summ[$arr['type']][$arr['pervich']])) {
//                            $zapis_summ[$arr['type']][$arr['pervich']] = array();
//                            $zapis_summ[$arr['type']][$arr['pervich']]['data'] = array();
//                            $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'] = array();
//                        }
//                        //Если страховой
//                        if ($arr['insure'] == 1) {
//                            if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']])) {
//                                $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] = 0;
//                            }
//                            $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] += (int)$arr['itog_price'];
//                        } else {
//                            if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']])) {
//                                $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] = 0;
//                            }
//                            $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] += (int)$arr['itog_price'];
//                        }
//                        //if ($arr['invoice_id'] = 83364) var_dump($arr);
//                    }
//                }
//                //сортируем по основным ключам
//                ksort($invoices_j);
//                ksort($zapis_summ);
//                //            var_dump($zapis_summ);
//
//                //!!! тестовая проверка нового определения первичек
//                $pervich_summ_arr_new = array();
//
//                //            foreach($pervich_summ_arr as $y_id => $y){
//                //                if (isset($zapis_summ[$y_id][5]['data'])){
//                //                    foreach ($zapis_summ[$y_id][5]['data'] as $i_id => $i_summ){
//                //                        //var_dump($i_id.' => '.$i_summ);
//                //                        if ($i_summ <= 1100){
//                //                            $pervich_summ_arr_new[5][$y_id] ++;
//                //                        }
//                //                    }
//                //                }
//                //            }
//                //var_dump($pervich_summ_arr_new);
//
//                foreach ($zapis_summ as $type => $pervich_data) {
//                    foreach ($pervich_data as $pervich => $zapis_data) {
//                        if (!isset($pervich_summ_arr_new[$type])) {
//                            $pervich_summ_arr_new[$type] = $pervich_summ_arr;
//                        }
//                        //                    if  (!isset($pervich_summ_arr_new[$type][$pervich])){
//                        //                        $pervich_summ_arr_new[$type][$pervich] = 0;
//                        //                    }
//                        if (isset($zapis_data['data'])) {
//                            if (!empty($zapis_data['data'])) {
//                                foreach ($zapis_data['data'] as $i_id => $i_summ) {
//                                    if ($pervich == 1 || $pervich == 2) {
//                                        //Стоматология
//                                        if ($type == 5) {
//                                            if ($i_summ >= 0) {
//                                                if ($i_summ < 1100) {
//                                                    $pervich_summ_arr_new[$type][1]++;
//                                                } else {
//                                                    $pervich_summ_arr_new[$type][2]++;
//                                                }
//                                            }
//                                        }
//                                        //Косметология
//                                        //!!! Доделать
//                                        if ($type == 6) {
//                                            if ($i_summ >= 0){
//                                                if ($i_summ < 550) {
//                                                    $pervich_summ_arr_new[$type][1]++;
//                                                } else {
//                                                    $pervich_summ_arr_new[$type][2]++;
//                                                }
//                                            }
//                                        }
//                                        //Соматика
//                                        if ($type == 10) {
//                                            if ($i_summ >= 0) {
//                                                if ($i_summ < 990) {
//                                                    $pervich_summ_arr_new[$type][1]++;
//                                                } else {
//                                                    $pervich_summ_arr_new[$type][2]++;
//                                                }
//                                            }
//                                        }
//                                    }
//                                    if ($pervich == 3 || $pervich == 4 || $pervich == 5) {
//                                        if ($i_summ > 0) {
//                                            $pervich_summ_arr_new[$type][3]++;
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//                //var_dump($pervich_summ_arr_new[5]);
//
//
//                //Памятка
//                //1 - Посещение для пациента первое без работы
//                //2 - Посещение для пациента первое с работой
//                //3 - Посещение для пациента не первое
//                //4 - Посещение для пациента не первое, но был более полугода назад
//                //5 - Продолжение работы
//                //6 - Без записи (enter)
//
//
//                foreach ($invoices_j as $id => $data) {
//                    //сортируем по ключам, которые тип стом, косм,...
//                    ksort($invoices_j[$id]);
//                }
//                //var_dump($invoices_j[1][5]['data']);
//                //var_dump($invoices_j2);
//
//                //Итоговый массив для хранения по типам стом/косм/...
//                $rezult_arr = array();
//                //для хранения страховые и нет
//                //$rezult_arr['data'] = array();
//                //$rezult_arr['insure_data'] = array();
//                //суммы по категориям
//                //$rezult_arr['data'][ID категории]['category_summ'] = 0;
//
//                //!!! Для костыля с %
//                //            //Массив для хранения общих сумм по категориям
//                //            $rezult_arr_summ = array();
//                //            //Массив для хранения % по категориям
//                //            $rezult_arr_prcnt = array();
//
//                //Костыль для типа 7
//                $rezult_arr[7]['data'] = array();
//
//                //переменная для строки, где будут ссылки на наряды, если с ними что-то не так
//                $warn_str_percent_cats = '';
//
//                //Проход по нарядам
//                foreach ($invoices_j as $enter => $enter_data) {
//                    //Если пришел к врачу
//                    if (($enter == 1) || ($enter == 6)) {
//                        foreach ($enter_data as $type => $type_data) {
//
//                            if (!isset($rezult_arr[$type])) {
//                                $rezult_arr[$type] = array();
//                                $rezult_arr[$type]['child_stom_summ'] = 0;
//                            }
//
//                            //Если стоматолог
//                            //if ($type == 5){
//                            //Проход по нарядам
//                            //не страховые
//                            foreach ($type_data['data'] as $invoice_id => $invoice_data) {
//                                //                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                //                                var_dump($invoice_id);
//
//                                if (!isset($rezult_arr[$type]['data'])) {
//                                    $rezult_arr[$type]['data'] = array();
//                                }
//
//                                //                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                //                                var_dump($invoice_id);
//
//                                $invoice_summ = 0;
//                                $invoice_summins = 0;
//
//                                $invoice_summ_pos = 0;
//
//                                $pervich_status = 0;
//
//                                //Дети стоматологии
//                                $child_stom_summ = 0;
//
//                                //Проход по данным наряда (позиции)
//                                foreach ($invoice_data as $data) {
//                                    //var_dump($data);
//                                    //var_dump($data['percent_cats']);
//
//                                    //debug
//                                    //                                    var_dump(strlen($data['percent_cats']));
//                                    //                                    if (strlen($data['percent_cats']) == 0){
//                                    //                                        var_dump($invoice_id);
//                                    //                                    }
//
//                                    //Если не гарантия/не подарок
//                                    if (($data['guarantee'] != 1) && ($data['gift'] != 1)) {
//
//                                        if (!isset($percents_j[$type][$data['percent_cats']])) {
//                                            if (strlen($data['percent_cats']) > 0) {
//                                                //var_dump($invoice_id.' ['.$type.'] => '.$data['percent_cats']);
//                                                $warn_str_percent_cats .= '<a href="invoice.php?id=' . $invoice_id . '" class="ahref button_tiny" style="margin: 0 2px; font-size: 80%;">#' . $invoice_id . '</a>';
//                                            }
//                                        }
//
//                                        $invoice_summ = $data['invoice_summ'];
//                                        $invoice_summins = $data['invoice_summins'];
//                                        //var_dump($data['itog_price']);
//
//                                        $invoice_summ_pos += $data['itog_price'];
//
//                                        //$pervich_status = $data['pervich'];
//
//                                        //Если не продолжение работы
//                                        //if ($data['pervich'] != 5) {
//                                        //var_dump($data['percent_cats']);
//
//                                        //Дети стоматология
//                                        //var_dump(getyeardiff(strtotime($data['birthday']), 0));
//                                        if (($type == 5) && (getyeardiff(strtotime($data['birthday']), 0) <= 14)) {
//                                            $child_stom_summ += $data['itog_price'];
//                                            //var_dump($invoice_id);
//
//                                            //Соберем общие суммы по категориям
//                                            if (!isset($rezult_arr_summ[$type])) {
//                                                $rezult_arr_summ[$type] = 0;
//                                            }
//                                            $rezult_arr_summ[$type] += $data['itog_price'];
//
//                                        } else {
//                                            //Костыль для категории 7 (ассистенты)
//                                            //Если не ассистенты
//                                            if (!in_array($data['percent_cats'], [58, 59, 61, 62])) {
//                                                if (!isset($rezult_arr[$type]['data'][$data['percent_cats']])) {
//                                                    $rezult_arr[$type]['data'][$data['percent_cats']] = 0;
//                                                }
//                                                $rezult_arr[$type]['data'][$data['percent_cats']] += $data['itog_price'];
//
//                                                //Соберем общие суммы по категориям
//                                                if (!isset($rezult_arr_summ[$type])) {
//                                                    $rezult_arr_summ[$type] = 0;
//                                                }
//                                                $rezult_arr_summ[$type] += $data['itog_price'];
//
//                                            } else {
//                                                //Если ассистенты (позиция, которая используется только для ассистов (кт, орто))
//                                                if (!isset($rezult_arr[7]['data'][$data['percent_cats']])) {
//                                                    $rezult_arr[7]['data'][$data['percent_cats']] = 0;
//                                                }
//                                                $rezult_arr[7]['data'][$data['percent_cats']] += $data['itog_price'];
//
//                                                //Соберем общие суммы по категориям
//                                                if (!isset($rezult_arr_summ[7])) {
//                                                    $rezult_arr_summ[7] = 0;
//                                                }
//                                                $rezult_arr_summ[7] += $data['itog_price'];
//                                            }
//                                        }
//                                        //}
//                                    }
//                                }
//                                //var_dump( $rezult_arr_summ);
//
//                                //Детство отдельно добавим
//                                if ($type == 5) {
//                                    $rezult_arr[$type]['child_stom_summ'] += $child_stom_summ;
//                                }
//                                //                                var_dump('_____________________________');
//                                //                                if ($pervich_status  == 5){
//                                //                                    var_dump('***___***___***___***');
//                                //                                }
//                                //                                var_dump('$pervich_status');
//                                //                                var_dump($pervich_status);
//                                //                                var_dump($invoice_summ_pos);
//                                //                                var_dump($invoice_summ);
//                                //                                var_dump($invoice_summ == $invoice_summ_pos);
//                                //                                var_dump($invoice_summins);
//                                //                                var_dump($invoice_summins == $invoice_summ_pos);
//
//                            }
//
//                            //страховые
//                            if (!empty($type_data['insure_data'])) {
//                                //Проход по страховым нарядам
//                                foreach ($type_data['insure_data'] as $invoice_id => $invoice_data) {
//
//                                    if (!isset($rezult_arr[$type]['insure_data'])) {
//                                        $rezult_arr[$type]['insure_data'] = array();
//                                    }
//
//                                    //                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
//                                    //                                var_dump($invoice_id);
//
//                                    $invoice_summ = 0;
//                                    $invoice_summins = 0;
//
//                                    $invoice_summ_pos = 0;
//
//                                    $pervich_status = 0;
//
//                                    //Проход по данным наряда (позиции)
//                                    foreach ($invoice_data as $data) {
//                                        //var_dump($data);
//                                        //var_dump($data['itog_price']);
//
//                                        $invoice_summ = $data['invoice_summ'];
//                                        $invoice_summins = $data['invoice_summins'];
//
//                                        $invoice_summ_pos += $data['itog_price'];
//
//                                        //$pervich_status = $data['pervich'];
//
//                                        //Если не продолжениении работы
//                                        if ($data['pervich'] != 5) {
//                                            if (!isset($rezult_arr[$type]['insure_data'][$data['percent_cats']])) {
//                                                $rezult_arr[$type]['insure_data'][$data['percent_cats']] = 0;
//                                            }
//                                            $rezult_arr[$type]['insure_data'][$data['percent_cats']] += $data['itog_price'];
//                                        }
//
//                                    }
//
//                                    //                                var_dump('_____________________________');
//                                    //                                if ($pervich_status  == 5){
//                                    //                                    var_dump('***___***___***___***');
//                                    //                                }
//                                    //                                var_dump($invoice_summ_pos);
//                                    //                                var_dump($invoice_summ);
//                                    //                                var_dump($invoice_summ == $invoice_summ_pos);
//                                    //                                var_dump($invoice_summins);
//                                    //                                var_dump($invoice_summins == $invoice_summ_pos);
//
//                                }
//                            }
//
//                            //}
//                        }
//                    }
//                }
//                //            var_dump($rezult_arr);
//                //            var_dump($rezult_arr_summ);
//
//
//                //Если какие-то косяки с категориями процентов
//                //            echo '<div class="no_print" style="font-size: 110%;">';
//                //
//                //            if (strlen($warn_str_percent_cats) > 0) {
//                //                echo '<li class="filterBlock" style="color: red;">Наряды, требуют дополнительной проверки указанных категорий</li>';
//                //                echo '<li class="filterBlock">'.$warn_str_percent_cats.'</li>';
//                //            }
//                //
//                //            echo '</div>';
//
//
//                //Расходы, выдано из кассы
//                $giveoutcash_j = array();
//                //Расходы, выдано из кассы подробно
//                $giveoutcash_ex_j = array();
//                //Сумма расходов
//                $giveoutcash_summ = 0;
//
//                //Даты от и до
////                $datastart = $year . '-' . $month . '-' . $day . ' 00:00:00';
////                $dataend = $year . '-' . $month . '-' . $day . ' 23:59:59';
//
//                //Поехали собирать расходные ордера
//                $query = "SELECT * FROM `journal_giveoutcash` WHERE
//                        MONTH(`date_in`) = '" . dateTransformation($month) . "' AND YEAR(`date_in`) = '{$year}'
//                        AND `office_id`='{$filial_id}' AND `status` <> '9'
//                        ORDER BY `type` DESC";
//                //var_dump($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($giveoutcash_ex_j, $arr);
//
//                        if (!isset($giveoutcash_j[$arr['type']])) {
//                            $giveoutcash_j[$arr['type']] = 0;
//                        }
//                        $giveoutcash_j[$arr['type']] += $arr['summ'];
//
//                        $giveoutcash_summ += $arr['summ'];
//                    }
//                }
//                //var_dump($giveoutcash_j);
//                //            var_dump($giveoutcash_ex_j);
//
//
//                //Сертификаты проданные
//                $certificates_j = array();
//                //Сумма общая, за которую продали
//                $certificates_summSell = 0;
//
//                $query = "SELECT `cell_price` FROM  `journal_cert` WHERE `office_id` = '{$filial_id}' AND (`status`='7' OR `status`='5') AND MONTH(`cell_time`) = '" . dateTransformation($month) . "' AND YEAR(`cell_time`) = '{$year}' ";
//                //var_dump($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($certificates_j, $arr);
//                        $certificates_summSell += $arr['cell_price'];
//                    }
//                }
//                //var_dump($certificates_j);
//                //var_dump($certificates_summSell);
//
//
//                //Сертификаты использованные при оплате из ранее проданных
//                $certificate_payments_j = array();
//                //Сумма общая, на котору. расплатились сертификатами
//                $certificate_payments_summ = 0;
//
//                $query = "SELECT `summ` FROM  `journal_payment` WHERE `filial_id` = '{$filial_id}' AND `cert_id`<>'0' AND `status`='0' AND MONTH(`date_in`) = '" . dateTransformation($month) . "' AND YEAR(`date_in`) = '{$year}' ";
//                //var_dump($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($certificate_payments_j, $arr);
//                        $certificate_payments_summ += $arr['summ'];
//                    }
//                }
//                //var_dump($certificate_payments_j);
//                //var_dump($certificate_payments_summ);
//
//
//                //Получаем данные из сводного отчета за месяц
//                $reports_j = array();
//
//                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month' AND `status` = '7'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($reports_j, $arr);
//                    }
//                }
//                //var_dump($reports_j);
//
//
//                //$report_header = '';
//
//
//
//                $cashbox_nal = 0;
//                $beznal = 0;
//                $arenda = 0;
//                $rashod = 0;
//                $ostatok = 0;
//
//                $temp_solar_beznal = 0;
//                $temp_solar_nal = 0;
//
//                foreach ($reports_j as $report) {
//                    $cashbox_nal += $report['nal'];
//                    $beznal += $report['beznal'];
//                    $arenda += $report['arenda'];
//                    $rashod += $report['temp_giveoutcash'];
//
//                    $temp_solar_nal += $report['temp_solar_nal'] + $report['cashbox_abon_nal'] + $report['cashbox_solar_nal'] + $report['cashbox_realiz_nal'];
//                    $temp_solar_beznal += $report['temp_solar_beznal'] + $report['cashbox_abon_beznal'] + $report['cashbox_solar_beznal'] + $report['cashbox_realiz_beznal'];
//                }
//
//                //получаем всё по солярию
//
//
//                //                var_dump('Итоги');
//                //                var_dump('---------------------------------');
//                //
//                //                var_dump('Нал');
//                //                var_dump($cashbox_nal);
//                //                var_dump('Безнал');
//                //                var_dump($beznal);
//                //                var_dump('Аренда');
//                //                var_dump($arenda);
//                //
//                //                var_dump('Выручка вся');
//                //                var_dump(number_format($cashbox_nal + $beznal + $arenda, 0, '.', ' '));
//                //
//                //                var_dump('/////////////////////////////////////////////');
//                //
//                //                var_dump('Наличные');
//                //                var_dump('---------------------------------');
//                //
//                //                var_dump('Нал');
//                //                var_dump($cashbox_nal);
//                //                var_dump('Аренда');
//                //                var_dump($arenda);
//                //                var_dump('Расход');
//                //                var_dump($rashod);
//                //                var_dump('Остаток');
//                //                var_dump(number_format($cashbox_nal + $arenda - $rashod, 0, '.', ' '));
//
//
//                //!!! костыль с % Добавим сумму за солярий в массив сумм
//                //            $rezult_arr_summ['sol'] = $temp_solar_nal + $temp_solar_beznal;
//                //var_dump($rezult_arr_summ);
//
//                //!!! Костыль!
//                // Вычислим % по каждому типу (5,6, etc)
//                //            foreach ($rezult_arr_summ as $type => $summ){
//                //                $rezult_arr_prcnt[$type] = number_format($summ * 100 / (array_sum($rezult_arr_summ)), 2, '.', '');
//                //            }
//                //            var_dump(array_sum($rezult_arr_summ));
//                //            var_dump($rezult_arr_prcnt);
//
//                //Получаем данные по выданным деньгам на филилале (зп, авансы и тд.)
//                $subtractions_j_temp = array();
//                $subtractions_j = array();
//                //Общая сумма
//                $subtractions_summ = 0;
//                //Выдано на карту (безнал)
//                $subtractions_summ_beznal = 0;
//
//                //По филиально в зависимости от оплат
//                //            $query = "SELECT flj_sub.*, sw.	permissions, sw.name
//                //                      FROM `fl_journal_filial_subtractions` flj_sub
//                //                      LEFT JOIN spr_workers sw ON sw.id = flj_sub.worker_id
//                //                      WHERE flj_sub.filial_id='{$filial_id}' AND flj_sub.year='$year' AND (flj_sub.month='$month' OR flj_sub.month='".(int)$month."')";
//
//                //            $query = "SELECT fl_jp.*, sw.permissions, sw.name
//                //                FROM `fl_journal_paidouts` fl_jp
//                //                LEFT JOIN `fl_journal_tabels` fl_tj ON fl_tj.id = fl_jp.tabel_id
//                //                LEFT JOIN spr_workers sw ON sw.id = fl_jp.worker_id
//                //                WHERE fl_tj.office_id='{$filial_id}' AND (fl_tj.month='{$month}' OR fl_tj.month='".(int)$month."') AND fl_tj.year='{$year}'";
//
//                //По филиалам конкретно по табелям (не зависит от оплат, только от того, где открыта была работа)
//                $query = "SELECT fl_jp.*, sw.permissions, sw.name, fl_tj.worker_id
//                    FROM `fl_journal_tabels` fl_tj
//                    INNER JOIN `fl_journal_paidouts` fl_jp ON fl_tj.id = fl_jp.tabel_id
//                    LEFT JOIN spr_workers sw ON sw.id = fl_tj.worker_id
//                    WHERE fl_tj.office_id='{$filial_id}' AND (fl_tj.month='{$month}' OR fl_tj.month='" . (int)$month . "') AND fl_tj.year='{$year}'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //var_dump($arr);
//                        //array_push($subtractions_j_temp, $arr);
//                        if ($arr['noch'] != 1) {
//                            if ($arr['type'] != 4) {
//                                if (!isset($subtractions_j_temp[$arr['permissions']])) {
//                                    $subtractions_j_temp[$arr['permissions']] = array();
//                                }
//                                if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']])) {
//                                    $subtractions_j_temp[$arr['permissions']][$arr['worker_id']] = array();
//
//                                    $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
//                                    $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
//                                }
//                                if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
//                                    $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();
//                                }
//                                array_push($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//
//                                $subtractions_summ += $arr['summ'];
//                            }
//                            //На карту
//                            if ($arr['type'] == 4) {
//                                $subtractions_summ_beznal += $arr['summ'];
//                            }
//                        }
//
//                    }
//                }
//                //var_dump($query);
//                //var_dump($subtractions_j_temp);
//                //var_dump($subtractions_j_temp[5]);
//
//                //отсортируем по $permissions_sort_method
//                foreach ($permissions_sort_method as $key) {
//                    //var_dump($key);
//
//                    if (isset($subtractions_j_temp[$key])) {
//                        $subtractions_j[$key] = $subtractions_j_temp[$key];
//                    }
//                }
//                //var_dump($subtractions_j);
//
//                //Банк
//                $bank_j = array();
//                $bank_summ = 0;
//
//                $query = "SELECT SUM(`summ`) AS summ FROM `fl_journal_in_bank` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND (`month`='$month' OR `month`='" . (int)$month . "')";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    //while ($arr = mysqli_fetch_assoc($res)){
//                    $arr = mysqli_fetch_assoc($res);
//
//                    $bank_summ = $arr['summ'];
//                    //}
//                }
//                //          var_dump($query);
//                //          var_dump($bank_summ);
//
//                //АН
//                $director_j = array();
//                $director_summ = 0;
//
//                $query = "SELECT SUM(`summ`) AS summ FROM `fl_journal_to_director` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    //while ($arr = mysqli_fetch_assoc($res)){
//                    $arr = mysqli_fetch_assoc($res);
//
//                    $director_summ = $arr['summ'];
//                    //}
//                }
//                //          var_dump($director_summ);
//
//
//                //Получаем дефициты предыдущих месяцев
//                $prev_month_filial_summ_arr = array();
//
//                $query = "SELECT `filial_id`, `summ` FROM `fl_journal_prev_month_filial_deficit` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //array_push($paidouts_temp_j, $arr);
//
//                        $prev_month_filial_summ_arr[$arr['filial_id']] = $arr['summ'];
//                    }
//                }
//                //            var_dump($prev_month_filial_summ_arr);
//
//
//                //Получаем данные по выданным деньгам сверх того, что у есть в программе.
//                //Например зп сотрудников, которых нет в программе
//                //Вносится вручную
//                $paidouts_temp_j = array();
//                $paidouts_temp_summ = 0;
//
//
//                $query = "SELECT * FROM `fl_journal_paidouts_temp` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month' ORDER BY `worker_id`";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($paidouts_temp_j, $arr);
//
//                        $paidouts_temp_summ += $arr['summ'];
//                    }
//                }
//                //            var_dump($paidouts_temp_j);

                //Пробуем вывести то, что получили

                echo '
                        <div id="report" class="report" style="margin-top: 10px;">';


                echo '<div style="display: inline-block; vertical-align: top;">';
                //
                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                //                           наличные касса
                //                        </div>
                //                        <div class="cellRight" style="width: 200px; min-width: 200px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';
                //
                //            echo number_format($cashbox_nal, 0, '.', ' ');
                //
                //                echo '
                //                        </div>
                //                    </li>';

                //Сумма по страховым
                $insure_summ = 0;

                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['insure_data'])) {
                        $insure_summ = array_sum($rezult_arr[5]['insure_data']);
                    }
                }

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Приход</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(236, 247, 95, 0.52); text-align: right;">
                                    <b id="allSumm">' . number_format($cashbox_nal + $arenda + $beznal + $insure_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Безнал</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format($beznal, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['insure_data'])) {
                        echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Страховые</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format(array_sum($rezult_arr[5]['insure_data']), 0, '.', ' ') . '</div>
                                </div>
                            </li>';
                    }
                }
                //border: 1px solid rgba(255, 107, 0, 0.52);
                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Нал касса</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format($cashbox_nal, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(95, 247, 145, 0.3);">
                                   <b>Аренда</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(95, 247, 145, 0.3);">
                                    <div style="float:left;">' . number_format($arenda, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                if ($money_from_outside > 0) {
                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(247,109,2,0.3);">
                                   <b>Приход извне</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(247,109,2,0.3);">
                                    <div style="float:left;">' . number_format($money_from_outside, 0, '.', ' ') . '</div>';

                    if (($_SESSION['permissions'] == 3) || ($god_mode)) {
                        echo '                
                                    <span class="never_print_it" style="font-size: 120%; float: right;">
                                        <i class="fa fa-times" aria-hidden="true" style="color: red; cursor: pointer;" title="Удалить все приходы" onclick="Ajax_MoneyFromOutsideDelete('.$filial_id.', '.$year.', \''.$month.'\');"></i>
                                    </span>';
                    }

                    echo '
                                </div>
                            </li>';
                }

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Всего нал:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(236, 247, 95, 0.52); text-align: right;">
                                    <b>' . number_format($cashbox_nal + $arenda, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';


                echo '
                        </div>';


                //!!! Суммы долгов/авансов за прошлые месяцы (пока ручной ввод)
                //                11 => ЦО,
                //                12 => Авиаконструкторов 10,
                //                13 => Просвещения 54,
                //                14 => Комендантский 17,
                //                15 => Энгельса 139,
                //                16 => Гражданский 114,
                //                17 => Чернышевского 17,
                //                18 => Некрасова 58,
                //                19 => Просвещения 72,
                //                20 => Литейный 59,
                //                21 => Бассейная 45

                //            $prev_month_filial_summ_arr = array(
                //                11 => 0,
                //                12 => -151929,
                //                13 => -169961,
                //                14 => -232,
                //                15 => -411380,
                //                16 => -684164,
                //                17 => -533,
                //                18 => -16780,
                //                19 => -218297,
                //                20 => -323,
                //                21 => 0
                //            );

                $prev_month_filial_summ = 0;

                if (isset($prev_month_filial_summ_arr[$filial_id])) {
                    $prev_month_filial_summ = $prev_month_filial_summ_arr[$filial_id];
                }

                $bg_color = 'rgba(219, 215, 214, 0.44)';

                //var_dump($prev_month_filial_summ_arr[$filial_id]);

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 310px; min-width: 310px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                    <b>Дефицит предыдущего м-ца</b>';

                if (($_SESSION['permissions'] == 3) || ($god_mode)) {
                    echo '                
                                    <span class="never_print_it" style="font-size: 120%; float: right;">
                                        <i class="fa fa-times" aria-hidden="true" style="color: red; cursor: pointer;" title="Удалить" onclick="Ajax_PrevMonthDeficitDelete('.$filial_id.', '.$year.', \''.$month.'\');"></i>
                                    </span>';
                }

                echo '
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                    
                                </div>
                                <div class="cellRight" style=" font-size: 120%; width: 180px; min-width: 180px; background-color: ' . $bg_color . '; text-align: right;">
                                    <b>' . number_format($prev_month_filial_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                //Расходы
                //var_dump($giveoutcash_j);
                //var_dump($give_out_cash_types_j);
                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Расходы</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
    
                                </div>
                            </li>';

                $giveout_wo_type_summ = 0;
                $giveout_all_summ = 0;

                if (!empty($giveoutcash_j)) {
                    foreach ($giveoutcash_j as $type => $summ) {

                        $giveout_all_summ += $summ;

                        if ($type != 0) {
                            echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>' . $give_out_cash_types_j[$type] . '</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
                                    <div style="float:left;">' . number_format($summ, 2, '.', ' ') . '</div>
                                </div>
                            </li>';
                        } else {
                            $giveout_wo_type_summ += $summ;
                        }
                    }
                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Прочее</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
                                    <div style="float:left;">' . number_format($giveout_wo_type_summ, 2, '.', ' ') . '</div>
                                </div>
                            </li>';
                }

                //Всего
                //var_dump($giveout_all_summ);
                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Всего:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(191, 191, 191, 0.38); text-align: right;">
                                    <b>' . number_format($giveoutcash_summ, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';

                echo '
                        </div>';

                //ЗП выданные

                //Для создания разных цветов полей
                $bg_color = 'rgba(219, 214, 214, 0.25)';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Заработная плата</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44);">

                                </div>
                            </li>';

                //таблица с ЗП по каждому сотруднику
                $personal_zp_str = '
                        
                        <table width="" style="border: 1px solid #CCC;">';

                //Загловок
                //            $personal_zp_str .= '
                //                        <tr>
                //                            <td colspan="6" style="font-size:80%;"><b>Выдачи сотрудникам:</b></td>
                //                        </tr>
                //                        <tr style="background-color: rgba(252, 237, 199, 0.77);">
                //                            <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">аванс</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">зп</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">отпускной</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">больничный</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">на карту</i></td>
                //                        </tr>';

                $personal_zp_str .= '
                            <tr>
                                <td colspan="2" style="font-size:80%;"><b>Выдачи сотрудникам:</b></td>
                            </tr>
                            <tr style="background-color: rgba(252, 237, 199, 0.77);">
                                <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">всего</i></td>
                            </tr>';

                //Пошли по типам/должностям
                //var_dump($subtractions_j);

                foreach ($subtractions_j as $permissions => $subtractions_data) {
                    //var_dump($subtractions_data);
                    //var_dump($permissions);

                    //Для создания разных цветов полей
                    //var_dump($bg_color);

                    if ($bg_color == 'rgba(219, 214, 214, 0.25)') {
                        $bg_color = 'rgba(219, 215, 214, 0.44)';
                    } else {
                        $bg_color = 'rgba(219, 214, 214, 0.25)';
                    }

                    //Сумма выданных денег по должностям
                    $permission_summ = 0;

                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                   <!--<b style="color: orangered;">' . $permissions_j[$permissions]['name'] . '</b>-->
                                   <b style="color: rgb(0, 36, 255);">' . $permissions_j[$permissions]['name'] . '</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: ' . $bg_color . ';">
    
                                </div>
                            </li>';

                    //                $personal_zp_str .= '
                    //                        <tr style="background-color: rgba(225, 248, 220, 0.77);">
                    //                            <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: left;"><b style="color: rgb(0, 36, 255); font-size: 90%;">'.$permissions_j[$permissions]['name'].'</b></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                        </tr>';

                    $personal_zp_str .= '
                            <tr style="background-color: rgba(225, 248, 220, 0.77);">
                                <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: left;"><b style="color: rgb(0, 36, 255); font-size: 90%;">' . $permissions_j[$permissions]['name'] . '</b></td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                            </tr>';

                    //foreach ($subtractions_data as $type => $type_data){
                    //var_dump($typ);
                    //var_dump($type_data);
                    //Пошли по сотрудникам
                    foreach ($subtractions_data as $worker_id => $worker_data) {
                        //var_dump($worker_data);

                        $w_name = $worker_data['name'];
                        //var_dump($w_name);
                        //Выдано всего сотруднику
                        $fin_summ_w = 0;

                        $personal_zp_str .= '
                            <tr>
                                <td style="outline: 1px solid rgb(233, 233, 233); text-align: left;">' . $w_name . '</td>';

                        //Массив для распределения сумм по типам
                        $temp_summ_arr = array(1 => 0, 7 => 0, 2 => 0, 3 => 0, 4 => 0);

                        foreach ($worker_data['data'] as $type => $type_data) {
                            //                        var_dump($type);

                            //                        if ($type == 1) {
                            //                            $type_name = ' аванс ';
                            //                        } elseif ($type == 2) {
                            //                            $type_name = ' отпускной ';
                            //                        } elseif ($type == 3) {
                            //                            $type_name = ' больничный ';
                            //                        } elseif ($type == 4) {
                            //                            $type_name = ' на карту ';
                            //                        } elseif ($type == 7) {
                            //                            $type_name = ' зп ';
                            //                        } elseif ($type == 5) {
                            //                            $type_name = ' ночь ';
                            //                        } else {
                            //                            $type_name = ' !!!ошибка данных ';
                            //                        }

                            foreach ($type_data as $data) {
                                //var_dump($data);
                                //$w_name = $data['name'];
                                $fin_summ_w += $data['summ'];
                                $permission_summ += $data['summ'];

                                //
                                $temp_summ_arr[$type] += $data['summ'];
                            }
                        }
                        //var_dump($temp_summ_arr);

                        //                    foreach ($temp_summ_arr as $temp_summ){
                        //
                        //                        $personal_zp_str .= '
                        //                            <td style="outline: 1px solid rgb(233, 233, 233); text-align: right;">
                        //                                '.$temp_summ.'
                        //                            </td>
                        //                            ';
                        //                    }
                        $personal_zp_str .= '
                                <td style="outline: 1px solid rgb(233, 233, 233); text-align: right;">
                                    ' . array_sum($temp_summ_arr) . '
                                </td>
                                ';


                        $personal_zp_str .= '
                            </tr>';
                    }

                    //Процент зп от выручки
                    $zp_percent = 0;

                    if (($cashbox_nal + $beznal + $insure_summ) > 0) {
                        $zp_percent = number_format(($permission_summ * 100 / ($cashbox_nal + $beznal + $insure_summ)), 2, '.', ' ');
                    }

                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                   <!--<b>всего:</b>-->
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: ' . $bg_color . ';">
                                    <div id="permission_summ'.$permissions.'" permission_id="'.$permissions.'" style="float:left;">' . number_format($permission_summ, 0, '.', ' ') . '</div>
                                    <div class="percent'.$permissions.'" style="float:right;">' . $zp_percent . '%</div>
                                </div>
                            </li>';
                }
                //var_dump($permission_summ);

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(219, 214, 214, 0.25);">
                                   <b>Всего:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 214, 214, 0.25); text-align: right;">
                                    <b>' . number_format($subtractions_summ, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';


                echo '
                        </div>';


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Прочие выдачи/расходы</b>
                                </div>
                                <div class="cellRight never_print_it" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44);">
                                    <a href="fl_paidout_another_test_in_tabel_add.php?filial_id=' . $filial_id . '" class="ahref b2 never_print_it"  target="_blank" rel="nofollow noopener">Добавить</a>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($paidouts_temp_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Банк</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
    
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($bank_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>АН</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44);">
    
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($director_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                //            //Выручка без страховых
                //            var_dump($cashbox_nal+$beznal+$arenda);
                //            //Расходы
                //            var_dump($giveoutcash_summ);
                //            //Выплаты персоналу
                //            var_dump($subtractions_summ);
                //            //Выплаты персоналу, которые не обрабатываются пока в программе
                //            var_dump($paidouts_temp_summ);
                //            //Банк
                //            var_dump($bank_summ);
                //            //АН
                //            var_dump($director_summ);


                //Остаток
                //var_dump(
                //$cashbox_nal
                // + $arenda
                // - $giveoutcash_summ
                // - $subtractions_summ
                // - $paidouts_temp_summ
                // - $bank_summ
                // - $director_summ
                // + $prev_month_filial_summ
                // + $subtractions_summ_beznal
                //);
                //var_dump($subtractions_summ_beznal);

                $ostatok = $cashbox_nal + $arenda - $giveoutcash_summ - $subtractions_summ - $paidouts_temp_summ - $bank_summ - $director_summ + $prev_month_filial_summ + $money_from_outside;

                $ostatok = number_format($ostatok, 0, '.', ' ');


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 310px; min-width: 310px; font-size: 120%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Дефицит текущего месяца</b> 
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);;">
                                    <button class="ahref b2 never_print_it" onclick="showPrevMonthDeficitAdd(' . $filial_id . ');">Сохранить</button>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44);; text-align: right;">
                                    <div id="ostatokDeficit" style="font-weight: bold;">' . $ostatok . '</div>
                                </div>
                            </li>';
                echo '
                        </div>';


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 140%; font-weight: bold; background-color: rgba(219, 214, 214, 0.25);">
                                   <b>Итог</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 140%; background-color: rgba(219, 214, 214, 0.25); text-align: right;">
                                    <b>0</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                echo '
                    </div>';


                //Процентное соотношение работ начатых в текущем месяце, опираясь на запись

                echo '<div style="display: inline-block; vertical-align: top; margin-left: 10px;">';


                //Сумма за детскую стоматологию
                $child_stom_summ = 0;

                //Если за детскую стоматологию есть сумма
                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['child_stom_summ'])) {
                        $child_stom_summ = $rezult_arr[5]['child_stom_summ'];
                    }
                }

                //Стоматология
                if (isset($rezult_arr[5])) {

                    //!!! Костыль.
                    // Предположительная сумма выручки по стоматологии
                    $stom_summ_temp = $cashbox_nal + $beznal + $insure_summ - ($temp_solar_nal + $temp_solar_beznal);

                    //Сумма по стоматологии будет разницей:
                    //Вся выручка минус косметология, специалисты, орто/кт, солярий
                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[7]['data']);
                        }
                    }
                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[6]['data']);
                        }
                    }
                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[10]['data']);
                        }
                    }

                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Стоматология
                            </div>
                            <div class="cellRight" style="width: 150px; min-width: 150px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[5])) {
                        if (!empty($rezult_arr[5]['data'])) {
                            //arsort($rezult_arr[5]['data']);

                            if ($stom_summ_temp < 0) $stom_summ_temp = 0;

                            //                        echo number_format(array_sum($rezult_arr[5]['data']) + $child_stom_summ, 0, '.', ' ').' ';
                            echo '<span id="summ5">'.number_format($stom_summ_temp, 0, '.', ' ').'</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[5], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[5], 0, '.', ' '));
                            //                        var_dump($cashbox_nal + $beznal + $insure_summ);

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }

                    echo '
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[5])) {
                    if (!empty($rezult_arr[5])) {
                        if (!empty($rezult_arr[5]['data'])) {
                            arsort($rezult_arr[5]['data']);
                            //var_dump($rezult_arr[5]['data']);

                            foreach ($rezult_arr[5]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;
                                //var_dump($percent_cat_id);

                                //Если все хорошо в массиве с данными
                                if ((strlen($percent_cat_id) > 0) && ($value != 0)) {
                                    //%
                                    $cat_prcnt_temp = $value * 100 / (array_sum($rezult_arr[5]['data']) + $child_stom_summ);
                                    //                                var_dump($cat_prcnt_temp);
                                    //                                var_dump($stom_summ_temp);
                                    //                                var_dump($stom_summ_temp / 100 * $cat_prcnt_temp);

                                    echo '
                                    <li class="filterBlock">
                                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                           <b>' . $percents_j[5][$percent_cat_id]['name'] . '</b>
                                        </div>
                                        <div class="cellRight" style="width: 150px; min-width: 150px;">
                                            <div style="float:left;">' . number_format($stom_summ_temp / 100 * $cat_prcnt_temp, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format($cat_prcnt_temp, 2, '.', '') . '%</div>
                                        </div>
                                    </li>';
                                } else {

                                }
                            }
                            //Детство отдельно

                            if (array_sum($rezult_arr[5]['data']) + $child_stom_summ > 0) {
                                //%
                                $cat_prcnt_temp = $child_stom_summ * 100 / (array_sum($rezult_arr[5]['data']) + $child_stom_summ);
                            } else {
                                $cat_prcnt_temp = 0;
                            }


                            echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>Детство</b>
                                    </div>
                                    <div class="cellRight" style="width: 150px; min-width: 150px;">
                                        <div style="float:left;">' . number_format($stom_summ_temp / 100 * $cat_prcnt_temp, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format($cat_prcnt_temp, 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                        }
                    }
                    if (isset($zapis_j[5])) {
                        if (!empty($zapis_j[5])) {
                            //var_dump($zapis_j[5]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                II
                                            </td>
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[5]['pervich_summ_arr'][1] + $zapis_j[5]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[5]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[5][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[5]['pervich_summ_arr'][3] + $zapis_j[5]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[5]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
                        }
                    }
                }

                //Ассистенты
                if (isset($rezult_arr[7])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Ассистенты
                            </div>
                            <div class="cellRight" style="width: 150px; min-width: 150px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            //arsort($rezult_arr[10]['data']);

                            echo '<span id="summ7">'.number_format(array_sum($rezult_arr[7]['data']), 0, '.', ' ') . '</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[7], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[7], 0, '.', ' '));

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[7])) {
                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            arsort($rezult_arr[7]['data']);

                            var_dump($rezult_arr[7]['data']);

                            foreach ($rezult_arr[7]['data'] as $percent_cat_id => $value) {

                                if (isset($percents_j[7][$percent_cat_id])) {
                                    $percent_cat_name = $percents_j[7][$percent_cat_id]['name'];
                                } else {
                                    $percent_cat_name = $percents_j[$percent_cat_id]['name'] . '<i class="fa fa-warning" aria-hidden="true"></i>';
                                }

                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percent_cat_name . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 150px; min-width: 150px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[7]['data'])), 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                            }
                        }
                    }
                }


                //Косметология
                if (isset($rezult_arr[6])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Косметология
                            </div>
                            <div class="cellRight" style="width: 150px; min-width: 150px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
                            //arsort($rezult_arr[5]['data']);

                            echo '<span id="summ6">'.number_format(array_sum($rezult_arr[6]['data']), 0, '.', ' ') . '</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[6], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[6], 0, '.', ' '));

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[6])) {
                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
                            arsort($rezult_arr[6]['data']);

                            foreach ($rezult_arr[6]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;


                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percents_j[6][$percent_cat_id]['name'] . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 150px; min-width: 150px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[6]['data'])), 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                            }
                        }
                    }
                    if (isset($zapis_j[6])) {
                        if (!empty($zapis_j[6])) {
                            //var_dump($zapis_j[6]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <td>
                                                II
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[6]['pervich_summ_arr'][1] + $zapis_j[6]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red-->;*/">' . ($pervich_summ_arr_new[6][1] + $pervich_summ_arr_new[6][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[6]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[6][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[6]['pervich_summ_arr'][3] + $zapis_j[6]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[6][3] + $pervich_summ_arr_new[6][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[6]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
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
                            <div class="cellRight" style="width: 150px; min-width: 150px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                    echo '<span id="summSol">'.number_format(($temp_solar_nal + $temp_solar_beznal), 0, '.', ' ') . '</span>';

                    //                //!!! Костыль для %
                    //                echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt['sol'], 0, '.', ' ');
                    //                var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt['sol'], 0, '.', ' '));


                    echo '
                            </div>
                        </li>';
                }

                //Специалисты
                if (isset($rezult_arr[10])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Специалисты
                            </div>
                            <div class="cellRight" style="width: 150px; min-width: 150px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            //arsort($rezult_arr[10]['data']);

                            echo '<span id="summ10">'.number_format(array_sum($rezult_arr[10]['data']), 0, '.', ' ') . '</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[10], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[10], 0, '.', ' '));

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[10])) {
                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            arsort($rezult_arr[10]['data']);

                            foreach ($rezult_arr[10]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;


                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percents_j[10][$percent_cat_id]['name'] . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 150px; min-width: 150px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[10]['data'])), 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                            }
                        }
                    }
                    if (isset($zapis_j[10])) {
                        if (!empty($zapis_j[10])) {
                            //var_dump($zapis_j[10]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <td>
                                                II
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[10]['pervich_summ_arr'][1] + $zapis_j[10]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[10][1] + $pervich_summ_arr_new[10][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[10]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[10][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[10]['pervich_summ_arr'][3] + $zapis_j[10]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[10][3] + $pervich_summ_arr_new[10][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[10]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
                        }
                    }
                }

                $personal_zp_str .= '</table>';


                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                //                           <b>Продано сертификатов</b>
                //                        </div>
                //                        <div class="cellRight" style="width: 180px; min-width: 180px;">
                //                            <div style="float:left;">'.count($certificates_j).' шт. на сумму: '.number_format($certificates_summSell, 0, '.', ' ').'</div>
                //                        </div>
                //                    </li>';
                //
                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                //                           <b>Оплаченно ранее проданными сертификатами</b>
                //                        </div>
                //                        <div class="cellRight" style="width: 180px; min-width: 180px;">
                //                            <div style="float:left;">на сумму: '.number_format($certificate_payments_summ, 0, '.', ' ').'</div>
                //                        </div>
                //                    </li>';


                echo '
                            <!--</ul>-->
                                    </div>
                                </div>
                            </div>';


                //ЗП по каждому сотруднику
                echo '
                            <div class="rezult_item3print" style="display: block; vertical-align: top; margin: 10px;">';

                echo '
                                <div style="vertical-align: top;">';

                echo $personal_zp_str;

                echo '
                                </div>
                                <div style="vertical-align: top; margin-top: 10px;">';

                //Прочие расходы подробно
                if (!empty($paidouts_temp_j)) {
                    //var_dump($paidouts_temp_j);

                    echo '
                                    <div class="" style="">
                                        <ul style="/*margin-left: 6px;*/ margin-bottom: 10px; font-size: 14px;">
                                            <li class="cellsBlock" style="width: auto; font-size: 80%;">
                                                <div class="cellOrder" style="width: 622px; text-align: left;">
                                                    <b>Прочие выдачи/расходы подробно:</b>
                                                </div>
                                            </li>';
                    echo '
                                            <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                    echo '
                                                <div class="cellOrder" style="text-align: center; border-right: none;">
                                                    <b>Кому</b>
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                    <b>Тип</b>           
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                    <b>Сумма</b>
                                                 </div>
                                                <div class="cellName" style="text-align: center;">
                                                    <b>Описание</b>
                                                </div>
                                                <div class="cellCosmAct info" style="text-align: center;">
                                                    -
                                                </div>';
                    echo '
                                            </li>';

                    foreach ($paidouts_temp_j as $paidouts_item) {

                        if ($paidouts_item['type'] == 1) {
                            $type_name = ' аванс ';
                        } elseif ($paidouts_item['type'] == 2) {
                            $type_name = ' отпускной ';
                        } elseif ($paidouts_item['type'] == 3) {
                            $type_name = ' больничный ';
                        } elseif ($paidouts_item['type'] == 4) {
                            $type_name = ' на карту ';
                        } elseif ($paidouts_item['type'] == 7) {
                            $type_name = ' зп ';
                        } elseif ($paidouts_item['type'] == 5) {
                            $type_name = ' ночь ';
                        } else {
                            $type_name = ' !!!ошибка данных ';
                        }

                        echo '
                                            <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                        echo '
                                                <div class="cellOrder" style="text-align: left; border-right: none;">
                                                    ' . WriteSearchUser('spr_workers', $paidouts_item['worker_id'], 'user', false) . '
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                      ' . $type_name . '
                                                </div>
                                                <div class="cellName" style="text-align: right; border-right: none;">
                                                    ' . $paidouts_item['summ'] . '
                                                 </div>
                                                <div class="cellName" style="text-align: center;">
                                                    ' . $paidouts_item['descr'] . '
                                                </div>
                                                <div class="cellCosmAct info" style="font-size: 100%; text-align: center; " onclick="deletePaidoutsTempItem('. $paidouts_item['id'].', this);">
                                                    <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                </div>';
                        echo '
                                            </li>';
                    }

                    echo '
                                        </ul>
                                    </div>';
                }

                echo '
                                </div>';
                echo '
                            </div>';

                //Расходы из кассы подробно
                if (!empty($giveoutcash_ex_j)) {

                    echo '
                            <div class="rezult_item3print" style="display: block; vertical-align: top; margin: 10px;">';

                    echo '
                                    <li class="cellsBlock" style="width: auto; ">
                                        <div class="cellOrder" style="width: 510px; text-align: left;">
                                            <b>Все расходы из кассы за месяц подробно:</b>
                                        </div>
                                    </li>';


                    foreach ($giveoutcash_ex_j as $item) {
                        //var_dump($item);

                        $bgColor = '';

                        echo '
                                    <li class="cellsBlock" style="width: auto; ' . $bgColor . '">';
                        echo '
                                        <div class="cellOrder" style="width: 120px; min-width: 120px; position: relative; border-right: none; border-top: none; font-size: 90%;">
                                            
                                            <a href="giveout_cash_all.php?filial_id=' . $item['office_id'] . '&d=' . date("d", strtotime($item['date_in'])) . '&m=' . date("m", strtotime($item['date_in'])) . '&y=' . date("Y", strtotime($item['date_in'])) . '" class="ahref" target="_blank" rel="nofollow noopener"><b>Расх. ордер #' . $item['id'] . '</b></a>
    
                                        </div>
                                        <div class="cellName" style="border-right: none; border-top: none;">';
                        if ($item['type'] != 0) {
                            echo $give_out_cash_types_j[$item['type']];
                        } else {
                            echo 'Прочее';

                            if ($item['additional_info'] != '') {
                                echo ':<br><i>' . $item['additional_info'] . '</i>';
                            }
                            //var_dump($item);
                        }

                        echo '                              
                                        </div>
                                        <div class="cellName" style="width: 90px; min-width: 90px; border-right: none; border-top: none;">
                                            <div style="text-align: right;">
                                                <span class="calculateInvoice" style="font-size: 90%; font-style: normal; color: #333;">' . $item['summ'] . '</span>
                                            </div>
                                        </div>
                                        <div class="cellName" style="border-right: none; border-top: none;">
                                            <div style="margin: 1px 0; padding: 1px 3px;">
                                                <span class="" style="font-size: 11px">' . $item['comment'] . '</span>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;">
                                            
                                        </div>';


                        echo '
                                    </li>';

                    }
                    echo '
                        </div>';
                }
            }else{
                echo '<span class="query_neok">Нет данных</span>';
            }

            echo '
                    </div>
                </div>';

            echo '
                            <div class="no_print" style="position: fixed; top: 45px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                                <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                                onclick="window.print();">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </div>
                            </div>';

            echo '    
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
                                               
                                               
                       //$(".permission_summ").each(function(){
                           
                            var allSumm = 0;
                            var stomSumm = 0;
                            var cosmSumm = 0;
                            var assSumm = 0;
                            var specSumm = 0;
                            var solSumm = 0;
                            
                            if ($("#allSumm").html() !== undefined){
                                //allSumm = Number($("#allSumm").html().replace(/\s+/g, \'\'));
                                allSumm = Number(('.$cashbox_nal.' + '.$beznal.' + '.$insure_summ.'));
                            }
                            if ($("#summ5").html() !== undefined){
                                stomSumm = Number($("#summ5").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ6").html() !== undefined){
                                cosmSumm = Number($("#summ6").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ7").html() !== undefined){
                                assSumm = Number($("#summ7").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ10").html() !== undefined){
                                specSumm = Number($("#summ10").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summSol").html() !== undefined){
                                solSumm = Number($("#summSol").html().replace(/\s+/g, \'\'));
                            }

                            var id = $(this).attr("permission_id");
                            //console.log(id);
//                            console.log(assSumm);
//                            console.log(allSumm);
                            
                            //if (($cashbox_nal + $beznal + $insure_summ) > 0) {
//                                number_format(($permission_summ * 100 / ($cashbox_nal + $beznal + $insure_summ)), 2, \'.\', \' \');
                            //}
                            
                            if (allSumm > 0){
                                
                                if ($(".percent5").html() !== undefined){
                                    $(".percent5").html(
                                        number_format((Number($("#permission_summ5").html().replace(/\s+/g, \'\')) * 100 / stomSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent5").html(0);
                                }
                                $(".percent5").append(" %");
                                
                                if ($(".percent6").html() !== undefined){
                                    $(".percent6").html(
                                        number_format((Number($("#permission_summ6").html().replace(/\s+/g, \'\')) * 100 / cosmSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent6").html(0);
                                }
                                $(".percent6").append(" %");
                                
                                if ($(".percent7").html() !== undefined){
                                    $(".percent7").html(
                                        number_format((Number($("#permission_summ7").html().replace(/\s+/g, \'\')) * 100 / (stomSumm + assSumm)), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent7").html(0);
                                }
                                $(".percent7").append(" %");
                                
                                if ($(".percent10").html() !== undefined){
                                    $(".percent10").html(
                                        number_format((Number($("#permission_summ10").html().replace(/\s+/g, \'\')) * 100 / specSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent10").html(0);
                                }
                                $(".percent10").append(" %");
                                
                                //адм
                                if ($(".percent4").html() !== undefined){
                                    $(".percent4").html(
                                        number_format((Number($("#permission_summ4").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent4").html(0);
                                }
                                $(".percent4").append(" %");
                                
                                if ($(".percent9").html() !== undefined){
                                    $(".percent9").html(
                                        number_format((Number($("#permission_summ9").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent9").html(0);
                                }
                                $(".percent9").append(" %");
                                
                                if ($(".percent11").html() !== undefined){
                                    $(".percent11").html(
                                        number_format((Number($("#permission_summ11").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent11").html(0);
                                }
                                $(".percent11").append(" %");
                                
                                if ($(".percent13").html() !== undefined){
                                    $(".percent13").html(
                                        number_format((Number($("#permission_summ13").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent13").html(0);
                                }
                                $(".percent13").append(" %");
                                
                                if ($(".percent14").html() !== undefined){
                                    $(".percent14").html(
                                        number_format((Number($("#permission_summ14").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent14").html(0);
                                }
                                $(".percent14").append(" %");
                                
                                if ($(".percent15").html() !== undefined){
                                    $(".percent15").html(
                                        number_format((Number($("#permission_summ15").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent15").html(0);
                                }
                                $(".percent15").append(" %");
                                    
                                if ($(".percent777").html() !== undefined){
                                    $(".percent777").html(
                                        number_format((Number($("#permission_summ777").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent777").html(0);
                                }
                                $(".percent777").append(" %");
                            }else{
                            }
                       //});
                                               
                                               
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