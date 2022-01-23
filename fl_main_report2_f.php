<?php

//fl_main_report2_f.php
//Функция для Финальный отчет v2.0

    function fl_main_report2_f($month, $year, $filial_id, $worker_id=0){
        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'ffun.php';
        require 'variables.php';

        $permissions_sort_method = [5, 6, 10, 7, 4, 13, 14, 15, 9, 12, 11, 777];

        //$filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        //Получили список прав
//        $permissions_j = getAllPermissions(false, true);
//        //var_dump($permissions_j);
//
//        //!!! костыль для меня =)
//        //array_push($permissions_j, array('id' => '777', 'name' => 'Сис.админ'));
//        $permissions_j[777] = array('id' => '777', 'name' => 'Сис.админ');
//        //var_dump($permissions_j);

//        $dop = 'filial_id=' . $filial_id;

        $msql_cnnct = ConnectToDB();

        //Массив с нарядами с ошибками
        $error_invoices = array();

        //Соберём все категории процентов (справочник)
        // по типу
        $percents_j = array();
        // по id (не используется)
        $percents_j2 = array();

        $query = "SELECT `id`, `name`, `type` FROM  `fl_spr_percents`";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                if (!isset($percents_j[$arr['type']])) {
                    $percents_j[$arr['type']] = array();
                }
                $percents_j[$arr['type']][$arr['id']]['name'] = $arr['name'];

                if (!isset($percents_j2[$arr['id']])) {
                    $percents_j2[$arr['id']] = array();
                }

                $percents_j2[$arr['id']]['name'] = $arr['name'];


            }
        }
        //var_dump($percents_j);
        //var_dump($percents_j2);

        //Типы расходов (справочник)
        $give_out_cash_types_j = array();

        $query = "SELECT `id`,`name` FROM `spr_cashout_types`";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);
        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                $give_out_cash_types_j[$arr['id']] = $arr['name'];
            }
        }

        //Типы посещений - первичка/нет (количество) (pervich)
        //Памятка
        //1 - Посещение для пациента первое без работы
        //2 - Посещение для пациента первое с работой
        //3 - Посещение для пациента не первое
        //4 - Посещение для пациента не первое, но был более полугода назад
        //--
        //5 - Продолжение работы
        //6 - Без записи (enter)
        $pervich_summ_arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

        //Получаем данные по записи за месяц
        $zapis_j = array();
        $zapis_j_noch = array();
        //Не пришло
        $zapis_not_enter = 0;
        //ID записей
        $zapis_ids = array();
        //Если указано, к кому записан
        $worker_str = '';
        $worker_str2 = '';
        if ($worker_id != 0){
            $worker_str = " AND `worker`='{$worker_id}'";
            $worker_str2 = " AND z.worker='{$worker_id}'";
        }

        //Кроме тех, которые удалены или не пришли
        $query = "SELECT `id`, `type`, `patient`, `pervich`, `insured`, `noch`, `enter` 
        FROM `zapis` 
        WHERE `office` = '{$filial_id}' AND `year` = '{$year}' AND `month` = '{$month}' AND `enter` <> 9 AND `enter` <> 8 ".$worker_str."
        ORDER BY `day` ASC";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                //Если ночь
                if ($arr['noch'] == 1) {
                    array_push($zapis_j_noch, $arr);
                } else {
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
                    } else {
                        //Если пришёл
                        //var_dump($arr['enter']);
                        //первичка/нет
                        if (($arr['enter'] == 1) || ($arr['enter'] == 6)) {
                            $zapis_j[$arr['type']]['pervich_summ_arr'][$arr['pervich']]++;

                            array_push($zapis_ids, $arr['id']);
                        } else {
                            if ($arr['enter'] == 0) {
                                //не пришел
                                $zapis_not_enter++;
                            }
                        }
                    }
                }
            }
        }

        //Выберем наряды по записям
        $invoices_j = array();
        $invoices_j2 = array();
        $invoices_notinsure_ids = array();

        //Массив, где будем хранить суммы нарядов, чтобы потом определять первичное посещение или нет по сумме
        $zapis_summ = array();

        $query = "
                SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.status AS invoice_status, ji.type AS type, ji.zapis_id AS zapis_id, z.enter AS enter, z.pervich AS pervich, sc.birthday2 AS birthday
                FROM `zapis` z
                INNER JOIN `journal_invoice` ji ON z.id = ji.zapis_id AND 
                z.office = '{$filial_id}' AND z.year = '{$year}' AND z.month = '{$month}' AND (z.enter = '1' OR z.enter = '6') ".$worker_str2."
                LEFT JOIN `journal_invoice_ex` jiex ON ji.id = jiex.invoice_id
                LEFT JOIN `spr_clients` sc ON ji.client_id = sc.id 
                WHERE ji.status <> '9'";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                //var_dump($arr);

                array_push($invoices_j2, $arr);

                //Пришел/не пришел/с улицы
                if (!isset($invoices_j[$arr['enter']])) {
                    $invoices_j[$arr['enter']] = array();

                }
                //тип стом, косм, ...
                if (!isset($invoices_j[$arr['enter']][$arr['type']])) {
                    $invoices_j[$arr['enter']][$arr['type']] = array();
                    $invoices_j[$arr['enter']][$arr['type']]['data'] = array();
                    $invoices_j[$arr['enter']][$arr['type']]['insure_data'] = array();
                    $invoices_j[$arr['enter']][$arr['type']]['child_stom_summ'] = 0;
                }
                //Если страховой
                if ($arr['insure'] == 1) {
                    if (!isset($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']])) {
                        $invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']] = array();
                    }
                    array_push($invoices_j[$arr['enter']][$arr['type']]['insure_data'][$arr['invoice_id']], $arr);

                } else {
                    if (!isset($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']])) {
                        $invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']] = array();
                    }
                    array_push($invoices_j[$arr['enter']][$arr['type']]['data'][$arr['invoice_id']], $arr);

                }


                //Теперь суммы нарядов
                //Пришел/не пришел/с улицы
                if (!isset($zapis_summ[$arr['type']])) {
                    $zapis_summ[$arr['type']] = array();

                }
                //тип стом, косм, ...
                if (!isset($zapis_summ[$arr['type']][$arr['pervich']])) {
                    $zapis_summ[$arr['type']][$arr['pervich']] = array();
                    $zapis_summ[$arr['type']][$arr['pervich']]['data'] = array();
                    $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'] = array();
                }
                //Если страховой
                if ($arr['insure'] == 1) {
                    if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']])) {
                        $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] = 0;
                    }
                    $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] += (int)$arr['itog_price'];
                } else {
                    if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']])) {
                        $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] = 0;
                    }
                    $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] += (int)$arr['itog_price'];
                }
                //if ($arr['invoice_id'] = 83364) var_dump($arr);
            }
        }
        //сортируем по основным ключам
        ksort($invoices_j);
        ksort($zapis_summ);
        //            var_dump($zapis_summ);

        //!!! тестовая проверка нового определения первичек
        $pervich_summ_arr_new = array();

        //            foreach($pervich_summ_arr as $y_id => $y){
        //                if (isset($zapis_summ[$y_id][5]['data'])){
        //                    foreach ($zapis_summ[$y_id][5]['data'] as $i_id => $i_summ){
        //                        //var_dump($i_id.' => '.$i_summ);
        //                        if ($i_summ <= 1100){
        //                            $pervich_summ_arr_new[5][$y_id] ++;
        //                        }
        //                    }
        //                }
        //            }
        //var_dump($pervich_summ_arr_new);

        foreach ($zapis_summ as $type => $pervich_data) {
            foreach ($pervich_data as $pervich => $zapis_data) {
                if (!isset($pervich_summ_arr_new[$type])) {
                    $pervich_summ_arr_new[$type] = $pervich_summ_arr;
                }
                //                    if  (!isset($pervich_summ_arr_new[$type][$pervich])){
                //                        $pervich_summ_arr_new[$type][$pervich] = 0;
                //                    }
                if (isset($zapis_data['data'])) {
                    if (!empty($zapis_data['data'])) {
                        foreach ($zapis_data['data'] as $i_id => $i_summ) {
                            if ($pervich == 1 || $pervich == 2) {
                                //Стоматология
                                if ($type == 5) {
                                    if ($i_summ >= 0) {
                                        if ($i_summ < 1100) {
                                            $pervich_summ_arr_new[$type][1]++;
                                        } else {
                                            $pervich_summ_arr_new[$type][2]++;
                                        }
                                    }
                                }
                                //Косметология
                                //!!! Доделать
                                if ($type == 6) {
                                    if ($i_summ >= 0) {
                                        if ($i_summ < 550) {
                                            $pervich_summ_arr_new[$type][1]++;
                                        } else {
                                            $pervich_summ_arr_new[$type][2]++;
                                        }
                                    }
                                }
                                //Соматика
                                if ($type == 10) {
                                    if ($i_summ >= 0) {
                                        if ($i_summ < 990) {
                                            $pervich_summ_arr_new[$type][1]++;
                                        } else {
                                            $pervich_summ_arr_new[$type][2]++;
                                        }
                                    }
                                }
                            }
                            if ($pervich == 3 || $pervich == 4 || $pervich == 5) {
                                if ($i_summ > 0) {
                                    $pervich_summ_arr_new[$type][3]++;
                                }
                            }
                        }
                    }
                }
            }
        }
        //var_dump($pervich_summ_arr_new[5]);

        foreach ($invoices_j as $id => $data) {
            //сортируем по ключам, которые тип стом, косм,...
            ksort($invoices_j[$id]);
        }
        //var_dump($invoices_j[1][5]['data']);
        //var_dump($invoices_j2);

        //Итоговый массив для хранения по типам стом/косм/...
        $rezult_arr = array();

        //Костыль для типа 7
        $rezult_arr[7]['data'] = array();

        //переменная для строки, где будут ссылки на наряды, если с ними что-то не так
        $warn_str_percent_cats = '';

        //Проход по нарядам
        foreach ($invoices_j as $enter => $enter_data) {
            //Если пришел к врачу
            if (($enter == 1) || ($enter == 6)) {
                foreach ($enter_data as $type => $type_data) {

                    if (!isset($rezult_arr[$type])) {
                        $rezult_arr[$type] = array();
                        $rezult_arr[$type]['child_stom_summ'] = 0;
                    }

                    //Если стоматолог
                    //if ($type == 5){
                    //Проход по нарядам
                    //не страховые
                    foreach ($type_data['data'] as $invoice_id => $invoice_data) {
                        //                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
                        //                                var_dump($invoice_id);

                        if (!isset($rezult_arr[$type]['data'])) {
                            $rezult_arr[$type]['data'] = array();
                        }

                        //                                var_dump('-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-');
                        //                                var_dump($invoice_id);

                        $invoice_summ = 0;
                        $invoice_summins = 0;

                        $invoice_summ_pos = 0;

                        $pervich_status = 0;

                        //Дети стоматологии
                        $child_stom_summ = 0;

                        //Проход по данным наряда (позиции)
                        foreach ($invoice_data as $data) {
                            //var_dump($data);
                            //var_dump($data['percent_cats']);

                            //debug
                            //                                    var_dump(strlen($data['percent_cats']));
                            //                                    if (strlen($data['percent_cats']) == 0){
                            //                                        var_dump($invoice_id);
                            //                                    }

                            //Если не гарантия/не подарок
                            if (($data['guarantee'] != 1) && ($data['gift'] != 1)) {

                                if (!isset($percents_j[$type][$data['percent_cats']])) {
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
                                //if ($data['pervich'] != 5) {
                                //var_dump($data['percent_cats']);

                                //Дети стоматология
                                //var_dump(getyeardiff(strtotime($data['birthday']), 0));
                                if (($type == 5) && (getyeardiff(strtotime($data['birthday']), 0) <= 14)) {
                                    $child_stom_summ += $data['itog_price'];
                                    //var_dump($invoice_id);

                                    //Соберем общие суммы по категориям
                                    if (!isset($rezult_arr_summ[$type])) {
                                        $rezult_arr_summ[$type] = 0;
                                    }
                                    $rezult_arr_summ[$type] += $data['itog_price'];

                                } else {
                                    //Костыль для категории 7 (ассистенты)
                                    //Если не ассистенты
                                    if (!in_array($data['percent_cats'], [58, 59, 61, 62])) {

                                        if (!isset($rezult_arr[$type]['data'][$data['percent_cats']])) {
                                            $rezult_arr[$type]['data'][$data['percent_cats']] = 0;
                                        }
                                        $rezult_arr[$type]['data'][$data['percent_cats']] += $data['itog_price'];

                                        //Соберем общие суммы по категориям
                                        if (!isset($rezult_arr_summ[$type])) {
                                            $rezult_arr_summ[$type] = 0;
                                        }
                                        $rezult_arr_summ[$type] += $data['itog_price'];

                                        //Если что-то пошло не так
                                        if ($type == 7){
                                            if (!isset($percents_j[$type][$data['percent_cats']])){
                                                if (!in_array($invoice_id, $error_invoices)) {
                                                    array_push($error_invoices, $invoice_id);
                                                }
                                            }
                                        }
                                        if ($type == 10){
                                            if (!isset($percents_j[$type][$data['percent_cats']])){
                                                if (!in_array($invoice_id, $error_invoices)) {
                                                    array_push($error_invoices, $invoice_id);
                                                }
                                            }
                                        }
                                        //if (isset($percents_j[7][$percent_cat_id])) {
                                    } else {
                                        //Если ассистенты (позиция, которая используется только для ассистов (кт, орто))
                                        if (!isset($rezult_arr[7]['data'][$data['percent_cats']])) {
                                            $rezult_arr[7]['data'][$data['percent_cats']] = 0;
                                        }
                                        $rezult_arr[7]['data'][$data['percent_cats']] += $data['itog_price'];

                                        //Соберем общие суммы по категориям
                                        if (!isset($rezult_arr_summ[7])) {
                                            $rezult_arr_summ[7] = 0;
                                        }
                                        $rezult_arr_summ[7] += $data['itog_price'];
                                    }
                                }
                                //}
                            }
                        }

                        //var_dump( $rezult_arr_summ);

                        //Детство отдельно добавим
                        if ($type == 5) {
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

                    //страховые
                    if (!empty($type_data['insure_data'])) {
                        //Проход по страховым нарядам
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
                                //var_dump($data);
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
        //            var_dump($rezult_arr_summ);

        //Расходы, выдано из кассы
        $giveoutcash_j = array();
        //Расходы, выдано из кассы подробно
        $giveoutcash_ex_j = array();
        //Сумма расходов
        $giveoutcash_summ = 0;


        //Поехали собирать расходные ордера
        $query = "SELECT * FROM `journal_giveoutcash` WHERE
                MONTH(`date_in`) = '" . dateTransformation($month) . "' AND YEAR(`date_in`) = '{$year}' 
                AND `office_id`='{$filial_id}' AND `status` <> '9' 
                ORDER BY `type` DESC";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);
        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                array_push($giveoutcash_ex_j, $arr);

                if (!isset($giveoutcash_j[$arr['type']])) {
                    $giveoutcash_j[$arr['type']] = 0;
                }
                $giveoutcash_j[$arr['type']] += $arr['summ'];

                $giveoutcash_summ += $arr['summ'];
            }
        }
        //var_dump($giveoutcash_j);
        //            var_dump($giveoutcash_ex_j);

        //Сертификаты проданные
        $certificates_j = array();
        //Сумма общая, за которую продали
        $certificates_summSell = 0;

        $query = "SELECT `cell_price` FROM  `journal_cert` WHERE `office_id` = '{$filial_id}' AND (`status`='7' OR `status`='5') AND MONTH(`cell_time`) = '" . dateTransformation($month) . "' AND YEAR(`cell_time`) = '{$year}' ";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                array_push($certificates_j, $arr);
                $certificates_summSell += $arr['cell_price'];
            }
        }
        //var_dump($certificates_j);
        //var_dump($certificates_summSell);


        //Сертификаты использованные при оплате из ранее проданных
        $certificate_payments_j = array();
        //Сумма общая, на котору. расплатились сертификатами
        $certificate_payments_summ = 0;

        $query = "SELECT `summ` FROM  `journal_payment` WHERE `filial_id` = '{$filial_id}' AND `cert_id`<>'0' AND `status`='0' AND MONTH(`date_in`) = '" . dateTransformation($month) . "' AND YEAR(`date_in`) = '{$year}' ";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                array_push($certificate_payments_j, $arr);
                $certificate_payments_summ += $arr['summ'];
            }
        }
        //var_dump($certificate_payments_j);
        //var_dump($certificate_payments_summ);

        //Получаем приходы извне
        $money_from_outside = 0;

        $query = "SELECT `summ` FROM `fl_journal_money_from_outside` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                $money_from_outside += $arr['summ'];
            }
        }


        //Получаем данные из сводного отчета за месяц
        $reports_j = array();

        $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month' AND `status` = '7'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                array_push($reports_j, $arr);
            }
        }
        //var_dump($reports_j);


        $cashbox_nal = 0;
        $beznal = 0;
        $arenda = 0;
        $rashod = 0;
        $ostatok = 0;

        $temp_solar_beznal = 0;
        $temp_solar_nal = 0;

        foreach ($reports_j as $report) {
            $cashbox_nal += $report['nal'];
            $beznal += $report['beznal'];
            $arenda += $report['arenda'];
            $rashod += $report['temp_giveoutcash'];

            $temp_solar_nal += $report['temp_solar_nal'] + $report['cashbox_abon_nal'] + $report['cashbox_solar_nal'] + $report['cashbox_realiz_nal'];
            $temp_solar_beznal += $report['temp_solar_beznal'] + $report['cashbox_abon_beznal'] + $report['cashbox_solar_beznal'] + $report['cashbox_realiz_beznal'];
        }

        //!!! костыль с % Добавим сумму за солярий в массив сумм
        //            $rezult_arr_summ['sol'] = $temp_solar_nal + $temp_solar_beznal;
        //var_dump($rezult_arr_summ);

        //!!! Костыль!
        // Вычислим % по каждому типу (5,6, etc)
        //            foreach ($rezult_arr_summ as $type => $summ){
        //                $rezult_arr_prcnt[$type] = number_format($summ * 100 / (array_sum($rezult_arr_summ)), 2, '.', '');
        //            }
        //            var_dump(array_sum($rezult_arr_summ));
        //            var_dump($rezult_arr_prcnt);

        //Получаем данные по выданным деньгам на филилале (зп, авансы и тд.)
        $subtractions_j_temp = array();
        $subtractions_j_beznal_temp = array();
        $subtractions_j_all_temp = array();
        $subtractions_j = array();
        $subtractions_j_beznal = array();
        $subtractions_j_all = array();

        //Сумма выданного на руки
        $subtractions_summ = 0;
        //Выдано на карту (безнал)
        $subtractions_summ_beznal = 0;
        //Выдано всё вместе
        $subtractions_summ_all = 0;

        //По филиально в зависимости от оплат
        //            $query = "SELECT flj_sub.*, sw.	permissions, sw.name
        //                      FROM `fl_journal_filial_subtractions` flj_sub
        //                      LEFT JOIN spr_workers sw ON sw.id = flj_sub.worker_id
        //                      WHERE flj_sub.filial_id='{$filial_id}' AND flj_sub.year='$year' AND (flj_sub.month='$month' OR flj_sub.month='".(int)$month."')";

        //            $query = "SELECT fl_jp.*, sw.permissions, sw.name
        //                FROM `fl_journal_paidouts` fl_jp
        //                LEFT JOIN `fl_journal_tabels` fl_tj ON fl_tj.id = fl_jp.tabel_id
        //                LEFT JOIN spr_workers sw ON sw.id = fl_jp.worker_id
        //                WHERE fl_tj.office_id='{$filial_id}' AND (fl_tj.month='{$month}' OR fl_tj.month='".(int)$month."') AND fl_tj.year='{$year}'";

        //По филиалам конкретно по табелям (не зависит от оплат, только от того, где открыта была работа)
        $query = "SELECT fl_jp.*, sw.permissions, sw.name, fl_tj.worker_id
            FROM `fl_journal_tabels` fl_tj
            INNER JOIN `fl_journal_paidouts` fl_jp ON fl_tj.id = fl_jp.tabel_id
            LEFT JOIN spr_workers sw ON sw.id = fl_tj.worker_id
            WHERE fl_tj.office_id='{$filial_id}' AND (fl_tj.month='{$month}' OR fl_tj.month='" . (int)$month . "') AND fl_tj.year='{$year}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                //var_dump($arr);
                //array_push($subtractions_j_temp, $arr);
                if ($arr['noch'] != 1) {
//                    if ($arr['type'] != 4) {
                        if (!isset($subtractions_j_temp[$arr['permissions']])) {
                            $subtractions_j_temp[$arr['permissions']] = array();
                        }
                        if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']])) {
                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']] = array();

                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
                        }
                        if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();

