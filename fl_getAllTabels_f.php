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

                $query = "SELECT `id`, `worker_id`, `office_id` FROM `fl_journal_tabels` WHERE `type`='{$_POST['typeW']}' AND `month` = '{$month}' AND `year` = '{$_POST['year']}' AND `status` <> '9';";

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