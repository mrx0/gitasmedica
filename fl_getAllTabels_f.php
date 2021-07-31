<?php

//fl_getAllTabels_f.php
//Функция поиска табелей за период (для fl_tabels2.php админы и ассистенты)

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            require 'variables.php';
            include_once 'fl_DBWork.php';

            $rez = array();

            //$summCalc = 0;

            //$rezult = '';

            //$invoice_rez_str = '';

            if (!isset($_POST['month']) || !isset($_POST['typeW']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Ошибка #24</div>', 'summCalc' => 0, 'notDeployCount' => 0));
            }else {

                $msql_cnnct = ConnectToDB();

                if ($_POST['month'] < 10) {
                    $month = '0'.$_POST['month'];
                }else{
                    $month = $_POST['month'];
                }

                if ($_POST['typeW'] == 999){
                    //Выберем всех сотрудников с такой должностью
                    $workers_target_str = implode(',', $workers_target_arr);

                    $query = "SELECT `id`, `worker_id`, `office_id`, `summ`, `status` FROM `fl_journal_tabels` WHERE `type` IN ($workers_target_str) AND `month` = '{$month}' AND `year` = '{$_POST['year']}' AND `status` <> '9';";

                }else {
                    $query = "
                    SELECT fl_j_tab.id, fl_j_tab.worker_id, fl_j_tab.office_id, fl_j_tab.summ, fl_j_tab.status 
                    FROM `fl_journal_tabels` fl_j_tab
                    LEFT JOIN `options_worker_spec` opt_ws ON opt_ws.worker_id = fl_j_tab.worker_id
                    WHERE (fl_j_tab.type='{$_POST['typeW']}' OR opt_ws.oklad = '1' OR opt_ws.oklad_work = '1') AND fl_j_tab.month = '{$month}' AND fl_j_tab.year = '{$_POST['year']}' AND fl_j_tab.status <> '9';";

//                    $query = "SELECT sw.*, sc.name AS cat_name, sc.id AS cat_id
//                        FROM `spr_workers` sw
//
//                        LEFT JOIN `options_worker_spec` opt_ws ON opt_ws.worker_id = sw.id
//
//                        LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
//                        LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
//                        WHERE (sw.permissions = '".$type."' OR opt_ws.oklad = '1' OR opt_ws.oklad_work = '1')  AND sw.status <> '8'
//                        ORDER BY sw.full_name ASC";



                }

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($rez, $arr);

                        if (!isset($rez[$arr['worker_id']])){
                            $rez[$arr['worker_id']] = $arr;
                        }
                        //array_push($rez[$arr['worker_id']], $arr);
                    }

                    echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rez, 'summCalc' => 0, 'notDeployCount' => 0));

                } else {
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0, 'notDeployCount' => 0));
                }
            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Ошибка #25</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>