<?php

//ajax_show_result_stat_insure_f.php
//

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

        include_once 'DBWork.php';
        include_once 'functions.php';

        //разбираемся с правами
        $god_mode = FALSE;

        require_once 'permissions.php';

        //$workerExist = false;
        $queryDopExist = false;
        $queryDopExExist = false;
        $queryDopClientExist = false;
        $query = '';
        $queryDop = '';
        $queryDopEx = '';
        $queryDopClient = '';

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;



        $msql_cnnct = ConnectToDB();

        /*if ($_POST['worker'] != ''){
            include_once 'DBWork.php';
            $workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

            if ($workerSearch == 0){
                $workerExist = false;
            }else{
                $workerExist = true;
                $worker = $workerSearch[0]['id'];
            }
        }else{*/
        $workerExist = true;
        $worker = 0;
        //}

        if ($workerExist){
            $query .= "SELECT * FROM `journal_invoice`";

            //$time = time();

            //Дата/время
            if ($_POST['all_time'] != 1){
                //$queryDop .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";

                $datastart = date('Y-m-d', strtotime($_POST['datastart'].' 00:00:00'));
                $dataend = date('Y-m-d', strtotime($_POST['dataend'].' 23:59:59'));

                $queryDop .= "`create_time` BETWEEN 
                STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                AND 
                STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s')";

                $queryDopExist = true;
            }

            //Сотрудник
            /*if ($worker != 0){
                if ($queryDopExist){
                    $queryDop .= ' AND';
                }
                $queryDop .= "`create_person` = '".$worker."'";
                $queryDopExist = true;
            }*/

            //Филиал
            if ($_POST['filial'] != 99){
                if ($queryDopExist){
                    $queryDop .= ' AND';
                }
                $queryDop .= "`office_id` = '".$_POST['filial']."'";
                $queryDopExist = true;
            }

            //Все записи
            /*if ($_POST['zapisAll'] != 0){
                //ничего
            }else{
                //Пришёл
                if ($_POST['zapisArrive'] != 0){
                    if ($queryDopExExist){
                        $queryDopEx .= ' OR';
                    }
                    if ($_POST['zapisArrive'] == 1){
                        $queryDopEx .= "`enter` = '1'";
                        $queryDopExExist = true;
                    }
                    //$queryDopExExist = true;
                }

                //Не пришёл
                if ($_POST['zapisNotArrive'] != 0){
                    if ($queryDopExExist){
                        $queryDopEx .= ' OR';
                    }
                    if ($_POST['zapisNotArrive'] == 1){
                        $queryDopEx .= "`enter` = '9'";
                        $queryDopExExist = true;
                    }
                    //$queryDopExExist = true;
                }

                //Не отмеченные
                if ($_POST['zapisNull'] != 0){
                    if ($queryDopExExist){
                        $queryDopEx .= ' OR';
                    }
                    if ($_POST['zapisNull'] == 1){
                        $queryDopEx .= "`enter` = '0'";
                        $queryDopExExist = true;
                    }
                    //$queryDopExExist = true;
                }

               //Ошибочные
                if ($_POST['zapisError'] != 0){
                    if ($queryDopExExist){
                        $queryDopEx .= ' OR';
                    }
                    if ($_POST['zapisError'] == 1){
                        $queryDopEx .= "`enter` = '8'";
                        $queryDopExExist = true;
                    }
                    //$queryDopExExist = true;
                }
            }*/


            if ($queryDopExist){
                $query .= ' WHERE '.$queryDop;

                if ($queryDopExExist){
                    $query .= ' AND ('.$queryDopEx.')';
                }
                /*if ($queryDopClientExist){
                    $queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
                    if ($queryDopExist){
                        $query .= ' AND';
                    }
                    $query .= "`client` IN (".$queryDopClient.")";
                }*/

                $query = $query." AND `summins` <> '0' AND `status`<>'9' ORDER BY `create_time` DESC";

                //var_dump($query);

                $arr = array();
                $rez = array();

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                    $journal = $rez;
                }else{
                    $journal = 0;
                }

                //Готовим и Выводим результат
                if ($journal != 0){
                    //var_dump($journal);

                    $invoice_ex_j = array();
                    $invoice_ex_j_mkb = array();

                    //Собираем остальные данные и перестраиваем массив с фамилиями и детали нарядов

                    //массив для результата
                    $rezult_arr = array();

                    foreach ($journal as $journal_item){
                        //var_dump($journal_item);
                        //var_dump($journal_item['id']);

                        //$rezult_arr[$journal_item['client_id']]['name'] = WriteSearchUser('spr_clients', $journal_item['client_id'], 'user_full', false);
                        //$rezult_arr[$journal_item['client_id']][$journal_item['create_time']] = $journal_item;

                        //Если выбираем по одной страховой
                        //if ($_POST['insure'] != 99){
                        //if ($_POST['insure'] ==
                        //$rezult_arr[WriteSearchUser('spr_clients', $journal_item['client_id'], 'user_full', false)][$journal_item['create_time']] = $journal_item;
                        //}else {
                        $rezult_arr[WriteSearchUser('spr_clients', $journal_item['client_id'], 'user_full', false)][$journal_item['create_time']] = $journal_item;
                        //}
                    }

                    //Сортируем по имени
                    ksort($rezult_arr);

                    //Дальше будет полная дичь, т.к. мы еще раз проходимся по тому же массиву
                    foreach ($rezult_arr as $fio => $rezult_arr_fio){
                        //Сортируем по дате !!! не канает так
                        //ksort($rezult_arr_item);

                        foreach ($rezult_arr_fio as $fio_time => $rezult_arr_time){
                            //var_dump($rezult_arr_time['summins']);

                            $invoice_ex_j = array();
                            $invoice_ex_j_mkb = array();

                            //Добавим в массив id пациента для ссылки на него потом
                            $rezult_arr[$fio]['data']['client_id'] = $rezult_arr_time['client_id'];

                            //Добавим в массив данные о полисе
                            $query = "SELECT `polis`,`insure` FROM `spr_clients` WHERE `id`='".$rezult_arr_time['client_id']."';";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                $arr = mysqli_fetch_assoc($res);
                                $rezult_arr[$fio]['data']['polis'] = $arr['polis'];
                                $rezult_arr[$fio]['data']['insure'] = $arr['insure'];
                            }else{
                                $rezult_arr[$fio]['data']['polis'] = 0;
                                $rezult_arr[$fio]['data']['insure'] = 0;
                            }


                            //Собираем точные данные по каждому наряду
                            $query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='".$rezult_arr_time['id']."';";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    if (!isset($invoice_ex_j[$arr['ind']])){
                                        $invoice_ex_j[$arr['ind']] = array();
                                        array_push($invoice_ex_j[$arr['ind']], $arr);
                                    }else{
                                        array_push($invoice_ex_j[$arr['ind']], $arr);
                                    }
                                }
                            }else
                                $invoice_ex_j = 0;
                            //var_dump ($invoice_ex_j);

                            //сортируем зубы по порядку
                            ksort($invoice_ex_j);

                            $rezult_arr[$fio][$fio_time]['invoice_ex'] = $invoice_ex_j;

                            //var_dump($invoice_ex_j);


                            //Собираем точные данные по каждому наряду (МКБ)
                            $query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='".$rezult_arr_time['id']."';";
                            //var_dump ($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    if (!isset($invoice_ex_j_mkb[$arr['ind']])){
                                        $invoice_ex_j_mkb[$arr['ind']] = array();
                                        array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                    }else{
                                        array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                    }
                                }
                            }else
                                $invoice_ex_j_mkb = 0;
                            //var_dump ($invoice_ex_j_mkb);


                            $rezult_arr[$fio][$fio_time]['invoice_ex_mkb'] = $invoice_ex_j_mkb;

                        }
                    }

                    echo '
                                <table width="100%" border="0" class="tableInsStat">
                                    <tr style="background-color: rgba(245, 245, 245, 0.9)">
                                        <td width="30px" style="text-align: center; font-weight: bold;">№ зуба</td>
                                        <td width="20%" style="text-align: center; font-weight: bold;">Диагноз</td>
                                        <td width="120px" style="text-align: center; font-weight: bold;">Код услуги</td>
                                        <td style="text-align: center; font-weight: bold;">Название услуги</td>
                                        <td width="90px" style="text-align: center; font-weight: bold;">Цена руб</td>
                                        <td style="text-align: center; font-weight: bold;">Кол-во</td>
                                        <td width="90px" style="text-align: center; font-weight: bold;">Сумма руб</td>
                                    </tr>';



                    /*echo '
								<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
									<li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellCosmAct" style="text-align: center; background-color:#FEFEFE;">№ зуба</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Диагноз</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Код услуги</div>
										<div class="cellName" style="text-align: center">Название услуги</div>
										<div class="cellName" style="text-align: center">Цена руб</div>
										<div class="cellCosmAct" style="text-align: center">Кол-во</div>
										<div class="cellName" style="text-align: center">Сумма руб</div>
									</li>';*/

                    //Пробуем вывести всё это на экран

                    foreach($rezult_arr as $fio => $rezult_arr_fio){
                        //var_dump($rezult_arr_fio['client_id']);

                        //Общая сумма по пациенту
                        $patient_summ = 0;
                        //Результат для вывода по пациенту
                        $rez_str_fio = '';

                        $rez_str_fio .= '
                                    <tr style="background-color: rgba(184, 255, 160, 0.7);">
                                        <td colspan="3" style="font-weight: bold; font-size: 13px;">'. WriteSearchUser('spr_clients', $rezult_arr_fio['data']['client_id'], 'user_full', true).'</td>
                                        <td colspan="4" style="font-weight: bold;">Полис '. $rezult_arr_fio['data']['polis'] .'</td>
                                    </tr>';


                        $fio_insure = $rezult_arr_fio['data']['insure'];

                        $insure_j = SelDataFromDB('spr_insure', $rezult_arr_fio['data']['insure'], 'id');

                        foreach($rezult_arr_fio as $fio_time => $rezult_arr_time) {

                            if ($fio_time != 'data'){
//                                var_dump ($rezult_arr_time['invoice_ex']);

                                $rez_str_invoice_zub = '';
                                //Сумма каждого наряда
                                $invoice_summ = 0;

                                $rez_str_fio .= '
                                    <tr style="background-color: rgba(225, 226, 110, 0.2);">
                                        <td></td>
                                        <td><b><a href="invoice.php?id='.$rezult_arr_time['id'].'" class="ahref" target="_blank" rel="nofollow noopener">'.date('d.m.y' ,strtotime($fio_time)).'</a></b></td>
                                        <td style="font-weight: bold;">'. WriteSearchUser('spr_workers', $rezult_arr_time['worker_id'], 'user', true).'</td>
                                        <td colspan="3">';

                                //Прописываем страховую и договор
                                //!!! переделать с учетом нескольких договоров
                                if ($insure_j !=0){
                                    $rez_str_fio .= $insure_j[0]['name'].'. Дог. №'.$insure_j[0]['contract'];
                                }else{
                                    $rez_str_fio .= '<span class="query_neok" style="padding-top: 0">ошибка страховой</span>';
                                }

                                foreach($rezult_arr_time['invoice_ex'] as $zub => $invoice_ex_data) {

                                    //Сумма каждого наряда
                                    $invoice_summ_zub = 0;

                                    $rez_str_invoice_ex = '';

                                    foreach($invoice_ex_data as $invoice_ex_zub_data) {
                                        //var_dump($invoice_ex_zub_data);

                                        $rez_str_invoice_ex .= '
                                         <tr>
                                            <td style="text-align: right;">';

                                        if ($zub == 99){
                                            $rez_str_invoice_ex .= '';
                                        }else{
                                            $rez_str_invoice_ex .= $zub;
                                        }

                                        $rez_str_invoice_ex .= '    
                                            </td>
                                            <td style="font-size: 11px;">';

                                        if (isset($rezult_arr_time['invoice_ex_mkb'][$zub])) {
                                            if (!empty($rezult_arr_time['invoice_ex_mkb'][$zub])) {
                                                foreach ($rezult_arr_time['invoice_ex_mkb'][$zub] as $mkb) {
                                                    $rez = array();
                                                    //$rezult2 = array();

                                                    $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb['mkb_id']}'";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                    $number = mysqli_num_rows($res);
                                                    if ($number != 0) {
                                                        while ($arr = mysqli_fetch_assoc($res)) {
                                                            $rez[$mkb['mkb_id']] = $arr;
                                                        }
                                                    } else {
                                                        $rez = 0;
                                                    }
                                                    if ($rez != 0) {
                                                        foreach ($rez as $mkb_name_val) {
                                                            $rez_str_invoice_ex .= $mkb_name_val['code'] . ' ' . $mkb_name_val['name'] . '<br>';
                                                        }
                                                    } else {
                                                        $rez_str_invoice_ex .= '<span class="query_neok" style="padding-top: 0">ошибка диагноза</span>';
                                                    }
                                                }
                                            }
                                        } else {
                                            $rez_str_invoice_ex .= '<span class="query_neok" style="padding-top: 0">нет диагноза</span>';
                                        }

                                        $rez_str_invoice_ex .= '
                                            </td>';

                                        $arr = array();
                                        $rez = array();

                                        $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$invoice_ex_zub_data['price_id']}'";

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                        $number = mysqli_num_rows($res);
                                        if ($number != 0) {
                                            while ($arr = mysqli_fetch_assoc($res)) {
                                                array_push($rez, $arr);
                                            }
                                            $rezult2 = $rez;
                                        } else {
                                            $rezult2 = 0;
                                        }

                                        //var_dump($rezult2);

                                        //Код
                                        //var_dump($rezult2[0]['code']);
                                        $rezult2_code = $rezult2[0]['code'];
                                        if ($rezult2[0]['code'] == NULL){
                                            $rezult2_code = $rezult2[0]['code_u'];
                                        }
                                        $rez_str_invoice_ex .= '<td>'.$rezult2_code.'</td>';
                                        //Название
                                        $rez_str_invoice_ex .= '<td>';

                                        if ($rezult2 != 0) {

                                            $rez_str_invoice_ex .= $rezult2[0]['name'];

                                        } else {
                                            $rez_str_invoice_ex .= '<span class="query_neok" style="padding-top: 0">ошибка названия позиции</span>`';
                                        }

                                        $rez_str_invoice_ex .= '
                                            </td>
                                            <td style="text-align: right;">';

                                        if ($invoice_ex_zub_data['price'] > 0){
                                            $rez_str_invoice_ex .= number_format($invoice_ex_zub_data['price'], 2, '.', '');
                                        }else{
                                            $rez_str_invoice_ex .= '<span class="query_neok" style="padding-top: 0;  text-align: right;">'.number_format($invoice_ex_zub_data['price'], 2, '.', ' ').'</span>';
                                        }

                                        $rez_str_invoice_ex .= '
                                           </td>
                                           <td style="text-align: right;">'.$invoice_ex_zub_data['quantity'].'</td>
                                           <td style="text-align: right;">'.number_format($invoice_ex_zub_data['price'] * $invoice_ex_zub_data['quantity'], 2, '.', '').'</td>
                                        </tr>';

                                        $invoice_summ_zub += $invoice_ex_zub_data['price'] * $invoice_ex_zub_data['quantity'];
                                    }
                                    //var_dump($invoice_summ_zub);

                                    $rez_str_invoice_zub .= $rez_str_invoice_ex;

                                    //$patient_summ += $invoice_summ_zub;
                                    $invoice_summ += $invoice_summ_zub;

                                    /*$rez_str_fio .= '
                                        </td>
                                        <td style="font-weight: bold; font-size: 13px; text-align: right;">'.number_format($invoice_summ, 2, '.', '').'</td>
                                    </tr>';


                                    $rez_str_fio .= $rez_str_invoice_ex;*/

                                }

                                $patient_summ += $invoice_summ;

                                $rez_str_fio .= '
                                        </td>
                                        <td style="font-weight: bold; font-size: 13px; text-align: right;">'.number_format($invoice_summ, 2, '.', '').'</td>
                                    </tr>';


                                $rez_str_fio .= $rez_str_invoice_zub;

                            }
                        }
                        $rez_str_fio .= '
                                    <tr>
                                        <td colspan="6"><b>Итого по пациенту:</b></td>
                                        <td style="font-weight: bold; font-size: 13px; text-align: right;">' .number_format($patient_summ, 2, '.', '').'</td>
                                    </tr>';

                        //Для отборки по страховой
                        if (($_POST['insure'] == 99) || ($fio_insure == $_POST['insure'])) {
                            echo $rez_str_fio;
                        }

                    }
                    //var_dump($rezult_arr['Бадаева Анастасия Андреевна']['2017-05-25 08:47:24'][ 'invoice_ex']);
                    //var_dump($rezult_arr);







                    // !!! **** тест с записью
                    /*include_once 'showZapisRezult.php';

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

                    echo showZapisRezult($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false);*/




                    //Общее кол-во посещений
                    $journal_count_orig = 0;
                    //Массив с оригинальными пациентами
                    $orig_clients = array();

                    $actions_stomat = SelDataFromDB('actions_stomat', '', '');
                    /*if (($stom['see_all'] == 1) || $god_mode){*/
                    $id4filter4worker = '';
                    /*	$id4filter4upr = 'id="4filter"';
                    }else{
                        $id4filter4worker = 'id="4filter"';*/
                    $id4filter4upr = '';




                    /* echo '
                             <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                 Всего<br>
                                 Осмотров отмечено: '.count($journal).'<br>
                                 Посещений: '.$journal_count_orig.'<br>
                                 Пациентов за период: '.count($orig_clients).'<br>
                             </li>';*/

                    echo '
                                  </table>
								<!--</ul>-->
							</div>';
                }else{
                    echo '<span style="color: red;">Ничего не найдено</span>';
                }

            }else{
                echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
            }

            //var_dump($query);
            //var_dump($queryDopEx);
            //var_dump($queryDopClient);

            //mysql_close();
        }else{
            echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
        }
    }
}
?>