<?php 

//fl_sclad_cat_item_edit_f.php
//Функция для изменения имени категории

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['name']) || !isset($_POST['id']) || !isset($_POST['type']) || !isset($_POST['item_units_val'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                if ($_POST['type'] == 'category') {
                    $query = "UPDATE `spr_sclad_category` SET `name` = '{$_POST['name']}' WHERE `id` ='{$_POST['id']}'";
                }else{
                    $query = "UPDATE `spr_sclad_items` SET `name` = '{$_POST['name']}', `unit`='{$_POST['item_units_val']}' WHERE `id` ='{$_POST['id']}'";
                }



                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен');

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>