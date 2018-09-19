<?php

//notes_get_f.php
//Функция для выдачи напоминаний

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';
            require 'variables.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            $removesMy = 0;
            $removesMe = 0;

            $rezult = '<a href="" class="b">Подробно</a>';

            if (!isset($_POST['worker_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $msql_cnnct = ConnectToDB ();
                $notes = array();
                $number = 0;

                if (($stom['see_own'] == 1) && ($stom['see_all'] != 1) && !$god_mode){
                    //$notes = SelDataFromDB ('notes', $_SESSION['id'], 'create_person');
                    $query = "SELECT * FROM `notes` WHERE `create_person`='".$_SESSION['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                }else{
                    if (($stom['see_all'] == 1) || $god_mode){
                        //$notes = SelDataFromDB ('notes', 'dead_line', 'dead_line');
                        $query = "SELECT * FROM `notes` WHERE (`dead_line` < '".time()."' OR `dead_line` = '".time()."') AND `closed` <> 1 ORDER BY `dead_line` DESC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                    }
                }

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($notes, $arr);
                    }
                }

                if (!empty($notes)){


                    //if ($stom['see_own'] == 1){
                        //$rezult .= 'Мои напоминания';
                    //}elseif (($stom['see_all'] == 1) || $god_mode){
                        //$rezult .= 'Все просроченные незакрытые напоминания';
                    //}

                    $rezult .= WriteNotes($notes, $_POST['worker_id'], true);

                }else{
                    $rezult .= '<br><br><div style="display: inline-block; color: red;"><i>Открытых напоминаний нет.</i></div>';
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>