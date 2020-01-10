<?php

//abonement.php
//карточка абонемента

	require_once 'header.php';

	if ($enter_ok){

        require_once 'permissions.php';

        //!!! переделай тут права!!!
        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || ($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {

            include_once 'DBWork.php';
            include_once 'functions.php';

            echo '

                <!DOCTYPE html>
                <html>
                <head>
                    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
                    <meta name="description" content=""/>
                    <meta name="keywords" content="" />
                    <meta name="author" content="" />
                    
                    <title>Асмедика</title>
                    
                    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
                    
                    <!-- Font Awesome -->
			        <link rel="stylesheet" href="css/font-awesome.css">
                    
                    <link rel="stylesheet" href="css/style.css" type="text/css" />
                    
                    <!--Для печати-->
                    <link rel="stylesheet" href="css/paper.css">
                    <style>@page { size: A4 }</style>
                    
                    <!--для печати-->	
                    <style type="text/css" media="print">
                      div.no_print {display: none; }
                      .never_print_it {display: none; }
                      #scrollUp {display: none; }
                    </style> 
        
                </head>';


            $paper_format = 'A5 landscape';

            if (isset($_GET['format'])){
                if ($_GET['format'] == 'A4') {
                    $paper_format = 'A4';
                }
            }

            echo '

                <!-- Set "A5", "A4" or "A3" for class name -->
                <!-- Set also "landscape" if you need -->
                <body class="'.$paper_format.'" style="">
                
                  <!-- Each sheet element should have the class "sheet" -->
                  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
                  <section class="sheet padding-3mm" style="border: 1px dotted #0C0C0C">';


            if ($_GET){
                if (isset($_GET['id'])){


                    $t_f_data_db = array();
                    $cosmet_data_db = array();

                    $back_color = '';

                    $summ = 0;
                    $summins = 0;

                    //Данные предварительного рассчета
                    $invoice_j = SelDataFromDB('journal_advanaced_invoice', $_GET['id'], 'id');

                    if ($invoice_j != 0){
                        $sheduler_zapis = array();
                        $invoice_ex_j = array();
                        $invoice_ex_j_mkb = array();

                        //пациент
                        $client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');

                        //филиалы
                        $filials_j = getAllFilials(false, false, true);

                        $msql_cnnct = ConnectToDB ();

                        //запись
                        $query = "SELECT * FROM `zapis` WHERE `id`='".$invoice_j[0]['zapis_id']."'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($sheduler_zapis, $arr);
                            }
                        }

                        $percent_cat_j = array();

                        //категории %
                        $query = "SELECT `id`, `name` FROM `fl_spr_percents`";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //array_push($percent_cat_j, $arr);
                                $percent_cat_j[$arr['id']] = $arr['name'];
                            }
                        }

                        //Наряды
                        $query = "SELECT * FROM `journal_advanaced_invoice_ex` WHERE `invoice_id`='".$_GET['id']."';";

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

                        //сортируем зубы по порядку
                        if (!empty($invoice_ex_j)){
                            ksort($invoice_ex_j);
                        }

                        //Для МКБ
                        $query = "SELECT * FROM `journal_advanaced_invoice_ex_mkb` WHERE `invoice_id`='".$_GET['id']."';";

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
                        }

                        echo '
                                <!-- Write HTML just like a web page -->
                                <article>
                                
                                   <h2 style="padding: 0 0 7px; font-size: 3.5mm; color: #0C0C0C; text-align: center; font-weight: bold;">Предварительный расчёт #'.$_GET['id'].'</h2>';
                        echo '	
										<div id="errror" class="<!--invoceHeader-->" style="">
                                            <div>
                                                <div style="display: inline-block; width: 60mm; vertical-align: top;">
                                                    <div>
                                                        <div style="font-size: 3mm;">Врач: <div id="calculateInvoice" style="color: #0C0C0C; font-size: 3.2mm;">' . WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user', false) . '</div></div>
                                                    </div>
                                                    <div>
                                                        <div style="font-size: 3mm;">Сумма: <div id="calculateInvoice" style="font-size: 3.2mm; color: #0C0C0C">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                    </div>
                                                </div>';
                        echo '
                                                <div style="display: inline-block; width: 110mm; vertical-align: top;">
                                                    <div>
                                                        <div style="font-size: 3mm;">Пациент: <div id="calculateInvoice" style="color: #0C0C0C; font-size: 3.2mm;">'.WriteSearchUser('spr_clients', $invoice_j[0]['client_id'], 'user', false).'</div></div>
                                                    </div>
                                                    <div>
                                                        <div style="font-size: 3mm;">Дата: <div id="calculateInvoice" style="font-size: 2.2mm; color: #0C0C0C">___/___/______</div>г. Подпись: _________________________</div>
                                                    </div>
							                    </div>';
                        echo '
                                                <div style="display: inline-block; vertical-align: top;">
                                                    <i>'.$invoice_j[0]['comment'].'</i>';
                        echo '
										        </div>';
                        echo '
											</div>';
                        echo '
										</div>';
                        echo '
										<div id="invoice_rezult" style="float: none; font-size: 2.9mm;/*width: 900px;*/">';
                        echo '
											<div class="cellsBlock">
												<div class="cellCosmAct" style="font-size: 80%; text-align: center;">';
                        if ($invoice_j[0]['type'] != 88) {
                            if ($sheduler_zapis[0]['type'] == 5) {
                                echo '
                                                    <i><b>Зуб</b></i>';
                            }

                            if (($sheduler_zapis[0]['type'] == 6) || ($sheduler_zapis[0]['type'] == 10) || ($sheduler_zapis[0]['type'] == 7)) {
                                echo '
                                                    <i><b>№</b></i>';
                            }
                        }
                        echo '
												</div>
												<div class="cellText2" style="font-size: 80%; text-align: center; padding: 1px;border-left: none;">
													<i><b>Наименование</b></i>
												</div>';
                        if ($invoice_j[0]['type'] != 88) {
                            if ($sheduler_zapis[0]['type'] == 5) {
//                                    echo '
//												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
//													<i><b>Страх.</b></i>
//												</div>
//												<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
//													<i><b>Сог.</b></i>
//												</div>';
                                echo '
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; padding: 1px; border-left: none;">
													<i><b>Челюсть</b></i>
												</div>';
                            }
                        }
                        echo '
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; padding: 1px; border-left: none;">
													<i><b>Цена, руб.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none;">
													<i><b>Коэфф.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none;">
													<i><b>Кол-во</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none;">
													<i><b>Скидка</b></i>
												</div>
