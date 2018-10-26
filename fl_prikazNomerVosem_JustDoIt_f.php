<?php

//fl_prikazNomerVosem_JustDoIt_f.php
//Приказ №8 второй этап, изменение % в РЛ

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabel_id']) || !isset($_POST['newPercent']) || !isset($_POST['controlCategories'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                //var_dump ($_POST);

                if (!empty($_POST['controlCategories'])) {

                    $temp_arr = array();
                    $conditionStr = '';

                    //Преобразуем элементы массива для запроса
                    foreach($_POST['controlCategories'] as $id => $item){
                        $temp_arr[$id] = "`percent_cats` = '".$item."'";
                    }

                    //Собираем кусочек запроса $dop
                    if (count($temp_arr) > 1){
                        $conditionStr = implode(' OR ', $temp_arr);
                    }

                    $calc_arr = array();
                    $calc_ex_arr = array();

                    $msql_cnnct = ConnectToDB();

                    //Получение данных (id в которых надо обновить %) расширенных таблиц РЛ только по указанному табелю
                    $query = "SELECT jcalcex.id, jcalcex.calculate_id FROM `fl_journal_calculate_ex` jcalcex WHERE jcalcex.calculate_id IN (
                      SELECT jcalc.id FROM `fl_journal_calculate` jcalc
                      LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '".$_POST['tabel_id']."'
                      WHERE jtabex.calculate_id = jcalc.id AND ({$conditionStr}))";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($calc_ex_arr, $arr['id']);
                            if (!in_array($arr['calculate_id'], $calc_arr)){
                                array_push($calc_arr, $arr['calculate_id']);
                            }
                        }
                    }else{

                    }

                    $conditionStr = '';

                    if (!empty($calc_ex_arr)){
                        $conditionStr = implode(',', array_map('intval', $calc_ex_arr));

                        //Обновим БД с новыми %
                        $query = "UPDATE `fl_journal_calculate_ex` SET `work_percent` = '{$_POST['newPercent']}' WHERE `id` IN ({$conditionStr})";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //Ф-ция для получения кол-ва Update'нутых строк, тут не используется сейчас,
                        //но мало ли надо будет потом
                        //affected_rows

                        echo json_encode(array('result' => 'success', 'data' => $calc_arr, 'data2' => $query));

                    }else{
                        echo json_encode(array('result' => 'error', 'data' => $query));
                    }
                }
            }
        }
    }
?>