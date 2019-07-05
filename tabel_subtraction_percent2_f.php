<?php
	
//tabel_subtraction_percent2.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        require 'variables.php';

        //ID табеля
        $tabel_id = $_POST['tabel_id'];
        //Сумма, которую хотим выдать сейчас (аванс, зп... не важно)
        $iWantMyMoney = $_POST['summ'];

        $tabel_j = array();

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();

        //Наряды с данными
        $invoices_j = array();
        //Позиции в нарядах
        $invoices_j_ex = array();
        //ID нарядов
        $invoices_ids_arr = array();
        //Оплаты
        $payments_j = array();
        //Итоговый массив, куда соберем общие суммы по филиалам
        $itog_filials_summ = array();

        //Позиции, которые прошли как подарки пациентам,
        //но нам же надо дать ЗП за них
        //НЕ разбиваем по филиалам
        //Филиал мы присвоим от филиала наряда, в котором был подарок
        $gift_invoice_ex = array();
        //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
        //просто чтоб отследить отдельно эту сумму
        $gift_invoice_summ = 0;
        //Страховые позиции в наряде
        //возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
        $filial_insure_invoice_ex = array();
        //Итоговый массив, куда соберем суммы по филиалам по тем работам,
        // которые мы хотим и должны оплатить другому человеку
        $itog_filials_summ_not4tou = array();
        //Сумма со всех филиалов
        $itog_all_filial_summ = 0;
        //Массив с процентами по филиалам
        $itog_filials_percents = array();
        //Массив, где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP = array();
        //Массив, где ключ - это ID филиала, а значение - это сколько всего УЖЕ было выдано денег с каких филиалов
        $summ4ZP_prev = array();
        //Сумма ЗП, которую мы могли бы выдать сейчас всю, как будто еще ничего не выплачивали
        $summ4ZP_All = 0;
        //Массив, где ключ - это ID филиала, а значение - это сколько с какого филиала ПРЕДЛАГАЕТСЯ вычесть сумму на выдачу ЗП
        $filial_subtraction = array();


        //Получаем табель
        //!!!по сути нам это надо только для того, чтоб получить id worker'a
        //!!!в будущем надо убрать и получать id через POST, как и id табеля
        $query = "SELECT * FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $tabel_j = mysqli_fetch_assoc($res);

            $worker_id = $tabel_j['worker_id'];
            //Обозначим общую сумму к выплате. Собираем её из всех начислений (сумма РЛ, отпуск, больничный, премия...)
            //Если доктор (стом, косм,...)
            if (($tabel_j['type'] == 5) || ($tabel_j['type'] == 6)) {
                //$summ4ZP_All = intval($tabel_j['summ_calc'] + $tabel_j['surcharge']);
                $summ4ZP_All = $tabel_j['summ_calc'] + $tabel_j['surcharge'];
            }

        }
        //var_dump($tabel_j);
        //var_dump($summ4ZP_All);


        if (!empty($tabel_j)) {
            //Наряды с позициями в нарядах + статус (открыт/закрыт) наряда, + филиал
            $query = "
                SELECT ji_ex.*, ji.office_id AS filial_id, ji.status AS status
                FROM `fl_journal_calculate` jcalc
                LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }' AND jtabex.noch = '0'
                LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
                RIGHT JOIN `journal_invoice_ex` ji_ex ON ji_ex.invoice_id = ji.id  
                WHERE jtabex.calculate_id = jcalc.id
                ORDER BY `ji_ex`.`invoice_id` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    array_push($invoices_j_ex, $arr);

                    array_push($invoices_ids_arr, "`invoice_id`='" . $arr['invoice_id'] . "'");
                }
            }
            //var_dump($invoices_j_ex);.
            //var_dump($invoices_ids_arr);

            //Оставим только уникальные ID
            $invoices_ids_arr = array_unique($invoices_ids_arr);
            //var_dump($invoices_ids_arr);

            //Строчка для следующего запроса
            $invoices_ids_str = implode(' OR ', $invoices_ids_arr);

            //Получаем все оплаты по всем нарядам
            $query = "
                SELECT *
                FROM `journal_payment`
                WHERE ({$invoices_ids_str})";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    array_push($payments_j, $arr);

                    //Раскидаем суммы оплат сразу по филиалам
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$arr['filial_id']])) {
                        $itog_filials_summ[$arr['filial_id']] = 0;
                    }
                    $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

                }
            }
            //var_dump($payments_j);
            //echo '<span style="font-size: 85%;">Сколько куда принесли денег</span>';
            //var_dump($itog_filials_summ);

            //    var_dump($invoices_ids_str);
            //    var_dump(str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str));

            //Получаем позиции, которые прошли как подарки пациентам
            $query = "
                SELECT ji_ex.*, ji.office_id
                FROM `journal_invoice_ex` ji_ex
                LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
                WHERE (" . str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str) . ")
                AND ji_ex.gift = '1'";
            //echo ($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    //var_dump($arr);

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$arr['office_id']])) {
                        $itog_filials_summ[$arr['office_id']] = 0;
                    }
                    $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

                    //Просто чтоб отследить отдельно эту сумму
                    $gift_invoice_summ += $arr['itog_price'];
                }
            }
            //var_dump($itog_filials_summ);


            //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
            //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
            $query = "
                SELECT ji_ex.*, ji.office_id
                FROM `journal_invoice_ex` ji_ex
                LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
                WHERE (" . str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str) . ")
                AND ji_ex.insure <> '0' AND ji_ex.insure_approve = '1'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            //Если что-то нашли страхового
            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$arr['office_id']])) {
                        $itog_filials_summ[$$arr['office_id']] = 0;
                    }
                    $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

                }
            }
            //var_dump($itog_filials_summ);

            //А теперь выберем РЛ, которые были не этому исполнителю.
            //Ибо может быть так, что наряд один, а исполнителей больше.
            //Выберем их и вычтем суммы из общих
            //... а может потом и не придется, заставим админов делать отдельные наряды
            $query = "
              SELECT * 
              FROM `fl_journal_calculate` 
              WHERE ({$invoices_ids_str}) 
              AND `worker_id` <> '{$worker_id}'";
            //echo($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            //Если нашли РЛ, которые не принадлежат указанному в табеле исполнителю
            //... и тут на самом деле жопа, потому что денег могли принести в одном, а работу сделают на другом
            //... и ппц
            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    //... Тут у нас опять филиал берётся по наряду, что наверное не есть хорошо,
                    //... но пока так
                    if (!isset($itog_filials_summ_not4tou[$arr['office_id']])) {
                        $itog_filials_summ_not4tou[$arr['office_id']] = 0;
                    }
                    $itog_filials_summ_not4tou[$arr['office_id']] += $arr['summ_inv'];
                }
            }
            //var_dump($itog_filials_summ_not4tou);

            //Вычтем с филиалов суммы, которые уйдут в зп другому человеку (ассистент например)
            //!!!перенести это потом в цикл выше, ибо зачем лишнее вот это вот всё
            foreach ($itog_filials_summ_not4tou as $filial_id => $summ) {
                if (isset($itog_filials_summ[$filial_id])) {
                    $itog_filials_summ[$filial_id] -= $summ;
                }
            }
            //echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
            //var_dump($itog_filials_summ);

            //Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
            $itog_all_filial_summ = array_sum($itog_filials_summ);


            //Вычислим процентное соотношение
            foreach ($itog_filials_summ as $filial_id => $summ) {

                $percent_value = 0;

                //предварительно добавляем в массив элемент с ID филиала, если его не было
                //!!! потом сделать это выше, когда суммы собираем
                if (!isset($itog_filials_percents[$filial_id])) {
                    $itog_filials_percents[$filial_id] = 0;
                }

                $percent_value = (100 * $summ) / $itog_all_filial_summ;

                $itog_filials_percents[$filial_id] = $percent_value;
            }
            //var_dump($itog_filials_percents);
            //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
            //var_dump(array_sum($itog_filials_percents));



            //Посчитаем по сколько могли бы выдать с каждого филиала на текущий момент, если бы выдавали со всей суммы
            //пропорционально полученным деньгам
            foreach ($itog_filials_percents as $filial_id => $percent) {
                $summ4ZP[$filial_id] = $summ4ZP_All / 100 * $percent;
            }
            //echo '<span style="font-size: 85%;"><b>Ключевое1 !</b> Сколько ВСЕГО БУДЕТ в итоге выдано с каждого филиала из общего объема денег</span>';
            //var_dump($summ4ZP);
            //Просто для самоконтроля
            //var_dump(array_sum($summ4ZP));


            //!!!Временно введём данные, будто мы уже выдавали аванс
            //Потом тут надо будет сделать получение этих данных из БД
