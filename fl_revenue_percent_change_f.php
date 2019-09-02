<?php

//fl_revenue_percent_change_f.php
//Функция изменения процента от выручки

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['table']) || !isset($_POST['permission']) || !isset($_POST['filial_id']) || !isset($_POST['category']) || !isset($_POST['value'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $time = date('Y-m-d H:i:s', time());

                $msql_cnnct = ConnectToDB2();

                $value = 0;

                //А нет ли уже такого значения в базе?
                if ($_POST['table'] == 'solar'){
                    $query = "SELECT `id` FROM `fl_spr_revenue_solar_percent` WHERE `permission`='{$_POST['permission']}' AND `filial_id`='{$_POST['filial_id']}' AND `category`='{$_POST['category']}'";
                }elseif ($_POST['table'] == 'realiz'){
                    $query = "SELECT `id` FROM `fl_spr_revenue_realiz_percent` WHERE `permission`='{$_POST['permission']}' AND `filial_id`='{$_POST['filial_id']}' AND `category`='{$_POST['category']}'";
                }elseif ($_POST['table'] == 'abon'){
                    $query = "SELECT `id` FROM `fl_spr_revenue_abon_percent` WHERE `permission`='{$_POST['permission']}' AND `filial_id`='{$_POST['filial_id']}' AND `category`='{$_POST['category']}'";
                }else{
                    $query = "SELECT `id` FROM `fl_spr_revenue_percent` WHERE `permission`='{$_POST['permission']}' AND `filial_id`='{$_POST['filial_id']}' AND `category`='{$_POST['category']}'";
                }

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        $id = $arr['id'];
                    }

                    if ($_POST['table'] == 'solar'){
                        $query = "UPDATE `fl_spr_revenue_solar_percent` SET `value` = '{$_POST['value']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$id}'";
                    }elseif ($_POST['table'] == 'realiz'){
                        $query = "UPDATE `fl_spr_revenue_realiz_percent` SET `value` = '{$_POST['value']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$id}'";
                    }elseif ($_POST['table'] == 'abon'){
                        $query = "UPDATE `fl_spr_revenue_abon_percent` SET `value` = '{$_POST['value']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$id}'";
                    }else{
                        $query = "UPDATE `fl_spr_revenue_percent` SET `value` = '{$_POST['value']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$id}'";
                    }

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                }else{
                    if ($_POST['table'] == 'solar'){
                        $query = "INSERT INTO `fl_spr_revenue_solar_percent` (`permission`, `filial_id`, `category`, `value`, `create_person`, `create_time`)
                        VALUES (
                        '{$_POST['permission']}', '{$_POST['filial_id']}', '{$_POST['category']}', '{$_POST['value']}', '{$time}', '{$_SESSION['id']}');";
                    }elseif ($_POST['table'] == 'realiz'){
                        $query = "INSERT INTO `fl_spr_revenue_realiz_percent` (`permission`, `filial_id`, `category`, `value`, `create_person`, `create_time`)
                        VALUES (
                        '{$_POST['permission']}', '{$_POST['filial_id']}', '{$_POST['category']}', '{$_POST['value']}', '{$time}', '{$_SESSION['id']}');";
                    }elseif ($_POST['table'] == 'abon'){
                        $query = "INSERT INTO `fl_spr_revenue_abon_percent` (`permission`, `filial_id`, `category`, `value`, `create_person`, `create_time`)
                        VALUES (
                        '{$_POST['permission']}', '{$_POST['filial_id']}', '{$_POST['category']}', '{$_POST['value']}', '{$time}', '{$_SESSION['id']}');";
                    }else{
                        $query = "INSERT INTO `fl_spr_revenue_percent` (`permission`, `filial_id`, `category`, `value`, `create_person`, `create_time`)
                        VALUES (
                        '{$_POST['permission']}', '{$_POST['filial_id']}', '{$_POST['category']}', '{$_POST['value']}', '{$time}', '{$_SESSION['id']}');";
                    }
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                }


                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">OK</div>'));

            }
        }
    }
?>