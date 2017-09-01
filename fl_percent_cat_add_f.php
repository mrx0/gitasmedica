<?php

//fl_percent_cat_add_f.php
//Функция добавления категории процентов непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            if (!isset($_POST['cat_name']) || !isset($_POST['work_percent']) || !isset($_POST['material_percent']) || !isset($_POST['personal_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                //А нет ли уже такого в базе?
                $query = "SELECT * FROM `fl_spr_percents` WHERE `personal_id`='{$_POST['personal_id']}' AND `name`='{$_POST['cat_name']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Эта категория уже заполнена, попробуйте её отредактировать.</div>'));
                } else {
                    //Добавляем категорию процентов в базу
                    $percent_cat_id = WritePercentCatToDB_Edit($_SESSION['id'], $_POST['cat_name'], (int)$_POST['work_percent'], (int)$_POST['material_percent'], $_POST['personal_id']);

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="certificate.php?id=1" class="ahref">Категория процентов</a> добавлена.</div>'));
                }
            }
        }
    }
?>