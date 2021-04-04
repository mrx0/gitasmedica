<?php

//fl_normahours_edit_f.php
//Функция редактирования нормы часов

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            if (!isset($_POST['norma_id']) || !isset($_POST['count'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                //Обновляем категорию процентов в базу
                $query = "UPDATE `fl_spr_normahours` 
                SET `count`='{$_POST['count']}'
                WHERE `id`='{$_POST['norma_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Готово</div>'));
            }
        }
    }
?>