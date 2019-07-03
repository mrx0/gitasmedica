<?php
	
//test_tabel_paidout_percent3.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';
    include_once 'functions.php';

    require 'variables.php';

    //ID табеля
    $tabel_id = 1101;

    $tabel_j = array();

    $filials_j = getAllFilials(false, false, false);
    //var_dump($filials_j);

    $msql_cnnct = ConnectToDB ();

    //Наряды
    $invoices_j = array();
    //Позиции
    $invoices_j_ex = array();

    //Итоговый массив, куда соберем общие суммы по филиалам
    $itog_filials_summ = array();
    //Итоговый массив, куда соберем суммы по филиалам по тем работам,
    // которые мы хотим и должны оплатить другому человеку
    $itog_filials_summ_not4tou = array();
    //Массив с процентами по филиалам
    $itog_filials_percents = array();
    //Массив с процентами по филиалам TEMP
    $itog_filials_percents_temp = array();
    //Массив остатков денег после выдачи
    $itog_filials_summ_ostatok = array();
    //Наряды, которые на момент выдачи были не закрыты, но мы по ним делали РЛ и выдавали ЗП
    $opened_invoices = array();

    //Табель
    $query = "SELECT * FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        $arr = mysqli_fetch_assoc($res);

        $tabel_j = $arr;
    }
    //var_dump($tabel_j);

    $worker_id = $tabel_j['worker_id'];

    //Наряды
//    $query = "
//            SELECT jcalc.invoice_id, ji.office_id AS filial_id, ji.status AS status
//            FROM `fl_journal_calculate` jcalc
//            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }'
//            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//            WHERE jtabex.calculate_id = jcalc.id
//            GROUP BY jcalc.invoice_id";

    //Наряды с позициями в нарядах + статус (открыт/закрыт) наряда, + филиал
    $query = "
            SELECT ji_ex.*, ji.office_id AS filial_id, ji.status AS status
            FROM `fl_journal_calculate` jcalc
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }'
            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
            RIGHT JOIn `journal_invoice_ex` ji_ex ON ji_ex.invoice_id = ji.id  
            WHERE jtabex.calculate_id = jcalc.id
            ORDER BY `ji_ex`.`invoice_id` ASC";

    //var_dump($query);

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            $invoices_j[$arr['invoice_id']]['filial_id'] = $arr['filial_id'];
            $invoices_j[$arr['invoice_id']]['status'] = $arr['status'];

            $invoices_j_ex[$arr['id']] = $arr;

            //Собираем массив по нарядам, чтоб потом сформировать строку запроса



        }
    }
    //var_dump($invoices_j_ex);

    echo '

    <div id="fact"></div>
    
    
    <span style="font-size: 85%;">Наряды (Филиал / статус)</span><br>';
    //var_dump($invoices_j);

    foreach ($invoices_j as $invoice_id => $invoice_item){
        //($invoice_item['status'] == 5)  - работа закрыта

        echo '<div style="width: 190px; margin-bottom: 5px; border: 1px solid rgba(191, 188, 181, 0.53);">';

        //Рисуем кнопку-ссылку на наряд
        echo '
            <div style="margin: 2px 0;">
                <a href="invoice.php?id='.$invoice_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;">
                    '.$invoice_id.' ('.$filials_j[$invoice_item['filial_id']]['name2'].' / '.$invoice_item['status'].')
                </a>';
        if ($invoice_item['status'] == 5){
            echo '<i class="fa fa-plus" style="color: green; font-size: 120%;"></i>';
        }else{
            echo '<i class="fa fa-minus" style="color: red; font-size: 120%;"></i>';
            //Добавим наряды, которые не закрыты в отдельный массив
            array_push($opened_invoices, $invoice_item);
        }
        echo '
            </div>';

        //Позиции, которые прошли как подарки пациентам,
        //но нам же надо дать ЗП за них,
        //НЕ разбитые по филиалам, на которых был сделан наряд
        $gift_invoice_ex = array();
        //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
        $gift_invoice_summ = 0;

        //Получаем позиции, которые прошли как подарки пациентам
        $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `gift` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //var_dump($arr);

                //Сразу добавляем в итоговый массив,
                //предварительно добавив в массив элемент с ID филиала, если его не было
                if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                    $itog_filials_summ[$invoice_item['filial_id']] = 0;
                }
                $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];


