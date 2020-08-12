<?php

//fl_money_from_outside_add_f.php
//Функция добавления прихода извне в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
//            include_once 'DBWork.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            include_once 'ffun.php';

            if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['summ']) || !isset($_POST['filial_id']) || !isset($_POST['cat_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $time = date('Y-m-d H:i:s', time());
                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                //$msql_cnnct = ConnectToDB ();

                $descr = addslashes($_POST['descr']);

                $db = new DB();

                //Вставить запись в БД:
                $query = "INSERT INTO `journal_material_costs_test` (
                `month`,
                `year`,
                `filial_id`,
                `category_id`,
                `summ`,
                `description`, 
                `create_time`, 
                `create_person`
                )
                VALUES (
                :month,
                :year,
                :filial_id,
                :category_id,
                :summ,
                :description,
                :create_time,
                :create_person
                )";

                $args = [
                'month' => $_POST['month'],
                'year' => $_POST['year'],
                'filial_id' => $_POST['filial_id'],
                'category_id' => $_POST['cat_id'],
                'summ' => $_POST['summ'],
                'description' => $descr,
                'create_time' => $time,
                'create_person' => $_SESSION['id']
                ];
                $db::sql($query, $args);

                /*$query = "INSERT INTO `fl_journal_money_from_outside` (`month`, `year`, `filial_id`, `summ`, `descr`, `create_time`, `create_person`)
                        VALUES (
                        '{$_POST['month']}', '{$_POST['year']}', '{$_POST['filial_id']}', '".round($_POST['summ'], 2)."', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}');";*/

                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success'));

            }
        }
    }
?>