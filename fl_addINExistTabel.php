<?php

//fl_addINExistTabel.php
//Добавить в существующий табель

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'ffun.php';

        require 'variables.php';

        require 'config.php';

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        $temp_arr = array();

        //var_dump($_SESSION);

        if (isset($_SESSION['fl_calcs_tabels'])) {

            if (!empty($_SESSION['fl_calcs_tabels'])) {
                //var_dump($_SESSION['fl_calcs_tabels']);

                $calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                $typeID = $calcData_Arr[1];
                $filialID = $calcData_Arr[3];
                $workerID = $calcData_Arr[2];

                $summCalcs = 0;

                $filial_j = SelDataFromDB('spr_filials', $filialID, 'offices');

                echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
                        <div class="nav">
                            <a href="fl_tabels.php" class="b">Важный отчёт</a>
                        </div>
					
						<h1>Добавление расчётных листов в существующий табель</h1>
						'.WriteSearchUser('spr_workers', $workerID, 'user', true).' / '.$filial_j[0]['name'].'<br>
						<!--Месяц: 
				        <select id="tabelMonth">';
	            /*foreach ($monthsName as $val => $name){

                    if ($val == date('m')){
                        $selected = 'selected';
                    }else{
                        $selected = '';
                    }

                    echo '<option value="'.$val.'" '.$selected.'>'.$name.'</option>';

	            }*/
                echo '
			            </select>
			            Год: <input id="tabelYear" type="number" value="'.date('Y').'" min="2000" max="2030" size="4" style="width: 60px;">-->
			            
			            
					</header>
					</div>';

                echo '
					<div id="data">';

                $calcArr = $_SESSION['fl_calcs_tabels']['main_data'];
                $queryDop = '';
                $calcsArrayData = array();
                $rezult = '';

                $msql_cnnct = ConnectToDB2 ();

                for ($i = 1; $i < count($calcArr); $i++){
                    $queryDop .=  " OR `id`='{$calcArr[$i]}'";
                }

                $query = "SELECT * FROM `fl_journal_calculate` WHERE `id`='{$calcArr[0]}' ".$queryDop."";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($calcsArrayData, $arr);
                    }
                }

                if (!empty($calcsArrayData)) {
                    //var_dump($calcsArrayData);

                    echo '<div class="tableDataNPaidCalcs">';

                    $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #717171; font-size: 90%;">
                                Расчётные листы, <br>которые хотите добавить
                            </div>';

                    foreach ($calcsArrayData as $rezData) {
                        $rezult .=
                            '
                                <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                    <div style="display: inline-block; width: 190px;">
                                        <div>
                                        <a href="fl_calculate.php?id=' . $rezData['id'] . '" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                </div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 70%;">
                                                    РЛ #'.$rezData['id'].' / ' . date('d.m.y', strtotime($rezData['create_time'])) . '
                                                </div>
                                            </div>
                                            <div>
                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                    Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                </div>
                                            </div>
                                            
                                        </a>
                                        </div>

                                    </div>

                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                </div>';

                        $summCalcs += $rezData['summ'];

                    }

                    echo $rezult;

                    echo '
                        <div style="margin: 5px 0; padding: 2px; text-align: right;">
                            Сумма: <span class="summCalcsNPaid calculateOrder">'.$summCalcs.'</span> руб.
                        </div>
                    </div>';


                    echo '
                    <div class="tableTabels">';


                    $rezult = '';
                    //$invoice_rez_str = '';
                    $summCalc = 0;

                    //Выберем табели уже существующие для этого работника
                    //$query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$typeID}' AND `worker_id`='{$workerID}' AND `office_id`='{$filialID}' AND `status` <> '7' AND `status` <> '9';";
                    $query = "SELECT * FROM `fl_journal_tabels` WHERE `type`='{$typeID}' AND `worker_id`='{$workerID}' AND `office_id`='{$filialID}' AND `status` <> '7' AND `status` <> '9' AND (`year` > '2018' OR (`year` = '2018' AND `month` > '05'));";

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

                            $rezult .= '
                            <div style="margin: 5px 0 -23px; padding: 2px; text-align: center; color: #717171; font-size: 90%;">
                                Выберите табель, <br>в который хотите добавить РЛ
                            </div>';

                            foreach ($rez as $year => $yearData){

                                $rezult .= '
                            <div style="margin: 33px 0 -2px; padding: 2px; text-align: left; color: #717171;">
                                Год <span style="color: #252525; font-weight: bold;">'.$year.'</span>
                            </div>';

                                ksort($yearData);

                                //$yearData = array_reverse($yearData);

                                foreach ($yearData as $month => $monthData) {

                                    $rezult .= '
                                    <div style="margin: 2px 0 2px; padding: 2px; text-align: right; color: #717171;">
                                        Месяц <span style="color: #252525; font-weight: bold;">'.$monthsName[$month].'</span>
                                    </div>';

                                    foreach ($monthData as $rezData) {

                                        $rezult .=
                                            '
                                        <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                            <div style="display: inline-block; /*width: 150px;*/">
                                                <a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">
                                                    <div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: middle;">
                                                            Табель #' . $rezData['id'] . '
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 13px">
                                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 14px">' . $rezData['summ'] . '</span> руб.
                                                        </div>
                                                    </div>
                                                    
                                                </a>
                                                
                                                
                                                
                                                
                                                
                                            </div>
                           
                                            <div style="display: inline-block; vertical-align: top; float: right;">
                                                <div style="border: none; padding: 3px; margin: 1px;">
                                                    <input type="radio" class="radioBtnCalcs" name="tabelForAdding" value="' . $rezData['id'] . '">
                                                </div>
                                            </div>

                                        </div>';

                                        $summCalc += $rezData['summ'];

                                    }
                                }
                                /*$rezult .= '
                            </div>';*/
                            }

                            echo $rezult;
                        }else{

                        }
                    }

                    echo '</div>';

                }

                echo '
                    </div>
                    <div style="margin: 5px 0;">

                        <input type="button" class="b" value="Применить" onclick="fl_addInExistTabel();">
                    </div>
                    <div id="doc_title">Добавление расчётных листов в существующий табель - Асмедика</div>';

            }
        }

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>