//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['nal'] = array();
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['beznal'] = array();
                        }
                        array_push($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//                        array_push($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['nal'], $arr);

                        $subtractions_summ += $arr['summ'];
//                        $subtractions_summ_all += $arr['summ'];


                        //Всё вместе
//                        if (!isset($subtractions_j_all_temp[$arr['permissions']])) {
//                            $subtractions_j_all_temp[$arr['permissions']] = array();
//                        }
//                        if (!isset($subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']])) {
//                            $subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']] = array();
//
//                            $subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
//                            $subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
//                        }
//                        if (!isset($subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
//                            $subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();
//                        }
//                        array_push($subtractions_j_all_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//
//                        $subtractions_summ += $arr['summ'];


//                    }
                    //На карту
                    if ($arr['type'] == 4) {
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']] = array();
//                        }
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']] = array();
//
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
//                        }
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();
//                        }
//                        array_push($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//
                        $subtractions_summ_beznal += $arr['summ'];


//                        if (!isset($subtractions_j_temp[$arr['permissions']])) {
//                            $subtractions_j_temp[$arr['permissions']] = array();
//                        }
//                        if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']])) {
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']] = array();
//
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
//                        }
//                        if (!isset($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();
//
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['nal'] = array();
//                            $subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['beznal'] = array();
//                        }
////                        array_push($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//                        array_push($subtractions_j_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']]['beznal'], $arr);
//
////                        $subtractions_summ += $arr['summ'];
//                        $subtractions_summ_all += $arr['summ'];



                        //Всё вместе
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']] = array();
//                        }
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']] = array();
//
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'] = array();
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['name'] = $arr['name'];
//                        }
//                        if (!isset($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']])) {
//                            $subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']] = array();
//                        }
//                        array_push($subtractions_j_beznal_temp[$arr['permissions']][$arr['worker_id']]['data'][$arr['type']], $arr);
//
//                        $subtractions_summ_beznal += $arr['summ'];


                    }
                }

            }
        }
        //var_dump($query);
        //var_dump($subtractions_j_temp);
        //var_dump($subtractions_j_temp[5][267]['data'][4]);


        //Сколько еще осталось выплатить (!!! тут неправильно, не учитывается коэффициент, ассистенты и так далее. Если будет использоваться, переделать правильно рассчет надо)
        $salary_debt_j_temp = array();
        $salary_debt_j = array();
        //Общая сумма
        $salary_debt_summ = 0;

        $query = "SELECT fl_jt.* FROM `fl_journal_tabels` fl_jt
                      WHERE fl_jt.month = '$month' AND fl_jt.year = '$year' AND fl_jt.office_id='{$filial_id}'
                       AND fl_jt.status <> '9' 
                       AND ((fl_jt.summ + fl_jt.surcharge + fl_jt.summ_calc + fl_jt.night_smena + fl_jt.empty_smena - fl_jt.paidout - fl_jt.deduction) <> '0')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