//                array_push($gift_invoice_ex, $arr);
//                if (!isset($gift_invoice_summ[$invoice_item['filial_id']])){
//                    $gift_invoice_summ[$filial_id] = 0;
//                }

                $gift_invoice_summ += $arr['itog_price'];
            }
        }
//        var_dump($gift_invoice_ex);
//        var_dump($gift_invoice_summ);

        //Если были подарочные позиции по наряду
        if ($gift_invoice_summ > 0){
            //Нарисуем полученные оплаты
            //foreach ($payments_j as $filial_id => $payment_item) {
                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(255, 235, 64, 0.42);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $gift_invoice_summ . '</b></span></i></div>';
            //}
        }


        //Оплаты
        $payments_j = array();

        //Получаем все оплаты по текущему наряду
        $query = "
            SELECT *
            FROM `journal_payment`
            WHERE `invoice_id` = '{$invoice_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //array_push($payments_j, $arr);
                //Раскидаем суммы оплат сразу по филиалам
                if (!isset($payments_j[$arr['filial_id']])){
                    $payments_j[$arr['filial_id']]['summ'] = 0;
                }
                $payments_j[$arr['filial_id']]['summ'] += $arr['summ'];

                //Сразу добавляем в итоговый массив,
                //предварительно добавив в массив элемент с ID филиала, если его не было
                if (!isset($itog_filials_summ[$arr['filial_id']])){
                    $itog_filials_summ[$arr['filial_id']] = 0;
                }
                $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

            }
        }

        //echo '<span style="font-size: 85%;">Оплаты: Филиал -> Сумма</span><br>';
        //var_dump($payments_j);
        //Если оплаты оп наряду есть
        if (!empty($payments_j)) {
            //Нарисуем полученные оплаты
            foreach ($payments_j as $filial_id => $payment_item) {
                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 220, 14, 0.2);">' . $filials_j[$filial_id]['name2'] . ' ->  <b style="font-size: 105%;">' . $payment_item['summ'] . '</b></span></i></div>';
            }
        }else{

            //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
            //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд

            $filial_insure_invoice_ex = array();

            $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `insure` <> '0' AND `insure_approve` = '1'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            //Если что-то нашли страхового
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    if (!isset($filial_insure_invoice_ex[$invoice_item['filial_id']])){
                        $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] = 0;
                    }
                    $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] += $arr['itog_price'];

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                        $itog_filials_summ[$invoice_item['filial_id']] = 0;
                    }
                    $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];

                }

                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 175, 220, 0.2);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] . '</b></span></i></div>';

            }else{

                if ($gift_invoice_summ > 0){
                    //Уж даже и не страховой наряд и нет там подарков
                   echo '<i><span style="color: red; font-size: 80%;">нет оплат (наряд скорее всего "нулевой", РЛ к нему можно было бы и не создавать)</span></i>';
                }/*else{
                    echo '<i><span style="color: red; font-size: 80%;">Ошибка #54. Требуется тщательная проверка.</span></i>';
                }*/
            }
        }

        //А теперь выберем РЛ, которые были не этому исполнителю.
        //Ибо может быть так, что наряд один, а исполнителей больше.
        //Выберем их и вычтем суммы из общих
        //... а может потом и не придется, заставим админов делать отдельные наряды
        $query = "SELECT * FROM `fl_journal_calculate` WHERE  `invoice_id` = '{$invoice_id}'  AND `worker_id` <> '{$worker_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

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
                if (!isset($itog_filials_summ_not4tou[$invoice_item['filial_id']])){
                    $itog_filials_summ_not4tou[$invoice_item['filial_id']] = 0;
                }
                $itog_filials_summ_not4tou[$invoice_item['filial_id']] += $arr['summ_inv'];
            }
        }

        echo '</div>';
    }

    //Сумма на всех филиалах
    $itog_all_filial_summ = 0;

    //Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников
    echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников</span>';
    //Сортируем, чтоб меньше денег было внизу
    arsort($itog_filials_summ);
    var_dump($itog_filials_summ);

    //Итоговый массив по филиалам по тем работам,
    //которые мы хотим и должны оплатить другому человеку
    echo '<span style="font-size: 85%;">Итоговый массив по филиалам по тем работам, которые мы хотим и должны оплатить ДРУГОМУ человеку</span>';
    var_dump($itog_filials_summ_not4tou);

    //Вычтем с филиалов суммы, которые уйдут в зп другому человеку
    foreach ($itog_filials_summ_not4tou as $filial_id => $summ){
        if (isset($itog_filials_summ[$filial_id])){
            $itog_filials_summ[$filial_id] -= $summ;
        }
    }
    //Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего
    echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего</span>';
    var_dump($itog_filials_summ);

    //Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
    echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
    $itog_all_filial_summ = array_sum($itog_filials_summ);
    var_dump($itog_all_filial_summ);

    //Нам нужен (?) последний ключ массива для
    //дальнейшей работы с ним
