<?php

//fl_mainReportAverage_getData_f.php
//Функция получения данных для отчета подсчета среднего за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {
            if (!isset($_POST['date']) || !isset($_POST['filial_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {
                include_once 'fl_main_report2_f.php';

                //Пробуем получить данные (тест)
                $datas = fl_main_report2_f(explode('.', $_POST['date'])[0], explode('.', $_POST['date'])[1], $_POST['filial_id']);

                $rezult_arr = $datas['rezult_arr'];
                $cashbox_nal = $datas['cashbox_nal'];
                $arenda = $datas['arenda'];
                $beznal = $datas['beznal'];
                $giveoutcash_summ = $datas['giveoutcash_summ'];
                $subtractions_j = $datas['subtractions_j'];
                $subtractions_summ = $datas['subtractions_summ'];
                $paidouts_temp_j = $datas['paidouts_temp_j'];
                $paidouts_temp_summ = $datas['paidouts_temp_summ'];
                $giveoutcash_ex_j = $datas['giveoutcash_ex_j'];
                $bank_summ = $datas['bank_summ'];
                $director_summ = $datas['director_summ'];
                $temp_solar_beznal = $datas['temp_solar_beznal'];
                $temp_solar_nal = $datas['temp_solar_nal'];
                $percents_j = $datas['percents_j'];
                $giveoutcash_j = $datas['giveoutcash_j'];
                $give_out_cash_types_j = $datas['give_out_cash_types_j'];
                $prev_month_filial_summ_arr = $datas['prev_month_filial_summ_arr'];
                $zapis_j = $datas['zapis_j'];
                $pervich_summ_arr_new = $datas['pervich_summ_arr_new'];


                $permission_summs = array();

                foreach ($subtractions_j as $permissions => $subtractions_data) {
                    //var_dump($subtractions_data);
                    //var_dump($permissions);

                    foreach ($subtractions_data as $worker_id => $worker_data) {
                        //var_dump($worker_data);

                        //Массив для распределения сумм по типам
                        //$temp_summ_arr = array(1 => 0, 7 => 0, 2 => 0, 3 => 0, 4 => 0);

                        foreach ($worker_data['data'] as $type => $type_data) {

                            foreach ($type_data as $data) {
                                //var_dump($data);
                                if (!isset($permission_summs[$permissions])){
                                    $permission_summs[$permissions] = 0;
                                }

                                $permission_summs[$permissions] += $data['summ'];

                            }
                        }
                        //var_dump($temp_summ_arr);
                    }
                }
                //var_dump($permission_summ);

                //Сумма по страховым
                $insure_summ = 0;

                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['insure_data'])) {
                        $insure_summ = array_sum($rezult_arr[5]['insure_data']);
                    }
                }

                echo json_encode(array('result' => 'success',
                    'month' => explode('.', $_POST['date'])[0],
                    'year' => explode('.', $_POST['date'])[1],
                    'bank_summ' => $bank_summ,
                    'director_summ' => $director_summ,
                    'nal' => $cashbox_nal,
                    'beznal' => $beznal,
                    'arenda' => $arenda,
                    'permission_summs' => $permission_summs,
                    'giveoutcash_j' => $giveoutcash_j,
                    'insure_summ' => $insure_summ,
                ));
            }
        }
    }

?>