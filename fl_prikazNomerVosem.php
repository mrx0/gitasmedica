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

                $msql_cnnct = ConnectToDB();

                //$query = "SELECT jcalc.* FROM `fl_journal_calculate` jcalc WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.office_id='{$_POST['office']}' AND jcalc.status <> '7' AND jcalc.id NOT IN (SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id) AND jcalc.date_in > '2018-05-31';";

                //Получение данных пока только по указанному табелю
                $query = "
SELECT * FROM `fl_journal_calculate_ex` jcalcex WHERE jcalcex.calculate_id IN (
SELECT jcalc.id FROM `fl_journal_calculate` jcalc LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '".$_POST['tabel_id']."' WHERE jtabex.calculate_id = jcalc.id)";

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

                $controlCategoriesSumm = 0;
                $allSumm = 0;

                foreach ($tabel_ex_calculates_j as $item){
                    if (in_array((int)$item['percent_cats'], $controlCategories)) {
                        $controlCategoriesSumm += (int)$item['price'];
                    }
                    $allSumm += (int)$item['price'];
                }

                var_dump($controlCategoriesSumm);
                var_dump($allSumm);

                //Вычисляем % от общего

                $controlPercent = $controlCategoriesSumm * 100 / $allSumm;
                var_dump($controlPercent);
                //Тестовая проверка
                //$controlPercent = 0;
                var_dump($controlPercent);
                var_dump(number_format($controlPercent, 2, ',', ''));



                //Премиальные выплаты
                $bonusPaymentPercent = 0;
                $bonusPaymentSumm = 0;

                //Вычисляем % премии
                if ($controlPercent <= 39){
                    $bonusPaymentPercent = 25;
                }elseif(($controlPercent >= 40) && ($controlPercent <= 59)){
                    $bonusPaymentPercent = 20;
                }elseif(($controlPercent >= 60) && ($controlPercent <= 69)){
                    $bonusPaymentPercent = 15;
                }elseif($controlPercent >= 70){
                    $bonusPaymentPercent = 10;
                }
                var_dump($bonusPaymentPercent);

                //Вычисляем сумму премии
                $bonusPaymentSumm = $allSumm / 100 * $bonusPaymentPercent;
                var_dump($bonusPaymentSumm);

                //echo json_encode(array('result' => 'success', 'data' => $query));
            }
        }
    }
?>