//    end($itog_filials_summ);
//    $last_key = key($itog_filials_summ);
//    var_dump($last_key);

    //Вычислим процентное соотношение
    foreach ($itog_filials_summ as $filial_id => $summ){

        $percent_value = 0;

        //предварительно добавляем в массив элемент с ID филиала, если его не было
        //!!! потом сделать это выше, когда суммы собираем
        if (!isset($itog_filials_percents[$filial_id])){
            $itog_filials_percents[$filial_id] = 0;
        }

        $percent_value = (100* $summ) / $itog_all_filial_summ;

        $itog_filials_percents[$filial_id] = $percent_value;
    }
    echo '<span style="font-size: 85%;">Процентное соотношение денег по филиалам в общей сумме</span>';
    var_dump($itog_filials_percents);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($itog_filials_percents));

    echo '<hr>';


    echo '<h3 style="font-size: 100%;">1. Мы хотим выдать сразу всю сумму зп. У нас все работы закрыты и оплачены:</h3>';

    //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
    $summ4ZP = array();

    //Сумма ЗП к выдаче
    //сейчас тут только сумма за РЛ и так по логике верно
    echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
    $iWantMyMoney1 = $tabel_j['summ_calc'];
    var_dump($iWantMyMoney1);


    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents as $filial_id => $percent){
        $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZP);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZP));


    //Вычислим остаток денег по филиалам после выдачи
    foreach ($itog_filials_summ as $filial_id => $summ){
        if (!isset($itog_filials_summ_ostatok[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = 0;
        }
        if (isset($summ4ZP[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZP[$filial_id];
        }
    }

    echo '<span style="font-size: 85%;">Остаток денег по филиалам после выдачи</span>';
    var_dump($itog_filials_summ_ostatok);

    echo '<hr>';

    echo '<h3 style="font-size: 100%;">2a. Мы хотим выдать только часть денег (аванс). У нас все работы закрыты и оплачены:</h3>';

    //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
    $summ4ZP = array();
    //Части по филиалам, которые хотим выдать, исходя из суммы аванса
    $summ4ZPNow = array();

    //Сумма ЗП к выдаче
    //сейчас тут только сумма за РЛ и так по логике верно
    echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
    $iWantMyMoney1 = $tabel_j['summ_calc'];
    var_dump($iWantMyMoney1);

    //Часть, которую хотим выдать
    echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
    $iWantMyMoney2 = 12000;
    var_dump($iWantMyMoney2);

    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents as $filial_id => $percent){
        $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZP);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZP));


    //Если выдаем часть !!! Потом можно будет расширить это понятие и на всю сумма. Сумма как часть самой себя
    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents as $filial_id => $percent){
        $summ4ZPNow[$filial_id] = intval($iWantMyMoney2 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZPNow);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZPNow));


    //Вычислим остаток денег еще останется выдать
    $summ4ZP_ostatok = array();

    foreach ($summ4ZP as $filial_id => $summ){
        if (!isset($summ4ZP_ostatok[$filial_id])){
            $summ4ZP_ostatok[$filial_id] = 0;
        }
        $summ4ZP_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
    }

    echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZP_ostatok);

    //Вычислим остаток денег по филиалам после выдачи
    foreach ($itog_filials_summ as $filial_id => $summ){
        if (!isset($itog_filials_summ_ostatok[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = 0;
        }
        if (isset($summ4ZPNow[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
        }

    }

    echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
    var_dump($itog_filials_summ_ostatok);


    echo '<hr>';

    echo '<h3 style="font-size: 100%;">2б. Мы уже выдали часть денег (аванс), а к концу месяца в табель еще накидали РЛ + были оплаты. У нас все работы закрыты и оплачены:</h3>';

    echo '<h2>Тут у нас все рассчеты до аванса, включая сам факт выдачи</h2>';

    //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
    $summ4ZP = array();
    //Части по филиалам, которые хотим выдать, исходя из суммы аванса
    $summ4ZPNow = array();

    //Сумма ЗП к выдаче
    //сейчас тут только сумма за РЛ и так по логике верно
    echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
    $iWantMyMoney1 = $tabel_j['summ_calc'];
    var_dump($iWantMyMoney1);

    //Часть, которую хотим выдать
    echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
    $iWantMyMoney2 = 12000;
    var_dump($iWantMyMoney2);

    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents as $filial_id => $percent){
        $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZP);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZP));


    //Если выдаем часть !!! Потом можно будет расширить это понятие и на всю сумма. Сумма как часть самой себя
    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents as $filial_id => $percent){
        $summ4ZPNow[$filial_id] = intval($iWantMyMoney2 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZPNow);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZPNow));


    //Вычислим остаток денег еще останется выдать
    $summ4ZP_ostatok = array();

    foreach ($summ4ZP as $filial_id => $summ){
        if (!isset($summ4ZP_ostatok[$filial_id])){
            $summ4ZP_ostatok[$filial_id] = 0;
        }
        $summ4ZP_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
    }

    echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег</span>';
    var_dump($summ4ZP_ostatok);

    //Вычислим остаток денег по филиалам после выдачи
    foreach ($itog_filials_summ as $filial_id => $summ){
        if (!isset($itog_filials_summ_ostatok[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = 0;
        }
        if (isset($summ4ZPNow[$filial_id])){
            $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
        }

    }

    echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
    var_dump($itog_filials_summ_ostatok);


    echo '<br><h2>Тут изменения в деньгах после выдачи аванса (данные именно в данном исследовании вводились вручную...)</h2>';

    //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
    $summ4ZP_temp = array();
    //Части по филиалам, которые хотим выдать, исходя из суммы аванса
    $summ4ZPNow_temp = array();

    echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (только сумма за РЛ)</span>';
    $iWantMyMoney1 = 400381;
    var_dump($iWantMyMoney1);

    //Часть, которую хотим выдать
    echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас (в данном случае это финальная выплата, выдаем всё, что осталось после аванса).</span>';
    $iWantMyMoney2 = $iWantMyMoney1 - $iWantMyMoney2;
    var_dump($iWantMyMoney2);

echo '<br>-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-><br>';

//Изменились исходные данные (ставим вручную)
//Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников
$itog_filials_summ_temp =  array(19 => 135180, 15 => 29560, 13 => 500000);

echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников (тут мы тоже и далее задали вручную, задав + еще 1 филиал)</span>';
//Сортируем, чтоб меньше денег было внизу
arsort($itog_filials_summ_temp);
var_dump($itog_filials_summ_temp);

//Итоговый массив по филиалам по тем работам,
//которые мы хотим и должны оплатить другому человеку
$itog_filials_summ_not4tou_temp =  $itog_filials_summ_not4tou;

echo '<span style="font-size: 85%;">Итоговый массив по филиалам по тем работам, которые мы хотим и должны оплатить ДРУГОМУ человеку</span>';
var_dump($itog_filials_summ_not4tou_temp);

//Вычтем с филиалов суммы, которые уйдут в зп другому человеку
foreach ($itog_filials_summ_not4tou_temp as $filial_id => $summ){
    if (isset($itog_filials_summ_temp[$filial_id])){
        $itog_filials_summ_temp[$filial_id] -= $summ;
    }
}
//Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего
echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего</span>';
var_dump($itog_filials_summ_temp);

//Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
$itog_all_filial_summ_temp = array_sum($itog_filials_summ_temp);
var_dump($itog_all_filial_summ_temp);

//Вычислим ИТОГОВОЕ процентное соотношение
foreach ($itog_filials_summ_temp as $filial_id => $summ){

    $percent_value = 0;

    //предварительно добавляем в массив элемент с ID филиала, если его не было
    //!!! потом сделать это выше, когда суммы собираем
    if (!isset($itog_filials_percents_temp[$filial_id])){
        $itog_filials_percents_temp[$filial_id] = 0;
    }

    $percent_value = (100* $summ) / $itog_all_filial_summ_temp;

    $itog_filials_percents_temp[$filial_id] = $percent_value;
}
echo '<span style="font-size: 85%;">Процентное ИТОГОВОЕ соотношение денег по филиалам в общей сумме</span>';
var_dump($itog_filials_percents_temp);
//Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
var_dump(array_sum($itog_filials_percents_temp));

echo '<br><-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-<br>';

    //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
    $summ4ZP_temp = array();
    //Части по филиалам, которые хотим выдать, исходя из суммы аванса
    $summ4ZPNow_temp = array();

    /*//Сумма ЗП к выдаче
    //сейчас тут только сумма за РЛ и так по логике верно
    echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
    $iWantMyMoney1 = $tabel_j['summ_calc'];
    var_dump($iWantMyMoney1);*/

    /*//Часть, которую хотим выдать
    echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
    $iWantMyMoney2 = 12000;
    var_dump($iWantMyMoney2);*/

    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам
    foreach ($itog_filials_percents_temp as $filial_id => $percent){
        $summ4ZP_temp[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
    }

    echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег НЕ учитывая то, что уже выдали</span>';
    var_dump($summ4ZP_temp);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZP_temp));


    //Если выдаем последнее, но уже выдавали аванс
    //Посчитаем по сколько надо выдать с каждого филиала
    //пропорционально полученным деньгам

    foreach ($summ4ZP_temp as $filial_id => $summ){
        $summ4ZPNow_temp[$filial_id] = $summ;

        if (isset($summ4ZPNow[$filial_id])) {
            $summ4ZPNow_temp[$filial_id] -= $summ4ZPNow[$filial_id];
        }
    }

    echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег, УЧИТЫВАЯ уже выданное в аванс</span>';
    var_dump($summ4ZPNow_temp);
    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
    var_dump(array_sum($summ4ZPNow_temp));

    echo '<span style="font-size: 85%;">Столько будет выдано ВСЕГО</span>';
    var_dump(array_sum($summ4ZPNow_temp) + array_sum($summ4ZPNow));

    //Вычислим остаток денег еще останется выдать
    $summ4ZP_ostatok_temp = array();

    foreach ($summ4ZP_temp as $filial_id => $summ){
        if (!isset($summ4ZP_ostatok_temp[$filial_id])){
            $summ4ZP_ostatok_temp[$filial_id] = 0;
        }
        //!!!сюда потом тоже добавить if как и ниже для универсальности
        $summ4ZP_ostatok_temp[$filial_id] = $summ - $summ4ZPNow_temp[$filial_id];

        if (isset($summ4ZPNow[$filial_id])){
            $summ4ZP_ostatok_temp[$filial_id] -= $summ4ZPNow[$filial_id];
        }
    }

    echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег (если не будет доплат и увеличения ЗП)</span>';
    var_dump($summ4ZP_ostatok_temp);

    //Вычислим остаток денег по филиалам после выдачи
    foreach ($itog_filials_summ_temp as $filial_id => $summ){
        if (!isset($itog_filials_summ_ostatok_temp[$filial_id])){
            $itog_filials_summ_ostatok_temp[$filial_id] = 0;
        }
        if (isset($summ4ZPNow_temp[$filial_id])){
            $itog_filials_summ_ostatok_temp[$filial_id] = $summ - $summ4ZPNow_temp[$filial_id];
        }

    }

    echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
    var_dump($itog_filials_summ_ostatok_temp);














echo '<hr>';



/*    //Все рассчетные листы
    $calculates_j = array();
    //Позиции, которые прошли по страховой (денег в кассе нет, а зп выдать надо с этого филиала)
    $insure_invoice_ex = array();
    //Позиции, которые прошли как подарки пациентам,
    //но нам же надо дать ЗП за них,
    //разбитые по филиалам, на которых был сделан наряд
    $gift_invoice_ex = array();
    //Сумма позиций с подарками, разбитые по филиалам, на которых юыл сделан наряд
    $gift_invoice_summ = array();

    //Пройдем по всем нарядам
    foreach ($invoices_j as $invoice_id => $filial_id){



        //Получаем все рассчетные листы
        $query = "
            SELECT *
            FROM `fl_journal_calculate`
            WHERE `invoice_id` = '{$invoice_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($calculates_j, $arr);
            }
        }

        //Получаем позиции, которые прошли как подарки пациентам
        $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `gift` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                if (!isset($gift_invoice_ex[$filial_id])){
                    $gift_invoice_ex[$filial_id] = array();
                }
                array_push($gift_invoice_ex[$filial_id], $arr);
                if (!isset($gift_invoice_summ[$filial_id])){
                    $gift_invoice_summ[$filial_id] = 0;
                }
                $gift_invoice_summ[$filial_id] += $arr['price'];
            }
        }
    }

    echo 'Позиции, которые прошли как подарки пациентам (по филиалам)';
    var_dump($gift_invoice_ex);

    echo 'Суммы позиций, которые прошли по страховой (по филиалам)';
    var_dump($gift_invoice_summ);


    //На каких филиалах какие оплаты были произведены
    $filial_payments = array();

    //Пройдемся по оплатам
    foreach ($payments_j as $payment_item){
        if (!isset($filial_payments[$payment_item['filial_id']])){
            $filial_payments[$payment_item['filial_id']] = 0;
        }
        $filial_payments[$payment_item['filial_id']] += $payment_item['summ'];
    }

    echo 'Суммы оплат по филиалам';
    var_dump($filial_payments);

    echo 'Все рассчетные листы';
    var_dump($calculates_j);

    //Позиции в рассчетных листах, которые прошли по страховой,
    //распределённые по филиалам, где был сделан наряд
    $filial_insure_calculate_ex = array();
    //Суммы, по страховым нарядам, разбитые по филиалам...
    $filial_insure_calculate_summ = array();

    //Пройдемся по рассчетным листам, соберем позиции, где есть страховые
    //добавим сюда филиал, в котором был сделан наряд и далее будем считать, что
    //выплачиваем ЗП за эти страховые работы с того филиала, где сделали наряд
    foreach ($calculates_j as $calculate_item){
        $query = "
            SELECT *
            FROM `fl_journal_calculate_ex`
            WHERE `calculate_id` = '{$calculate_item['id']}'
            AND `insure` <> '0' AND `insure_approve` = '1'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                if (!isset($filial_insure_calculate_ex[$calculate_item['office_id']])){
                    $filial_insure_calculate_ex[$payment_item['filial_id']] = array();
                }
                array_push($filial_insure_calculate_ex[$payment_item['filial_id']], $arr);
                if (!isset($filial_insure_calculate_summ[$calculate_item['office_id']])){
                    $filial_insure_calculate_summ[$payment_item['filial_id']] = 0;
                }
                $filial_insure_calculate_summ[$payment_item['filial_id']] += $arr['price'];
            }
        }
    }

    echo 'Позиции в рассчетных листах, которые прошли по страховой (по филиалам)';
    var_dump($filial_insure_calculate_ex);

    echo 'Суммы в рассчетных листах, которые прошли по страховой (по филиалам)';
    var_dump($filial_insure_calculate_summ);*/


require_once 'footer.php';
	
?>