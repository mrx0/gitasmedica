<?php

//fl_mainReportAverage_getDates_f.php
//Отдельная функция, которая вернёт данные по датам для отчета и отрисует табличку

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {
            if (!isset($_POST['month_start']) || !isset($_POST['year_start']) || !isset($_POST['month_end']) || !isset($_POST['year_end']) || !isset($_POST['filial_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once 'DBWork.php';
                include_once 'functions.php';
                include_once 'ffun.php';
                require 'variables.php';

                $filials_j = getAllFilials(false, false, false);

                //операции со временем
                $month_start = $_POST['month_start'];
                $year_start = $_POST['year_start'];
                $month_end = $_POST['month_end'];
                $year_end = $_POST['year_end'];

                //Филиал
                $filial_id = $_POST['filial_id'];

                //Даты словами для ответа
                $date_start_name = $monthsName[$month_start] . ' ' . $year_start;
                $date_end_name = $monthsName[$month_end] . ' ' . $year_end;
                $filial_name = $filials_j[$filial_id]['name'];

                //Временная переменная со строками
                $str_arr_temp = array(
                    "allMoney" => "Общ.выручка",
                    "arenda" => "Аренда",
                    "nal" => "Нал",
                    "beznal" => "Безнал (+страх)",
                    "zpStom" => "з/п Стом.",
                    "zpCosm" => "з/п Космет.",
                    "zpSomat" => "з/п Cомат.",
                    "zpAssist" => "з/п Ассист.",
                    "zpAdm" => "з/п Админы",
                    "zpSanitUborDvor" => "з/п Сан./Убор./Двор.",
                    "zpZavh" => "з/п Завхозы",
                    "zpPom" => "з/п Помощницы",
                    "remont" => "Ремонт",
                    "bank" => "Банк",
                    "director" => "А.Н.",
                );

                //Временная переменная с месяцами
                $month_arr_temp = array();

                //Работа с датами, вывод таблички
                $date_start = $year_start . '-' . $month_start . '-01';
                $date_end = $year_end . '-' . $month_end . '-28';

                $date = explode('-', $date_start);

                $year_start = $date[0];
                $month_start = $date[1];

                $date = explode('-', $date_end);

                $year_end = $date[0];
                $month_end = $date[1];

                for ($cur_year = $year_start; $cur_year <= $year_end; $cur_year++) {
                    if ($cur_year == $year_end)
                        $max_month = $month_end;
                    else
                        $max_month = 12;
                    //
                    if ($cur_year == $year_start)
                        $cur_month = $month_start;
                    else
                        $cur_month = 1;
                    for ($cur_month; $cur_month <= $max_month; $cur_month++) {
                        //print dateTransformation($cur_month).'.'.$cur_year.'<br />';
                        array_push($month_arr_temp, dateTransformation($cur_month) . '.' . $cur_year);
                    }
                }
                //var_dump($month_arr_temp);

                //Переменная, сюда пишем табличку
                $res_str = '';

                if (!empty($month_arr_temp)) {

                    $res_str .= '
                            <table style="border: 1px solid #BFBCB5; background: #fff;">';
                    $res_str .= '
                                <tr>';
                    $res_str .= '
                                    <td style="text-align: center; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><span id="filial_name2"></span></td>';
                    foreach ($month_arr_temp as $my_date) {

                        $res_str .= '
                                    <td style="text-align: center; font-size: 70%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;"><div class="need_date" need_date="'.explode('.', $my_date)[0].'_'.explode('.', $my_date)[1].'">' . $monthsName[explode('.', $my_date)[0]] . '</div><div>' . explode('.', $my_date)[1] . '<div></td>';
                    }

                    $res_str .= '
                                    <td style="text-align: center; font-size: 70%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Итого</td>';
                    $res_str .= '
                                    <td style="text-align: center; font-size: 70%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">Среднее</td>';
                    $res_str .= '
                                </tr>';

                    foreach ($str_arr_temp as $marker => $str) {
                        $res_str .= '
                                <tr>';
                        $res_str .= '
                                    <td style="text-align: left; font-size: 90%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">' . $str . '</td>';

                        foreach ($month_arr_temp as $my_date) {

                            $res_str .= '
                                    <td class="'.$marker.'_'.explode('.', $my_date)[0].'_'.explode('.', $my_date)[1].'" style="text-align: right; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">0</td>';
                        }

                        $res_str .= '
                                    <td class="'.$marker.'_summ" style="text-align: right; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">0</td>';
                        $res_str .= '
                                    <td class="'.$marker.'_average" style="text-align: right; font-size: 80%; padding: 5px; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5;">0</td>';


                        $res_str .= '
                                </tr>';

                    }
                }
                echo json_encode(array('result' => 'success', 'res_str' => $res_str, 'date_start_name' => $date_start_name, 'date_end_name' => $date_end_name, 'months_arr' => $month_arr_temp, 'filial_name' => $filial_name));
            }
        }
    }

?>