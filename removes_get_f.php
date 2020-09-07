<?php

//removes_get_f.php
//Функция для выдачи направлений

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

            $removesMy = 0;
            $removesMe = 0;

//            $rezult = '<a href="" class="b">Подробно</a>';
            $rezult = '';

            if (!isset($_POST['worker_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $msql_cnnct = ConnectToDB ();
                $removesMy = array();
                $removesMe = array();
                $number = 0;
                $number2 = 0;

//                if (($_SESSION['id'] == $_POST['worker_id']) && !$god_mode && ($stom['see_all'] != 1)){
                    //Перенаправления мои
                    //$removesMy = SelDataFromDB ('removes', $_SESSION['id'], 'create_person');

                    if ($_POST['worker_id'] == 0){

                        $query = "SELECT r.*, s_c.name, s_w.name AS w_name FROM `removes` r 
                        RIGHT JOIN `spr_clients` s_c 
                        ON s_c.id = r.client  
                        RIGHT JOIN `spr_workers` s_w
                        ON s_w.id = r.create_person 
                        WHERE r.closed <> 1 
                        ORDER BY r.create_time DESC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                    }else {

                        $query = "SELECT r.*, s_c.name, s_w.name AS w_name FROM `removes` r 
                        RIGHT JOIN `spr_clients` s_c 
                        ON s_c.id = r.client  
                        RIGHT JOIN `spr_workers` s_w
                        ON s_w.id = r.create_person 
                        WHERE r.create_person='" . $_SESSION['id'] . "' AND r.closed <> 1 
                        ORDER BY r.create_time DESC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        //... Ко мне
                        //$removesMe = SelDataFromDB ('removes', $_SESSION['id'], 'whom');

                        $query = "SELECT r.*, s_c.name, s_w.name AS w_name FROM `removes` r 
                        RIGHT JOIN `spr_clients` s_c 
                        ON s_c.id = r.client  
                        RIGHT JOIN `spr_workers` s_w
                        ON s_w.id = r.create_person 
                        WHERE r.whom='" . $_SESSION['id'] . "' AND r.closed <> 1 
                        ORDER BY r.create_time DESC";

                        $res2 = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number2 = mysqli_num_rows($res2);

                    }

//                }else{
//                    if (($stom['see_all'] == 1) || $god_mode){
//                        //Перенаправления мои
//                        //$removesMy = SelDataFromDB ('removes',  $_POST['worker_id'], 'create_person');
//                        //... Ко мне
//                        //$removesMe = SelDataFromDB ('removes',  $_POST['worker_id'], 'whom');
//
//                        //... Все незакрытые
//                        $query = "SELECT * FROM `removes` WHERE `closed` <> 1 ORDER BY `create_time` DESC";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                        $number = mysqli_num_rows($res);
//
//                    }
//                }

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($removesMy, $arr);
                    }
                }


                if ($_POST['worker_id'] != 0) {
                    if ($number2 != 0) {
                        while ($arr = mysqli_fetch_assoc($res2)) {
                            array_push($removesMe, $arr);
                        }
                    }
                }

                if (empty($removesMy) && empty($removesMe)){
                    $rezult .= '<br><br><div style="display: inline-block; color: red;"><i>Открытых направлений нет.</i></div>';
                }else {

                    if (!empty($removesMy)) {
                        $rezult .= WriteRemoves($removesMy, $_POST['worker_id'], false, true, $finances);
                    }

                    if (!empty($removesMe)) {
                        $rezult .= WriteRemoves($removesMe, $_POST['worker_id'], true, true, $finances);
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => 'Ошибка #13'));
        }
    }
?>