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

                $msql_cnnct = ConnectToDB();

                $query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND `status` <> '9' AND (`year` > '2018' OR (`year` = '2018' AND `month` > '05'));";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($rez, $arr);

                        if (!isset($rez[$arr['year']])) {
                            $rez[$arr['year']] = array();
                        }
                        if (!isset($rez[$arr['year']][$arr['month']])) {
                            $rez[$arr['year']][$arr['month']] = array();
                        }

                        array_push($rez[$arr['year']][$arr['month']], $arr);

                    }

                    if (!empty($rez)){

                        //include_once 'fl_showCalculateRezult.php';

                        krsort($rez);

                        //счетчик непроведенных табелей
                        $notDeployCount = 0;

                        $rezult .= '
                            <div style="margin: 5px 0 -17px; padding: 2px; text-align: center; color: #0C0C0C;">
                                <b><i>Табели сотрудника</i></b>
                            </div>';

                        foreach ($rez as $year => $yearData){

                            $bgColor = '';
                            $display = '';
                            $onclick = '';
                            $lastYearDescr = '';

                            /*if (date('Y', time()) == $year) {
                                $bgColor = 'background-color: rgba(254, 63, 63, 0.69);';
                            }*/

                            /*if ($year != date('Y', time())){
                                //$rezult .= date('Y', time());
                                ksort($yearData);
                            }else{
                                krsort($yearData);
                            }*/



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


                            //ksort($yearData);
                            krsort($yearData);

                            //$yearData = array_reverse($yearData);

                            foreach ($yearData as $month => $monthData) {

                                $bgColor = '';

                                if ($year == date('Y', time())) {
                                    if (date('n', time()) == $month) {
                                        //$bgColor = 'background-color: rgba(244, 254, 63, 0.54);';
                                        $bgColor = 'background-color: rgba(101, 228, 83, 0.33);';
                                    }
                                }

                                $rezult .= '
                                    <div style="margin: 2px 0 2px; padding: 2px; text-align: right; color: #717171; '.$bgColor.'">
                                        <!--Месяц --><span style="color: #252525; font-weight: bold;">'.$monthsName[$month].'</span>
                                    </div>';

                                foreach ($monthData as $rezData) {

                                    $rezult .=
                                        '
                                        <div class="cellsBlockHover" style="background-color: rgb(255, 255, 255); border: 1px solid #BFBCB5; margin-top: 1px; position: relative; '.$bgColor.'">
                                            <div style="display: inline-block; width: 180px;">
                                                <a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">
                                                    <div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 123%">
                                                            <b><i>Табель #' . $rezData['id'] . '</i></b>
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
                                                                        ' . intval($rezData['summ']+$rezData['surcharge']+$rezData['night_smena']+$rezData['empty_smena']) . '
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
                                                                        ' . intval($rezData['summ']-$rezData['paidout']-$rezData['deduction']+$rezData['surcharge']+$rezData['night_smena']+$rezData['empty_smena']) . '
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
                } else {
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0, 'notDeployCount' => 0));
                }
                //echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>