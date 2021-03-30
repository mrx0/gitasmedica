<?php

//reportLamps_f.php
//Отдельная функция, которая вернёт данные по лампам

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {
            if (!isset($_POST['month_start']) || !isset($_POST['year_start']) || !isset($_POST['month_end']) || !isset($_POST['year_end']) || !isset($_POST['filial_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once('DBWorkPDO.php');
                include_once 'DBWork.php';
                include_once 'functions.php';
                include_once 'ffun.php';
                require 'variables.php';

                //$filials_j = getAllFilials(true, true, true);

                $db = new DB();

                $args = [
                    'filial_id' => $_POST['filial_id']
                ];

                $query = "
                    SELECT s_lamp.id, s_lamp.descr, s_lamp.solar
                    FROM `spr_lamps` s_lamp
                    WHERE s_lamp.filial_id = :filial_id 
                    AND
                    s_lamp.status <> '9'
                    ";

                $spr_lamps_j = $db::getRows($query, $args);

                if (!empty($spr_lamps_j)) {
                    $args = [
                        'start_date' => $_POST['year_start'] . '-' . $_POST['month_start'],
                        'end_date' => $_POST['year_end'] . '-' . $_POST['month_end'],
                        'filial_id' => $_POST['filial_id']
                    ];

                    $query = "
                        SELECT lamp_rep.*
                        FROM `fl_journal_lamp_report` lamp_rep
                        WHERE lamp_rep.filial_id = :filial_id 
                        AND
                        CONCAT_WS('-', lamp_rep.year, LPAD(lamp_rep.month, 2, '0')) BETWEEN :start_date AND :end_date
                        ORDER BY lamp_rep.lamp_id DESC, CONCAT_WS('-', lamp_rep.year, LPAD(lamp_rep.month, 2, '0'), LPAD(lamp_rep.day, 2, '0')) ASC, lamp_rep.evening DESC
                        ";

                    $lamp_counts_j = $db::getRows($query, $args);

                    $res = array();

                    if (!empty($lamp_counts_j)) {
                        foreach ($lamp_counts_j as $item) {
                            if (!isset($res[$item['lamp_id']])) {
                                $res[$item['lamp_id']] = array();
                            }
                            //Если вечер
                            if ($item['evening'] == 1){
                                $datetime = $item['year'].'-'.$item['month'].'-'.$item['day'].' 09:00';
                            }else{
                                $datetime = $item['year'].'-'.$item['month'].'-'.$item['day'].' 21:00';
                            }

                            array_push($res[$item['lamp_id']], ['x' => $datetime, 'y' => $item['count']]);
                        }
                    }

                    echo json_encode(array('result' => 'success',  'spr_lamps_j' => $spr_lamps_j, 'data_count' => $res));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Ничего не найдено.</span>'));
                }

//                    if (!empty($calcex_j)){
//                        foreach ($calcex_j as $calc_item) {
//                            if (!isset($rez_temp[$calc_item['worker_id']])) {
//                                $rez_temp[$calc_item['worker_id']] = array();
//                                $rez_temp[$calc_item['worker_id']]['w_name'] = $calc_item['worker_name'];
//                                $rez_temp[$calc_item['worker_id']]['data'] = array();
//                            }
//                            if (!isset($rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']])) {
//                                $rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']] = array();
//                            }
//                            if (!isset($rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']])) {
//                                $rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']] = array();
//                            }
//                            if (!isset($rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']][$calc_item['month']])) {
//                                $rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']][$calc_item['month']] = array();
//                            }
//                            if (!isset($rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']][$calc_item['month']][$calc_item['calculate_id']])) {
//                                $rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']][$calc_item['month']][$calc_item['calculate_id']] = array();
//                            }
//                            array_push($rez_temp[$calc_item['worker_id']]['data'][$calc_item['filial_id']][$calc_item['year']][$calc_item['month']][$calc_item['calculate_id']], $calc_item);
//                        }
//                    }
//
//
//                    $rez = '';
//                    //Формируем HTML для вывода в браузер
//                    if (!empty($rez_temp)){
//                        //Массив сумм по позициям для Наряда за весь период
//                        $summ_all_arr = array();
//                        //Строка для всего периода
//                        $temp_all_str = '';
//
//                        foreach ($rez_temp as $w_id => $w_data) {
//                            //Массив сумм по позициям для Наряда по сотруднику
//                            $summ_worker_arr = array();
//                            //Строка для сотрудника
//                            $temp_worker_str = '';
//
//                            foreach ($w_data['data'] as $f_id => $f_data) {
//                                //Массив сумм по позициям для Наряда по филиалу
//                                $summ_filial_arr = array();
//                                //Строка для филиала
//                                $temp_filial_str = '';
//
//                                foreach ($f_data as $year => $y_data) {
//                                    //Массив сумм по позициям для Наряда за год
//                                    $summ_year_arr = array();
//                                    //Строка для года
//                                    $temp_year_str = '';
//
//                                    foreach ($y_data as $month => $m_data) {
//                                        //Массив сумм по позициям для Наряда за месяц
//                                        $summ_month_arr = array();
//                                        //Строка для месяца
//                                        $temp_month_str = '';
//
//                                        foreach ($m_data as $calc_id => $calc_data) {
//                                            //Строка для категорий
//                                            $temp_cats_str = '';
//                                            //Массив с суммами по позициям для РЛ
//                                            $summ_arr = array();
//
//                                            //Соберём набор всех позиций тут
//                                            foreach ($calc_data as $c_item) {
//                                                //Массив с суммами по позициям для каждого РЛ
//                                                if (!isset($summ_arr[$c_item['percent_cats']])){
//                                                    $summ_arr[$c_item['percent_cats']] = array();
//                                                    $summ_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//
//                                                //Массив с суммами по позициям для всех РЛ за месяц
//                                                if (!isset($summ_month_arr[$c_item['percent_cats']])){
//                                                    $summ_month_arr[$c_item['percent_cats']] = array();
//                                                    $summ_month_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_month_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_month_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_month_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_month_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//                                                //Массив с суммами по позициям для всех РЛ за год
//                                                if (!isset($summ_year_arr[$c_item['percent_cats']])){
//                                                    $summ_year_arr[$c_item['percent_cats']] = array();
//                                                    $summ_year_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_year_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_year_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_year_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_year_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//                                                //Массив с суммами по позициям для всех РЛ по филиалу
//                                                if (!isset($summ_filial_arr[$c_item['percent_cats']])){
//                                                    $summ_filial_arr[$c_item['percent_cats']] = array();
//                                                    $summ_filial_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_filial_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_filial_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_filial_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_filial_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//                                                //Массив с суммами по позициям для всех РЛ по сотруднику
//                                                if (!isset($summ_worker_arr[$c_item['percent_cats']])){
//                                                    $summ_worker_arr[$c_item['percent_cats']] = array();
//                                                    $summ_worker_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_worker_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_worker_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_worker_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_worker_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//                                                //Массив с суммами по позициям для всех РЛ по всему периоду
//                                                if (!isset($summ_all_arr[$c_item['percent_cats']])){
//                                                    $summ_all_arr[$c_item['percent_cats']] = array();
//                                                    $summ_all_arr[$c_item['percent_cats']]['name'] = $c_item['cat_name'];
//                                                    $summ_all_arr[$c_item['percent_cats']]['inv_summ'] = 0;
//                                                    $summ_all_arr[$c_item['percent_cats']]['calc_summ'] = 0;
//                                                }
//                                                $summ_all_arr[$c_item['percent_cats']]['inv_summ'] += $c_item['price'];
//                                                $summ_all_arr[$c_item['percent_cats']]['calc_summ'] += $c_item['summ'];
//
//                                                //Собираем визуализацию позиций
//                                                $temp_cats_str .= '
//                                                            <div class="cellsBlock">
//                                                                <div class="cellText2" style="width: 250px; min-width: 250px; max-width: 250px;">
//                                                                    '.$c_item['price_name'].'<br>
//                                                                    <i class="percentCatID_'.$c_item['percent_cats'].'" style="color: rgb(15, 6, 142); font-size: 80%;">'.$c_item['cat_name'].'</i>
//                                                                </div>
//                                                                <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
//                                                                    '.$c_item['price'].'<br>
//                                                                    <span style="font-size:80%; color: #999; ">Из наряда</span>
//                                                                </div>
//                                                                <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 120px; min-width: 120px; max-width: 120px;">
//                                                                    <b>'.$c_item['summ'].'</b> ( '.$c_item['work_percent'].'% )<br>
//                                                                    <span style="font-size:80%; color: #999; ">Рассчёт</span>
//                                                                </div>
//                                                            </div>';
//
//
//                                            }
//
//
//                                            $temp_month_str .= '
//                                                    <div class="" data-sort="" style="width: max-content; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; /*display: inline-block; */vertical-align: top; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);">
//                                                        <div style="display: inline-block;">
//                                                            <div>
//                                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px;padding: 2px 10px font-weight: bold; font-style: italic;">
//                                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
//                                                                </div>
//                                                                <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
//                                                                    <a href="fl_calculate.php?id='.$calc_id.'" class="ahref" target="_blank" rel="nofollow noopener">
//                                                                        <b>РЛ #'.$calc_id.'</b>
//                                                                    </a>
//                                                                    <i>По Наряду: <a href="invoice.php?id='.$c_item['invoice_id'].'" class="ahref" target="_blank" rel="nofollow noopener"><b>#'.$c_item['invoice_id'].'</b></a></i>.
//                                                                    пац.: <a href="client.php?id='.$c_item['client_id'].'" class="ahref" target="_blank" rel="nofollow noopener"><b>'.$c_item['client_name']. '</b></a>
//                                                                </div>
//                                                            </div>
//                                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">';
//
////                                                                <b>Сумма из наряда: <span class="invoice_summ" style="color: #14e314;">' . $inv_summ . '</span> р.</b> <br>
////                                                                <b>Сумма из РЛ: <span class="invoice_summ" style="color: #6c2fdc; font-size: 120%;">' . $calc_summ . '</span> р.</b>
//                                            foreach ($summ_arr as $arr_item){
//                                                $temp_month_str .= '<div><b>'.$arr_item['name'].'</b> ';
//                                                $temp_month_str .= 'Сумма из наряда: <span class="invoice_summ" style="color: #14e314; font-weight: bold;">' . $arr_item['inv_summ'] . '</span> р. ';
//                                                $temp_month_str .= 'Сумма из РЛ: <span class="invoice_summ" style="color: #6c2fdc; font-size: 120%; font-weight: bold;">' . $arr_item['calc_summ'] . '</span> р.';
//                                                $temp_month_str .= '</div>';
//                                            }
//
//                                            $temp_month_str .= '
//                                                            </div>';
//                                            $temp_month_str .= $temp_cats_str;
//                                            $temp_month_str .= '
//                                                        </div>
//                                                    </div>';
//                                        }
//
//                                        $temp_year_str .= '<div style="/*border-left: 1px solid #CCC;*/ padding: 3px; box-shadow: 5px 1px 5px rgb(3 3 3 / 46%);"><div onclick="toggleSomething(\'#month_'.$month.'_'.$year.'_'.$f_id.'_'.$w_id.'\');" style="cursor: pointer; text-decoration: underline; font-size: 110%; font-weight: bold;">'.$monthsName[$month].'</div>';
//                                        $temp_year_str .= '
//                                        <table>
//                                            <tr>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Название</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из нарядов</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из РЛов</td>
//                                            </tr>';
//                                        foreach ($summ_month_arr as $arr_item){
//                                            $temp_year_str .= '<td style="font-size: 110%; text-align: left; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><b>'.$arr_item['name'].'</b></td>';
//                                            $temp_year_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #14e314; font-weight: bold;text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['inv_summ'] . '</span> р.</td>';
//                                            $temp_year_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #6c2fdc; font-size: 120%; font-weight: bold;text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['calc_summ'] . '</span> р.</td>';
//                                            $temp_year_str .= '</tr>';
//                                        }
//                                        $temp_year_str .= '</table>';
//                                        $temp_year_str .= '
//                                                    <div id="month_'.$month.'_'.$year.'_'.$f_id.'_'.$w_id.'" class="forToggle" style="display: none; margin-top: 10px; /*border: 1px solid #6c2fdc;*/ box-shadow: 0 0 5px rgb(108 47 220 / 30%); /*margin-left: 10px;*/padding: 2px 10px">
//                                                        '.$temp_month_str.'
//                                                    </div>
//                                                    </div>';
//                                    }
//
//                                    $temp_filial_str .= '<div style="/*border-left: 1px solid #CCC;*/ padding: 3px; box-shadow: 5px 1px 5px rgb(3 3 3 / 46%);"><div onclick="toggleSomething(\'#year_'.$year.'_'.$f_id.'_'.$w_id.'\');" style="cursor: pointer; text-decoration: underline; font-size: 110%; font-weight: bold;">'.$year.'</div>';
//                                    $temp_filial_str .= '
//                                        <table>
//                                            <tr>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Название</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из нарядов</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из РЛов</td>
//                                            </tr>';
//                                    foreach ($summ_year_arr as $arr_item){
//                                        $temp_filial_str .= '<td style="font-size: 110%; text-align: left; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><b>'.$arr_item['name'].'</b></td>';
//                                        $temp_filial_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #14e314; font-weight: bold;text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['inv_summ'] . '</span> р.</td>';
//                                        $temp_filial_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #dc21ff; font-size: 120%; font-weight: bold; text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['calc_summ'] . '</span> р.</td>';
//                                        $temp_filial_str .= '</tr>';
//                                    }
//                                    $temp_filial_str .= '</table>';
//                                    $temp_filial_str .= '
//                                                    <div id="year_'.$year.'_'.$f_id.'_'.$w_id.'" class="forToggle" style="display: none; margin-top: 10px; /*border: 1px solid #dc21ff; */box-shadow: 0 0 5px rgb(220 33 255 / 30%); /*margin-left: 10px;*/padding: 2px 10px">
//                                                        '.$temp_year_str.'
//                                                    </div>
//                                                    </div>';
//                                }
//
//                                $temp_worker_str .= '<div style="/*border-left: 1px solid #CCC;*/ padding: 3px; box-shadow: 5px 1px 5px rgb(3 3 3 / 46%);"><div onclick="toggleSomething(\'#filial_'.$f_id.'_'.$w_id.'\');" style="cursor: pointer; text-decoration: underline; font-size: 110%; font-weight: bold;">'.$filials_j[$f_id]['name'].'</div>';
//                                $temp_worker_str .= '
//                                        <table>
//                                            <tr>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Название</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из нарядов</td>
//                                                <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из РЛов</td>
//                                            </tr>';
//                                foreach ($summ_filial_arr as $arr_item){
//                                    $temp_worker_str .= '<tr>';
//                                    $temp_worker_str .= '<td style="font-size: 110%; text-align: left; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><b>'.$arr_item['name'].'</b></td>';
//                                    $temp_worker_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #14e314; font-weight: bold;text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['inv_summ'] . '</span> р.</td>';
//                                    $temp_worker_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #ff884b; font-size: 120%; font-weight: bold;text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['calc_summ'] . '</span> р.</td>';
//                                    $temp_worker_str .= '</tr>';
//                                }
//                                $temp_worker_str .= '</table>';
//                                $temp_worker_str .= '
//                                                    <div id="filial_'.$f_id.'_'.$w_id.'" class="forToggle" style="display: none; margin-top: 10px; /*border: 1px solid #ff884b;*/ box-shadow: 0 0 5px rgb(255 136 75 / 30%); /*margin-left: 10px;*/padding: 2px 10px">
//                                                        '.$temp_filial_str.'
//                                                    </div>
//                                                    </div>';
//                            }
//
//                            $temp_all_str .= '<div style="/*border-left: 1px solid #CCC;*/ padding: 3px; box-shadow: 5px 1px 5px rgb(3 3 3 / 46%);"><div onclick="toggleSomething(\'#worker_'.$w_id.'\');" style="cursor: pointer; text-decoration: underline; font-size: 110%; font-weight: bold;">'.$w_data['w_name'].'</div>';
//                            $temp_all_str .= '
//                                    <table>
//                                        <tr>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Название</td>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из нарядов</td>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из РЛов</td>
//                                        </tr>';
//                            foreach ($summ_worker_arr as $arr_item){
//                                $temp_all_str .= '<tr>';
//                                $temp_all_str .= '<td style="font-size: 110%; text-align: left; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><b>'.$arr_item['name'].'</b></td>';
//                                $temp_all_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #14e314; font-weight: bold; text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['inv_summ'] . '</span> р.</td>';
//                                $temp_all_str .= '<td style="font-size: 110%; text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #ffc800; font-size: 120%; font-weight: bold; text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['calc_summ'] . '</span> р.</td>';
//                                $temp_all_str .= '</tr>';
//                            }
//                            $temp_all_str .= '</table>';
//                            $temp_all_str .= '
//                                                    <div id="worker_'.$w_id.'" class="forToggle" style="display: none; margin-top: 10px; /*border: 1px solid #ffc800; */box-shadow: 0 0 5px rgb(255 200 0 / 30%); /*margin-left: 10px;*/padding: 2px 10px">
//                                                        '.$temp_worker_str.'
//                                                    </div>
//                                                    </div>';
//
//                        }
//
//                        //$rez .= '<div class="ahref button_tiny no_print" style="display: inline; font-size: 70%; margin-top: 10px;" onclick="toggleSomethingByClass(\'.forToggle\');">Показать/скрыть всё подробно</div>';
//                        $rez .= '<div onclick="toggleSomething(\'#all_data\');" style="margin-top: 20px; cursor: pointer; text-decoration: underline;"><b>Общий результат: </b></div>';
//                        $rez .= '
//                                    <table>
//                                        <tr>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Название</td>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из нарядов</td>
//                                            <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Сумма из РЛов</td>
//                                        </tr>';
//                        foreach ($summ_all_arr as $arr_item){
//                            $rez .= '<tr>';
//                            $rez .= '<td style="font-size: 110%; text-align: left; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><b>'.$arr_item['name'].'</b></td>';
//                            $rez .= '<td style="text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #14e314; font-weight: bold; text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['inv_summ'] . '</span> р. </td>';
//                            $rez .= '<td style="text-align: right; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span class="invoice_summ" style="color: #ff0000; font-size: 120%; font-weight: bold; text-shadow: 1px 1px 0px rgb(111 115 117);">' . $arr_item['calc_summ'] . '</span> р.</td>';
//                            $rez .= '</tr>';
//                        }
//                        $rez .= '</table>';
//                        $rez .= '
//                                                    <div id="all_data" class="forToggle" style="display: none; margin-top: 10px; /*border: 1px solid #ff6666; */box-shadow: 0 0 5px rgb(255 0 0 / 30%); /*margin-left: 10px;*/padding: 2px 10px">
//                                                        ' .$temp_all_str.'
//                                                    </div>';
//
//                        echo json_encode(array('result' => 'success', 'data' => $rez));

//                    }else{
//                        echo json_encode(array('result' => 'error', 'data' => $lamp_counts_j));
//                    }
                }
//            }
        }
    }

?>