//                var_dump($arr['id']);
//                var_dump($arr['summ'] + $arr['surcharge'] + $arr['night_smena'] + $arr['empty_smena'] - $arr['paidout'] - $arr['deduction']);

                $salary_debt_summ += intval($arr['summ'] + $arr['surcharge'] + $arr['night_smena'] + $arr['empty_smena'] - $arr['paidout'] - $arr['deduction']);

                if(($arr['type'] != 5) && ($arr['type'] != 6) && ($arr['type'] != 10)){
                    $salary_debt_summ += intval($arr['summ_calc']);
                }
            }
        }

        //var_dump($salary_debt_summ);


        //отсортируем по $permissions_sort_method
        foreach ($permissions_sort_method as $key) {
            //var_dump($key);

            if (isset($subtractions_j_temp[$key])) {
                $subtractions_j[$key] = $subtractions_j_temp[$key];
            }

            if (isset($subtractions_j_beznal_temp[$key])) {
                $subtractions_j_beznal[$key] = $subtractions_j_beznal_temp[$key];
            }
        }
        //var_dump($subtractions_j);

        //Банк
        $bank_j = array();
        $bank_summ = 0;

        $query = "SELECT SUM(`summ`) AS summ FROM `fl_journal_in_bank` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND (`month`='$month' OR `month`='" . (int)$month . "')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            //while ($arr = mysqli_fetch_assoc($res)){
            $arr = mysqli_fetch_assoc($res);

            $bank_summ = $arr['summ'];
            //}
        }
        //          var_dump($query);
        //          var_dump($bank_summ);

        //АН
        $director_j = array();
        $director_summ = 0;

        $query = "SELECT SUM(`summ`) AS summ FROM `fl_journal_to_director` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            //while ($arr = mysqli_fetch_assoc($res)){
            $arr = mysqli_fetch_assoc($res);

            $director_summ = $arr['summ'];
            //}
        }
        //          var_dump($director_summ);


        //Получаем дефициты предыдущих месяцев
        $prev_month_filial_summ_arr = array();

        $query = "SELECT `filial_id`, `summ` FROM `fl_journal_prev_month_filial_deficit` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                //array_push($paidouts_temp_j, $arr);

                $prev_month_filial_summ_arr[$arr['filial_id']] = $arr['summ'];
            }
        }
        //            var_dump($prev_month_filial_summ_arr);


        //Получаем данные по выданным деньгам сверх того, что у есть в программе.
        //Например зп сотрудников, которых нет в программе
        //Вносится вручную
        $paidouts_temp_j = array();
        $paidouts_temp_summ = 0;


        $query = "SELECT * FROM `fl_journal_paidouts_temp` WHERE `filial_id`='{$filial_id}' AND `year`='$year' AND `month`='$month' ORDER BY `worker_id`";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {
                array_push($paidouts_temp_j, $arr);

                $paidouts_temp_summ += $arr['summ'];
            }
        }
        //            var_dump($paidouts_temp_j);


        $result = array(
            'rezult_arr' => $rezult_arr,
            'cashbox_nal' => $cashbox_nal,
            'arenda' => $arenda,
            'money_from_outside' => $money_from_outside,
            'beznal' => $beznal,
            'giveoutcash_summ' => $giveoutcash_summ,
            'subtractions_j' => $subtractions_j,
            'subtractions_j_beznal' => $subtractions_j_beznal,
            'subtractions_j_all' => $subtractions_j_all,
            'subtractions_summ' => $subtractions_summ,
            'subtractions_summ_beznal' => $subtractions_summ_beznal,
            'subtractions_summ_all' => $subtractions_summ_all,
            'paidouts_temp_j' => $paidouts_temp_j,
            'paidouts_temp_summ' => $paidouts_temp_summ,
            'giveoutcash_ex_j' => $giveoutcash_ex_j,
            'bank_summ' => $bank_summ,
            'director_summ' => $director_summ,
            'temp_solar_beznal' => $temp_solar_beznal,
            'temp_solar_nal' => $temp_solar_nal,
            'percents_j' => $percents_j,
            'giveoutcash_j' => $giveoutcash_j,
            'give_out_cash_types_j' => $give_out_cash_types_j,
            'prev_month_filial_summ_arr' => $prev_month_filial_summ_arr,
            'zapis_j' => $zapis_j,
            'pervich_summ_arr_new' => $pervich_summ_arr_new,
            'salary_debt_summ' => $salary_debt_summ,
            'error_invoices' => $error_invoices,
        );

        return $result;

    }

?>