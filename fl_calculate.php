<?php

//fl_calculate.php
//Расчет

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if ($_GET){
            if (isset($_GET['id'])){

                $calculate_j = SelDataFromDB('fl_journal_calculate', $_GET['id'], 'id');
                //var_dump($calculate_j);

                if ($calculate_j != 0){

                    if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])){

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


						$sheduler_zapis = array();
						$calculate_ex_j = array();
						//$invoice_ex_j_mkb = array();
                        $mat_cons_j_ex = array();

                        $invoice_j = array();

						$client_j = SelDataFromDB('spr_clients', $calculate_j[0]['client_id'], 'user');
						//var_dump($client_j);

                        $msql_cnnct = ConnectToDB ();
						
						$query = "SELECT * FROM `zapis` WHERE `id`='".$calculate_j[0]['zapis_id']."'";

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

                        //Наряд данные
						$query = "SELECT * FROM `journal_invoice` WHERE `id`='".$calculate_j[0]['invoice_id']."'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($invoice_j, $arr);
							}
						}else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
						//if ($client !=0){
						if (!empty($sheduler_zapis)){
                            if (!empty($invoice_j)){
                                //var_dump($invoice_j);
						
                                //сортируем зубы по порядку
                                //ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);

                                //var_dump($_SESSION);
                                //var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
                                //var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['mkb']);

                                if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
                                else $month = $sheduler_zapis[0]['month'];


                                //Категории процентов
                                $percent_cats_j = getAllPercentCats($invoice_j[0]['type']);

                                echo '
                                <div id="status">
                                    <header>
    
                                        <h2>Расчетный лист #'.$_GET['id'].'';

                                /*if (($finances['edit'] == 1) || $god_mode){
                                    if ($calculate_j[0]['status'] != 9){
                                        echo '
                                                    <a href="invoice_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                    }
                                    if (($calculate_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                                        echo '
                                            <a href="#" onclick="Ajax_reopen_invoice('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                                    }
                                }*/
                                //if (($finances['close'] == 1) || $god_mode){
                                    //if ($calculate_j[0]['status'] != 9){
                                        echo '
                                                    <span class="info" style="font-size: 100%; cursor: pointer;" title="Удалить" onclick="fl_deleteCalculateItem('.$_GET['id'].', '.$calculate_j[0]['client_id'].', '.$calculate_j[0]['invoice_id'].');" ><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
                                    //}
                                //}

                                echo '
                                            </h2>
                                            <div id="tabel_info">';

                                $query = "SELECT `tabel_id` AS total FROM `fl_journal_tabels_ex` WHERE `calculate_id` = '{$_GET['id']}' LIMIT 1";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                $number = mysqli_num_rows($res);

                                $arr = mysqli_fetch_assoc($res);

                                if ($number > 0){
                                    echo "<span style='font-size: 80%; color: red;'>Добавлен в <a href='fl_tabel.php?id={$arr['total']}' class='ahref'>Табель #{$arr['total']}</a></span>";
                                }

                                echo '
                                            </div>';

                                /*if ($calculate_j[0]['status'] == 9){
                                    echo '<i style="color:red;">Наряд удалён (заблокирован).</i><br>';
                                }*/


                                echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:80%;  color: #555;">';

                                if (($calculate_j[0]['create_time'] != 0) || ($calculate_j[0]['create_person'] != 0)){
                                    echo '
                                                        Добавлен: '.date('d.m.y H:i' ,strtotime($calculate_j[0]['create_time'])).'<br>
                                                        Автор: '.WriteSearchUser('spr_workers', $calculate_j[0]['create_person'], 'user', true).'<br>';
                                }else{
                                    echo 'Добавлен: не указано<br>';
                                }
                                if (($calculate_j[0]['last_edit_time'] != 0) || ($calculate_j[0]['last_edit_person'] != 0)){
                                    echo '
                                                        Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($calculate_j[0]['last_edit_time'])).'<br>
                                                        Кем: '.WriteSearchUser('spr_workers', $calculate_j[0]['last_edit_person'], 'user', true).'';
                                }
                                echo '
                                                </span>
                                            </div>';



                                echo '
                                        </header>';
                                echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">	
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Посещение</li>';


                                $t_f_data_db = array();
                                $cosmet_data_db = array();

                                $back_color = '';

                                $summ = 0;
                                $summins = 0;

                                //if(($sheduler_zapis[0]['enter'] != 8) || ($scheduler['see_all'] == 1) || $god_mode){
                                    if ($sheduler_zapis[0]['enter'] == 1){
                                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                                    }elseif($sheduler_zapis[0]['enter'] == 9){
                                        $back_color = 'background-color: rgba(239,47,55, .7);';
                                    }elseif($sheduler_zapis[0]['enter'] == 8){
                                        $back_color = 'background-color: rgba(137,0,81, .7);';
                                    }else{
                                        //Если оформлено не на этом филиале
                                        if($sheduler_zapis[0]['office'] != $sheduler_zapis[0]['add_from']){
                                            $back_color = 'background-color: rgb(119, 255, 250);';
                                        }else{
                                            $back_color = 'background-color: rgba(255,255,0, .5);';
                                        }
                                    }

                                    $dop_img = '';

                                    if ($sheduler_zapis[0]['insured'] == 1){
                                        $dop_img .= '<img src="img/insured.png" title="Страховое"> ';
                                    }
                                    if ($sheduler_zapis[0]['pervich'] == 1){
                                        $dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
                                    }
                                    if ($sheduler_zapis[0]['noch'] == 1){
                                        $dop_img .= '<img src="img/night.png" title="Ночное"> ';
                                    }

                                    /*echo '
                                            <li class="cellsBlock" style="width: auto;">';

                                    echo '
                                                <div class="cellName" style="position: relative; '.$back_color.'">';
                                    $start_time_h = floor($sheduler_zapis[0]['start_time']/60);
                                    $start_time_m = $sheduler_zapis[0]['start_time']%60;
                                    if ($start_time_m < 10) $start_time_m = '0'.$start_time_m;
                                    $end_time_h = floor(($sheduler_zapis[0]['start_time']+$sheduler_zapis[0]['wt'])/60);
                                    if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
                                    $end_time_m = ($sheduler_zapis[0]['start_time']+$sheduler_zapis[0]['wt'])%60;
                                    if ($end_time_m < 10) $end_time_m = '0'.$end_time_m;

                                    echo
                                        '<b>'.$sheduler_zapis[0]['day'].' '.$monthsName[$month].' '.$sheduler_zapis[0]['year'].'</b><br>'.
                                        $start_time_h.':'.$start_time_m.' - '.$end_time_h.':'.$end_time_m;

                                    echo '
                                                    <div style="position: absolute; top: 1px; right: 1px;">'.$dop_img.'</div>';
                                    echo '
                                                </div>';
                                    echo '
                                                <div class="cellName">';
                                    echo
                                                    'Пациент <br /><b>'.WriteSearchUser('spr_clients',  $sheduler_zapis[0]['patient'], 'user', true).'</b>';
                                    echo '
                                                </div>';
                                    echo '
                                                <div class="cellName">';

                                    $offices = SelDataFromDB('spr_filials', $sheduler_zapis[0]['office'], 'offices');
                                    echo '
                                                    Филиал:<br>'.
                                                $offices[0]['name'];
                                    echo '
                                                </div>';
                                    echo '
                                                <div class="cellName">';
                                    echo
                                                    $sheduler_zapis[0]['kab'].' кабинет<br>'.'Врач: <br><b>'.WriteSearchUser('spr_workers', $sheduler_zapis[0]['worker'], 'user', true).'</b>';
                                    echo '
                                                </div>';
                                    echo '
                                                <div class="cellName">';
                                    echo  '
                                                    <b><i>Описание:</i></b><br><div style="text-overflow: ellipsis; overflow: hidden; white-space: inherit; display: block; width: 120px;" title="'.$sheduler_zapis[0]['description'].'">'.$sheduler_zapis[0]['description'].'</div>';
                                    echo '
                                                </div>
                                            </li>';*/

                                    // !!! **** тест с записью
                                    include_once 'showZapisRezult.php';

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

                                    //echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);
                                    echo showZapisRezult($sheduler_zapis, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, false, false);

                                    echo '
                                        </ul>';
                                //}

                                echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">	
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Наряд</li>';

                                $invoiceAll_str = '';
                                $invoiceClose_str = '';

                                foreach ($invoice_j as $invoice_item) {

                                    $invoiceTemp_str = '';

                                    //Отметка об объеме оплат
                                    $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;"></i>';

                                    if ($invoice_item['summ'] == $invoice_item['paid']) {
                                        $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i>';
                                    }

                                    $invoiceTemp_str .= '
                                    <li class="cellsBlock" style="width: auto;">';
                                    $invoiceTemp_str .= '
                                        <a href="invoice.php?id=' . $invoice_item['id'] . '" class="cellName ahref" style="position: relative;">
                                            <b>Наряд #' . $invoice_item['id'] . '</b><br>
                                            <span style="font-size:80%;  color: #555;">';

                                    if (($invoice_item['create_time'] != 0) || ($invoice_item['create_person'] != 0)) {
                                        $invoiceTemp_str .= '
                                                    Добавлен: ' . date('d.m.y H:i', strtotime($invoice_item['create_time'])) . '<br>
                                                    <!--Автор: ' . WriteSearchUser('spr_workers', $invoice_item['create_person'], 'user', true) . '<br>-->';
                                    } else {
                                        $invoiceTemp_str .= 'Добавлен: не указано<br>';
                                    }
                                    if (($invoice_item['last_edit_time'] != 0) || ($invoice_item['last_edit_person'] != 0)) {
                                        $invoiceTemp_str .= '
                                                    Последний раз редактировался: ' . date('d.m.y H:i', strtotime($invoice_item['last_edit_time'])) . '<br>
                                                    <!--Кем: ' . WriteSearchUser('spr_workers', $invoice_item['last_edit_person'], 'user', true) . '-->';
                                    }
                                    $invoiceTemp_str .= '
                                            </span>
                                            <span style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</span>
                                        </a>
                                        <div class="cellName">
                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                Сумма:<br>
                                                <span class="calculateInvoice" style="font-size: 13px">' . $invoice_item['summ'] . '</span> руб.
                                            </div>';
                                    if ($invoice_item['summins'] != 0) {
                                        $invoiceTemp_str .= '
                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                Страховка:<br>
                                                <span class="calculateInsInvoice" style="font-size: 13px">' . $invoice_item['summins'] . '</span> руб.
                                            </div>';
                                    }
                                    $invoiceTemp_str .= '
                                        </div>';

                                    $invoiceTemp_str .= '
                                        <div class="cellName">
                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                Оплачено:<br>
                                                <span class="calculateInvoice" style="font-size: 13px; color: #333;">' . $invoice_item['paid'] . '</span> руб.
                                            </div>';
                                    /*if ($invoice_item['summ'] != $invoice_item['paid']) {
                                        $invoiceTemp_str .= '
                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                Осталось <a href="payment_add.php?invoice_id='.$invoice_item['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                                <span class="calculateInvoice" style="font-size: 13px">'.($invoice_item['summ'] - $invoice_item['paid']).'</span> руб.
                                            </div>';
                                    }*/

                                    $invoiceTemp_str .= '
                                        </div>';
                                    $invoiceTemp_str .= '
                                    </li>';

                                    if ($invoice_item['status'] != 9) {
                                        $invoiceAll_str .= $invoiceTemp_str;
                                    } else {
                                        $invoiceClose_str .= $invoiceTemp_str;
                                    }

                                }

                                if (strlen($invoiceAll_str) > 1){
                                    echo $invoiceAll_str;
                                }else{
                                    echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;">Нет нарядов</li>';
                                }

                                //Удалённые
                                /*if ((strlen($invoiceClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                                    echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                                    echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы наряды</li>';
                                    echo $invoiceClose_str;
                                    echo '</div>';
                                }*/


                                echo '
								</ul>';

                                //Расчёт

                                //$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['id']."'";
                                //!!! пробуем JOIN
                                //$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['id']."';";
                                $query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `calculate_id`='".$_GET['id']."';";
                                //var_dump($query);

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                $number = mysqli_num_rows($res);
                                if ($number != 0){
                                    while ($arr = mysqli_fetch_assoc($res)){
                                        if (!isset($calculate_ex_j[$arr['ind']])){
                                            $calculate_ex_j[$arr['ind']] = array();
                                            array_push($calculate_ex_j[$arr['ind']], $arr);
                                        }else{
                                            array_push($calculate_ex_j[$arr['ind']], $arr);
                                        }
                                    }
                                }else {
                                    //$calculate_ex_j = 0;
                                }
                                //var_dump ($calculate_ex_j);

                                //сортируем зубы по порядку
                                if ($calculate_ex_j != 0){
                                    ksort($calculate_ex_j);
                                }
                                //var_dump ($calculate_ex_j);


                                //Затраты на материалы
                                $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                                LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                                ON jimc.id = jimcex.inv_mat_cons_id
                                WHERE jimc.invoice_id = '".$calculate_j[0]['invoice_id']."';";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                $number = mysqli_num_rows($res);

                                if ($number != 0) {
                                    while ($arr = mysqli_fetch_assoc($res)) {

                                        //array_push($mat_cons_j, $arr);

                                        if (!isset($mat_cons_j_ex['data'])){
                                            $mat_cons_j_ex['data'] = array();
                                        }

                                        if (!isset($mat_cons_j_ex['data'][$arr['inv_pos_id']])){
                                            $mat_cons_j_ex['data'][$arr['inv_pos_id']] = $arr['summ'];
                                        }

                                        $mat_cons_j_ex['create_person'] = $arr['create_person'];
                                        $mat_cons_j_ex['create_time'] = $arr['create_time'];
                                        $mat_cons_j_ex['all_summ'] = $arr['all_summ'];
                                        $mat_cons_j_ex['descr'] = $arr['descr'];
                                        $mat_cons_j_ex['id'] = $arr['mc_id'];
                                    }
                                } else {

                                }

                                //var_dump($mat_cons_j_ex);


                                echo '
                                    <div id="data">';

                                echo '			
                                        <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                                echo '	
                                            <div id="errror" class="invoceHeader" style="">
                                                 <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        Кому: <b>'.WriteSearchUser('spr_workers', $calculate_j[0]['worker_id'], 'user', true).'</b>
                                                    </div>';
                                /*if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
                                                    <div>
                                                        <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $calculate_j[0]['summins'] . '</div> руб.</div>
                                                    </div>';
                                }*/
                                /*echo '
                                                    <div>
                                                        <div style="">Скидка: <div id="discountValue" class="calculateInvoice" style="color: rgb(255, 0, 198);">'.$calculate_j[0]['discount'].'</div><span  class="calculateInvoice" style="color: rgb(255, 0, 198);">%</span></div>
                                                    </div>';*/
                                echo '
                                                </div> 
                                                <!--<div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">'.$calculate_j[0]['paid'].'</div> руб.</div>
                                                    </div>';
                                /*if ($calculate_j[0]['summ'] != $calculate_j[0]['paid']) {
                                    if ($calculate_j[0]['status'] != 9) {
                                        echo '
                                                    <div>
                                                        <div style="display: inline-block;">Осталось внести: <div id="calculateInvoice" style="">' . ($calculate_j[0]['summ'] - $calculate_j[0]['paid']) . '</div> руб.</div>
                                                    </div>
                                                    <div>
                                                        <div style="display: inline-block;"><a href="payment_add.php?invoice_id=' . $calculate_j[0]['id'] . '" class="b">Оплатить</a></div>
                                                        <div style="display: inline-block;"><a href="certificate_payment_add.php?invoice_id='.$calculate_j[0]['id'].'" class="b">Оплатить сертификатом</a></div>
                                                    </div>';
                                    }
                                }*/
                                /*if ($calculate_j[0]['summ'] != $calculate_j[0]['paid']) {
                                    echo '
                                                    <div style="color: red; ">
                                                        Наряд не закрыт (оплачен не полностью)
                                                    </div>';
                                }*/
                                /*if ($calculate_j[0]['summ'] == $calculate_j[0]['paid']) {
                                    if ($calculate_j[0]['closed_time'] == 0){
                                        echo '
                                                    <div>
                                                        <div style="display: inline-block; color: red;">Наряд оплачен, но не закрыт. Если наряд <br><b>не страховой</b>, перепроведите оплаты или обратитесь к руководителю.</div>                                                    <!--<div style="display: inline-block;"><div class="b" onclick="alert('.$calculate_j[0]['id'].');">Закрыть</div></div>-->
                                                    </div>';
                                    }else{
                                        echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">Наряд закрыт</div>
                                                        <div style="display: inline-block;">'.date('d.m.y', strtotime($calculate_j[0]['closed_time'])).'</div>
                                                    </div>';
                                    }
                                    echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block;"><a href="fl_calculation_add3.php?invoice_id=' . $calculate_j[0]['id'] . '" class="b">Внести расчётный лист</a></div>
                                                    </div>';

                                }*/
                                echo '
                                                </div>-->';


                                echo '
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <!--<div style="">Расчет: <div id="calcValue" class="calculateInvoice" style="color: rgb(255, 0, 198);"></div> руб.</div>
                                                        <div style="font-size: 10px;"></div>-->
                                                    </div>
                                                </div>';


                                echo '
                                            </div>';




                                echo '
                                            <div id="invoice_rezult" style="float: none; width: 850px;">';

                                echo '
                                                <div class="cellsBlock">
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center;">';
                                if ($sheduler_zapis[0]['type'] == 5){
                                    echo '
                                                        <i><b>Зуб</b></i>';
                                }
                                if ($sheduler_zapis[0]['type'] == 6){
                                    echo '
                                                        <i><b>№</b></i>';
                                }
                                echo '
                                                    </div>
                                                    <div class="cellText2" style="font-size: 100%; text-align: center;">
                                                        <i><b>Наименование</b></i>
                                                    </div>';
                                /*if ($sheduler_zapis[0]['type'] == 5){
                                    echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
                                                        <i><b>Страх.</b></i>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center;">
                                                        <i><b>Сог.</b></i>
                                                    </div>';
                                }*/
                                echo '
                                                    <!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>Цена, руб.</b></i>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        <i><b>Коэфф.</b></i>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        <i><b>Кол-во</b></i>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        <i><b>Скидка</b></i>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        <i><b>Гар.</b></i>
                                                    </div>-->
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>Цена из наряда, руб.</b></i>
                                                    </div>';

                                //if (($finances['see_all'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])) {
                                if (($finances['see_all'] == 1) || $god_mode) {
                                    echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>Вычет затрат на материалы, руб.</b></i>
                                                    </div>';

                                    echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>% работа/материал</b></i>
                                                    </div>';
                                }

                                if (($finances['see_all'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])) {
                                    echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 120px; min-width: 120px; max-width: 120px;">
                                                        <i><b>Расчёт, руб.</b></i>
                                                    </div>';
                                }

                                echo '
                                                    <div class="cellName" style="font-size: 80%; text-align: center;">
                                                        <div>
                                                            <i><b>Категория</b></i>
                                                        </div>
                                                    </div>
                                                </div>';



                                if (!empty ($calculate_ex_j)) {
                                    foreach ($calculate_ex_j as $ind => $calculate_data) {

                                        //var_dump($calculate_data);

                                        echo '
                                            <div class="cellsBlock">
                                                <div class="cellCosmAct toothInInvoice" style="text-align: center;">';
                                        if ($ind == 99) {
                                            echo 'П';
                                        } else {
                                            echo $ind;
                                        }
                                        echo '
                                                </div>';

                                        //Диагноз
                                        if ($sheduler_zapis[0]['type'] == 5) {

                                            /*if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])){
                                                echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
                                                            <b>';
                                                if ($ind == 99){
                                                    echo '<i>Полость</i>';
                                                }else{
                                                    echo '<i>Зуб</i>: '.$ind;
                                                }
                                                echo '
                                                            </b>. <i>Диагноз</i>: ';

                                                foreach ($invoice_ex_j_mkb[$ind] as $mkb_key => $mkb_data_val){
                                                    $rez = array();
                                                    //$rezult2 = array();

                                                    $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                                    $number = mysqli_num_rows($res);
                                                    if ($number != 0){
                                                        while ($arr = mysqli_fetch_assoc($res)){
                                                            $rez[$mkb_data_val['mkb_id']] = $arr;
                                                        }
                                                    }else{
                                                        $rez = 0;
                                                    }
                                                    if ($rez != 0){
                                                        foreach ($rez as $mkb_name_val){
                                                            echo '
                                                                <div class="mkb_val" style="background: rgb(239, 255, 255); border: 1px dotted #bababa;"><b>'.$mkb_name_val['code'].'</b> '.$mkb_name_val['name'].'

                                                                </div>';
                                                        }
                                                    }else{
                                                        echo '<div class="mkb_val">???</div>';
                                                    }

                                                }

                                                echo '
                                                        </div>
                                                    </div>';
                                            }*/

                                            /*if (isset($invoice_ex_j_mkb[''])){
                                                echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.14) none repeat scroll 0% 0%;">
                                                            <b>';
                                                if ($ind == 99){
                                                    echo '<i>Полость</i>';
                                                }else{
                                                    echo '<i>Зуб</i>: '.$ind;
                                                }
                                                echo '
                                                            </b>. <i>Диагноз</i>: '.$calculate_data[0]['mkb_id'].'
                                                        </div>
                                                    </div>';
                                            }*/

                                        }

                                        foreach ($calculate_data as $item) {
                                            //var_dump($item);

                                            //!!!Коэффициенты %
                                            //$percent_cat = getPercentCat ();
                                            //$percent_cat_pers = getPercentCatPers ();

                                            //часть прайса
                                            //if (!empty($calculate_data)){

                                            //foreach ($calculate_data as $key => $items){
                                            echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                    <!--<div class="cellCosmAct" style="">
                                                        -
                                                    </div>-->
                                                        <div class="cellText2" style="">';

                                            //Хочу имя позиции в прайсе
                                            $arr = array();
                                            $rez = array();

                                            $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$item['price_id']}'";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                            $number = mysqli_num_rows($res);
                                            if ($number != 0) {
                                                while ($arr = mysqli_fetch_assoc($res)) {
                                                    array_push($rez, $arr);
                                                }
                                                $rezult2 = $rez;
                                            } else {
                                                $rezult2 = 0;
                                            }

                                            if ($rezult2 != 0) {

                                                echo $rezult2[0]['name'];

                                                //Узнать цену
                                                /*$arr = array();
                                                $rez = array();
                                                $price = 0;
                                                $stoim_item = 0;
                                                //Для отбора цены по времени создания наряда
                                                $price_arr = array();



                                                $query = "SELECT `date_from`, `price` FROM `spr_priceprices` WHERE `item`='{$item['price_id']}' ORDER BY `date_from` DESC, `create_time`";

                                                if ($item['insure'] != 0){
                                                    $query = "SELECT `date_from`, `price` FROM `spr_priceprices_insure` WHERE `item`='{$item['price_id']}' AND `insure`='".$item['insure']."' ORDER BY `date_from` DESC, `create_time`";
                                                }

                                                $res = mysql_query($query) or die(mysql_error().' -> '.$query);
                                                $number = mysql_num_rows($res);
                                                if ($number != 0){
                                                    //если кол-во цен == 1
                                                    if ($number == 1){
                                                        $arr = mysql_fetch_assoc($res);
                                                        $price = $arr['price'];
                                                    //если > 1
                                                    }else{
                                                        while ($arr = mysql_fetch_assoc($res)){
                                                            $price_arr[$arr['date_from']] = $arr;
                                                        }
                                                        //обратная сортировка
                                                        krsort($price_arr);
                                                        //var_dump($price_arr);
                                                        //var_dump(strtotime($calculate_j[0]['create_time']));

                                                        foreach($price_arr as $date_from => $value_arr){
                                                            if (strtotime($calculate_j[0]['create_time']) > $date_from){
                                                                $price = $value_arr['price'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    $price = '?';
                                                }*/

                                            } else {
                                                echo '?';
                                            }

                                            echo '
                                                    </div>';

                                            $price = $item['price'];

                                            /*if ($sheduler_zapis[0]['type'] == 5){
                                                if ($item['insure'] != 0){
                                                    //Написать страховую
                                                    $insure_j = SelDataFromDB('spr_insure', $item['insure'], 'id');

                                                    if ($insure_j != 0){
                                                        $insure_name = $insure_j[0]['name'];
                                                    }else{
                                                        $insure_name = '?';
                                                    }
                                                }else{
                                                    $insure_name = 'нет';
                                                }
                                            }*/

                                            /*if ($sheduler_zapis[0]['type'] == 5){
                                                echo '
                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;">
                                                    '.$insure_name.'
                                                </div>';


                                                if ($item['insure'] != 0){
                                                    if ($item['insure_approve'] == 1){
                                                        echo '
                                                            <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                                <i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
                                                            </div>';
                                                    }else{
                                                        echo '
                                                        <div class="cellCosmAct" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;">
                                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                                        </div>';
                                                    }

                                                }else{
                                                    echo '
                                                    <div class="cellCosmAct" insureapprove="'.$item['insure_approve'].'" style="font-size: 70%; text-align: center;">
                                                        -
                                                    </div>';
                                                }
                                            }*/

                                            echo '
                                                    <!--<div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <b>' . $price . '</b>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        ' . $item['spec_koeff'] . '
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        <b>' . $item['quantity'] . '</b>
                                                    </div>
                                                    <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                        ' . $item['discount'] . '
                                                    </div>-->
                                                    <!--<div class="cellCosmAct settings_text" guarantee="' . $item['guarantee'] . '" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">';
                                            if ($item['guarantee'] != 0) {
                                                echo '
                                                            <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                            } else {
                                                echo '-';
                                            }
                                            echo '
                                                    </div>-->
                                                    <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">';

                                            //вычисляем стоимость
                                            //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                            //$stoim_item = $item['quantity'] * $price;
                                            $stoim_item = $price;

                                            //с учетом скидки акции
                                            if ($item['insure'] == 0) {
                                                //$stoim_item = $stoim_item - ($stoim_item * $calculate_j[0]['discount'] / 100);
                                                $stoim_item = $stoim_item - ($stoim_item * $item['discount'] / 100);
                                                //$stoim_item = round($stoim_item/10) * 10;
                                                $stoim_item = round($stoim_item);
                                            }
                                            //$stoim_item = round($stoim_item/10) * 10;




                                            //!!! надо сделать чтоб в базу попадало сразу как надо, а то при пересчете пиздец какой-то, так быть не должно


                                            $stoim_item = $price;

                                            echo $stoim_item;


                                            //Общая стоимость
                                            /*if ($item['guarantee'] == 0){
                                                if ($item['insure'] != 0){
                                                    if ($item['insure_approve'] != 0){
                                                        $summins += $stoim_item;
                                                    }
                                                }else{
                                                    $summ += $stoim_item;
                                                }
                                            }*/


                                            echo '
                                                    </div>';

                                            if (($finances['see_all'] == 1) || $god_mode) {
                                                echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <i><b>';
                                                if (!empty($mat_cons_j_ex['data'])) {
                                                    if (isset($mat_cons_j_ex['data'][$item['inv_pos_id']])) {
                                                        echo '<span style="color: red;">' . -$mat_cons_j_ex['data'][$item['inv_pos_id']] . '</span>';

                                                        $stoim_item = $stoim_item - $mat_cons_j_ex['data'][$item['inv_pos_id']];

                                                    } else {
                                                        echo 0;
                                                    }
                                                } else {
                                                    echo 0;
                                                }
                                                echo '
                                                        </b></i>
                                                    </div>';

                                            }

                                            //$percents_j = SelDataFromDB('fl_spr_percents', $item['percent_cats'], 'id');
                                            //var_dump($item['percent_cats']);

                                            //if (($finances['see_all'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])) {
                                            if (($finances['see_all'] == 1) || $god_mode) {

                                                echo '
                                                        <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                            <i><b>'.$item['work_percent'].' / '.$item['material_percent'].'</b></i>
                                                        </div>';

                                            }

                                            if (($finances['see_all'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])) {

                                                echo '
                                                        <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 120px; min-width: 120px; max-width: 120px;">
                                                        <b>';

                                                echo calculateResult($stoim_item, $item['work_percent'], $item['material_percent']);

                                                $summ += calculateResult($stoim_item, $item['work_percent'], $item['material_percent']);

                                                echo '
                                                        </b>
                                                        </div>';
                                            }

                                            echo '
                                                    <div class="cellName" style="text-align: center;">
                                                        <div>';

                                            if ($item['percent_cats'] > 0) {
                                                echo '<i>' . $percent_cats_j[$item['percent_cats']]['name'] . '</i>';
                                            }else{
                                                echo '<i style="color: red; font-size: 100%;">Ошибка #15</i><br>';
                                            }

                                            echo '
                                                        </div>
                                                    </div>
                                                </div>';
                                        }
                                        echo '
                                            </div>';
                                    }
                                }else{
                                    echo '<span class="query_neok">Ошибка в расчёте.</span>';
                                }

                                echo '	
                                            <div class="cellsBlock" style="font-size: 90%;" >
                                                <!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
                                                    Итого:-->';
                                //if (($summ != $calculate_j[0]['summ']) || ($summins != $calculate_j[0]['summins'])){
                                    /*echo '<br>
                                        <span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
                                //}

                                echo '				
                                                        
                                                <!--</div>
                                                <div class="cellName" style="padding: 2px 4px;">
                                                    <div>
                                                        <div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">'.$summ.'</div> руб.</div>
                                                    </div>-->';
                                if ($sheduler_zapis[0]['type'] == 5){
                                    echo '
                                                    <!--<div>
                                                        <div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">'.$summins.'</div> руб.</div>
                                                    </div>-->';
                                }

                                    echo '
                                                </div>';
                                if (($finances['see_all'] == 1) || $god_mode || ($calculate_j[0]['worker_id'] == $_SESSION['id'])) {
                                    echo '                
                                                <div class="invoceHeader" style="">
                                                    <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                        <!--<li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                            Сумма наряда: <div id="calculateInvoice" style="">' . $calculate_j[0]['summ'] . '
                                                        </li>-->

                                                       <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                            Сумма расчёта <div id="calculateInvoice" style="">' . $calculate_j[0]['summ'] . '</div> руб.
                                                       </li>
                                                       <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                            <!--<input type="button" class="b" value="Перерасчёт" onclick="fl_reloadPercentsCalculate()">-->
                                                       </li>

                                                    </div>
                                                    </ul>
                                                </div>';
                                }


                                if (!empty($mat_cons_j_ex)) {
                                    if (!empty($mat_cons_j_ex['data'])) {
                                        echo '
                                                <div class="invoceHeader" style="">
                                                    <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                        <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                            Затраты на материалы:
                                                        </li>';
                                        //foreach ($mat_cons_j_ex['data'] as $mat_cons_item) {

                                        echo '
                                                        <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                                        echo '
                                                            <a href="#" class="cellOrder ahref" style="position: relative;">
                                                                <b>Расход #' . $mat_cons_j_ex['id'] . '</b> от ' . date('d.m.y', strtotime($mat_cons_j_ex['create_time'])) . '<br>
                                                                <span style="font-size:80%;  color: #555;">';

                                        if (($mat_cons_j_ex['create_time'] != 0) || ($mat_cons_j_ex['create_person'] != 0)) {
                                            echo '
                                                                    Добавлен: ' . date('d.m.y H:i', strtotime($mat_cons_j_ex['create_time'])) . '<br>
                                                                    <!--Автор: ' . WriteSearchUser('spr_workers', $mat_cons_j_ex['create_person'], 'user', true) . '<br>-->';
                                        } else {
                                            echo 'Добавлен: не указано<br>';
                                        }
                                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                            echo'
                                                                    Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                                                    <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                        }*/
                                        echo '
                                                                </span>
                                                                
                                                            </a>
                                                            <div class="cellName">
                                                                ' . $mat_cons_j_ex['descr'] . '<br>
                                                            </div>
                                                            <div class="cellName">
                                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                    Сумма:<br>
                                                                    <span class="calculateOrder" style="font-size: 13px">' . $mat_cons_j_ex['all_summ'] . '</span> руб.
                                                                </div>
                                                            </div>
                                                            <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="fl_deleteMaterialConsumption(' . $mat_cons_j_ex['id'] . ', ' . $invoice_j[0]['id'] . ');">
                                                                <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                            </div>
                                                            ';
                                        echo '
                                                        </li>';
                                        //}

                                        echo '
                                                    </ul>
                                                </div>';
                                    }
                                }

                                 echo '
                                            </div>';
                                echo '			
                                            </div>';
                                echo '
                                        </div>';
                                echo '
                                    </div>';

                                echo '
		                            <div id="doc_title">РЛ #'.$_GET['id'].' - Асмедика</div>';
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
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
        }else{
            echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
        }
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>