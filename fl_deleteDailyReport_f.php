<?php

//fl_deleteDailyReport_f.php
//Функция удаления (блокировки) ежедневного отчета администратора

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {

            if (!isset($_POST['report_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once 'DBWork.php';
                include_once 'functions.php';

                include_once 'ffun.php';

                //require 'variables.php';

                //разбираемся с правами
                $god_mode = FALSE;

                require_once 'permissions.php';


                $report_j = SelDataFromDB('fl_journal_daily_report', $_POST['report_id'], 'id');

                if ($report_j != 0) {

                    $can_delete = false;
                    $data = 'Ошибка #19';

                    //Смотрим даты и права на всякие действия и на посмотреть, если заднее число
                    if ($report_j[0]['day'].'.'.$report_j[0]['month'].'.'.$report_j[0]['year'] == date('d.n.Y', time())) {
                        $can_delete = true;
                        $data = 'Ok';
                    }else{
                        //Если есть права
                        if (($finances['see_all'] == 1) || $god_mode) {
                            //Если отчет проверили уже
                            if ($report_j[0]['status'] == 7){
                                $can_delete = false;
                                $data = 'Отчёт уже проверен, удалить невозможно.';
                            }else{
                                $can_delete = true;
                                $data = 'Ok';
                            }
                        }else{
                            //Если нет прав
                            $can_delete = false;
                            $data = 'Не хватает прав для этого действия';

                        }
                    }

                    if ($can_delete) {

                        $msql_cnnct = ConnectToDB2();

                        //Обновляем
                        $query = "DELETE FROM `fl_journal_daily_report` WHERE `id`='{$_POST['report_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        CloseDB($msql_cnnct);

                        echo json_encode(array('result' => 'success', 'data' => $data));

                    }else{

                        echo json_encode(array('result' => 'error', 'data' => $data));
                    }

                }else{
                    echo json_encode(array('result' => 'error', 'data' => 'Ошибка #18. Нет такого отчёта.'));
                }
            }
        }
    }

?>