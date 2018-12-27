<?php

//fl_prikazNomerVosem.php
//Приказ №8

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['worker_id']) || !isset($_POST['tabel_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                //var_dump ($_POST);

                $tabel_ex_calculates_j = array();
                $rez = array();

                $month = 0;
                $year = 0;

                $msql_cnnct = ConnectToDB();

                //Получим месяц и год табеля, из которого инициировала приказ номер 8
                $query = "SELECT `month`, `year` FROM `fl_journal_tabels` WHERE `id`='{$_POST['tabel_id']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $month = $arr['month'];
                        $year = $arr['year'];
                    }
                }else{

                }
                //var_dump ($month);
                //var_dump ($year);

                //Получение данных только по указанному табелю
                $query = "
                      SELECT * FROM `fl_journal_calculate_ex` jcalcex WHERE jcalcex.calculate_id IN (
                      SELECT jcalc.id FROM `fl_journal_calculate` jcalc
                      LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '".$_POST['tabel_id']."'
                      WHERE jtabex.calculate_id = jcalc.id)";

                //Если все хорошо и нащли нужные данные
                //if (($month != 0) && ($year != 0)){


                    //Получение данных по всем табелям сотрудника со всех филиалов месяца и года,
                    //указанного в табеле, из которого инициировали
                    /*$query = "
                      SELECT * FROM `fl_journal_calculate_ex` jcalcex WHERE jcalcex.calculate_id IN (
                        SELECT jcalc.id FROM `fl_journal_calculate` jcalc 
                        LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.calculate_id = jcalc.id WHERE jtabex.tabel_id IN (
                          SELECT jtab.id FROM `fl_journal_tabels` jtab WHERE jtab.worker_id='{$_POST['worker_id']}' AND jtab.month='{$month}' AND jtab.year='{$year}'
                        )
                      )";*/

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            //$tabel_ex_calculates_j[$arr['id']] = $arr;
                            array_push($tabel_ex_calculates_j, $arr);
                        }
                    }else{

                    }
                    //var_dump ($tabel_ex_calculates_j);

                    //Категории по которым идёт контроль %
                    //Сейчас тут Диод и Александрит
                    $controlCategories = array(9, 11);
                    //Категории, которые надо вычесть перед контролем
                    $controlCategoriesMinus = array(15, 14);

                    $controlCategoriesSumm = 0;
                    //Не используем пока, ибо не надо
                    $controlCategoriesMinusSumm = 0;
                    $allSumm = 0;

                    foreach ($tabel_ex_calculates_j as $item){
                        if (in_array((int)$item['percent_cats'], $controlCategories)) {
                            $controlCategoriesSumm += (int)$item['price'];
                        }

                        if (!in_array((int)$item['percent_cats'], $controlCategoriesMinus)) {
                            $allSumm += (int)$item['price'];
                        }
                    }

                    //var_dump($controlCategoriesSumm);
                    //var_dump($allSumm);

                    //Вычисляем процент от общего %

                    $controlPercent = $controlCategoriesSumm * 100 / $allSumm;
                    //var_dump($controlPercent);
                    //Тестовая проверка
                    //$controlPercent = 0;
                    //var_dump($controlPercent);
                    //var_dump(number_format($controlPercent, 2, ',', ''));



                    //Премиальные %
                    $newPaymentPercent = 0;
                    //$newPaymentSumm = 0;

                    //Вычисляем % премии
                    if ($controlPercent < 40){
                        $newPaymentPercent = 25;
                    }elseif(($controlPercent >= 40) && ($controlPercent < 60)){
                        $newPaymentPercent = 20;
                    }elseif(($controlPercent >= 60) && ($controlPercent < 70)){
                        $newPaymentPercent = 15;
                    }elseif($controlPercent >= 70){
                        $newPaymentPercent = 10;
                    }
                    //var_dump($newPaymentPercent);

                    //Вычисляем сумму премии (убрал это 2018-09-18, так как изменились вводные.
                    //Меняться будет процент за работу и надо будет просто пересчитать РЛы)
                    //$bonusPaymentSumm = $allSumm / 100 * $newPaymentPercent;
                    //var_dump($bonusPaymentSumm);

                    echo json_encode(array('result' => 'success', 'allSumm' => $allSumm, 'controlCategoriesSumm' => $controlCategoriesSumm, 'controlPercent' => number_format($controlPercent, 2, ',', ''), 'newPaymentPercent' => $newPaymentPercent, 'controlCategories' => $controlCategories));
                //}


            }
        }
    }
?>