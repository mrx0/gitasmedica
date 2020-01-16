<?php

//fl_menuForClearTabel_f.php
//Меню с выбором месяца и года при создании пустого табеля

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{

        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'ffun.php';
        require 'variables.php';

        $temp_res = array();
        $result = '';

        //var_dump($_SESSION);

        //if (isset($_SESSION['fl_calcs_tabels2'])) {

//            if (isset($_SESSION['fl_calcs_tabels2']['data'])) {
//                if (!empty($_SESSION['fl_calcs_tabels2']['data'])) {
                    //var_dump($_SESSION['fl_calcs_tabels2']);

                    //$calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                    $typeID = $_POST['type_id'];
                    $filialID = $_POST['filial_id'];
                    $workerID = $_POST['worker_id'];

                    $summCalcs = 0;

                    $filials_j = getAllFilials(false, true, true);

                    $result .= '
                    <header style="margin-bottom: 10Px;">';

                    //if ($_POST['newTabel'] == 1) {
                        $result .= '
                        <h2>Создать новый пустой табель?</h2>';
                    //} else {
//                        $result .= '
//                        <h2>Добавление РЛ</h2>';
                    //}


                    $result .= '
                        <div style="text-align: left;">
						    ' . WriteSearchUser('spr_workers', $workerID, 'user', true) . ' / ' . $filials_j[$filialID]['name'] . '<br><br>';
                    if ($_POST['newTabel'] == 1) {
                        $result .= '
						    Месяц:
				            <select id="tabelMonth">';

                        foreach ($monthsName as $val => $name) {

                            if ($val == date('m')) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }

                            $result .= '
                                    <option value="' . $val . '" ' . $selected . '>' . $name . '</option>';

                        }

                        $result .= '
			                </select>
			                Год: <input id="tabelYear" type="number" value="' . date('Y') . '" min="2000" max="2030" size="4" style="width: 60px;">
                        </div>';
                    }
                    $result .= '
					</header>';

