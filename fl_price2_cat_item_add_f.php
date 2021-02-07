<?php 

//fl_price2_cat_item_add_f.php
//Функция для добавления категории или позиции в новый прайс 2021

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['name']) || !isset($_POST['type']) || !isset($_POST['targetId']) || !isset($_POST['item_units_val'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                //Добавляем позицию
                if ($_POST['type'] == 'category'){
                    $query = "INSERT INTO `spr_price2_category` (`name`, `parent_id`)
                                VALUES (
                                '{$_POST['name']}', '{$_POST['targetId']}');";
                }else{
                    $query = "INSERT INTO `spr_price2_items` (`name`, `parent_id`, `unit`)
                                VALUES (
                                '{$_POST['name']}', '{$_POST['targetId']}', '{$_POST['item_units_val']}');";
                }



                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //Если это категория и мы сразу указали родителя, то надо родителю увеличить вес (node_count)
                if ($_POST['type'] == 'category'){
                    if ($_POST['targetId'] != 0){

                        $query = "UPDATE `spr_price2_category` SET `node_count`=`node_count`+1 WHERE `id`='{$_POST['targetId']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    }
                }
                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен');

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>