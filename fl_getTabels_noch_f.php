<?php

//fl_getTabels_noch_f.php
//Выборка табелей (ночных) по исполнителю

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
        if ($_POST) {
            if (isset($_POST['type_id']) && isset($_POST['worker_id']) && isset($_POST['filial_id'])) {

                $filials_j = getAllFilials(false, true, true);

                $workerID = $_POST['worker_id'];

                $tabels_j = fl_getTabels($_POST['type_id'], $workerID, $_POST['filial_id'], true, false);
//                var_dump($tabels_j);

//                $result .= '
//                    <div style="padding: 2px; text-align: center; color: #717171; font-size: 80%;">
//                        Выберите табель, <br>в который хотите добавить вычет
//                    </div>';


                $result .= '
                    <header style="margin-bottom: 10Px;">
                        <h2>Выберите табель</h2>';

                $result .= '
                        Или создайте новый ';

//                $result .= '
//						    Месяц:
//				            <select id="tabelMonth">';
//
//                foreach ($monthsName as $val => $name) {
//
//                    if ($val == date('m')) {
//                        $selected = 'selected';
//                    } else {
//                        $selected = '';
//                    }
//
//                    $result .= '
//                                    <option value="' . $val . '" ' . $selected . '>' . $name . '</option>';
//
//                }
//
//                $result .= '
//			                </select>
//			                Год: <input id="tabelYear" type="number" value="' . date('Y') . '" min="2000" max="2030" size="4" style="width: 60px;">';
                $result .= "
			                <input type='button' class='b4' value='Создать' onclick='fl_addNewNochTabel(".$_POST['type_id'].", ".$_POST['worker_id'].", ".$_POST['filial_id'].", ".json_encode($_POST['dopData']).");'>";
                $result .= '
                        </div>';

//                $result .= '<input type="button" class="b" value="Создать" onclick="fl_addNewNochTabel();">';


                $result .= '
                        <div style="text-align: left;">
                            '.WriteSearchUser('spr_workers', $workerID, 'user', true).' / ';

                if ($_POST['filial_id'] == 0){
                    $result .= 'Все филиалы';
                }else{
                    $result .= '['.$filials_j[$_POST['filial_id']]['name2'].']';
                }

                $result .= '
                            <br><br>
                        </div>';

                $result .= '
                    </header>';

                //Хотим добавить в существующий уже табель
                $result .= '
                    <div class="tableTabels" style="width: 240px; background-color: rgb(234, 234, 234); text-align: left; height: 280px; overflow-y: scroll;  overflow-x: hidden;">';

                $result .= '
                        <div style="padding: 2px; text-align: center; color: #717171; font-size: 80%;">
                            Выберите табель<!--, <br>в который хотите добавить-->
                        </div>';

                if (!empty($tabels_j)) {

                        foreach ($tabels_j as $year => $yearData) {

                            $bgColor = '';
                            $display = '';
                            $onclick = '';
                            $lastYearDescr = '';

                            if ($year != date('Y', time())) {
                                $display = 'display: none;';
                                $onclick = 'onclick="$(\'#data2_' . $year . '_' . $workerID . '\').stop(true, true).slideToggle(\'slow\');"';
                                $lastYearDescr = ' <span style="font-size: 75%;"> Развернуть/Свернуть</span>';
                            }

                            $result .= '
                                <div style="margin: 15px 0 -2px; padding: 2px; text-align: left; color: #717171; font-size: 85%; cursor: pointer; ' . $bgColor . '" ' . $onclick . '">
                                    Год <span style="color: #252525; font-weight: bold;">' . $year . '</span>' . $lastYearDescr . '
                                </div>';

                            $result .= '
                                <div id="data2_' . $year . '_' . $workerID . '"  style="' . $display . '">';

                            krsort($yearData);
                            //$yearData = array_reverse($yearData);

                            foreach ($yearData as $month => $monthData) {

                                $bgColor = '';

                                if ($year == date('Y', time())) {
                                    if (date('n', time()) == $month) {
                                        //$bgColor = 'background-color: rgba(244, 254, 63, 0.54);';
                                        //$bgColor = 'background-color: rgb(255, 241, 114);';
                                        $bgColor = 'box-shadow: 2px 4px 7px rgb(0, 216, 255); border-top: 1px dotted rgb(0, 216, 255);';
                                    }
                                }
                                //var_dump($month);

                                $result .= '
                                    <div style="margin: 2px 0 2px; padding: 2px; text-align: right; color: #717171;">
                                        <!--Месяц --><span style="color: #252525; font-weight: bold; ' . $bgColor . '">' . $monthsName[$month] . '</span>
                                    </div>';

                                foreach ($monthData as $rezData) {

                                    $result .= '
                                    <!--<div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">-->
                                    <div class="cellsBlockHover" style="background-color: white; border: 1px solid #BFBCB5; margin-top: 1px; position: relative; ' . $bgColor . '">
                                        <div style="display: inline-block; /*width: 150px;*/">
                                            <!--<a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">-->
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle;">
                                                        Табель #' . $rezData['id'] . ' <span style="font-size: 80%;">['.$filials_j[$rezData['filial_id']]['name2'].']</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 13px">
                                                        Сумма РЛ: <span class="calculateInvoice calculateCalculateN" style="font-size: 14px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
                                                
                                            <!--</a>-->
                                        </div>
                       
                                        <div style="position: absolute; top: -4px; right: -4px;">
                                            <div style="border: none; padding: 3px; margin: 1px;">
                                                <input type="radio" class="radioBtnCalcs" name="tabelForAdding" value="' . $rezData['id'] . '">
                                            </div>
                                        </div>
        
                                    </div>';

                                    //$summCalc += $rezData['summ'];

                                }
                            }

                            $result .= '
                                </div>';

                        }
                        $result .= '
                            </div>';

                    } else {

                    }

                echo $result;

            }
        }
    }

?>