//                    $calcArr = $_SESSION['fl_calcs_tabels2']['data'];
//                    $queryDop = '';
//                    $calcsArrayData = array();
//                    $temp_res = '';
//
//                    $msql_cnnct = ConnectToDB();
//
//                    foreach ($calcArr as $calcId => $status) {
//                        $queryDop .= "'{$calcId}'  OR `id`=";
//                    }
//
//                    $queryDop = substr($queryDop, 0, -10);
//
//                    $query = "SELECT * FROM `fl_journal_calculate` WHERE `id`=" . $queryDop;
//                    //var_dump($query);
//
//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                    $number = mysqli_num_rows($res);
//
//                    if ($number != 0) {
//                        while ($arr = mysqli_fetch_assoc($res)) {
//                            array_push($calcsArrayData, $arr);
//                        }
//                    }
//                    //var_dump($calcsArrayData);
//
//                    if (!empty($calcsArrayData)) {
//
//                        $result .= '
//                                <div style="display: block; text-align: center;">
//                                    <div class="tableDataNPaidCalcs" style="width: 210px; background-color: rgb(234, 234, 234); text-align: left; height: 280px; overflow-y: scroll;  overflow-x: hidden;">';
//
//                        $result .= '
//                                <div style="padding: 2px; text-align: center; color: #717171; font-size: 80%;">
//                                    Расчётные листы, <br>которые хотите добавить
//                                </div>';
//
//                        foreach ($calcsArrayData as $rezData) {
//                            $temp_res .=
//                                '
//                                        <div class="cellsBlockHover" style="background-color: white; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
//                                            <div style="display: inline-block; width: 190px;">
//                                                <div>
//                                                <!--<a href="fl_calculate.php?id=' . $rezData['id'] . '" class="ahref">-->
//                                                    <div>
//                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
//                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
//                                                        </div>
//                                                        <div style="display: inline-block; vertical-align: middle; font-size: 70%;">
//                                                            #' . $rezData['id'] . ' / ' . date('d.m.y', strtotime($rezData['create_time'])) . '
//                                                        </div>
//                                                    </div>
//                                                    <div>
//                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
//                                                            Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
//                                                        </div>
//                                                    </div>
//
//                                                <!--</a>-->
//                                                </div>
//
//                                            </div>
//
//                                            <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
//                                        </div>';
//
//                            $summCalcs += $rezData['summ'];
//
//                        }
//
//                        $result .= $temp_res;
//
//                        $result .= '
//                                    </div>';
//
//
//                        //Если хотим добавить в существующий уже табель
//                        if ($_POST['newTabel'] != 1) {
//
//                            $result .= '
//                                    <div class="tableTabels" style="width: 240px; background-color: rgb(234, 234, 234); text-align: left; height: 280px; overflow-y: scroll;  overflow-x: hidden;">';
//
//                            //$invoice_rez_str = '';
//                            //$summCalc = 0;
//
//                            //Выберем табели уже существующие для этого работника
//                            $tabels_j = fl_getTabels($typeID, $workerID, $filialID, false, false);
//
////                            //$query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$typeID}' AND `worker_id`='{$workerID}' AND `office_id`='{$filialID}' AND `status` <> '7' AND `status` <> '9';";
////                            $query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$typeID}' AND `worker_id`='{$workerID}' AND `office_id`='{$filialID}' AND `status` <> '7' AND `status` <> '9' AND (`year` > '2018' OR (`year` = '2018' AND `month` > '05'));";
////
////                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
////
////                            $number = mysqli_num_rows($res);
////
////                            if ($number != 0) {
////                                while ($arr = mysqli_fetch_assoc($res)) {
////                                    //array_push($rez, $arr);
////
////                                    if (!isset($rez[$arr['year']])) {
////                                        $rez[$arr['year']] = array();
////                                    }
////                                    if (!isset($rez[$arr['year']][$arr['month']])) {
////                                        $rez[$arr['year']][$arr['month']] = array();
////                                    }
////
////                                    array_push($rez[$arr['year']][$arr['month']], $arr);
////
////                                }
//
//                            if (!empty($tabels_j)) {
//
//                                //include_once 'fl_showCalculateRezult.php';
//
//                                //krsort($rez);
//
//                                $result .= '
//                                    <div style="padding: 2px; text-align: center; color: #717171; font-size: 80%;">
//                                        Выберите табель, <br>в который хотите добавить РЛ
//                                    </div>';
//
//                                foreach ($tabels_j as $year => $yearData) {
//
//                                    $bgColor = '';
//                                    $display = '';
//                                    $onclick = '';
//                                    $lastYearDescr = '';
//
//                                    if ($year != date('Y', time())) {
//                                        $display = 'display: none;';
//                                        $onclick = 'onclick="$(\'#data2_' . $year . '_' . $workerID . '_' . $filialID . '\').stop(true, true).slideToggle(\'slow\');"';
//                                        $lastYearDescr = ' <span style="font-size: 75%;"> Развернуть/Свернуть</span>';
//                                    }
//
//                                    $result .= '
//                                    <div style="margin: 15px 0 -2px; padding: 2px; text-align: left; color: #717171; font-size: 85%; cursor: pointer; ' . $bgColor . '" ' . $onclick . '">
//                                        Год <span style="color: #252525; font-weight: bold;">' . $year . '</span>' . $lastYearDescr . '
//                                    </div>';
//
//                                    $result .= '
//                                    <div id="data2_' . $year . '_' . $workerID . '_' . $filialID . '"  style="' . $display . '">';
//
//                                    krsort($yearData);
//
//                                    //$yearData = array_reverse($yearData);
//
//                                    foreach ($yearData as $month => $monthData) {
//
//                                        $bgColor = '';
//
//                                        if ($year == date('Y', time())) {
//                                            if (date('n', time()) == $month) {
//                                                //$bgColor = 'background-color: rgba(244, 254, 63, 0.54);';
//                                                //$bgColor = 'background-color: rgb(255, 241, 114);';
//                                                $bgColor = 'box-shadow: 2px 4px 7px rgb(0, 216, 255); border-top: 1px dotted rgb(0, 216, 255);';
//                                            }
//                                        }
//
//                                        $result .= '
//                                    <div style="margin: 2px 0 2px; padding: 2px; text-align: right; color: #717171;">
//                                        <!--Месяц --><span style="color: #252525; font-weight: bold; ' . $bgColor . '">' . $monthsName[$month] . '</span>
//                                    </div>';
//
//                                        foreach ($monthData as $rezData) {
//
//                                            $result .= '
//                                    <!--<div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">-->
//                                    <div class="cellsBlockHover" style="background-color: white; border: 1px solid #BFBCB5; margin-top: 1px; position: relative; ' . $bgColor . '">
//                                        <div style="display: inline-block; /*width: 150px;*/">
//                                            <!--<a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">-->
//                                                <div>
//                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
//                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
//                                                    </div>
//                                                    <div style="display: inline-block; vertical-align: middle;">
//                                                        Табель #' . $rezData['id'] . ' <span style="font-size: 80%;">['.$filials_j[$rezData['office_id']]['name2'].']</span>
//                                                    </div>
//                                                </div>
//                                                <div>
//                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 13px">
//                                                        Сумма РЛ: <span class="calculateInvoice calculateCalculateN" style="font-size: 14px">' . $rezData['summ'] . '</span> руб.
//                                                    </div>
//                                                </div>
//
//                                            <!--</a>-->
//                                        </div>
//
//                                        <div style="position: absolute; top: -4px; right: -4px;">
//                                            <div style="border: none; padding: 3px; margin: 1px;">
//                                                <input type="radio" class="radioBtnCalcs" name="tabelForAdding" value="' . $rezData['id'] . '">
//                                            </div>
//                                        </div>
//
//                                    </div>';
//
//                                            //$summCalc += $rezData['summ'];
//
//                                        }
//                                    }
//
//                                    $result .= '
//                                </div>';
//
//                                }
//
//                            } else {
//
//                            }
//                            //}
//
//                            $result .= '
//                                    </div>';
//                        }
//                        $result .= '
//                                </div>';
//
//                        $result .= '
//                                <div class="tableDataNPaidCalcs" style="background-color: white; margin-top: 5px; border: none;">
//                                    <div style="background-color: white; margin: 5px 0; padding: 2px; text-align: right;">
//                                        Сумма: <span class="summCalcsForTabel calculateOrder">' . $summCalcs . '</span> руб.
//                                    </div>
//                                </div>';
//                        $result .= '
//                            </div>';
//
//
//                    }

                    echo $result;

                    /*echo '
                        </div>
                        <div style="margin: 5px 0;">

                            <input type="button" class="b" value="Сохранить" onclick="fl_addNewTabel();">
                        </div>
                        <div id="doc_title">Добавление расчётных листов в Новый табель - Асмедика</div>';*/

//                }
//            }
        //}
    }

?>