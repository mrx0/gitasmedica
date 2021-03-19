<?php

//fl_get_giveouts_f.php
//Функция поиска выплат выдач за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            require 'variables.php';
            include_once 'DBWork.php';
            include_once 'functions.php';

            if (!isset($_POST['filial_id']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {
                //var_dump ($_POST);

                //Приводим месяц к виду 01 02 09 ...
                //$month = dateTransformation ($month);

                $msql_cnnct = ConnectToDB();


                //Выдачи (возвраты) денег пациентам
                $fl_withdraw_j = array();

                $query = "SELECT * FROM  `journal_withdraw` WHERE `filial_id`='{$_POST['filial_id']}' AND MONTH(`date_in`) = '".dateTransformation($_POST['month'])."' AND YEAR(`date_in`) = '{$_POST['year']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($fl_withdraw_j, $arr);
                    }
                }
                //var_dump($fl_withdraw_j);

                //Выплаты !!! не доделал, переделать всё, если понадобится вообще.
                $subtractions_j = array();
                $fl_refunds_j = array();

                //Затраты на материалы
                $material_consumption_j = array();

                //Типы расходов (справочник)
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
                //var_dump( $give_out_cash_types_j);

                //Выдачи из кассы (подробно)
                $giveouts_j = array();
                $giveouts_result_str = '';

                //$query = "SELECT * FROM  `fl_journal_filial_subtractions` WHERE `filial_id`='{$_POST['filial_id']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";

                $query = "SELECT * FROM `journal_giveoutcash` WHERE
                    MONTH(`date_in`) = '{$_POST['month']}' AND YEAR(`date_in`) = '{$_POST['year']}'
                    AND `office_id`='{$_POST['filial_id']}'
                    AND `status` <> '9'
                    ORDER BY `id` ASC";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($giveouts_j, $arr);
                    }

                    if (!empty($giveouts_j)){

                        $giveouts_result_str .= '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; ">
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">Все расходы из кассы за месяц подробно:</div>
                                    <!--<div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">giveout_cash_all.php?filial_id=15&d=31&m=07&y=2019</div>-->
                                </li>';


                        foreach ($giveouts_j as $item){

                            $bgColor = '';

                            $giveouts_result_str .= '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
                            $giveouts_result_str .= '
                                    <div class="cellOrder" style="width: 120px; min-width: 120px; position: relative; border-right: none; border-top: none;">
                                        <b>Расходный ордер #' . $item['id'] . '</b><br>от ' . date('d.m.y', strtotime($item['date_in'])) . '<br>
                                        <span style="font-size: 90%;  color: #555;">';

                            $giveouts_result_str .= '
                                        </span>
                                                        
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">';
                            if ($item['type'] != 0) {
                                $giveouts_result_str .= $give_out_cash_types_j[$item['type']];
                            }else{
                                $giveouts_result_str .= 'Прочее';

                                if ($item['additional_info'] != '') {
                                    $giveouts_result_str .= ':<br><i>' . $item['additional_info'] . '</i>';
                                }
                                //var_dump($item);
                            }

                            $giveouts_result_str .= '                              
                                    </div>
                                    <div class="cellName" style="width: 90px; min-width: 90px; border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
                                        </div>
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="margin: 1px 0; padding: 1px 3px;">
                                            <span class="" style="font-size: 11px">' . $item['comment'] . '</span>
                                        </div>
                                    </div>';

                            //Удалить или восстановить
//                            if ( $item['status'] != 9) {
                                $giveouts_result_str .= '
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;">
                                        <a href="giveout_cash_all.php?filial_id='.$_POST['filial_id'].'&d='.date("d", strtotime($item['date_in'])).'&m='.$_POST['month'].'&y='.$_POST['year'].'"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                    </div>';
//                            }else {
//                                $result_temp .= '
//                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenGiveout_cash('.$item['id'].');">
//                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
//                                    </div>';
//                            }

                            $giveouts_result_str .= '
                                </li>';

                            //Если не удалённый
//                            if ( $item['status'] != 9){
//                                $giveouts_result_str .= $result_temp;
//                            }else{
//                                $giveouts_result_str .= $result_temp;
//                            }
                        }
                    }
                }


                echo json_encode(array('result' => 'success', 'subtractions_j' => 0, 'refunds_j' => $fl_refunds_j, 'withdraw_j' => $fl_withdraw_j, 'material_consumption_j' => $material_consumption_j, 'giveouts_j' => $giveouts_result_str, 'prev_month_filial_summ' => 0));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>