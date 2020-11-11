<?php

//fl_get_tabels_f.php
//Функция поиска табелей за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            require 'variables.php';
            include_once 'fl_DBWork.php';

            $rez = array();
            $arr = array();

            $summCalc = 0;

            $rezult = '';

            $invoice_rez_str = '';

            if (!isset($_POST['permission']) || !isset($_POST['worker']) || !isset($_POST['office']) || !isset($_POST['month'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                //Массив табелей
                $tabels_j = array();

                //счетчик непроведенных табелей
                $notDeployCount = 0;

                $msql_cnnct = ConnectToDB();

                //Выбираем обычные табели
                $query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND `status` <> '9' AND (`year` > '2019' OR (`year` = '2019' AND `month` > '05'));";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($tabels_j, $arr);

                        if (!isset($tabels_j[$arr['year']])) {
                            $tabels_j[$arr['year']] = array();
                        }
                        if (!isset($tabels_j[$arr['year']][$arr['month']])) {
                            $tabels_j[$arr['year']][$arr['month']] = array();
                        }

                        array_push($tabels_j[$arr['year']][$arr['month']], $arr);

                    }
                }

                //Выбираем ночные табели
                $query = "SELECT * FROM `fl_journal_tabels_noch` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `filial_id`='{$_POST['office']}' AND `status` <> '9' AND (`year` > '2019' OR (`year` = '2019' AND `month` > '05'));";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($tabels_j, $arr);

                        if (!isset($tabels_j[$arr['year']])) {
                            $tabels_j[$arr['year']] = array();
                        }
                        if (!isset($tabels_j[$arr['year']][$arr['month']])) {
                            $tabels_j[$arr['year']][$arr['month']] = array();
                        }

                        array_push($tabels_j[$arr['year']][$arr['month']], $arr);

                    }
                }

                //Если что-то есть
                if (!empty($tabels_j)){

                    //Сортируем по году в обратном порядке
                    krsort($tabels_j);

                    $rezult .= '
                        <div style="margin: 5px 0 -17px; padding: 2px; text-align: center; color: #0C0C0C;">
                            <b><i>Табели сотрудника</i></b>
                        </div>';

                    //Раскладываем по годам
                    foreach ($tabels_j as $year => $yearData){

                        $bgColor = '';
                        $display = '';
                        $onclick = '';
                        $lastYearDescr = '';

                        //Сворачиваем старые года
                        if ($year != date('Y', time())) {
                            $display = 'display: none;';
                            $onclick = 'onclick="$(\'#data_'.$year.'_'.$_POST['worker'].'_'.$_POST['office'].'\').stop(true, true).slideToggle(\'slow\');"';
                            $lastYearDescr = ' <span style="font-size: 85%;"> Развернуть/Свернуть</span>';
                        }

                        $rezult .= '
                        <div style="margin: 23px 0 -2px; padding: 2px; text-align: left; color: #717171; cursor: pointer; '.$bgColor.'" '.$onclick.'>
                            Год <span style="color: #252525; font-weight: bold;">'.$year.'</span>'.$lastYearDescr.'
                        </div>';

                        $rezult .= '
                        <div id="data_'.$year.'_'.$_POST['worker'].'_'.$_POST['office'].'"  style="'.$display.'">';

                        //Сортируем по году в обратном порядке
                        krsort($yearData);

                        //$yearData = array_reverse($yearData);

                        //Раскладываем по месяцам
                        foreach ($yearData as $month => $monthData) {

                            $bgColor = '';

                            if ($year == date('Y', time())) {
                                if (date('n', time()) == $month) {
                                    //$bgColor = 'background-color: rgba(244, 254, 63, 0.54);';
                                    //$bgColor = 'background-color: rgba(101, 228, 83, 0.33);';
                                    $bgColor = 'box-shadow: 2px 4px 7px rgb(0, 216, 255); border-top: 1px dotted rgb(0, 216, 255);';
                                }
                            }

                            $rezult .= '
                                <div style="margin: 2px 0 2px; padding: 2px; text-align: right; color: #717171; '.$bgColor.'">
                                    <!--Месяц --><span style="color: #252525; font-weight: bold;">'.$monthsName[$month].'</span>
                                </div>';

                            //Идём по табелям
                            foreach ($monthData as $rezData) {

                                //Выявляем метку ночи (по умолчанию false)
                                $tabel_noch = false;

                                if (isset($rezData['noch'])){
                                    if ($rezData['noch'] == 1){
                                        $tabel_noch = true;
                                    }
                                }

                                //Общая сумма, которую начислили
                                if (!$tabel_noch) {
                                    $summItog = $rezData['summ'] + $rezData['surcharge'] + $rezData['night_smena'] + $rezData['empty_smena'];
                                }else{
                                    $summItog = $rezData['summ'] + $rezData['surcharge'];
                                }

                                //Если ассистент, то плюсуем сумму за РЛ
                                if ($_POST['permission'] == 7){
                                    $summItog += $rezData['summ_calc'];
                                }

                                //Коэффициенты +/-
                                if (($rezData['k_plus'] != 0) || ($rezData['k_minus'] != 0)){
                                    $summItog = $summItog + $summItog/100*($rezData['k_plus'] - $rezData['k_minus']);
                                }
                                //var_dump($summItog);

                                $rezult .= '
                                    <div class="cellsBlockHover" style="background-color: rgb(255, 255, 255); border: 1px solid #BFBCB5; margin-top: 1px; position: relative; '.$bgColor.'">
                                        <div style="display: inline-block; width: 180px;">';
                                if (!$tabel_noch){
                                    $rezult .= '
                                            <a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">';
                                }else{
                                    $rezult .= '
                                            <a href="fl_tabel_noch.php?id=' . $rezData['id'] . '" class="ahref">';
                                }
                                $rezult .= '
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 123%">';

                                if (!$tabel_noch) {
                                    $rezult .= '
                                                        <b><i>Табель #' . $rezData['id'] . '</i></b>';
                                }else{
                                    $rezult .= '
                                                        <b><i>Табель <img src="img/night.png" style="width: 11px;" title="Ночное"> #' . $rezData['id'] . '</i></b>';
                                }

                                $rezult .= '
                                                    </div>
                                                </div>
                                                <div>
                                                    <table style="width: 180px; border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 11px">
                                                        <tr>
                                                            <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                Начислено:
                                                            </td>
                                                            <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                <span class="calculateOrder calculateCalculateN" style="font-size: 13px;">
                                                                    ' . intval($summItog) . '
                                                                </span> руб.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                Удержано: 
                                                            </td>
                                                            <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                <span class="calculateInvoice calculateCalculateN" style="font-size: 13px">
                                                                    ' . $rezData['deduction'] . '
                                                                </span> руб.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                Выплачено:
                                                            </td>
                                                            <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                <span class="calculateInvoice calculateCalculateN" style="font-size: 13px; color: rgb(12, 0, 167);">
                                                                    ' . $rezData['paidout'] . '
                                                                </span> руб.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                Осталось: 
                                                            </td>
                                                            <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                <span class="calculateInvoice calculateCalculateN" style="font-size: 13px">
                                                                    ' . intval($summItog - $rezData['paidout'] - $rezData['deduction']) . '
                                                                </span> руб.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                
                                            </a>
                                            ' . $invoice_rez_str . '
                                        </div>';
                                if ($rezData['status'] == 7) {
                                    $rezult .= '
                                        <div style="display: inline-block; vertical-align: top; font-size: 180%;">
                                            <!--<div style="border: 1px solid #CCC; padding: 3px; margin: 1px;">-->
                                                <i class="fa fa-check" aria-hidden="true" style="color: green;" title="Проведён"></i>
                                            <!--</div>-->
                                        </div>';

                                }else{
                                    $notDeployCount++;
                                }
                                $rezult .= '
                                    </div>';

                                $summCalc += $rezData['summ'];

                            }
                        }
                        $rezult .= '
                            </div>
                        </div>';
                    }

                        echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rezult, 'summCalc' => $summCalc, 'notDeployCount' => $notDeployCount));
                }else{
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => $summCalc, 'notDeployCount' => $notDeployCount));
                }
            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>