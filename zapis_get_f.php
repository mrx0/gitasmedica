<?php

//zapis_get_f.php
//Функция для выдачи записи в карточку пациента

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

            if (!isset($_POST['client_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $rezult = '';

                $sheduler_zapis = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `zapis` WHERE `patient`='".$_POST['client_id']."' ORDER BY `year`, `month`, `day`, `start_time` ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($sheduler_zapis, $arr);
                    }
                }

                if (!empty($sheduler_zapis)) {

                    $rezult .= '
                        <div style="margin: 10px 0;">
                            <ul style="margin-left: 6px; margin-bottom: 20px;">';

                    $sheduler_zapis = array_reverse($sheduler_zapis);

                    // !!! **** тест с записью
                    include_once 'showZapisRezult.php';

                    $edit_options = false;
                    $upr_edit = false;
                    $admin_edit = false;
                    $stom_edit = false;
                    $cosm_edit = false;
                    $finance_edit = false;

                    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
                        $finance_edit = true;
                        $edit_options = true;
                    }

                    if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode){
                        $stom_edit = true;
                        $edit_options = true;
                    }
                    if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode){
                        $cosm_edit = true;
                        $edit_options = true;
                    }

                    if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                        $admin_edit = true;
                        $edit_options = true;
                    }

                    if (($scheduler['see_all'] == 1) || $god_mode){
                        $upr_edit = true;
                        $edit_options = true;
                    }

                    $rezult .= showZapisRezult($sheduler_zapis, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, true);


                }else{
                    $rezult .= '
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                    <span style="color: rgb(255, 30, 30);"><i>Нет записи</i></span>
                                </li>';
                }

                $rezult .= '
                            </ul>
                        </div>';

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }
?>