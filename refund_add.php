<?php

//invoice.php
//Наряд заказ

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
						}

                        //Расходы материалов
                        $mat_cons_j = array();
                        $mat_cons_j_ex = array();

                        if (($finances['see_all'] == 1) || $god_mode) {

                            //$query = "SELECT * FROM `journal_inv_material_consumption` WHERE `invoice_id`='" . $_GET['invoice_id'] . "' ORDER BY `create_time` DESC";
                            //var_dump($query);
                            $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                            LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                            ON jimc.id = jimcex.inv_mat_cons_id
                            WHERE jimc.invoice_id = '" . $_GET['invoice_id'] . "';";

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

                        }

                        echo '
							<div id="status">
								<header>

									<h2>Возврат средств на счёт '.WriteSearchUser('spr_clients', $invoice_j[0]['client_id'], 'user_full', true).' по <a href="invoice.php?id='.$_GET['invoice_id'].'" class="ahref">наряду #'.$_GET['invoice_id'].'</a>';

                        echo '			
                                    </h2>
                                     <div id="errror"></div>';

                        if ($invoice_j[0]['status'] == 9){
                            echo '<i style="color:red;">Наряд удалён (заблокирован).</i><br>';
                        }

                        echo '
                                </header>';

//                        echo '
//                            <div style="margin-top: 7px; font-size: 80%; color: #777;">
//                                Если работа была закры
//                            </div>';

                        $t_f_data_db = array();
                        $cosmet_data_db = array();

                        $back_color = '';

                        $summ = 0;
                        $summins = 0;

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

                        //сортируем зубы по порядку
                        if (!empty($invoice_ex_j)){
                            ksort($invoice_ex_j);
                        }
                        //var_dump ($invoice_ex_j);

                        //Расчетные листы списком
                        $fl_calculate_j = array();
                        //табели, если есть, чтобы отслеживать выплачено ли зп за работу
                        $fl_tabel_j = array();

                        //$query = "SELECT * FROM `fl_journal_calculate` WHERE `invoice_id`='".$_GET['invoice_id']."' ORDER BY `create_time` DESC";
                        $query = "SELECT 
                            jcalcex.*, jcalc.id, jtabelex.tabel_id
                            FROM `fl_journal_calculate` jcalc
                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
                            LEFT JOIN `fl_journal_tabels_ex` jtabelex ON jcalc.id = jtabelex.calculate_id
                            WHERE jcalc.invoice_id='{$_GET['invoice_id']}'";

                        //var_dump($query);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $fl_calculate_j[$arr['inv_pos_id']] = $arr;
                                if ($arr['tabel_id'] != NULL){
                                    $fl_tabel_j[$arr['calculate_id']] = $arr['tabel_id'];
                                }
                            }
                        }
                        //var_dump($fl_calculate_j);
                        //var_dump($fl_tabel_j);

                        echo '
								<div id="data">';

                        echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                        echo '	
										<div class="invoceHeader" style="">
                                            <div>
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Сумма: <div class="calculateInvoice" style="">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                    </div>';
                        echo '
                                                    <div>
                                                        <div style="">Исполнитель: <div id="calculateInsInvoice" style="">' . WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user', true) . '</div></div>
                                                    </div>';
                        echo '
                                                </div>';

                        echo '
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">'.$invoice_j[0]['paid'].'</div> руб.</div>
                                                    </div>
                                                    <div>
                                                        <div style="">Будет возвращено: <div id="refundSumm" class="calculateInvoice" style="">0</div> руб.</div>
                                                    </div>';

                        echo '
							                    </div>';


                        echo '
                                                <div style="display: inline-block; vertical-align: top;">';

                        //Если сумма выписанная не равна сумме оплаченной
                        if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                            echo '
                                                    <div style="color: red; ">
                                                        Наряд не оплачен
                                                    </div>';
                        }else{
                            echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">Наряд оплачен</div>
                                                    </div>';
                        }

                        //Если статус не равен 5, то есть не закрыт
                        if ($invoice_j[0]['status'] != 5) {
                            echo '
                                                    <div style="color: red; ">
                                                        Работа не закрыта
                                                    </div>';

                            //if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {
//                                echo '
//                                                    <div style="display: inline-block;">
//                                                        <!--<a href="invoice_status_close.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Закрыть работу</a>-->
//                                                        <input type="button" class="b" value="Закрыть работу" onclick="showInvoiceClose(' . $invoice_j[0]['id'] . ')">
//                                                    </div>';
                            //}

                        }else{
                            echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">
                                                            Работа закрыта '.date('d.m.Y', strtotime($invoice_j[0]['closed_time']));
                                //var_dump(date('Y-m-d', strtotime($invoice_j[0]['create_time'])));
                                //var_dump(date('Y-m-d'));

//                                if ((($finances['see_all'] == 1) || $god_mode) ||
//                                (($finances['see_all'] != 1) && !$god_mode && (date('Y-m-d', strtotime($invoice_j[0]['create_time'])) == date('Y-m-d'))))
//                                {
//                                    echo '
//                                                            <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%; cursor: pointer;" title="Снять отметку о закрытии работы" onclick="showInvoiceOpen(' . $invoice_j[0]['id'] . ')"></i>';
//                                }
                            echo '
                                                        </div>
                                                    </div>';
                        }

                        echo '
										        </div>';

                        echo '
											</div>';
                        //Если расчетные листы в табелях
                        if (!empty($fl_tabel_j)){
                            echo '
                                            <div style="margin-top: 10px;">
                                                <div style="font-size: 90%; color: red;">Расчетный лист был создан, и работа полностью или частично включена в табели для расчёта ЗП.<br>Если заплату уже выдали и необходимо будет сделать вычет, поставьте галочку: <input type="checkbox" class="salary_deduction_checkbox" name="salary_deduction" value="1"></div>
                                            </div>
                                            <div style="font-size: 90%; color: red;">
                                                Если зарплату по работе не выдавали, галочку ставить не надо, удалите расчётный лист.
                                            </div>
                                            <div style="margin-top: 5px;">
                                                <div style="">
                                                    Будет вычтено из зарплаты: <div id="salaryDeductionSumm" class="calculateInvoice" style="">0</div> руб. Выберите табель, в который включить вычет: !!!
                                                </div>
                                            </div>';
                        }

                        echo '
										</div>';
                        echo '
										<div id="invoice_rezult" style="float: none; width: 900px;">';

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
                                            <div class="cellText2" style="font-size: 100%; text-align: center;">
                                                <i><b>Наименование</b></i>
                                            </div>';
                        if ($invoice_j[0]['type'] != 88) {
                            if ($sheduler_zapis[0]['type'] == 5) {
                                echo '
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
                                                <i><b>Страх.</b></i>
                                            </div>
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center;">
                                                <i><b>Сог.</b></i>
                                            </div>';
                            }
                        }

                        echo '
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
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
                                            </div>
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                <i><b>Всего, руб.</b></i>
                                            </div>
                                            <!--<div class="cellName" style="font-size: 80%; text-align: center;">
                                                <i><b>Категория</b></i>
                                            </div>-->
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                <i><b>Выплачено зп, руб.</b></i>
                                            </div>
                                            <div class="cellCosmAct" style="font-size: 80%; text-align: center;">
                                                <input type="checkbox" class="all_position_check" name="all_position_check" value="1">
                                            </div>
                                        </div>';

											
                        if (!empty($invoice_ex_j)) {

                            foreach ($invoice_ex_j as $ind => $invoice_data) {

                                //var_dump($invoice_data);
                                echo '
                                        <div class="cellsBlock">
                                            <div class="cellCosmAct toothInInvoice" style="text-align: center;">';
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

//                                    if ($invoice_j[0]['type'] != 88) {
//                                        //Диагноз МКБ
////                                        if ($sheduler_zapis[0]['type'] == 5) {
////
//////                                            if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])) {
//////                                                echo '
//////                                                    <div class="cellsBlock" style="font-size: 100%;" >
//////                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
//////                                                            <b>';
//////                                                if ($ind == 99) {
//////                                                    echo '<i>Полость</i>';
//////                                                } else {
//////                                                    echo '<i>Зуб</i>: ' . $ind;
//////                                                }
//////                                                echo '
//////                                                            </b>. <i>Диагноз</i>: ';
//////
//////                                                foreach ($invoice_ex_j_mkb[$ind] as $mkb_key => $mkb_data_val) {
//////                                                    $rez = array();
//////                                                    //$rezult2 = array();
//////
//////                                                    $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";
//////
//////                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//////                                                    $number = mysqli_num_rows($res);
//////                                                    if ($number != 0) {
//////                                                        while ($arr = mysqli_fetch_assoc($res)) {
//////                                                            $rez[$mkb_data_val['mkb_id']] = $arr;
//////                                                        }
//////                                                    } else {
//////                                                        $rez = 0;
//////                                                    }
//////                                                    if ($rez != 0) {
//////                                                        foreach ($rez as $mkb_name_val) {
//////                                                            echo '
//////                                                                <div class="mkb_val" style="background: rgb(239, 255, 255); border: 1px dotted #bababa;"><b>' . $mkb_name_val['code'] . '</b> ' . $mkb_name_val['name'] . '
//////
//////                                                                </div>';
//////                                                        }
//////                                                    } else {
//////                                                        echo '<div class="mkb_val">???</div>';
//////                                                    }
//////
//////                                                }
//////
//////                                                echo '
//////                                                        </div>
//////                                                    </div>';
//////                                            }
////
////                                        }
//                                    }

                                foreach ($invoice_data as $item) {
                                    //var_dump($item);

                                    //часть прайса
                                    //if (!empty($invoice_data)){

                                    //foreach ($invoice_data as $key => $items){
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
                                        echo '<i>'.$rezult2[0]['code'].'</i>'.$rezult2[0]['name'].' <span style="font-size: 90%; background: rgba(197, 197, 197, 0.41);">[#'.$rezult2[0]['id'].']</span>';
                                    } else {
                                        echo '?';
                                    }

                                    echo '
                                            </div>';

                                    $price = $item['price'];

                                    if ($invoice_j[0]['type'] != 88) {
                                        if ($sheduler_zapis[0]['type'] == 5) {
                                            if ($item['insure'] != 0) {
                                                //Написать страховую
                                                $insure_j = SelDataFromDB('spr_insure', $item['insure'], 'id');

                                                if ($insure_j != 0) {
                                                    $insure_name = $insure_j[0]['name'];
                                                } else {
                                                    $insure_name = '?';
                                                }
                                            } else {
                                                $insure_name = 'нет';
                                            }
                                        }


                                        if ($sheduler_zapis[0]['type'] == 5) {
                                            echo '
                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic; overflow: hidden;">
                                                    ' . $insure_name . '
                                                </div>';


                                            if ($item['insure'] != 0) {
                                                if ($item['insure_approve'] == 1) {
                                                        echo '
                                                    <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                        <i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
                                                    </div>';
                                                } else {
                                                    echo '
                                                    <div class="cellCosmAct" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;">
                                                        <i class="fa fa-ban" aria-hidden="true"></i>
                                                    </div>';
                                                }

                                            } else {
                                                echo '
                                                <div class="cellCosmAct" insureapprove="' . $item['insure_approve'] . '" style="font-size: 70%; text-align: center;">
                                                    -
                                                </div>';
                                            }
                                        }
                                    }

                                    echo '
                                                <div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
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
                                                </div>
                                                <div class="cellCosmAct settings_text" guarantee="' . $item['guarantee'] . '" gift="' . $item['gift'] . '" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">';

                                    if ($item['guarantee'] != 0) {
                                        echo '
                                                    <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                    } elseif ($item['gift'] != 0) {
                                        echo '
                                                    <i class="fa fa-gift" aria-hidden="true" style="color: blue; font-size: 150%;"></i>';
                                    } else {
                                        echo '-';
                                    }

                                    echo '
                                                </div>
                                                <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; font-weight: bold; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">';

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



//                                        if ($item['percent_cats'] > 0) {
//                                            $percent_cat = $percent_cat_j[$item['percent_cats']];
//                                        }else{
//                                            $percent_cat = '<i style="color: red;">Ошибка #15</i>';
//                                        }


//                                        echo '
//                                                <div class="cellName" style="font-size: 80%; text-align: right;">
//                                                    <i>'.$percent_cat.'</i>
//                                                </div>';
                                    echo '
                                                <div class="cellCosmAct salaryDeductionItemPriceItog" style="font-size: 90%; font-style: italic; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">';

                                    if (isset($fl_calculate_j[$item['id']])){
                                        //echo $fl_calculate_j[$item['id']]['price'];
                                        //var_dump($fl_calculate_j[$item['id']]);

                                        if (!empty($mat_cons_j_ex['data'])) {
                                            //var_dump($mat_cons_j_ex['data']);

                                            if (isset($mat_cons_j_ex['data'][$item['id']])) {

                                                $stoim_item = $stoim_item - $mat_cons_j_ex['data'][$item['id']];

                                            }
                                        }

                                        //Рассчет цены за позицию
                                        echo calculateResult($stoim_item, $fl_calculate_j[$item['id']]['work_percent'], $fl_calculate_j[$item['id']]['material_percent'], $fl_calculate_j[$item['id']]['summ_special']);
                                    }else{
                                        echo 0;
                                    }

                                    echo '
                                                </div>';

                                    echo '
                                            <!--</div>-->
                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center;">
                                                    <input type="checkbox" item_id="'.$item['id'].'" class="position_check" name="position_check" value="1">
                                                </div>
                                            </div>';
                                }
                                echo '
                                        </div>';
                            }
                        }
					
                        echo '	
										<div class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
											</div>
											<!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
												Итого:-->';
                        if (($summ != $invoice_j[0]['summ']) || ($summins != $invoice_j[0]['summins'])){
                            /*echo '<br>
                                <span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
                        }

                        echo '				
													
											<!--</div>
											<div class="cellName" style="padding: 2px 4px;">
												<div>
													<div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">'.$summ.'</div> руб.</div>
												</div>-->';
                        if ($invoice_j[0]['type'] != 88) {
                            if ($sheduler_zapis[0]['type'] == 5) {
                                echo '
                                            <!--<div>
                                                <div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">' . $summins . '</div> руб.</div>
                                            </div>-->';
                            }
                        }
                        echo '
                                        </div>';

                        //Документы закрытия/оплаты нарядов списком
                        $payment_j = array();

                        $query = "SELECT * FROM `journal_payment` WHERE `invoice_id`='".$_GET['invoice_id']."' ORDER BY `date_in` DESC";
                        //var_dump($query);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($payment_j, $arr);
                            }
                        }

//                            if (!empty($payment_j)) {
//                                echo '
//                                            <div class="invoceHeader" style="">
//                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
//                                                    <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
//                                                        Проведённые оплаты по наряду:
//                                                    </li>';

//                                foreach ($payment_j as $payment_item) {
//
//                                    $pay_type_mark = '';
//                                    $cert_num = '';
//
//                                    if ($payment_item['type'] == 1){
//                                        $pay_type_mark = '<i class="fa fa-certificate" aria-hidden="true" title="Оплата сертификатом"></i>';
//                                        //Найдем сертификат по его id
//                                        $query = "SELECT `num` FROM `journal_cert` WHERE `id`='".$payment_item['cert_id']."' LIMIT 1";
//                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                                        $number = mysqli_num_rows($res);
//                                        if ($number != 0) {
//                                            $arr = mysqli_fetch_assoc($res);
//                                            $cert_num = 'Сертификатом №'.$arr['num'];
//                                        } else {
//                                            $cert_num = 'Ошибка сертификата';
//                                        }
//                                    }
//
////                                    echo '
////                                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
////                                    echo '
////                                                        <a href="" class="cellOrder ahref" style="position: relative;">
////                                                            <b>Оплата #' . $payment_item['id'] . '</b> от ' . date('d.m.y', strtotime($payment_item['date_in'])) . ' '.$cert_num.'<br>
////                                                            <span style="font-size:90%;  color: #555;">';
//
////                                    if (($payment_item['create_time'] != 0) || ($payment_item['create_person'] != 0)) {
////                                        echo '
////                                                                Добавлен: ' . date('d.m.y H:i', strtotime($payment_item['create_time'])) . '<br>
////                                                                Автор: ' . WriteSearchUser('spr_workers', $payment_item['create_person'], 'user', false) . '<br>';
////                                    } else {
////                                        echo 'Добавлен: не указано<br>';
////                                    }
//                                    /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
//                                        echo'
//                                                                Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
//                                                                <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
//                                    }*/
////                                    echo '
////                                                            </span>
////                                                            <span style="position: absolute; top: 2px; right: 3px;">'. $pay_type_mark .'</span>
////                                                        </a>
////                                                        <div class="cellName">
////                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
////                                                                Сумма:<br>
////                                                                <span class="calculateOrder" style="font-size: 13px">' . $payment_item['summ'] . '</span> руб.
////                                                            </div>
////                                                        </div>
////                                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="deletePaymentItem('.$payment_item['id'].', '.$invoice_j[0]['client_id'].', '.$invoice_j[0]['id'].');">
////                                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
////                                                        </div>
////                                                        ';
////                                    echo '
////                                                    </li>';
//                                }
//
//                                echo '
//                                                </ul>
//                                            </div>';
//                            }




                        if (!empty($fl_calculate_j)) {
//                                echo '
//                                            <div class="invoceHeader" style="">
//                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
//                                                    <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
//                                                        Расчётные листы по наряду:
//                                                    </li>';
                            foreach ($fl_calculate_j as $calculate_item) {
//
//                                    echo '
//                                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
//                                    echo '
//                                                        <a href="fl_calculate.php?id='.$calculate_item['id'].'" class="cellOrder ahref" style="position: relative;">
//                                                            <b>Расчёт #' . $calculate_item['id'] . '</b> от ' . date('d.m.y', strtotime($calculate_item['date_in'])) . '<br>
//                                                            <span style="font-size:80%;  color: #555;">';

//                                    if (($calculate_item['create_time'] != 0) || ($calculate_item['create_person'] != 0)) {
//                                        echo '
//                                                                Добавлен: ' . date('d.m.y H:i', strtotime($calculate_item['create_time'])) . '<br>
//                                                                <!--Автор: ' . WriteSearchUser('spr_workers', $calculate_item['create_person'], 'user', true) . '<br>-->';
//                                    } else {
//                                        echo 'Добавлен: не указано<br>';
//                                    }
                                    /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                        echo'
                                                                Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                                                <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                    }*/
//                                    echo '
//                                                            </span>
//
//                                                        </a>
//                                                        <div class="cellName">
//                                                            '.WriteSearchUser('spr_workers', $calculate_item['worker_id'], 'user', true).'<br>
//                                                        </div>
//                                                        <div class="cellName">
//                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//                                                                Сумма к расчёту:<br>
//                                                                <span class="calculateOrder" style="font-size: 13px">' . $calculate_item['summ_inv'] . '</span> руб.
//                                                            </div>
//                                                        </div>
//                                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="fl_deleteCalculateItem('.$calculate_item['id'].', '.$invoice_j[0]['client_id'].', '.$invoice_j[0]['id'].');">
//                                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
//                                                        </div>
//                                                        ';
//                                    echo '
//                                                    </li>';
                            }

                            echo '
                                                </ul>
                                            </div>';
                        }


                        if (!empty($mat_cons_j_ex)) {
                            if (!empty($mat_cons_j_ex['data'])) {
//                                    echo '
//                                                <div class="invoceHeader" style="">
//                                                    <ul style="margin-left: 6px; margin-bottom: 10px;">
//                                                        <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
//                                                            Затраты на материалы:
//                                                        </li>';
                                    //foreach ($mat_cons_j_ex['data'] as $mat_cons_item) {

//                                        echo '
//                                                        <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
//                                        echo '
//                                                            <a href="#" class="cellOrder ahref" style="position: relative;">
//                                                                <b>Расход #' . $mat_cons_j_ex['id'] . '</b> от ' . date('d.m.y', strtotime($mat_cons_j_ex['create_time'])) . '<br>
//                                                                <span style="font-size:80%;  color: #555;">';

                                if (($mat_cons_j_ex['create_time'] != 0) || ($mat_cons_j_ex['create_person'] != 0)) {
//                                            echo '
//                                                                    Добавлен: ' . date('d.m.y H:i', strtotime($mat_cons_j_ex['create_time'])) . '<br>
//                                                                    <!--Автор: ' . WriteSearchUser('spr_workers', $mat_cons_j_ex['create_person'], 'user', true) . '<br>-->';
                                } else {
//                                            echo 'Добавлен: не указано<br>';
                                }
                                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                            echo'
                                                                    Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                                                    <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                        }*/
//                                        echo '
//                                                                </span>
//
//                                                            </a>
//                                                            <div class="cellName">
//                                                                ' . $mat_cons_j_ex['descr'] . '<br>
//                                                            </div>
//                                                            <div class="cellName">
//                                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//                                                                    Сумма:<br>
//                                                                    <span class="calculateOrder" style="font-size: 13px">' . $mat_cons_j_ex['all_summ'] . '</span> руб.
//                                                                </div>
//                                                            </div>
//                                                            <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="fl_deleteMaterialConsumption(' . $mat_cons_j_ex['id'] . ', ' . $invoice_j[0]['id'] . ');">
//                                                                <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
//                                                            </div>
//                                                            ';
//                                        echo '
//                                                        </li>';
//                                    //}
//
//                                    echo '
//                                                    </ul>
//                                                </div>';
                            }
                        }


                        echo '
										</div>';
                        echo '			
										</div>';
                        echo '
									</div>';


                        echo '
		                            <div id="doc_title">Возврат средств на счёт по наряду #'.$_GET['invoice_id'].' / '.WriteSearchUser('spr_clients',  $invoice_j[0]['client_id'], 'user', false).' - Асмедика</div>';
                        echo '
								</div>
							';
						/*}else{
							echo '<h1>Что-то пошло не так_4</h1><a href="index.php">Вернуться на главную</a>';
						}*/
					}else{
						echo '<h1>Что-то пошло не так_3</h1><a href="index.php">Вернуться на главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так_2</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так_1</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>