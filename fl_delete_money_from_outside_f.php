<?php

//fl_delete_money_from_outside_f.php
//Функция удаления приходов денег извне

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['filial_id']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                //Сначала посмотрим, нет ли в базе уже за этот месяц чего-то такого
                //Если есть, то удалим это и добавим новое

//                $deficit_id = 0;

                //Сравнение дат, если меньше либо равно текущему месяцу, то ничего нельзя делать
                $date = $_POST['year'].'-'.$_POST['month'].'-01';
                $todaydate = date('Y').'-'.date('m').'-01';

                //ограничение по времени на редактирование
                if (($date >= $todaydate) || ($_SESSION['permissions'] == 3)) {

                    $msql_cnnct = ConnectToDB();

                    $query = "DELETE FROM `fl_journal_money_from_outside` WHERE `filial_id` = '{$_POST['filial_id']}' AND `year` = '{$_POST['year']}' AND `month` = '{$_POST['month']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);


                    //логирование
                    //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                    echo json_encode(array('result' => 'success', 'data' => 'Ok'));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => 'Ошибка #63. Нельзя сохранять/обновлять данные прошлыми датами.'));
                }

            }
        }
    }
?>