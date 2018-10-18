<?php

//invoice.php
//Наряд заказ

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
	
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
				if (isset($_GET['id'])){
					
					$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
					
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
						}else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }

                        $percent_cat_j = array();

                        $query = "SELECT `id`, `name` FROM `fl_spr_percents`";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //array_push($percent_cat_j, $arr);
                                $percent_cat_j[$arr['id']] = $arr['name'];
                            }
                        }
                        //var_dump($percent_cat_j);

						//if ($client !=0){
						//if (!empty($sheduler_zapis) || ){

							//сортируем зубы по порядку
							//ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);

							//var_dump($_SESSION);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['mkb']);

							/*if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
							else $month = $sheduler_zapis[0]['month'];*/

                            //Расходы материалов
                            $mat_cons_j = array();
                            $mat_cons_j_ex = array();

                            if (($finances['see_all'] == 1) || $god_mode) {

                                //$query = "SELECT * FROM `journal_inv_material_consumption` WHERE `invoice_id`='" . $_GET['id'] . "' ORDER BY `create_time` DESC";
                                //var_dump($query);
                                $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                                LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                                ON jimc.id = jimcex.inv_mat_cons_id
                                WHERE jimc.invoice_id = '" . $_GET['id'] . "';";

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

                            //var_dump($mat_cons_j);
                            //var_dump($mat_cons_j_ex);


							echo '
							<div id="status">
								<header>

									<h2>Наряд #'.$_GET['id'].'';

							if (($finances['edit'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="invoice_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
								}
								if (($invoice_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
									echo '
										<a href="#" onclick="Ajax_reopen_invoice('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
								}
							}
							//Изменить дату внесения
							if (($finances['see_all'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="invoice_time_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Изменить дату"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
								}
							}
							if (($finances['close'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="invoice_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
								}
							}

							echo '			
										</h2>';

							if ($invoice_j[0]['status'] == 9){
								echo '<i style="color:red;">Наряд удалён (заблокирован).</i><br>';
							}


							echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                            //echo 'Врач:'.WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user', true).'<br>';

							if (($invoice_j[0]['create_time'] != 0) || ($invoice_j[0]['create_person'] != 0)){
								echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($invoice_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $invoice_j[0]['create_person'], 'user', true).'<br>';
							}else{
								echo 'Добавлен: не указано<br>';
							}
							if (($invoice_j[0]['last_edit_time'] != 0) || ($invoice_j[0]['last_edit_person'] != 0)){
								echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($invoice_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $invoice_j[0]['last_edit_person'], 'user', true).'';
							}
							echo '
											</span>
										</div>';



							echo '
									</header>';

                            $t_f_data_db = array();
                            $cosmet_data_db = array();

                            $back_color = '';

                            $summ = 0;
                            $summins = 0;


                            if($invoice_j[0]['type'] != 88){
                                echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">	
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Посещение</li>';


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
							}

							//Наряды

							//$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['id']."'";
							//!!! пробуем JOIN
							//$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['id']."';";
							$query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='".$_GET['id']."';";
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
							}/*else
								$invoice_ex_j = 0;*/
							//var_dump ($invoice_ex_j);

							//сортируем зубы по порядку
                            if (!empty($invoice_ex_j)){
							    ksort($invoice_ex_j);
                            }

							//Для МКБ
							$query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='".$_GET['id']."';";
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
							}/*else
								$invoice_ex_j_mkb = 0;*/
							//var_dump ($invoice_ex_j_mkb);


							echo '
								<div id="data">';

							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

							echo '	
										<div id="errror" class="invoceHeader" style="">
                                            <div>
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Сумма: <div id="calculateInvoice" style="">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                    </div>';
							if ($invoice_j[0]['type'] != 88) {
                                if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
                                                    <div>
                                                        <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                    </div>';
                                }
                            }else{
                                //--
                            }
                            echo '
                                                    <div>
                                                        <div style="">Исполнитель: <div id="calculateInsInvoice" style="">' . WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user', true) . '</div></div>
                                                    </div>
                                                </div>';

                            echo '
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">'.$invoice_j[0]['paid'].'</div> руб.</div>
                                                    </div>';
                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                if ($invoice_j[0]['status'] != 9) {
                                    echo '
                                                        <div>
                                                            <div style="display: inline-block;">Осталось внести: <div id="calculateInvoice" style="">' . ($invoice_j[0]['summ'] - $invoice_j[0]['paid']) . '</div> руб.</div>
                                                        </div>
                                                    <div>
                                                        <div style="display: inline-block;"><a href="payment_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Оплатить</a></div>
                                                        <div style="display: inline-block;"><a href="certificate_payment_add.php?invoice_id='.$invoice_j[0]['id'].'" class="b">Оплатить сертификатом</a></div>
                                                    </div>';
                                }
							}
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
                                    echo '
                                        <div style="display: inline-block;">
                                            <!--<a href="invoice_status_close.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Закрыть работу</a>-->
                                            <input type="button" class="b" value="Закрыть работу" onclick="showInvoiceClose(' . $invoice_j[0]['id'] . ')">
                                        </div>';
                                //}

                            }else{
                                echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">
                                                            Работа закрыта '.date('d.m.Y', strtotime($invoice_j[0]['closed_time']));
                                //var_dump(date('Y-m-d', strtotime($invoice_j[0]['create_time'])));
                                //var_dump(date('Y-m-d'));

                                if ((($finances['see_all'] == 1) || $god_mode) ||
                                (($finances['see_all'] != 1) && !$god_mode && (date('Y-m-d', strtotime($invoice_j[0]['create_time'])) == date('Y-m-d'))))
                                {
                                    echo '
                                                            <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%; cursor: pointer;" title="Снять отметку о закрытии работы" onclick="showInvoiceOpen(' . $invoice_j[0]['id'] . ')"></i>';
                                }
                                echo '
                                                        </div>
                                                    </div>';
                            }

                            echo '
										        </div>';


                            echo '
                                                <div style="margin-top: 5px; margin-left: -2px;">';

                            //Если всё оплачено, вносим расход материалов
                            if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {

                                //Расход материалов
                                if (($finances['see_all'] == 1) || $god_mode) {
                                    echo '
                                                    <div style="display: inline-block;"><a href="fl_materials_consumption_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Внести расходы на материалы</a></div>';
                                }
                            }
                            //Корректировка даты закрытия
                            if ((($finances['see_all'] == 1) || $god_mode) && ($invoice_j[0]['status'] == 5)){
                                echo '
                                                    <div style="display: inline-block;"><a href="invoice_change_close_time.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Корректировать дату закрытия работы</a></div>';
                            }
                            //Если закрыта работа
                            if ((($invoice_j[0]['status'] == 5) && (($finances['see_all'] == 1) || $god_mode)) ||
                            (($invoice_j[0]['status'] == 5) && ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) && ($finances['see_all'] != 1) && !$god_mode))
                            {

                                //Добавить расчетный лист
                                //if (($invoice_j[0]['type'] == 5) || ($invoice_j[0]['type'] == 6)) {
                                    echo '
                                                    <div style="display: inline-block;"><a href="fl_calculation_add3.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Внести расчётный лист</a></div>';
                                //}
                                //if ($invoice_j[0]['type'] == 88) {
                                //    echo '
                                //                    <div style="display: inline-block;"><a href="fl_calculation_add3.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Внести расчётный лист</a></div>';
                                //}
                            }

                            echo '
                                                </div>';

                            echo '
											</div>';



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

                                if ($sheduler_zapis[0]['type'] == 6) {
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
												<div class="cellName" style="font-size: 80%; text-align: center;">
													<i><b>Категория</b></i>
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
                                        echo $ind;
                                    }
                                    echo '
                                            </div>';

                                    if ($invoice_j[0]['type'] != 88) {
                                        //Диагноз МКБ
                                        if ($sheduler_zapis[0]['type'] == 5) {

                                            if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])) {
                                                echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
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
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;">
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

                                        /*if ($item['guarantee'] != 0){
                                            echo '
                                                <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                        }else{
                                            echo '-';
                                        }*/
                                        echo '
                                                </div>
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

                                        //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                                        /*if ($item['guarantee'] == 0) {
                                            echo $stoim_item;
                                        }else{
                                            echo 0;
                                        }*/
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
                                                    </b>
                                                </div>';



                                        if ($item['percent_cats'] > 0) {
                                            $percent_cat = $percent_cat_j[$item['percent_cats']];
                                        }else{
                                            $percent_cat = '<i style="color: red;">Ошибка #15</i>';
                                        }


                                        echo '
                                                <div class="cellName" style="font-size: 80%; text-align: right;">
                                                    <i>'.$percent_cat.'</i>
                                                </div>';

                                        echo '
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

                            $query = "SELECT * FROM `journal_payment` WHERE `invoice_id`='".$_GET['id']."' ORDER BY `date_in` DESC";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($payment_j, $arr);
                                }
                            }else{

                            }

                            if (!empty($payment_j)) {
                                echo '
                                            <div class="invoceHeader" style="">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Проведённые оплаты по наряду:
                                                    </li>';

                                foreach ($payment_j as $payment_item) {

                                    $pay_type_mark = '';
                                    $cert_num = '';

                                    if ($payment_item['type'] == 1){
                                        $pay_type_mark = '<i class="fa fa-certificate" aria-hidden="true" title="Оплата сертификатом"></i>';
                                        //Найдем сертификат по его id
                                        $query = "SELECT `num` FROM `journal_cert` WHERE `id`='".$payment_item['cert_id']."' LIMIT 1";
                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                        $number = mysqli_num_rows($res);
                                        if ($number != 0) {
                                            $arr = mysqli_fetch_assoc($res);
                                            $cert_num = 'Сертификатом №'.$arr['num'];
                                        } else {
                                            $cert_num = 'Ошибка сертификата';
                                        }
                                    }

                                    echo '
                                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                                    echo '
                                                        <a href="" class="cellOrder ahref" style="position: relative;">
                                                            <b>Оплата #' . $payment_item['id'] . '</b> от ' . date('d.m.y', strtotime($payment_item['date_in'])) . ' '.$cert_num.'<br>
                                                            <span style="font-size:90%;  color: #555;">';

                                    if (($payment_item['create_time'] != 0) || ($payment_item['create_person'] != 0)) {
                                        echo '
                                                                Добавлен: ' . date('d.m.y H:i', strtotime($payment_item['create_time'])) . '<br>
                                                                Автор: ' . WriteSearchUser('spr_workers', $payment_item['create_person'], 'user', false) . '<br>';
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
                                                            <span style="position: absolute; top: 2px; right: 3px;">'. $pay_type_mark .'</span>
                                                        </a>
                                                        <div class="cellName">
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Сумма:<br>
                                                                <span class="calculateOrder" style="font-size: 13px">' . $payment_item['summ'] . '</span> руб.
                                                            </div>
                                                        </div>
                                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="deletePaymentItem('.$payment_item['id'].', '.$invoice_j[0]['client_id'].', '.$invoice_j[0]['id'].');">
                                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                        </div>
                                                        ';
                                    echo '
                                                    </li>';
                                }

                                echo '
                                                </ul>
                                            </div>';
                            }

                            //Расчетных листов списком
                            $fl_calculate_j = array();

                            $query = "SELECT * FROM `fl_journal_calculate` WHERE `invoice_id`='".$_GET['id']."' ORDER BY `create_time` DESC";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($fl_calculate_j, $arr);
                                }
                            }else{

                            }


                            if (!empty($fl_calculate_j)) {
                                echo '
                                            <div class="invoceHeader" style="">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Расчётные листы по наряду:
                                                    </li>';
                                foreach ($fl_calculate_j as $calculate_item) {

                                    echo '
                                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                                    echo '
                                                        <a href="fl_calculate.php?id='.$calculate_item['id'].'" class="cellOrder ahref" style="position: relative;">
                                                            <b>Расчёт #' . $calculate_item['id'] . '</b> от ' . date('d.m.y', strtotime($calculate_item['date_in'])) . '<br>
                                                            <span style="font-size:80%;  color: #555;">';

                                    if (($calculate_item['create_time'] != 0) || ($calculate_item['create_person'] != 0)) {
                                        echo '
                                                                Добавлен: ' . date('d.m.y H:i', strtotime($calculate_item['create_time'])) . '<br>
                                                                <!--Автор: ' . WriteSearchUser('spr_workers', $calculate_item['create_person'], 'user', true) . '<br>-->';
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
                                                            '.WriteSearchUser('spr_workers', $calculate_item['worker_id'], 'user', true).'<br>
                                                        </div>
                                                        <div class="cellName">
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Сумма к расчёту:<br>
                                                                <span class="calculateOrder" style="font-size: 13px">' . $calculate_item['summ_inv'] . '</span> руб.
                                                            </div>
                                                        </div>
                                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="fl_deleteCalculateItem('.$calculate_item['id'].', '.$invoice_j[0]['client_id'].', '.$invoice_j[0]['id'].');">
                                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                        </div>
                                                        ';
                                    echo '
                                                    </li>';
                                }

                                echo '
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
		                            <div id="doc_title">Наряд #'.$_GET['id'].' Сумма: '.$invoice_j[0]['summ'].' / '.WriteSearchUser('spr_clients',  $invoice_j[0]['client_id'], 'user', false).' - Асмедика</div>';
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