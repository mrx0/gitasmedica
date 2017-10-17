<?php

//fl_get_calculates_f.php
//Функция поиска данных расчётов за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
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

                $query = "SELECT * FROM `fl_journal_calculate` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND `status` <> '7';";
                //$query = "SELECT * FROM `fl_journal_calculate` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND MONTH(`create_time`) = '09' AND `status` <> '7';";

                /*Собираем данные с дополнительными
                $query = "SELECT jcalc.*, jcalc.id as calc_id, jcalcex.*
                FROM `fl_journal_calculate_ex` jcalcex
                RIGHT JOIN (
                  SELECT * FROM `fl_journal_calculate` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND MONTH(`create_time`) = '09' AND `status` <> '7'
                ) jcalc ON jcalc.id = jcalcex.calculate_id";*/

                /*$query = "SELECT jcalc.*, jcalcex.*
                FROM `journal_announcing_readmark` jannrm
                RIGHT JOIN (
                  SELECT * FROM `journal_announcing` WHERE `status` <> '9'
                ) jcalc ON jcalc.id = jannrm.announcing_id
                AND jannrm.create_person = '{$_SESSION['id']}'
                ORDER BY `create_time` DESC";*/


                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($rez, $arr);
                    }

                    if (!empty($rez)){

                        //include_once 'fl_showCalculateRezult.php';

                        $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C;">
                                Табели сотрудника
                            </div>';

                        foreach ($rez as $rezData){

                            /*$invoice_data_db = array();
                            $zapis_data_db = array();
                            $invoice_rez_str = '';
                            $zapis_rez_str = '';

                            //Данные записи от расчёта
                            //$query = "SELECT `summ` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1;";

                            $query = "SELECT * FROM `zapis` WHERE `id` = '{$rezData['zapis_id']}' LIMIT 1";
                            //var_dump($query);
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($zapis_data_db, $arr);
                                }
                            }

                            //Данные наряда от расчёта
                            //$query = "SELECT `summ` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1;";

                            $query = "SELECT * FROM `journal_invoice` WHERE `id` = '{$rezData['invoice_id']}' LIMIT 1";
                            //var_dump($query);
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($invoice_data_db, $arr);
                                }
                            }*/

                            //var_dump($invoice_data_db);


                                //Отметка об объеме оплат
                                /*$paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;"></i>';

                                if ($invoice_data_db[0]['summ'] == $invoice_data_db[0]['paid']) {
                                    $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i>';
                                }

                                $invoice_rez_str .= '
                                                <div class="" style="border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                                    <a href="invoice.php?id=' . $invoice_data_db[0]['id'] . '" class="ahref">
                                                        <div>
                                                            <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                            </div>
                                                            <div style="display: inline-block; vertical-align: middle;">
                                                                ' . date('d.m.y', strtotime($invoice_data_db[0]['create_time'])) . '
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                <span class="calculateInvoice" style="font-size: 11px">' . $invoice_data_db[0]['summ'] . '</span> руб.
                                                            </div>';
                                if ($invoice_data_db[0]['summins'] != 0) {
                                    $invoice_rez_str .= '
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                Страховка:<br>
                                                                <span class="calculateInsInvoice" style="font-size: 11px">' . $invoice_data_db[0]['summins'] . '</span> руб.
                                                            </div>';
                                }
                                $invoice_rez_str .= '
                                                        </div>
                                                        
                                                    </a>
                                                    <span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . '</span>
                                                </div>';*/


                            $rezult .=
                                '
                                <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                    <div style="display: inline-block; width: 150px;">
                                        <a href="fl_calculate.php?id='.$rezData['id'].'" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                </div>
                                                <div style="display: inline-block; vertical-align: middle;">
                                                    '.date('d.m.y H:i', strtotime($rezData['create_time'])).'
                                                </div>
                                            </div>
                                            <div>
                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                    Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">'.$rezData['summ'].'</span> руб.
                                                </div>
                                            </div>
                                            
                                        </a>
                                        '.$invoice_rez_str.'
                                    </div>
                                    <div style="display: inline-block; vertical-align: top;">
                                        <div style="border: 1px solid #CCC; padding: 3px; margin: 1px;">
                                            <input type="checkbox" class="chkBoxCalcs chkBox_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$_POST['office'].'" name="nPaidCalcs_'.$rezData['id'].'" value="1">
                                        </div>
                                    </div>
                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                </div>';

                            $summCalc += $rezData['summ'];

                        }


                        $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: right;">
                                Сумма: <span class="summCalcsNPaid calculateInvoice">0</span> руб.
                            </div>';

                        echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rezult, 'summCalc' => $summCalc));
                    }else{
                        echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => $summCalc));
                    }
                } else {
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0));
                }
                //echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0));
        }
    }
?>