//        $summ4ZP_prev = array(
//            19 => 9091,
//            16 => 909
//        );

            //var_dump($summ4ZP_prev);

            //Если ранее были выплаты по этому табелю, то вычтем эти суммы из итоговых остатков,
            //доступных к выдаче денег
            if (!empty($summ4ZP_prev)) {
                foreach ($summ4ZP_prev as $filial_id => $summ) {
                    if (isset($summ4ZP[$filial_id])) {
                        $summ4ZP[$filial_id] -= $summ;
                    }
                }
            }
            //echo '<span style="font-size: 85%;"><b>Ключевое2 !</b> Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег. ПОСЛЕ вычета того, что уже с этих филиалов вычли</span>';
            //var_dump($summ4ZP);
            //Просто для самоконтроля
            //var_dump(array_sum($summ4ZP));


            //Посчитаем, сколько откуда реально выдадим с учетом суммы,
            //которую реально хотим выдать $iWantMyMoney
            foreach ($itog_filials_percents as $filial_id => $percent) {
                if (!isset($filial_subtraction[$filial_id])) {
                    $filial_subtraction[$filial_id] = 0;
                }
                $filial_subtraction[$filial_id] = $summ4ZP[$filial_id] / 100 * ( $iWantMyMoney * 100 / array_sum($summ4ZP) );
            }
            echo '<span style="font-size: 85%;"><b>Ключевое3 !</b> Сколько ПРЕДЛАГАЕТСЯ ВСЕГО выдать с какого филиала в ЭТОТ раз</span>';
            //var_dump($filial_subtraction);

            //Просто для самоконтроля, должна получиться общая сумма выдаче
            //var_dump(array_sum($filial_subtraction));
            //var_dump(array_sum($filial_subtraction) + array_sum($summ4ZP_prev));


            echo '<table>';


            foreach ($filials_j as $f_id => $filials_j_data) {
                echo '<tr>';
                //Значение, сколько с какого филиала будем выдавать
                $value = 0;
                //Если мы посчитали, что с этого филиала будем выдавать столько, то пожалуйста
                if (isset($filial_subtraction[$f_id])){
                    $value = intval($filial_subtraction[$f_id]);
                }
                echo '<td>'.$filials_j_data['name2'].'</td>';
                echo '<td><input type="text" size="20" name="" placeholder="" autocomplete="off" value="'.$value.'"></td>';
                echo '<tr>';
            }


            echo '</table>';


        }
    }

}

?>