<!--												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Гар.</b></i>
												</div>-->
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; padding: 1px; border-left: none;">
													<i><b>Всего, руб.</b></i>
												</div>
<!--												<div class="cellName" style="font-size: 80%; text-align: center;">
													<i><b>Категория</b></i>
												</div>-->
											</div>';

                        //var_dump($invoice_ex_j);

                        if (!empty($invoice_ex_j)) {

                            foreach ($invoice_ex_j as $ind => $invoice_data) {

                                //var_dump($invoice_data);
                                echo '
                                        <div class="cellsBlock">
                                            <div class="cellCosmAct" style="text-align: center;">';
                                if ($ind == 99) {
                                    echo 'П';
                                } else {
                                    if ($invoice_j[0]['type'] == 5) {
                                        echo $ind;
                                    }else{
                                        echo $ind + 1;
                                    }
                                }
                                echo '
                                            </div>';

                                if ($invoice_j[0]['type'] != 88) {
                                    //Диагноз МКБ
                                    if ($sheduler_zapis[0]['type'] == 5) {

                                        if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])) {
                                            echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 1px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
                                                            <b>';
                                            if ($ind == 99) {
                                                echo '<i>Полость</i>';
                                            } else {
                                                echo '<i>Зуб</i>: ' . $ind;
                                            }
                                            echo '
                                                            </b>. <i>Диагноз</i>: ';

                                            foreach ($invoice_ex_j_mkb[$ind] as $mkb_key => $mkb_data_val) {
                                                $rez = array();
                                                //$rezult2 = array();

                                                $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                $number = mysqli_num_rows($res);
                                                if ($number != 0) {
                                                    while ($arr = mysqli_fetch_assoc($res)) {
                                                        $rez[$mkb_data_val['mkb_id']] = $arr;
                                                    }
                                                } else {
                                                    $rez = 0;
                                                }
                                                if ($rez != 0) {
                                                    foreach ($rez as $mkb_name_val) {
                                                        echo '
                                                                <div class="mkb_val" style="background: rgb(239, 255, 255); border: 1px dotted #bababa;"><b>' . $mkb_name_val['code'] . '</b> ' . $mkb_name_val['name'] . '
                
                                                                </div>';
                                                    }
                                                } else {
                                                    echo '<div class="mkb_val">???</div>';
                                                }

                                            }

                                            echo '
                                                        </div>
                                                    </div>';
                                        }

                                    }
                                }

                                foreach ($invoice_data as $item) {
                                    //var_dump($item);

                                    //Если уже был возврат по этой позиции, то покажем это
                                    $textColor = '';
                                    $bgColor = '';

                                    echo '
                                                <div class="cellsBlock" style="font-size: 100%; '.$bgColor.' '.$textColor.'" >
                                                <!--<div class="cellCosmAct" style="">
                                                    -
                                                </div>-->
                                                    <div class="cellText2" style="padding: 1px; border-left: none; border-top: none;">';

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

                                        echo '<i>'.$rezult2[0]['code'].'</i> '.$rezult2[0]['name'].' <a href="pricelistitem.php?id='.$rezult2[0]['id'].'" class="ahref" target="_blank" rel="nofollow noopener"><span style="font-size: 90%; background: rgba(197, 197, 197, 0.41);"></span></a>';


                                    } else {
                                        echo '?';
                                    }

                                    echo '
                                                </div>';

                                    $price = $item['price'];

                                    if ($invoice_j[0]['type'] != 88) {
                                        if ($sheduler_zapis[0]['type'] == 5) {

                                            echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic; overflow: hidden; padding: 1px; border-left: none; border-top: none;">';

                                            if ($item['jaw_select'] == 1){
                                                echo 'ВЧ';
                                            }elseif($item['jaw_select'] == 2){
                                                echo 'НЧ';
                                            }else {
                                                echo '-';
                                            }

                                            echo '                                                        
                                                    </div>';

                                        }
                                    }
                                    echo '
                                                <div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; padding: 1px; border-left: none; border-top: none;">
                                                    ' . $price . '
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none; border-top: none;">
                                                    ' . $item['spec_koeff'] . '
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none; border-top: none;">
                                                    ' . $item['quantity'] . '
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; padding: 1px; border-left: none; border-top: none;">
                                                    ' . $item['discount'] . '
                                                </div>';
                                    echo '
                                                <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; padding: 1px; border-left: none; border-top: none;">';


                                    if (($item['itog_price'] != 0) && ($price != 0)) {

                                        $stoim_item = $item['itog_price'];

                                    } else {
                                        //вычисляем стоимость
                                        //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                        $stoim_item = $item['quantity'] * $price;

                                        //с учетом скидки акции
                                        if ($item['insure'] == 0) {
                                            //$stoim_item = $stoim_item - ($stoim_item * $invoice_j[0]['discount'] / 100);
                                            $stoim_item = $stoim_item - ($stoim_item * $item['discount'] / 100);
                                            //$stoim_item = round($stoim_item/10) * 10;
                                            $stoim_item = round($stoim_item);
                                        }
                                    }

                                    echo $stoim_item;


                                    //Общая стоимость
                                    if (($item['guarantee'] == 0) && ($item['gift'] == 0)) {
                                        if ($item['insure'] != 0) {
                                            if ($item['insure_approve'] != 0) {
                                                $summins += $stoim_item;
                                            }
                                        } else {
                                            $summ += $stoim_item;
                                        }
                                    }


                                    echo '
                                                </div>';

                                    echo '
                                            </div>';
                                }
                                echo '
                                        </div>';
                            }
                        }

                        echo '           
                                </article>';

                    }else{
                        echo '<h1>Что-то пошло не так_3</h1><a href="index.php">Вернуться на главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так_2</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так_1</h1><a href="index.php">Вернуться на главную</a>';
            }

            echo '                
                    </section>
                  
                    <div class="no_print" style="position: fixed; top: 10px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                        <a href="?id='.$_GET['id'].'&format=A4" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
                            A4
                        </a>
                        <a href="?id='.$_GET['id'].'&format=A5" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
                            A5
                        </a>
                        <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                        onclick="window.print();">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </div>
                    </div>
                
                </body>';


        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}

echo '
	</html>';

?>