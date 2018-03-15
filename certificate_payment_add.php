<?php

//payment_add.php
//Оплатить наряд заказ с баланса/счета

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($finances['add_own'] == 1) || ($finances['add_new'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['invoice_id'])){
					
					$invoice_j = SelDataFromDB('journal_invoice', $_GET['invoice_id'], 'id');

					if ($invoice_j != 0){
						//var_dump($invoice_j);
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
						
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
						//var_dump ($sheduler_zapis);
						
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

									<h2>Оплата наряда <a href="invoice.php?id='.$_GET['invoice_id'].'" class="ahref">#'.$_GET['invoice_id'].'</a> сертификатом</h2>';

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
										</li>';

								echo '
									</ul>';
							//}


							//Наряды

							echo '
								<div id="data">';
					
							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';
									
							echo '	
										<div class="invoceHeader" style="">
                                             <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="margin-bottom: 10px;">Сумма: <div id="calculateInvoice" style="">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                </div>';
							/*if ($sheduler_zapis[0]['type'] == 5) {
                                echo '
                                                <div>
                                                    <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                </div>';
                            }*/
                            echo '
                                                <div>
                                                    <div style="">Оплачено: <div class="calculateInvoice" style="color: #333;">'.$invoice_j[0]['paid'].'</div> руб.</div>
                                                </div>';
                                if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                    echo '
                                                    <div>
                                                        <div style="">Осталось внести: <div id="leftToPay" class="calculateInvoice" style="">'.($invoice_j[0]['summ'] - $invoice_j[0]['paid']).'</div> руб.</div>
                                                    </div>
                                                </div>';
                                }else{
                                    echo '</div>';
                                }
                            echo '
                                        <div>
                                            <a href="payment_add.php?invoice_id='.$_GET['invoice_id'].'" class="b">Оплатить со счёта</a>
                                        </div>';
							echo '
										</div>';




                            //работаем с балансом и доступными средствами
                            //!!! @@@
                            //Баланс контрагента
                            include_once 'ffun.php';
                            $client_balance = json_decode(calculateBalance ($client_j[0]['id']), true);
                            //Долг контрагента
                            //$client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);

                            $have_no_money_style = '';

							echo '	
										<div id="paymentAddRezult" class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
                                                <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">';
                            /*echo '
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Доступный остаток средств:
                                                    </li>';*/
                            /*if (($client_balance['summ'] <= 0) || ($client_balance['summ'] - $client_balance['debited'] <= 0)){
                                $have_no_money_style = 'display: none;';

                                /*echo '
                                                     <li style="font-size: 110%; color: red; margin-bottom: 5px;">
                                                        <div class="availableBalance" id="availableBalance" style="display: inline;">Нет доступных средств на счету</div>
                                                    </li>
                                                    
                                                    <li style="font-size: 100%; color: #7D7D7D; margin-bottom: 5px;">
                                                        <a href="add_order.php?client_id='.$client_j[0]['id'].'" class="b">Добавить приходный ордер</a>
                                                    </li>
                                                    <li style="font-size: 100%; color: #7D7D7D; margin-bottom: 5px;">
												        <a href="finance_account.php?client_id='.$client_j[0]['id'].'" class="b">Управление счётом</a>
											        </li>';*/

                            /*}else{
                                $have_no_money_style = '';

                                /*echo '
                                                    <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                                        <div class="availableBalance" id="addSummInPayment" style="display: inline; cursor:pointer;">' . ($client_balance['summ'] - $client_balance['debited']) . '</div><div style="display: inline;"> руб.</div>
                                                    </li>';*/
                                //Календарик
                                echo '
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-top: 20px; margin-bottom: 5px;">
                                                        <span style="color: rgb(125, 125, 125);">
                                                            Дата внесения: <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date("d").'.'.date("m").'.'.date("Y").'" onfocus="this.select();_Calendar.lcs(this)" 
                                                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                                                        </span>
                                                    </li>';
                                echo '
                                                     <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px; margin-bottom: 5px;">
                                                        <div class="cellsBlock2">
                                                            <div class="cellRight">
                                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                                    <li style="font-size: 105%; color: #7D7D7D; margin-bottom: 5px;">
                                                                        Сертификаты
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;" id="showCertPayAdd_button">
                                                                        <input type="button" class="b" value="Добавить сертификат" onclick="showCertPayAdd()">
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;">
                                                                        <table id="certs_result" width="100%" border="0" class="tableInsStat" style="background-color: rgba(255,255,250, .7); color: #333; display: none;">
                                                                            <tr>
                                                                                <td><span class="lit_grey_text">Номер</span></td>
                                                                                <td><span class="lit_grey_text">Номинал</span></td>
                                                                                <td><span class="lit_grey_text">К оплате (остаток)</span></td>
                                                                                <td style="text-align: center;"><i class="fa fa-times" aria-hidden="true" title="Удалить"></i></td>
                                                                            </tr>
                                                                        </table>
                                                                        
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                     </li>';
                                echo '
                                                    <li style="">
                                                        <div class="cellsBlock2">
                                                            <div class="cellRight">
                                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                                    <li style="font-size: 105%; color: #7D7D7D; margin-bottom: 5px;">
                                                                        Сумма к оплате (руб.) <label id="summ_error" class="error"></label>
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;">
                                                                        <div id="summ" class="calculateInvoice" style="color: #333;">0<div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="cellsBlock2">
                                                            <div class="cellRight">
                                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                                        Комментарий
                                                                    </li>
                                                                    <li style="font-size: 90%; margin-bottom: 5px;">
                                                                        <textarea name="comment" id="comment" cols="35" rows="2"></textarea>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                       
                                                    </li>';
                            //}

                            /*echo '
                                                     <li style="font-size: 100%; color: #7D7D7D; margin-bottom: 5px;">
                                                        <a href="certificate_payment_add.php?client_id='.$client_j[0]['id'].'" class="b">Оплатить сертификатом</a>
                                                     </li>';*/

                            echo '
                                                        <div id="have_money_or_not" style="'.$have_no_money_style.'">
                                                            <div id="errror"></div>
                                                            <input type="hidden" id="client_id" name="client_id" value="'.$invoice_j[0]['client_id'].'">
                                                            <input type="hidden" id="invoice_id" name="invoice_id" value="'.$_GET['invoice_id'].'">
                                                            <input type="button" class="b" value="Сохранить" onclick="showPaymentAddCert(\'add\')">
                                                        </div>';



                            echo '
                                                </ul>
											</div>';

							echo '
										</div>';


							echo '			
										</div>';
							echo '
									</div>';
							echo '
								</div>
								<div id="search_cert_input" style="display: none;">
							        <input type="text" size="30" name="searchdata" id="search_cert" placeholder="Наберите номер сертификата для поиска" value="" class="who"  autocomplete="off" style="width: 90%;">
							        <span class="lit_grey_text" style="font-size: 75%">Нажмите на галочку, чтобы добавить</span>
                                    <div id="search_result_cert" class="search_result_cert" style="text-align: left;"></div>
							    </div>

                            <!-- Подложка только одна -->
					        <div id="overlay"></div>';


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