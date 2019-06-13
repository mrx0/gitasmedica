<?php

//fl_getCalcsFromSessionForExistTabel_f.php
//Соберём все расчетные листы из сессии и покажем их перед добавлением в табель + табели
//!!! не уверен, что это рационально, туда сюда запросы к сессии каждый раз

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

        if (isset($_SESSION['fl_calcs_tabels2'])) {

            if (!empty($_SESSION['fl_calcs_tabels2'])) {
                //var_dump($_SESSION['fl_calcs_tabels2']);

                //$calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                $typeID = $_SESSION['fl_calcs_tabels2']['type'];
                $filialID = $_SESSION['fl_calcs_tabels2']['filial_id'];
                $workerID = $_SESSION['fl_calcs_tabels2']['worker_id'];

                $summCalcs = 0;

                $filials_j = getAllFilials(false, false, false);

                $result .= '
                    <header style="margin-bottom: 10Px;">
                        <h2>Новый табель</h2>
                        <div style="text-align: left;">
						    '.WriteSearchUser('spr_workers', $workerID, 'user', true).' / '.$filials_j[$filialID]['name'].'<br><br>
						    Месяц:
				            <select id="tabelMonth">';

	            foreach ($monthsName as $val => $name){

                    if ($val == date('m')){
                        $selected = 'selected';
                    }else{
                        $selected = '';
                    }

                    $result .= '
                                <option value="'.$val.'" '.$selected.'>'.$name.'</option>';

	            }

                $result .= '
			                </select>
			                Год: <input id="tabelYear" type="number" value="'.date('Y').'" min="2000" max="2030" size="4" style="width: 60px;">
                        </div>
					</header>';

                $calcArr = $_SESSION['fl_calcs_tabels2']['data'];
                $queryDop = '';
                $calcsArrayData = array();
                $temp_res = '';

                $msql_cnnct = ConnectToDB ();

                foreach ($calcArr as $calcId => $status){
                    $queryDop .=  "'{$calcId}'  OR `id`=";
                }

                $queryDop = substr($queryDop, 0, -10);

                $query = "SELECT * FROM `fl_journal_calculate` WHERE `id`=".$queryDop;
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($calcsArrayData, $arr);
                    }
                }
                //var_dump($calcsArrayData);

                if (!empty($calcsArrayData)){

                    $result .= '
                                <div class="tableDataNPaidCalcs" style="background-color: rgb(254, 253, 211); text-align: left; height: 280px; ;overflow-y: scroll;  overflow-x: hidden;">';

                    foreach ($calcsArrayData as $rezData) {
                        $temp_res .=
                            '
                                    <div class="cellsBlockHover" style="background-color: white; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                        <div style="display: inline-block; width: 190px;">
                                            <div>
                                            <!--<a href="fl_calculate.php?id=' . $rezData['id'] . '" class="ahref">-->
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 70%;">
                                                        #'.$rezData['id'].' / ' . date('d.m.y', strtotime($rezData['create_time'])) . '
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
    
                                            <!--</a>-->
                                            </div>
    
                                        </div>
    
                                        <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                    </div>';

                        $summCalcs += $rezData['summ'];

                    }

                    $result .= $temp_res;

                    $result .= '
                                </div>';

                    $result .= '
                                <div class="tableDataNPaidCalcs" style="background-color: white; margin-top: 5px;">
                                    <div style="background-color: white; margin: 5px 0; padding: 2px; text-align: right;">
                                        Сумма: <span class="summCalcsForTabel calculateOrder">'.$summCalcs.'</span> руб.
                                    </div>
                                </div>';

                }

                echo $result;

                /*echo '
                    </div>
                    <div style="margin: 5px 0;">

                        <input type="button" class="b" value="Сохранить" onclick="fl_addNewTabel();">
                    </div>
                    <div id="doc_title">Добавление расчётных листов в Новый табель - Асмедика</div>';*/


            }
        }
    }

?>