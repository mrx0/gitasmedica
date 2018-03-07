<?php

//fl_materials_consumption_add.php
//Расход материалов

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        require 'variables.php';

        require 'config.php';

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        //var_dump($_SESSION);
        //unset($_SESSION['invoice_data']);

        if ($_GET){
            if (isset($_GET['invoice_id'])){

                $invoice_j = SelDataFromDB('journal_invoice', $_GET['invoice_id'], 'id');

                if ($invoice_j != 0){
                    //var_dump($invoice_j);
                    //array_push($_SESSION['invoice_data'], $_GET['client']);
                    //$_SESSION['invoice_data'] = $_GET['client'];
                    //var_dump($invoice_j[0]['closed_time'] == 0);

                    $sheduler_zapis = array();
                    $mat_cons_j = array();
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
                    //if ($client !=0){
                    if (!empty($sheduler_zapis)){

                        //сортируем зубы по порядку
                        //ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);

                        //var_dump($_SESSION);
                        //var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);
                        //var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['mkb']);

                        if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
                        else $month = $sheduler_zapis[0]['month'];



                        echo '
							<div id="status">
								<header>

									<h2>Добавить расход на материалы для <a href="invoice.php?id='.$_GET['invoice_id'].'" class="ahref">Наряда #'.$_GET['invoice_id'].'</a>';


                        echo '			
										</h2>';

                        if ($invoice_j[0]['status'] == 9){
                            echo '<i style="color:red;">Наряд удалён (заблокирован).</i><br>';
                        }


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

                        if ($invoice_j[0]['status'] == 9){
                            echo '<i style="color:red;">Для удалённого наряда не указываются затртаы на материалы.</i><br>';
                        }else {

                            //Затраты
                            $query = "SELECT * FROM `journal_inv_material_consumption` WHERE `invoice_id`='" . $_GET['invoice_id'] . "'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($mat_cons_j, $arr);
                                }
                            } else {
                                //$sheduler_zapis = 0;
                                //var_dump ($sheduler_zapis);
                            }
                            //if ($client !=0){
                            if (empty($mat_cons_j)) {


                                //Наряды

                                //$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['invoice_id']."'";
                                //!!! пробуем JOIN
                                //$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['invoice_id']."';";
                                $query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='" . $_GET['invoice_id'] . "';";
                                //var_dump($query);

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                $number = mysqli_num_rows($res);
                                if ($number != 0) {
                                    while ($arr = mysqli_fetch_assoc($res)) {
                                        if (!isset($invoice_ex_j[$arr['ind']])) {
                                            $invoice_ex_j[$arr['ind']] = array();
                                            array_push($invoice_ex_j[$arr['ind']], $arr);
                                        } else {
                                            array_push($invoice_ex_j[$arr['ind']], $arr);
                                        }
                                    }
                                } else
                                    $invoice_ex_j = 0;
                                //var_dump ($invoice_ex_j);

                                //сортируем зубы по порядку
                                if ($invoice_ex_j != 0) {
                                    ksort($invoice_ex_j);
                                }

                                //Для МКБ
                                $query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='" . $_GET['invoice_id'] . "';";
                                //var_dump ($query);

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                $number = mysqli_num_rows($res);
                                if ($number != 0) {
                                    while ($arr = mysqli_fetch_assoc($res)) {
                                        if (!isset($invoice_ex_j_mkb[$arr['ind']])) {
                                            $invoice_ex_j_mkb[$arr['ind']] = array();
                                            array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                        } else {
                                            array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                        }
                                    }
                                } else
                                    $invoice_ex_j_mkb = 0;
                                //var_dump ($invoice_ex_j_mkb);


                                echo '
                                        <div id="data">';

                                echo '			
                                            <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                                echo '	
                                                <div id="errror" class="invoceHeader" style="">
                                                     <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                        <div>
                                                            <div style="">Сумма: <div id="calculateInvoice" style="">' . $invoice_j[0]['summ'] . '</div> руб.</div>
                                                        </div>';
                                if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
                                                        <div>
                                                            <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                        </div>';
                                }


                                echo '
                                                    </div>';


                                echo '
                                                </div>';


                                echo '
                                                <div id="invoice_rezult" style="float: none; width: 850px;">';

                                echo '
                                                    <div class="cellsBlock">
                                                        <div class="cellCosmAct" style="font-size: 80%; text-align: center;">';
                                if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
                                                            <i><b>Зуб</b></i>';
                                }
                                if ($sheduler_zapis[0]['type'] == 6) {
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
                                                            <i><b>Всего, руб.</b></i>
                                                        </div>
                                                        
                                                        <div class="cellText2" style="font-size: 100%; text-align: center; width: 220px; min-width: 220px; max-width: 220px;">
                                                            <i><b>Наименование</b></i>
                                                        </div>
                                                        
                                                    </div>';


                                foreach ($invoice_ex_j as $ind => $invoice_data) {

                                    //var_dump($invoice_data);
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


                                    foreach ($invoice_data as $item) {
                                        //var_dump($item);

                                        //часть прайса
                                        //if (!empty($invoice_data)){

                                        //foreach ($invoice_data as $key => $items){
                                        echo '
                                                    <div class="cellsBlock invoicePosClass" style="font-size: 100%;" >
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

                                            //echo $item['id'].' -> '.$rezult2[0]['name'];
                                            echo $rezult2[0]['name'];

                                        } else {
                                            echo '?';
                                        }

                                        echo '
                                                    </div>';

                                        $price = $item['price'];


                                        echo '
                                                    <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                        <b>';


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
                                            //$stoim_item = round($stoim_item/10) * 10;
                                        }

                                        if ($item['guarantee'] == 0) {
                                            echo $stoim_item;
                                        } else {
                                            echo 0;
                                        }

                                        //Общая стоимость
                                        if ($item['guarantee'] == 0) {
                                            if ($item['insure'] != 0) {
                                                if ($item['insure_approve'] != 0) {
                                                    $summins += $stoim_item;
                                                }
                                            } else {
                                                $summ += $stoim_item;
                                            }
                                        }


                                        echo '</b>
                                                    </div>
                                                    
                                                    
                                                    <div class="cellText2" style="font-size: 100%; width: 220px; min-width: 220px; max-width: 220px;">
                                                        <input type="text" name="mat_cons_pos_summ" id="mat_cons_pos_summ" positionID="' . $item['id'] . '" class="materials_consumption_pos" value="0">
                                                        <input type="checkbox" class="chkMatCons" value="1" checked>
                                                    </div>
                                                        
                                                    
                                                </div>';
                                    }
                                    echo '
                                            </div>';
                                }


                                echo '	
                                                <div class="cellsBlock" style="font-size: 90%;" >
                                                    <div class="cellText2" style="padding: 12px 4px;">
                                            
                                                        <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                                            Общая сумма расходов на материалы (руб.)
                                                        </div>
                                                    
                                                        <input type="text" name="mat_cons_pos_summ_all" id="mat_cons_pos_summ_all" class="materials_consumption_pos_all" style="font-size: 17px;" value="0">
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="cellsBlock">
                                                    <div class="cellText2">
                                                        <span style="font-size:90%;  color: #555;">Комментарий</span><br>
                                                        <textarea name="descr" id="descr" cols="60" rows="8"></textarea>
                                                    </div>
                                                </div>';
                                echo '
                                            </div>';


                                echo '
                                        </div>';
                                echo '
                                        <div id="errrror"></div>
                                        <input type="button" class="b" value="Применить" onclick="fl_showMaterialsConsumptionAdd(' . $_GET['invoice_id'] . ', \'add\')">';


                                echo '
                                            <div id="doc_title">Добавить расходы материалы для Наряда #' . $_GET['invoice_id'] . ' - Асмедика</div>';

                                echo '
                                            <!-- Подложка только одна -->
                                            <div id="overlay"></div>';
                            } else {
                                echo '<i style="color:red;">У этого наряда уже указаны затраты. Больше 1 нельзя.</i><br>';
                            }
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