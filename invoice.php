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

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['id'])){
					
					$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
					
					if ($invoice_j != 0){
						//var_dump($invoice_j);
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
						
						$sheduler_zapis = array();
						$invoice_ex_j = array();
						$invoice_ex_j_mkb = array();

						$client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');
						//var_dump($client_j);
						
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						
						$query = "SELECT * FROM `zapis` WHERE `id`='".$invoice_j[0]['zapis_id']."'";
						
						$res = mysql_query($query) or die(mysql_error().' -> '.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($sheduler_zapis, $arr);
							}
						}else
							$sheduler_zapis = 0;
						//var_dump ($sheduler_zapis);
						
						//if ($client !=0){
						if ($sheduler_zapis != 0){
						
							//сортируем зубы по порядку
							//ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
							
							//var_dump($_SESSION);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['mkb']);
							
							if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
							else $month = $sheduler_zapis[0]['month'];
							
							echo '
							<div id="status">
								<header>

									<h2>Наряд #'.$_GET['id'].'';
									
							if (($finances['edit'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="edit_invoice.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
								}
								if (($invoice_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
									echo '
										<a href="#" onclick="Ajax_reopen_invoice('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
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
										
								echo '
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
								
								$offices = SelDataFromDB('spr_office', $sheduler_zapis[0]['office'], 'offices');
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
										</li>';

								echo '
									</ul>';
							//}

							//Наряды

							//$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['id']."'";
							//!!! пробуем JOIN
							//$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['id']."';";
							$query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='".$_GET['id']."';";
							//var_dump($query);
							
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
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

							//Для МКБ
							$query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='".$_GET['id']."';";
							//var_dump ($query);
							
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
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


							echo '
								<div id="data">';
					
							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';
									
							echo '	
										<div id="errror" class="invoceHeader" style="">
                                             <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="">Сумма: <div id="calculateInvoice" style="">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                </div>';
							if ($sheduler_zapis[0]['type'] == 5) {
                                echo '
                                                <div>
                                                    <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                </div>';
                            }
                            echo '
											</div> 
                                            <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">'.$invoice_j[0]['paid'].'</div> руб.</div>
                                                </div>';
                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                echo '
                                                <div>
                                                    <div style="">Осталось внести: <div id="calculateInvoice" style="">'.($invoice_j[0]['summ'] - $invoice_j[0]['paid']).'</div> руб.</div>
                                                </div>
											</div>
											<div style="display: inline-block; vertical-align: top;">
                                                <div>
                                                    <div style=""><a href="payment_add.php?invoice_id='.$invoice_j[0]['id'].'" class="b">Оплатить</a></div>
                                                </div>
											</div>';
							}else{
                                echo '</div>';
                            }
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
							if ($sheduler_zapis[0]['type'] == 5){
								echo '
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
													<i><b>Страх.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
													<i><b>Сог.</b></i>
												</div>';
							}
							echo '
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
													<i><b>Цена, руб.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Кол-во</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Коэфф.</b></i>
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
											</div>';

											
											
											
					foreach ($invoice_ex_j as $ind => $invoice_data){
						
						//var_dump($invoice_data);
						echo '
							<div class="cellsBlock">
								<div class="cellCosmAct toothInInvoice" style="text-align: center;">';
						if ($ind == 99){
							echo 'П';
						}else{
							echo $ind;
						}
						echo '
								</div>';
						
						//Диагноз
						if ($sheduler_zapis[0]['type'] == 5){
							
							if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])){
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
									$rezult2 = array();
									
									$query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";	
									
									$res = mysql_query($query) or die(mysql_error().' -> '.$query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										while ($arr = mysql_fetch_assoc($res)){
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
							}					

							
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
											</b>. <i>Диагноз</i>: '.$invoice_data[0]['mkb_id'].'
										</div>
									</div>';
							}*/
							
						}
						
						foreach ($invoice_data as $item){
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
						
									$res = mysql_query($query) or die(mysql_error().' -> '.$query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										while ($arr = mysql_fetch_assoc($res)){
											array_push($rez, $arr);
										}
										$rezult2 = $rez;
									}else{
										$rezult2 = 0;
									}
									
									if ($rezult2 != 0){
										
										echo $rezult2[0]['name'];
										
										//Узнать цену
										$arr = array();
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
												//var_dump(strtotime($invoice_j[0]['create_time']));
												
												foreach($price_arr as $date_from => $value_arr){
													if (strtotime($invoice_j[0]['create_time']) > $date_from){
														$price = $value_arr['price'];
														break;
													}
												}
											}
										}else{
											$price = '?';
										}
										
									}else{
										echo '?';
									}
									
									echo '
									</div>';
									
									$price = $item['price'];
									
									if ($sheduler_zapis[0]['type'] == 5){
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
									}
									
									if ($sheduler_zapis[0]['type'] == 5){
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
									}
									
									echo '
									<div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
										<b>'.$price.'</b>
									</div>
									<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
										<b>'.$item['quantity'].'</b>
									</div>
									<div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
										'.$item['spec_koeff'].'
									</div>
									<div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
										'.$item['discount'].'
									</div>
									<div class="cellCosmAct settings_text" guarantee="'.$item['guarantee'].'" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">';
									if ($item['guarantee'] != 0){
										echo '
											<i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
									}else{
										echo '-';
									}
									echo '
									</div>
									<div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
										<b>';
										
									//вычисляем стоимость
									$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
			
									//с учетом скидки акции
									$stoim_item = $stoim_item - ($stoim_item * $item['discount'] / 100);
									$stoim_item = round($stoim_item/10) * 10;
									
									echo $stoim_item;
			
									//Общая стоимость
									if ($item['guarantee'] == 0){
										if ($item['insure'] != 0){
											if ($item['insure_approve'] != 0){
												$summins += $stoim_item;
											}
										}else{
											$summ += $stoim_item;
										}
									}

			
									echo '</b>
									</div>
								</div>';
						}
						echo '
							</div>';
					}
					
					
							echo '	
										<div class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
											</div>
											<!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
												Итого:';
							if (($summ != $invoice_j[0]['summ']) || ($summins != $invoice_j[0]['summins'])){
								/*echo '<br>
									<span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
							}

							echo '				
													
											</div>
											<div class="cellName" style="padding: 2px 4px;">
												<div>
													<div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">'.$summ.'</div> руб.</div>
												</div>';
							if ($sheduler_zapis[0]['type'] == 5){
								echo '
												<div>
													<div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">'.$summins.'</div> руб.</div>
												</div>-->';
							}
							echo '
										</div>';		

											
							echo '			
										</div>';
							echo '
									</div>';
							echo '
								</div>
							';
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