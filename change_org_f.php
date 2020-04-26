<?php

//change_org_f.php
//Получаем реквизиты организации

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['org_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $org_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `spr_org` 
                WHERE `id` = '{$_POST['org_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    $org_j = mysqli_fetch_assoc($res);
                }

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $org_j));
            }
        }
    }

?>