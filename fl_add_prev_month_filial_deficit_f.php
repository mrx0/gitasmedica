<?php

//fl_add_prev_month_filial_deficit_f.php
//Функция добавления дефицита филиала в выбранный месяц/год

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['filial_id']) || !isset($_POST['summ']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                //Сначала посмотрим, нет ли в базе уже за этот месяц чего-то такого
                //Если есть, то удалим это и добавим новое

                $deficit_id = 0;

                //Сравнение дат, если меньше либо равно текущему месяцу, то ничего нельзя делать
                $date = $_POST['year'].'-'.$_POST['month'].'-01';
                $todaydate = date('Y').'-'.date('m').'-01';

                $msql_cnnct = ConnectToDB();

                $uncheckDailyReport = 'false';

                //Настройка разрешения изменений  задним числом
                $query = "SELECT `value` FROM `settings` WHERE `option`='uncheckDailyReport' LIMIT 1";
                $res2 = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                $uncheckDailyReport = mysqli_fetch_assoc($res2);

                //ограничение по времени на редактирование
                if (($date >= $todaydate) || ($_SESSION['permissions'] == 3) || ($uncheckDailyReport['value'] == 'true')) {

                    $query = "SELECT `id` FROM `fl_journal_prev_month_filial_deficit` 
                          WHERE `filial_id`='" . $_POST['filial_id'] . "' AND `month`='" . $_POST['month'] . "' AND `year`='" . $_POST['year'] . "' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);
                        $deficit_id = $arr['id'];

                        $query = "DELETE FROM `fl_journal_prev_month_filial_deficit`
                          WHERE `id`='" . $deficit_id . "'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    }

                    $time = date('Y-m-d H:i:s', time());

                    $query = "INSERT INTO `fl_journal_prev_month_filial_deficit` (`filial_id`, `month`, `year`, `summ`, `create_time`, `create_person`)
                            VALUES (
                            '{$_POST['filial_id']}', '{$_POST['month']}', '{$_POST['year']}', '{$_POST['summ']}', '{$time}', '{$_SESSION['id']}');";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);


                    //логирование
                    //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                    echo json_encode(array('result' => 'success', 'data' => 'Ok'));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => 'Ошибка #56. Нельзя сохранять/обновлять данные прошлыми датами.'));
                }

            }
        }
    }
?>