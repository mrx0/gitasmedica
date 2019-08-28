<?php

//fl_solar_add_f.php
//оплата солярия

//!!! доделать сравнение времени, учитывая месяц и тд и тп

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);

    if ($_POST){
        include_once 'DBWork.php';
        include_once 'functions.php';

        //разбираемся с правами
        $god_mode = FALSE;

        require_once 'permissions.php';

        //$temp_arr = array();
        //переменная для дополнительного текста в запросе при обновлении наряда
        $query_invoice_dop = '';
        //...и сертификата
        $query_abon_dop = '';

        if (!isset($_POST['filial_id']) || !isset($_POST['date_in']) || !isset($_POST['device_type']) ||
            !isset($_POST['min_count']) || !isset($_POST['summ_type']) ||
            !isset($_POST['oneMinPrice']) || !isset($_POST['finPrice']) ||
            !isset($_POST['descr']) || !isset($_POST['abon_id']) ||
            !isset($_POST['realiz_summ'])
        ){
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
        }else{
            //var_dump ($_POST);

            //Маркер ошибок
            $weHaveError = false;
            //Текст ошибки
            $errorStr = '';

            $time = date('Y-m-d H:i:s', time());
            $now_time = date('H:i:s', time());
            $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." ".$now_time));

            $descr = addslashes($_POST['descr']);

            $res_data = '';

            //Проверки, проверочки
            include_once 'DBWork.php';
            include_once 'ffun.php';

            //Заднее число
            if ((time() > strtotime($_POST['date_in'] . " 21:00:00") + 2 * 24 * 60 * 60) &&  ($finances['add_new'] != 1) && !$god_mode){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Нельзя вносить задним числом</div>'));
            } else {

                $msql_cnnct = ConnectToDB2();

                //Если абонемент
                if ($_POST['summ_type'] == 3) {
                    if ($_POST['abon_id'] > 0) {
                        //Работа с абонементом
                        //Ищем абонемент
                        $abon_j = SelDataFromDB('journal_abonement_solar', $_POST['abon_id'], 'id');

                        if ($abon_j != 0) {

                            //Осталось минут
                            if ($abon_j[0]['min_count'] - $abon_j[0]['debited_min'] > 0) {

                                //Если хотим списать минут больше чем доступно
                                if ($_POST['min_count'] > ($abon_j[0]['min_count'] - $abon_j[0]['debited_min'])) {
                                    $weHaveError = true;
                                    $errorStr = '<span style="color: red;">На абонементе недостаточно минут для списания.</span>';
                                } else {
                                    //Обновим потраченные минуты в абонементе
                                    $query = "UPDATE `journal_abonement_solar` SET `debited_min`='" . ($abon_j[0]['debited_min'] + $_POST['min_count']) . "}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['abon_id']}';";
                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                }

                            } else {
                                $weHaveError = true;
                                $errorStr = '<span style="color: red;">На абонементе не осталось доступных минут.</span>';
                            }

                        } else {
                            $weHaveError = true;
                            $errorStr = '<span style="color: red;">Нет такого абонемента в базе.</span>';
                        }
                    } else {
                        $weHaveError = true;
                        $errorStr = '<span style="color: red;">Не указан абонемент.</span>';
                    }
                }

                if (!$weHaveError) {

                    if ($_POST['summ_type'] != 3){
                        $_POST['abon_id'] = 0;
                    }else{
                        $_POST['oneMinPrice'] = 0;
                        $_POST['finPrice'] = 0;
                    }

                    //Если минут больше 0, то добавим
                    if ($_POST['min_count'] > 0) {

                        //Вставим новую запись по солярию
                        $query = "INSERT INTO `journal_solar` (
                        `filial_id`, `date_in`, `device_type`, `min_count`, `summ_type`, `abon_id`, `min_price`, `summ`, `descr`, `create_time`, `create_person`)
                        VALUES (
                        '{$_POST['filial_id']}', '{$date_in}', '{$_POST['device_type']}', '{$_POST['min_count']}', '{$_POST['summ_type']}', '{$_POST['abon_id']}', '{$_POST['oneMinPrice']}', '{$_POST['finPrice']}', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //ID новой позиции
//                    $mysqli_insert_id = mysqli_insert_id($msql_cnnct);
                    }

                    //Реализация (средства для загара)
                    if ($_POST['realiz_summ'] > 0){
                        //Вставим новую запись по реализации
                        $query = "INSERT INTO `journal_realiz` (
                        `filial_id`, `date_in`, `summ_type`, `summ`, `create_time`, `create_person`)
                        VALUES (
                        '{$_POST['filial_id']}', '{$date_in}', '{$_POST['summ_type']}', '{$_POST['realiz_summ']}', '{$time}', '{$_SESSION['id']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    }

                    echo json_encode(array('result' => 'success', 'data' => 'Ok'));

                } else {
                    echo json_encode(array('result' => 'error', 'data' => $errorStr));
                }
            }
        }
    }
}
?>