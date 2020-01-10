<?php

//fl_calculation_add3.php
//Расчет для стоматологов

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'ffun.php';

        require 'variables.php';

        require 'config.php';

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        $temp_arr = array();

        //var_dump($_SESSION);
        //var_dump($_SESSION['calculate_data']['28237']['104737']['data']);
        //unset($_SESSION['invoice_data']);

        if ($_GET){
            if (isset($_GET['invoice_id'])){

                //unset($_SESSION['calculate_data']);

                $invoice_j = SelDataFromDB('journal_invoice', $_GET['invoice_id'], 'id');

                if ($invoice_j != 0){
                    //var_dump($invoice_j);
                    //array_push($_SESSION['invoice_data'], $_GET['client']);
                    //$_SESSION['invoice_data'] = $_GET['client'];
                    //var_dump($invoice_j[0]['closed_time'] == 0);

                    $calculate_j = array();
                    $calculate_exist_j = array();
                    $sheduler_zapis = array();
                    $invoice_ex_j = array();
                    $invoice_ex_j_mkb = array();

                    $client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');
                    //var_dump($client_j);

                    $msql_cnnct = ConnectToDB ();

                    $query = "SELECT * FROM `zapis` WHERE `id`='".$invoice_j[0]['zapis_id']."'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($sheduler_zapis, $arr);
                        }
                    }else{
                        //$sheduler_zapis = 0;
                        //var_dump ($sheduler_zapis);
                    }
                    //var_dump ($sheduler_zapis);
                    //if ($client !=0){
                    //if (!empty($sheduler_zapis)){

                    //!!! 2019.04.04 патч. Сброс сессии к Расчетному листу, чтобы избавится потом от кнопки сброса изменений
                        unset($_SESSION['calculate_data']);
                        //if (!isset($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']])){
                            $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['filial'] = $invoice_j[0]['office_id'];
                            $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['worker'] = $invoice_j[0]['worker_id'];
                            $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['t_number_active'] = 0;
                            $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['discount'] = 0;
                            $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = array();
                            //$_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['mkb'] = array();
                        //}

                        //Категории процентов
                        //$percents_j = SelDataFromDB('fl_spr_percents', $sheduler_zapis[0]['type'], 'type');
                        //var_dump($percents_j);

                        //сортируем зубы по порядку
                        //ksort($_SESSION['calculate_data'][$_GET['client']][$_GET['invoice_id']]['data']);

                        //var_dump($_SESSION);
                        //var_dump($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data']);
                        //var_dump($_SESSION['calculate_data'][$_GET['client']][$_GET['invoice_id']]['mkb']);

                        /*if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
                        else $month = $sheduler_zapis[0]['month'];*/

                        echo '
							<div id="status">
								<header>

									<h2>Добавить расчётный лист к наряду <a href="invoice.php?id='.$_GET['invoice_id'].'" class="ahref">#'.$_GET['invoice_id'].'</a></h2>';

                        echo '			
										</h2>';

                        echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                        echo '
											</span>
										</div>';

                        //var_dump(SelDataFromDB('spr_workers', '', 'full_name'));

                        echo '
									</header>';

                        $t_f_data_db = array();
                        $cosmet_data_db = array();

                        $back_color = '';

                        $summ_inv = 0;
                        $summins_inv = 0;

                        echo '
                                    <div id="data">';
                        echo '				
                                        <div id="errrror"></div>';

                        if ($invoice_j[0]['type'] != 88) {

                            echo '
								<ul style="margin-left: 6px; margin-bottom: 10px;">	
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Посещение</li>';

                            //if(($sheduler_zapis[0]['enter'] != 8) || ($scheduler['see_all'] == 1) || $god_mode){
                            if ($sheduler_zapis[0]['enter'] == 1) {
                                $back_color = 'background-color: rgba(119, 255, 135, 1);';
                            } elseif ($sheduler_zapis[0]['enter'] == 9) {
                                $back_color = 'background-color: rgba(239,47,55, .7);';
                            } elseif ($sheduler_zapis[0]['enter'] == 8) {
                                $back_color = 'background-color: rgba(137,0,81, .7);';
                            } elseif ($sheduler_zapis[0]['enter'] == 6) {
                                $back_color = 'background-color: rgba(160, 160, 160, 0.5);';
                            } else {
                                //Если оформлено не на этом филиале
                                if ($sheduler_zapis[0]['office'] != $sheduler_zapis[0]['add_from']) {
                                    $back_color = 'background-color: rgb(119, 255, 250);';
                                } else {
                                    $back_color = 'background-color: rgba(255,255,0, .5);';
                                }
                            }

                            $dop_img = '';

                            /*if ($sheduler_zapis[0]['insured'] == 1) {
                                $dop_img .= '<img src="img/insured.png" title="Страховое"> ';
                            }*/


                            //var_dump($sheduler_zapis[0]['pervich']);

                            /*if ($sheduler_zapis[0]['pervich'] == 1) {
                                $dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
                            }*/

                            /*if ($sheduler_zapis[0]['pervich'] == 1) {
                                $dop_img .= '<img src="img/pervich.png" title="Посещение для пациента первое без работы"> ';
                            }elseif ($sheduler_zapis[0]['pervich'] == 2) {
                                $dop_img .= '<img src="img/pervich_ostav_2.png" title="Посещение для пациента первое с работой"> ';
                            }elseif ($sheduler_zapis[0]['pervich'] == 3) {
                                $dop_img .= '<img src="img/vtorich_3.png" title="Посещение для пациента не первое"> ';
                            }elseif ($sheduler_zapis[0]['pervich'] == 4) {
                                $dop_img .= '<img src="img/vtorich_davno_4.png" title="Посещение для пациента не первое, но был более полугода назад"> ';
                            }

                            if ($sheduler_zapis[0]['noch'] == 1) {
                                $dop_img .= '<img src="img/night.png" title="Ночное"> ';
                            }*/

                            // !!! **** тест с записью
                            include_once 'showZapisRezult.php';

                            if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode) {
                                $finance_edit = true;
                                $edit_options = true;
                            }

                            if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {
                                $stom_edit = true;
                                $edit_options = true;
                            }
                            if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode) {
                                $cosm_edit = true;
                                $edit_options = true;
                            }

                            if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                                $admin_edit = true;
                                $edit_options = true;
                            }

                            if (($scheduler['see_all'] == 1) || $god_mode) {
                                $upr_edit = true;
                                $edit_options = true;
                            }

                            //echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);
                            echo showZapisRezult($sheduler_zapis, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, false, false);

                            echo '
                                        </ul>';
                            //}
                        }

                        //Наряды

                        //$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['invoice_id']."'";
                        //!!! пробуем JOIN
                        //$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['invoice_id']."';";
                        $query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='".$_GET['invoice_id']."';";
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
                        }
                        //var_dump ($invoice_ex_j);

                        //сортируем зубы по порядку
                        if (!empty($invoice_ex_j)){
                            ksort($invoice_ex_j);
                        }
                        //var_dump ($invoice_ex_j);

                        if (!empty($invoice_ex_j)){

                            $rez = array();
                            $arr = array();

                            $calculate_summ_inv = 0;
                            $calculate_summins_inv = 0;

                            //Получим уже существующие расчёты
                            if ($invoice_j[0]['type'] != 88) {
                                $query = "SELECT `id`, `summ_inv`, `summ` FROM `fl_journal_calculate` WHERE `invoice_id`='" . $_GET['invoice_id'] . "' AND `zapis_id`='" . $sheduler_zapis[0]['id'] . "';";
                            }else{
                                $query = "SELECT `id`, `summ_inv`, `summ` FROM `fl_journal_calculate` WHERE `invoice_id`='" . $_GET['invoice_id'] . "';";
                            }

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($rez, $arr);
                                }
                                $calculate_j = $rez;
                            }

                            //И данные по ним
                            if (!empty($calculate_j)){
                                //var_dump ($calculate_j);

                                foreach ($calculate_j as $calculate_item){
                                    $query = "SELECT `inv_pos_id` FROM `fl_journal_calculate_ex` WHERE `calculate_id`='".$calculate_item['id']."';";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                    $number = mysqli_num_rows($res);
                                    if ($number != 0){
                                        while ($arr = mysqli_fetch_assoc($res)){
                                            array_push($calculate_exist_j, (int)$arr['inv_pos_id']);
                                        }
                                    }

                                    $calculate_summ_inv += (int)$calculate_item['summ_inv'];
                                    //$calculate_summins_inv += (int)$calculate_item['summins_inv'];

                                }
                            }
                            //var_dump($calculate_exist_j);
                            //var_dump($calculate_summ_inv);
                            //var_dump($calculate_summins_inv);
                            //var_dump($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data']);

                            if (empty($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'])) {
                                //var_dump($invoice_ex_j);

                                //надо костыльно преобразовать массив
                                foreach ($invoice_ex_j as $ind => $invoice_ex_j_arr) {

                                    foreach ($invoice_ex_j_arr as $invoice_ex_j_val) {
                                        //var_dump((int)$invoice_ex_j_val['id']);
                                        //var_dump($invoice_ex_j_arr);

                                        if (!in_array((int)$invoice_ex_j_val['id'], $calculate_exist_j)) {

                                            $temp_arr2['id'] = (int)$invoice_ex_j_val['id'];
                                            $temp_arr2['price_id'] = (int)$invoice_ex_j_val['price_id'];
                                            $temp_arr2['quantity'] = (int)$invoice_ex_j_val['quantity'];
                                            $temp_arr2['insure'] = (int)$invoice_ex_j_val['insure'];
                                            $temp_arr2['insure_approve'] = (int)$invoice_ex_j_val['insure_approve'];
                                            $temp_arr2['price'] = (int)$invoice_ex_j_val['price'];
                                            $temp_arr2['guarantee'] = (int)$invoice_ex_j_val['guarantee'];
                                            $temp_arr2['spec_koeff'] = $invoice_ex_j_val['spec_koeff'];
                                            $temp_arr2['discount'] = (int)$invoice_ex_j_val['discount'];

                                            $temp_arr2['itog_price'] = (int)$invoice_ex_j_val['itog_price'];
                                            $temp_arr2['manual_itog_price'] = (int)$invoice_ex_j_val['itog_price'];

                                            //$temp_arr2['percent_cats'] = 1;
                                            $temp_arr2['percent_cats'] = (int)$invoice_ex_j_val['percent_cats'];
                                            $temp_arr2['work_percent'] = 0;
                                            $temp_arr2['material_percent'] = 0;
                                            $temp_arr2['summ_special'] = 0;


                                            //Категории по прайсу
                                            //$query = "SELECT `id` FROM `fl_spr_percents` WHERE `type`='".$invoice_j[0]['type']."' LIMIT 1;";
                                            $query = "SELECT `category` FROM `spr_pricelist_template` WHERE `id`='".$invoice_ex_j_val['price_id']."' LIMIT 1;";
                                            //var_dump($query);

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            $number = mysqli_num_rows($res);

                                            if ($number != 0) {
                                                $arr = mysqli_fetch_assoc($res);

                                                //$percents_j = getPercents($invoice_j[0]['worker_id'], (int)$arr['category']);
                                                //var_dump($percents_j);

                                                /*$temp_arr2['percent_cats'] = (int)$percents_j[(int)$arr['category']]['category'];
                                                $temp_arr2['work_percent'] = (int)$percents_j[(int)$arr['category']]['work_percent'];
                                                $temp_arr2['material_percent'] = (int)$percents_j[(int)$arr['category']]['material_percent'];*/


                                            }else {
                                                //$invoice_ex_j = 0;
                                            }

                                            //var_dump($temp_arr2);


                                            if ($invoice_ex_j_val['manual_price'] == 1) {
                                                $temp_arr2['manual_price'] = true;
                                            } else {
                                                $temp_arr2['manual_price'] = false;
                                            }

                                            if ($invoice_j[0]['type'] == 5) {
                                                if (!isset($temp_arr[$ind])) {
                                                    $temp_arr[$ind] = array();
                                                }

                                                array_push($temp_arr[$ind], $temp_arr2);
                                            }

                                            if (($invoice_j[0]['type'] == 6) || ($invoice_j[0]['type'] == 10) || ($invoice_j[0]['type'] == 7)) {
                                                array_push($temp_arr, $temp_arr2);
                                            }

                                            if ($invoice_j[0]['type'] == 88) {
                                                if (!isset($temp_arr[$ind])) {
                                                    $temp_arr[$ind] = array();
                                                }

                                                array_push($temp_arr[$ind], $temp_arr2);
                                            }
                                        }
                                    }
                                }

                                //var_dump($temp_arr);

                                if (!empty($temp_arr)) {
                                    //var_dump($temp_arr);

                                    if ($invoice_j[0]['type'] == 5) {
                                        $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = $temp_arr;
                                    }
                                    //Костыль для сессионых данных косметологов
                                    if (($invoice_j[0]['type'] == 6) || ($invoice_j[0]['type'] == 10) || ($invoice_j[0]['type'] == 7)) {
                                        $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = $temp_arr;
                                    }
                                    //Костыль для пустого пациента
                                    if ($invoice_j[0]['type'] == 88) {
                                        $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = $temp_arr;
                                    }
                                    //скидку тут добавлю в сессию
                                    if ($invoice_j[0]['type'] != 88) {
                                        $discount = $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['discount'] = $invoice_j[0]['discount'];
                                    }else{
                                        $discount = $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['discount'] = $invoice_j[0]['discount'];
                                    }

                                    if ($invoice_ex_j_mkb != 0) {
                                        $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['mkb'] = $invoice_ex_j_mkb;

                                    }
                                }
                            }
                        }


                        echo '
								<div id="data">';
                        //if ((!empty($temp_arr) || !empty($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'])) && (($invoice_j[0]['summ'] + $invoice_j[0]['summins'] - $calculate_summ_inv) != 0)) {
                        if (!empty($temp_arr) || !empty($_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'])) {
                            echo '	
                                        <input type="hidden" id="invoice_id" name="client" value="' . $invoice_j[0]['id'] . '">
                                        <input type="hidden" id="client" name="client" value="' . $invoice_j[0]['client_id'] . '">
                                        <input type="hidden" id="client_insure" name="client_insure" value="' . $client_j[0]['insure'] . '">
                                        <input type="hidden" id="zapis_id2" name="zapis_id2" value="' . $invoice_j[0]['zapis_id'] . '">';
                            if ($invoice_j[0]['type'] != 88) {
                                echo '
                                        <input type="hidden" id="zapis_insure" name="zapis_insure" value="' . $sheduler_zapis[0]['insured'] . '">';
                            }else{
                                echo '
                                        <input type="hidden" id="zapis_insure" name="zapis_insure" value="0">';
                            }
                            echo '
                                        <input type="hidden" id="filial2" name="filial2" value="' . $invoice_j[0]['office_id'] . '">
                                        <input type="hidden" id="worker" name="worker" value="' . $invoice_j[0]['worker_id'] . '">
                                        <input type="hidden" id="t_number_active" name="t_number_active" value="' . $_SESSION['calculate_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['t_number_active'] . '">
                                        <input type="hidden" id="invoice_type" name="invoice_type" value="' . $invoice_j[0]['type'] . '">
                                        <input type="hidden" id="calculate_type" name="calculate_type" value="'.$invoice_j[0]['type'].'">';

                            echo '			
                                        <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                            echo '	
                                            <div id="errror" class="invoceHeader" style="">
                                                 <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Сумма наряда: <div id="calculateInvoice" style="">' . $invoice_j[0]['summ'] . '</div> руб.</div>
                                                    </div>';
                            if ($invoice_j[0]['type'] == 5) {
                                echo '
                                                    <div>
                                                        <div style="">Страховка наряда: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                    </div>';
                            }

                            echo '
                                                    <div>
                                                        <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">' . $invoice_j[0]['paid'] . '</div> руб.</div>
                                                    </div>';

                            /*echo '
                                                <div>
                                                    <div style="">Скидка: <div id="discountValue" class="calculateInvoice" style="color: rgb(255, 0, 198);">'.$invoice_j[0]['discount'].'</div><span  class="calculateInvoice" style="color: rgb(255, 0, 198);">%</span></div>
                                                </div>';*/
                            echo '
                                                </div> 
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">';

                            //!!! Доделать переделать (надо чтоб сумма бралась из массива наряда)
                            /*echo '
                                                    <div>
                                                        <div style="">Остаток для расчётов: <div class="calculateInvoice" style="color: #333;">' . (($invoice_j[0]['summ'] + $invoice_j[0]['summins']) - ($calculate_summ_inv)) . '</div> руб.</div>
                                                    </div>';*/
                            echo '
                                                    <div>
                                                        <div style="">Сумма расчёта: <div id="calculateSumm" style="">' . (($invoice_j[0]['summ'] + $invoice_j[0]['summins']) - ($calculate_summ_inv)) . '</div> руб.</div>
                                                    </div>';


                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                if ($invoice_j[0]['status'] != 9) {
                                    echo '
                                                    <div>
                                                        <div style="display: inline-block;">Осталось внести: <div id="calculateInvoice" style="">' . ($invoice_j[0]['summ'] - $invoice_j[0]['paid']) . '</div> руб.</div>
                                                    </div>
                                                    <div>
                                                        <div style="display: inline-block;"><a href="payment_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Оплатить</a></div>
                                                        <div style="display: inline-block;"><a href="certificate_payment_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Оплатить сертификатом</a></div>
                                                    </div>';
                                }
                            }

                            echo '
                                                </div>
                                                <div style="display: inline-block; vertical-align: top;">';

                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                echo '
                                                    <div style="color: red; ">
                                                        Наряд не оплачен
                                                    </div>';
                            }
                            if ($invoice_j[0]['status'] != 5) {
                                echo '
                                                    <div style="color: red; ">
                                                        Работа не закрыта
                                                    </div>';
                            }
                            /*if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {
                                if ($invoice_j[0]['closed_time'] == 0) {
                                    echo '
                                                    <div>
                                                        <div style="display: inline-block; color: red;">Наряд оплачен, но не закрыт. Если наряд <br><b>не страховой</b>, перепроведите оплаты или обратитесь к руководителю.</div>                                                    <!--<div style="display: inline-block;"><div class="b" onclick="alert(' . $invoice_j[0]['id'] . ');">Закрыть</div></div>-->
                                                    </div>';
                                } else {
                                    echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">Наряд закрыт</div>
                                                        <div style="display: inline-block;">' . date('d.m.y', strtotime($invoice_j[0]['closed_time'])) . '</div>
                                                    </div>';
                                }
                                /*echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block;"><a href="fl_calculation_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Внести расчётный лист</a></div>
                                                    </div>';*/

                            /*}*/
                            echo '
                                                </div>';
                            echo '
                                                <div style="margin-top: 5px; font-size: 110%;">
                                                   <b>Исполнитель: </b>
                                                   <input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="' . WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user_full', false) . '" class="who2"  autocomplete="off">
                                                   <!--!!!Изменить-->
                                                   <span class="button_tiny" style="font-size: 87%; cursor: pointer" onclick="changeWorkerInCalculate();"><i class="fa fa-check-square" style=" color: green;"></i> Изменить</span>
                                                   <ul id="search_result2" class="search_result2"></ul><br>
                                 
                                                </div>';
                            echo '
                                            </div>';

                            echo '
                                             <div id="calculate_rezult_head" style="float: none; width: 850px;">
                                                <div class="cellsBlock">
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center;">
                                                        <i><b>-</b></i>
                                                    </div>
                                                    <div class="cellText2" style="font-size: 100%; text-align: center;">
                                                        <i><b>Наименование</b></i>
                                                    </div>
                                                    <div class="cellName" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>Всего, руб.</b></i>
                                                    </div>
                                                    <div class="cellName" style="font-size: 80%; text-align: center;">
                                                        <!--<div id="percent_cats" class="settings_text" onclick="contextMenuShow(0, ' . $invoice_j[0]['type'] . ', event, \'percent_cats\');">-->
                                                            <i><b>Категория</b></i>
                                                        <!--</div>-->
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                        <i><b>-</b></i>
                                                    </div>
                                                </div>
                                             </div>
                                             <div id="calculate_rezult" style="float: none; width: 850px;">
                                             </div>';





                            echo '	
                                            <div class="cellsBlock" style="font-size: 90%;" >
                                                <div class="cellText2" style="padding: 2px 4px;">
                                                    <div style="vertical-align: middle; font-size: 11px;">
                                                        <div style="text-align: left; float: left;">	
                                                            <!--<input type="button" class="b" value="Сбросить изменения" onclick="showCalculateAdd(' . $invoice_j[0]['type'] . ', \'reset\')">-->
                                                        </div>
                                                        <div style="text-align: right;">';
                            if ($god_mode || ($finances['see_all'] == 1) || (($invoice_j[0]['summ'] == $invoice_j[0]['paid']) && ($invoice_j[0]['status'] == 5))) {
                                echo '
                                                            <input type="button" class="b" value="Сохранить" onclick="showCalculateAdd(' . $invoice_j[0]['type'] . ', \'add\')">';
                            }
                            echo '
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
                                                    Итого:-->';
                            //if (($summ_inv != $invoice_j[0]['summ']) || ($summins_inv != $invoice_j[0]['summins'])) {
                                /*echo '<br>
                                    <span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
                            //}

                            echo '				
                                                        
                                                <!--</div>
                                                <div class="cellName" style="padding: 2px 4px;">
                                                    <div>
                                                        <div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">' . $summ_inv . '</div> руб.</div>
                                                    </div>-->';
                            if ($invoice_j[0]['type'] == 5) {
                                echo '
                                                    <!--<div>
                                                        <div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">' . $summins_inv . '</div> руб.</div>
                                                    </div>-->';
                            }
                            echo '
                                                </div>';



                            echo '
                                            </div>';
                            echo '			
                                            </div>';
                            echo '
                                        </div>';
                        }else{
                            echo '<span style="color: red; font-weight: bold;">Вся сумма уже распределена.</span>
                            <input type="hidden" id="invoice_type" name="invoice_type" value="'.$invoice_j[0]['type'].'">
                            <input type="hidden" id="calculate_type" name="calculate_type" value="'.$invoice_j[0]['type'].'">
                            <input type="hidden" id="calculate_type" name="calculate_type" value="'.$invoice_j[0]['type'].'">
                            <input type="hidden" id="zapis_insure" name="zapis_insure" value="0">
                            <input type="hidden" id="filial2" name="filial2" value="'.$invoice_j[0]['office_id'].'">
                            <input type="hidden" id="worker" name="worker" value="'.$invoice_j[0]['worker_id'].'">';
                        }
                        echo '
                                    </div>
								</div>
								<!-- Подложка только одна -->
                                <div id="overlay"></div>
                                
                                <script>
                                
                                    $(document).ready(function(){

                                        //получим активный зуб
                                        var t_number_active = $("#t_number_active").val();
                                        
                                        if (t_number_active != 0){
                                            colorizeTButton (t_number_active);
                                        }
                                        
                                        //Кликанье по зубам в счёте
                                        $(".sel_tooth").live("click", function() {
                                            //получам номер зуба
                                            var t_number = Number(this.innerHTML);
                                            
                                            addInvoiceInSession(t_number);
                                        });

                                        //Кликанье по полости в счёте
                                        $(".sel_toothp").click(function(){
                                            
                                            //получам номер полости
                                            var t_number = 99;
                                            
                                            addInvoiceInSession(t_number);
                                        });
                                        
                                        fillCalculateRez();
                                    });
                                    
                                </script>
                                
                                
                                ';
                    /*}else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }*/
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
        }else{
            echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
        }
    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>