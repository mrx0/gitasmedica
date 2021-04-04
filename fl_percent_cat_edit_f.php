<?php

//fl_percent_cat_edit_f.php
//Функция редактирования категории процентов непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            if (!isset($_POST['cat_id']) || !isset($_POST['cat_name']) || !isset($_POST['work_percent']) || !isset($_POST['material_percent']) || !isset($_POST['personal_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                //А нет ли уже такого в базе?
                $query = "SELECT * FROM `fl_spr_percents` WHERE `personal_id`='{$_POST['personal_id']}' AND `name`='{$_POST['cat_name']}' AND `id`<>'{$_POST['cat_id']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Такая категория уже есть.</div>'));
                } else {

                    $time = time();

                    //Обновляем категорию процентов в базу
                    $query = "UPDATE `fl_spr_percents` 
                    SET `name`='{$_POST['cat_name']}', `work_percent`='".(int)$_POST['work_percent']."', `material_percent`='".(int)$_POST['material_percent']."', `summ_special`='".(int)$_POST['summ_special']."', `type`='{$_POST['personal_id']}', `personal_id`='{$_POST['personal_id']}', 
                    `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}'
                    WHERE `id`='{$_POST['cat_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="fl_percent_cat.php?id='.$_POST['cat_id'].'" class="ahref">Категория процентов</a> изменена.</div>'));
                }
            }
        }
    }
?>