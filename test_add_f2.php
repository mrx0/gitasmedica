<?php

//test_add_f.php
//Добавление задания

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);

    if ($_POST){

        if (!isset($_POST['descr']) || !isset($_POST['workers']) || !isset($_POST['workers_type']) || !isset($_POST['filial'])){
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
        }else{

            $time = date('Y-m-d H:i:s', time());

            $theme = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['theme'])))));
            $descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
            $workers = $_POST['workers'];
            $workers_type = $_POST['workers_type'];
            $filials = $_POST['filial'];

            //Проверки, проверочки
            include_once 'DBWork.php';

            $msql_cnnct = ConnectToDB ();

            if ($comment != ''){

                $query = "INSERT INTO `journal_announcing` (`text`, `theme`, `type`, `create_time`, `create_person`) 
				VALUES (
				'{$comment}', '{$theme}', '{$announcing_type}', '{$time}', '{$_SESSION['id']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //ID новой позиции
                $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                if (!empty($filials)){
                    foreach ($filials as $filial_id){
                        $query = "INSERT INTO `journal_announcing_filial` (`annoncing_id`, `filial_id`) 
                        VALUES (
                        '{$mysql_insert_id}', '{$filial_id}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    }
                }

                if (!empty($workers_type)){
                    foreach ($workers_type as $workers_type_id){
                        $query = "INSERT INTO `journal_announcing_worker` (`annoncing_id`, `worker_type`) 
                        VALUES (
                        '{$mysql_insert_id}', '{$workers_type_id}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Объявление добавлено.</div>'));
            }
        }
    }
}
?>