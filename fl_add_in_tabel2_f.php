<?php

//fl_add_in_tabel2_f.php
//Новый табель добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabelForAdding'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                if (isset($_SESSION['fl_calcs_tabels2'])) {

                    if (!empty($_SESSION['fl_calcs_tabels2'])) {

                        //$calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels2']['data']);
                        $typeID = $_SESSION['fl_calcs_tabels2']['type'];
                        $filialID = $_SESSION['fl_calcs_tabels2']['filial_id'];
                        $workerID =$_SESSION['fl_calcs_tabels2']['worker_id'];

                        $thisCalcIsInAnotherTabel = FALSE;

                        $summCalcs = 0;

                        $msql_cnnct = ConnectToDB2();

                        $calcArr = $_SESSION['fl_calcs_tabels2']['data'];

                        //Проверить, нет ли их уже в другом табеле
                        foreach ($calcArr as $calcID => $status) {

                            $arr = array();

                            $query = "SELECT `id` FROM `fl_journal_tabels_ex` WHERE `id` = '$calcID';";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {

                                $thisCalcIsInAnotherTabel = TRUE;
                                break;

                            }
                        }

                        $query = '';

                        if (!$thisCalcIsInAnotherTabel) {

                            foreach ($calcArr as $calcID => $status) {
                                $query .= "INSERT IGNORE INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$_POST['tabelForAdding']}', '{$calcID}');";

                                //$summCalcs += $rezData['summ'];

                            }

                            //тут пример ожидание MySQL, ждём все инсерты перед селектом
                            if (count($calcArr) > 1) {

                                $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                while (mysqli_next_result($msql_cnnct)) // flush multi_queries
                                {
                                    if (!mysqli_more_results($msql_cnnct)) break;
                                }
                                //А если всего 1, то и нечего паузы ставить
                            } else {
                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                            }

                            unset($_SESSION['fl_calcs_tabels2']);

                            CloseDB($msql_cnnct);

                            //Обновим баланс табеля
                            updateTabelBalance($_POST['tabelForAdding']);

                            echo json_encode(array('result' => 'success', 'data' => ''));
                            //var_dump($arr);
                        }else{

                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #23. РЛ уже в другом табеле.</div>'));
                        }
                    } else {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                    }
                } else {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
            }
        }
    }

?>