<?php

//payment_from_alien_add.php
//Оплатить наряд заказ с баланса/счета другого пациента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($finances['add_own'] == 1) || ($finances['add_new'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';
			
			require 'config.php';

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if ((isset($_GET['invoice_id'])) && (isset($_GET['client_id']))){
					
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

                        $msql_cnnct = ConnectToDB();
						
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
						//if (!empty($sheduler_zapis)){
						
							//сортируем зубы по порядку
							//ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);

							//var_dump($_SESSION);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['mkb']);

                            if ($invoice_j[0]['type'] != 88) {

                                if ($sheduler_zapis[0]['month'] < 10) $month = '0' . $sheduler_zapis[0]['month'];
                                else $month = $sheduler_zapis[0]['month'];
                            }

							echo '
							<div id="status">
								<header>

									<h2>Внесение оплаты по наряду <a href="invoice.php?id='.$_GET['invoice_id'].'" class="ahref">#'.$_GET['invoice_id'].'</a> с выбором плательщика</h2>';

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

								

                        // !!! **** тест с записью
                        include_once 'showZapisRezult.php';


                        echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);

							//Наряды

							echo '
								<div id="data">';
					
							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                            if (isset($_SESSION['filial'])) {
                                echo '	
										<div class="invoceHeader" style="">
                                             <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="margin-bottom: 10px;">Сумма: <div id="calculateInvoice" style="">' . $invoice_j[0]['summ'] . '</div> руб.</div>
                                                </div>';
                                /*if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
                                                <div>
                                                    <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                                </div>';
                                }*/
                                echo '
                                                <div>
                                                    <div style="">Оплачено: <div class="calculateInvoice" style="color: #333;">' . $invoice_j[0]['paid'] . '</div> руб.</div>
                                                </div>';
                                if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                    echo '
                                                    <div>
                                                        <div style="">Осталось внести: <div id="leftToPay" class="calculateInvoice" style="">' . ($invoice_j[0]['summ'] - $invoice_j[0]['paid']) . '</div> руб.</div>
                                                    </div>
                                                </div>';
                                } else {
                                    echo '
                                        </div>';
                                }
                                /*echo '
                                        <div>
                                            <a href="certificate_payment_add.php?invoice_id=' . $_GET['invoice_id'] . '" class="b">Оплатить сертификатом</a>
                                        </div>';*/
                                echo '
										</div>';


                                //работаем с балансом и доступными средствами
                                //!!! @@@
                                //Баланс контрагента
                                include_once 'ffun.php';
                                $client_balance = json_decode(calculateBalance($_GET['client_id']), true);
                                //Долг контрагента
                                //$client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);

                                $have_no_money_style = '';

                                echo '	
										<div id="paymentAddRezult" class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
                                                <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">';

                                echo '
                                                <li style="margin-top: 5px; margin-bottom: 5px; font-size: 90%;">
                                                   <b>Плательщик: </b><br>
                                                   <input type="text" size="40" name="searchdata" id="search_client" placeholder="Введите первые три буквы для поиска" value="' . WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full', false) . '" class="who"  autocomplete="off" style="margin-top: 5px; font-size: 120%;">
                                                   <!--!!!Изменить-->
                                                   <span id="changeNewPayer_id" class="button_tiny" style="font-size: 110%; cursor: pointer" onclick="changeNewPayer();"><i class="fa fa-check-square" style=" color: green;"></i> Изменить</span>
                                                   <ul id="search_result" class="search_result"></ul><br>
                                                </li>
                                                <input type="hidden" id="new_payer_id" value="'.$_GET['client_id'].'">';

                                echo '
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Доступный остаток средств:
                                                    </li>';
                                if (($client_balance['summ'] <= 0) || ($client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'] <= 0)) {
                                    $have_no_money_style = 'display: none;';

                                    echo '
                                                     <li style="font-size: 110%; color: red; margin-bottom: 5px;">
                                                        <div class="availableBalance" id="availableBalance" style="display: inline;">Нет доступных средств на счету</div>
                                                    </li>
                                                    
                                                    <!--<li style="font-size: 100%; color: #7D7D7D; margin-bottom: 5px;">
                                                        <a href="add_order.php?client_id=' . $client_j[0]['id'] . '" class="b">Добавить приходный ордер</a>
                                                    </li>
                                                    <li style="font-size: 100%; color: #7D7D7D; margin-bottom: 5px;">
												        <a href="finance_account.php?client_id=' . $client_j[0]['id'] . '" class="b">Управление счётом</a>
											        </li>-->';

                                } else {
                                    $have_no_money_style = '';

                                    echo '
                                                    <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                                        <div class="availableBalance" id="addSummInPayment" style="display: inline; cursor:pointer;">' . ($client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund']) . '</div><div style="display: inline;"> руб.</div>
                                                    </li>';

                                    //Филиал
                                    echo '
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px; margin-bottom: 5px;">
                                                        Филиал, на котором произведена оплата: <span style="color: #333;">';
                                                            echo $filials_j[$_SESSION['filial']]['name'].'<input type="hidden" id="filial_id" value="'.$_SESSION['filial'].'">';
                                    echo '
                                                        </span>
                                                    </li>';

                                    //Календарик
                                    echo '
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        <span style="color: rgb(125, 125, 125);">
                                                            Дата внесения: <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="' . date("d") . '.' . date("m") . '.' . date("Y") . '" onfocus="this.select();_Calendar.lcs(this)" 
                                                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                                                        </span>';
                                    echo '
                                                    </li>';
                                    echo '
                                                    <li style="">
                                                        <div class="cellsBlock2">
                                                            <div class="cellRight">
                                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                                    <li style="font-size: 105%; color: #7D7D7D; margin-bottom: 5px;">
                                                                        Внесите сумму к оплате (руб.) <label id="summ_error" class="error"></label>
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;">
                                                                        <input type="text" size="15" name="summ" id="summ" placeholder="Введите сумму" value="0" class="who2"  autocomplete="off">
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
                                }
                                /*echo '
                                                     <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px; margin-bottom: 5px;">
                                                        <div class="cellsBlock2">
                                                            <div class="cellRight">
                                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                                    <li style="font-size: 105%; color: #7D7D7D; margin-bottom: 5px;">
                                                                        Оплатить сертификатом
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;">
                                                                        <!--<input type="button" class="b" value="Добавить сертификат" onclick="showCertPayAdd()">-->
                                                                        <a href="certificate_payment_add.php" class="b">Добавить сертификат</a>
                                                                    </li>
                                                                    <li style="margin-bottom: 5px;">
                                                                        <table id="certs_result" width="100%" border="0" class="tableInsStat" style="background-color: rgba(255,255,250, .7); color: #333; display: none;">
                                                                            <tr>
                                                                                <td><span class="lit_grey_text">Номер</span></td><td><span class="lit_grey_text">Номинал</span></td><td><span class="lit_grey_text">К оплате (остаток)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                        
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                     </li>';*/

                                echo '
                                                        <div id="have_money_or_not" style="' . $have_no_money_style . '">
                                                            <div id="errror"></div>
                                                            <input type="hidden" id="client_id" name="client_id" value="' . $invoice_j[0]['client_id'] . '">
                                                            <input type="hidden" id="invoice_id" name="invoice_id" value="' . $_GET['invoice_id'] . '">
                                                            <input type="button" class="b" value="Сохранить" onclick="showPaymentAdd(\'add\', true)">
                                                        </div>';


                                echo '
                                                </ul>
											</div>';
                                echo '
										</div>';
                                echo '			
                                    </div>';
                            }else{
                                echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                            }
							echo '
									</div>';
							echo '
								</div>
								<div id="search_cert_input" style="display: none;">
							        <input type="text" size="30" name="searchdata" id="search_cert" placeholder="Наберите номер сертификата для поиска" value="" class="who"  autocomplete="off" style="width: 90%;">
							        <br><span class="lit_grey_text" style="font-size: 75%">Нажмите на галочку, чтобы добавить</span>
                                    <div id="search_result_cert" class="search_result_cert" style="text-align: left;"></div>
							    </div>

                            <!-- Подложка только одна -->
					        <div id="overlay"></div>';

                        echo '
							<script>
                                $(".search_result").on("click", "li", function(){

                                    let client_id = $(this).attr("client_id");
                                    //console.log(client_id);
                                    
                                    $("#new_payer_id").val(client_id);
        
                                    $("#changeNewPayer_id").css({
                                        "border": "1px dashed red",
                                        "color": "red",
                                    })
                                    
                                });
                            </script>';


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