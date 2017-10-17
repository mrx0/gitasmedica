<?php

//client.php
//Карточка клиента

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'tooth_status.php';
            include_once 'variables.php';


            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

            //require 'config.php';

			//переменная для просроченных
			$allPayed = true;

            //доступный остаток
            $dostOstatok = 0;

			$text_tooth_status = array(
				'up' => -9,
				'down' => 138,
				'left' => array (
					1 => 258,
					2 => 221,
					3 => 186,
					4 => 149,
					5 => 113,
					6 => 77,
					7 => 42,
					8 => 5,						
				),
				'right' => array (
					1 => 311,
					2 => 350,
					3 => 386,
					4 => 422,
					5 => 459,
					6 => 495,
					7 => 529,
					8 => 566,			
				),
			);
			
			$client_j = SelDataFromDB('spr_clients', $_GET['id'], 'user');

			//!!!ДР по-новому надо сделать
            /*
SELECT
    `name`,
    `birth`,
    (YEAR(CURRENT_DATE)-YEAR(`birth`))-(RIGHT(CURRENT_DATE,5)<RIGHT(`birth`,5)
    ) AS `age`
FROM `users`
ORDER BY `name`;
            */


			//var_dump($client_j);
			if ($client_j != 0){
				echo '
					<script src="js/init.js" type="text/javascript"></script>
					<!--<script src="js/init2.js" type="text/javascript"></script>-->
					<div id="status">
						<header>
							<h2>
								Карточка пациента #'.$client_j[0]['id'].'';
				
				if (($clients['edit'] == 1) || $god_mode){
					if ($client_j[0]['status'] != 9){
						echo '
									<a href="client_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					if (($client_j[0]['status'] == 9) && (($clients['close'] == 1) || $god_mode)){
						echo '
							<a href="#" onclick="Ajax_reopen_client('.$_SESSION['id'].', '.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
					}
				}
				if (($clients['close'] == 1) || $god_mode){
					if ($client_j[0]['status'] != 9){
						echo '
									<a href="move_all.php?client='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Переместить"><i class="fa fa-external-link-square" aria-hidden="true"></i></a>';
						echo '
									<a href="client_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
					}
					
				}

				echo '
							</h2>';
							
				if ($client_j[0]['status'] == 9){
					echo '<i style="color:red;">Пациент удалён (заблокирован).</i><br>';												
				}
				
				echo '
							Номер карты: '.$client_j[0]['card'].'
						</header>';

				echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

                echo $block_fast_search_client;

				echo '
					</div>';

				echo '
						<div id="data">';


				echo '

								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">'.$client_j[0]['full_name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">';
				if (($client_j[0]['birthday'] == '-1577934000') || ($client_j[0]['birthday'] == 0)){
					echo 'не указана';
				}else{
					echo 
						date('d.m.Y', $client_j[0]['birthday']).'<br>
						полных лет <b>'.getyeardiff($client_j[0]['birthday'], 0).'</b>';
				}
				echo '						
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">';
				if ($client_j[0]['sex'] != 0){
					if ($client_j[0]['sex'] == 1){
						echo 'М';
					}
					if ($client_j[0]['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo 
									'</div>
								</div>';
				
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Телефон</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">мобильный</span><br>
											'.$client_j[0]['telephone'].'
										</div>';
				if ($client_j[0]['htelephone'] != ''){
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">домашний</span><br>
											'.$client_j[0]['htelephone'].'
										</div>';
				}
				echo '
									</div>
								</div>';
								
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Паспорт</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер</span><br>
											'.$client_j[0]['passport'].'
										</div>';
				if (($client_j[0]['alienpassportser'] != NULL) && ($client_j[0]['alienpassportnom'] != NULL)){
					echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
											'.$client_j[0]['alienpassportser'].'
											'.$client_j[0]['alienpassportnom'].'
										</div>';
				}
				echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Выдан когда</span><br>
											'.$client_j[0]['passportvidandata'].'
										</div>
										<div>
											<span style="font-size: 70%; color: #AAA">Кем</span><br>
											'.$client_j[0]['passportvidankem'].'
										</div>
									</div>
								</div>';
								
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">
										'.$client_j[0]['address'].'
									</div>
								</div>';
				if ($client_j[0]['polis'] != ''){
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Полис</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Номер</span><br>
											'.$client_j[0]['polis'].'
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Дата</span><br>
											'.$client_j[0]['polisdata'].'
										</div>';
					if ($client_j[0]['insure'] == 0){
						$insure = 'не указана';
					}else{
						$insures_j = SelDataFromDB('spr_insure', $client_j[0]['insure'], 'offices');
						if ($insures_j == 0){
							$insure = 'ошибка';
						}else{
							$insure = $insures_j[0]['name'];
						}
					}
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>
											'.$insure.'
										</div>';
					echo '					
									</div>
								</div>';
				}

				if (($client_j[0]['fo'] != '') || ($client_j[0]['io'] != '')){
					echo '
							<div class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block;">
								<div class="cellLeft" style="font-weight: bold; width: 500px;">
									Опекун
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									'.$client_j[0]['fo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									'.$client_j[0]['io'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									'.$client_j[0]['oo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Телефон</div>
								<div class="cellRight">
									<div>
										<span style="font-size: 80%; color: #AAA">мобильный</span><br>
										'.$client_j[0]['telephoneo'].'
									</div>';
					if ($client_j[0]['htelephoneo'] != ''){
						echo '
									<div>
										<span style="font-size: 80%; color: #AAA">домашний</span><br>
										'.$client_j[0]['htelephoneo'].'
									</div>';
					}
					echo '
								</div>
							</div>';
				}
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">'.$client_j[0]['comment'].'</div>
								</div>';
								
				if (TRUE){
				echo '				
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">стоматология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client_j[0]['therapist'], 'user', true).'</div>
								</div>';
				}
				if (TRUE){
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">косметология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client_j[0]['therapist2'], 'user', true).'</div>
								</div>';
				}
								
				echo '
								<div class="cellsBlock2">
									<span style="font-size:80%;">';
				if (($client_j[0]['create_time'] != 0) || ($client_j[0]['create_person'] != 0)){
					echo '
										Добавлен: '.date('d.m.y H:i', $client_j[0]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client_j[0]['create_person'], 'user', true).'<br>';
				}else{
					echo 'Добавлен: не указано<br>';
				}
				if (($client_j[0]['last_edit_time'] != 0) || ($client_j[0]['last_edit_person'] != 0)){
					echo '
										Последний раз редактировался: '.date('d.m.y H:i', $client_j[0]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client_j[0]['last_edit_person'], 'user', true).'';
				}
				echo '
									</span>
								</div>';
								
								
				//Смотрим счёт (авансы/долги)
				if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode){

				    //Долги/авансы
                    //
                    //!!! @@@
                    //Баланс контрагента
                    include_once 'ffun.php';
                    $client_balance = json_decode(calculateBalance ($_GET['id']), true);
                    //Долг контрагента
                    $client_debt = json_decode(calculateDebt ($_GET['id']), true);

                    if ($client_debt['summ'] > 0){
                        $allPayed = false;
                    }

                    /*$clientDP = DebtsPrepayments ($client_j[0]['id']);

					if ($clientDP != 0){
						//var_dump ($clientDP);
						$allPayed = false;
						for ($i=0; $i<count($clientDP); $i++){
							$repayments = Repayments($clientDP[$i]['id']);
							//var_dump ($repayments);
							
							if ($repayments != 0){
								//var_dump ($repayments);
								
								$ostatok = 0;
								foreach($repayments as $value){
									$ostatok += $value['summ'];
								}
								if ($clientDP[$i]['summ'] - $ostatok == 0){
									$allPayed = true;
								}else{
									$allPayed = false;
								}
							}
								
						}
					}*/
				}
				if ($client_j[0]['status'] != 9){
					//Вкладки 
					echo '
						<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
							<ul>
								<li><a href="#tabs-1">Посещения (запись)</a></li>';
								
					if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode){
						echo '
								<li>
									<a href="#tabs-2">Счёт</a>';
						if (!$allPayed){
							echo '
									<div class="notes_count2">
										<i class="fa fa-exclamation-circle" aria-hidden="true" title="Есть долги"></i>
									</div>';
							}
						echo '
								</li>';
					}
					
					if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '
								<li><a href="#tabs-3">Стоматология</a></li>';
					}
					
					if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
						echo '
								<li><a href="#tabs-4">Косметология</a></li>';
					}
					echo '
							</ul>';
							
					echo '
							<div id="tabs-1">';
					
					//Запись пациента (aka посещения) -->
				
					echo '
								<div style="margin: 10px 0;">
									<ul style="margin-left: 6px; margin-bottom: 20px;">';
									
					$sheduler_zapis = array();

                    $msql_cnnct = ConnectToDB ();

					$query = "SELECT * FROM `zapis` WHERE `patient`='".$client_j[0]['id']."' ORDER BY `year`, `month`, `day`, `start_time` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);
					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							array_push($sheduler_zapis, $arr);
						}
					}else
						$sheduler_zapis = 0;
					
					//var_dump ($sheduler_zapis);

                    if ($sheduler_zapis != 0){

                        $sheduler_zapis = array_reverse($sheduler_zapis);

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

                        echo showZapisRezult($sheduler_zapis, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, true);

					}else{
						echo '
										<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
											<span style="color: rgb(255, 30, 30);">Нет записи</span>
										</li>';

					}






					echo '
									</ul>
								</div>';
						
					echo '
							</div>';

					//--> Запись пациента (aka посещения)
					
					
					//Счёт -->
					
					if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode){
						if ($client_j[0]['status'] != 9){
						
							echo '
							<div id="tabs-2">';

							echo '<div>';

							//!!! @@@
                            //Баланс контрагента
                            //include_once 'ffun.php';
                            //$client_balance = json_decode(calculateBalance ($_GET['id']), true);
                            //Долг контрагента
                            //$client_debt = json_decode(calculateDebt ($_GET['id']), true);

                            //Если доступный остаток ОТРИЦАТЕЛЕН
                            $dostOstatok = $client_balance['summ'] - $client_balance['debited'];

                            //var_dump(json_decode($client_balance, true));
                            echo '
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Доступный остаток средств:
                                        </li>
                                        <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                             '.$dostOstatok.' руб.
                                        </li>
                                        <!--<li style="font-size: 85%; color: #7D7D7D; margin-top: 10px;">
                                            Всего внесено:
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
                                            '.$client_balance['summ'].' руб.
                                        </li>-->
                                        <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px;">
                                             <a href="finance_account.php?client_id='.$client_j[0]['id'].'" class="b">Управление счётом</a>
                                        </li>                                        
                                    </ul>
                            
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Общий долг составляет:
                                        </li>
                                        <li class="calculateInvoice" style="font-size: 110%; font-weight: bold;">
                                             '.$client_debt['summ'].' руб.
                                        </li>
                                      
                                     </ul>';

                            echo '</div>';

                            echo '<div>';



                            //Выписанные наряды
                            $arr = array();
                            $invoice_j = array();

                            echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Выписанные наряды</li>';

                            $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$_GET['id']."'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($invoice_j, $arr);
                                }
                            }else
                                $invoice_j = 0;
                            //var_dump ($invoice_j);

                            $invoiceAll_str = '';
                            $invoiceClose_str = '';

                            if ($invoice_j != 0) {
                                //var_dump ($invoice_j);

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
                                    if ($invoice_item['summ'] != $invoice_item['paid']) {
                                        $invoiceTemp_str .= '
												<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
													Осталось <a href="payment_add.php?invoice_id='.$invoice_item['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
													<span class="calculateInvoice" style="font-size: 13px">'.($invoice_item['summ'] - $invoice_item['paid']).'</span> руб.
												</div>';
                                    }

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

                            }else{
                                echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</li>';
                            }

                            echo '
								</ul>';



                            //Внесенные оплаты/ордеры
                            $arr = array();
                            $order_j = array();

                            echo '
								<ul id="orders" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
									    Внесенные оплаты/ордеры	<a href="add_order.php?client_id='.$client_j[0]['id'].'" class="b">Добавить новый</a>
									</li>';

                            $query = "SELECT * FROM `journal_order` WHERE `client_id`='".$_GET['id']."'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($order_j, $arr);
                                }
                            }else
                                $order_j = 0;
                            //var_dump ($order_j);

                            $orderAll_str = '';
                            $orderClose_str = '';

                            if ($order_j != 0){
                                //var_dump ($order_j);

                                foreach($order_j as $order_item){

                                    $order_type_mark = '';

                                    if ($order_item['summ_type'] == 1){
                                        $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал"></i>';
                                    }

                                    if ($order_item['summ_type'] == 2){
                                        $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                                    }
                                    $orderTemp_str = '';

                                    $orderTemp_str .= '
										<li class="cellsBlock" style="width: auto;">';
                                    $orderTemp_str .= '
											<a href="order.php?id='.$order_item['id'].'" class="cellOrder ahref" style="position: relative;">
												<b>Ордер #'.$order_item['id'].'</b> от '.date('d.m.y' ,strtotime($order_item['date_in'])).'<br>
												<span style="font-size:80%;  color: #555;">';

                                    if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)){
                                        $orderTemp_str .= '
														Добавлен: '.date('d.m.y H:i' ,strtotime($order_item['create_time'])).'<br>
														<!--Автор: '.WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true).'<br>-->';
                                    }else{
                                        $orderTemp_str .= 'Добавлен: не указано<br>';
                                    }
                                    if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                        $orderTemp_str .= '
														Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
														<!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                    }
                                    $orderTemp_str .= '
												</span>
												<span style="position: absolute; top: 2px; right: 3px;">'. $order_type_mark.'</span>
											</a>
											<div class="cellName">
												<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
													Сумма:<br>
													<span class="calculateOrder" style="font-size: 13px">'.$order_item['summ'].'</span> руб.
												</div>';
                                    /*if ($order_item['summins'] != 0){
                                        echo '
												<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
													Страховка:<br>
													<span class="calculateInsInvoice" style="font-size: 13px">'.$order_item['summins'].'</span> руб.
												</div>';
                                    }*/
                                    $orderTemp_str .= '
											</div>';
                                    $orderTemp_str .= '
										</li>';

                                    if ($order_item['status'] != 9) {
                                        $orderAll_str .= $orderTemp_str;
                                    } else {
                                        $orderClose_str .= $orderTemp_str;
                                    }

                                }


                                if (strlen($orderAll_str) > 1){
                                    echo $orderAll_str;
                                }else{
                                    echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;">Нет ордеров</li>';
                                }

                                //Удалённые
                                /*if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                                    echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                                    echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                                    echo $orderClose_str;
                                    echo '</div>';
                                }*/

                            }else{
                                echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет ордеров</li>';
                            }

                            echo '
								</ul>';



                            echo '</div>';

							//Сертификаты
							/*echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Сертификаты пациента</li>
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
										<a href="add_certificate.php?client_id='.$client_j[0]['id'].'" class="b">Добавить сертификат</a>
									</li>';
							echo '
								</ul>';*/
						
							echo '				
								<div class="cellsBlock2">
									<a href="client_finance.php?client='.$client_j[0]['id'].'" class="b">Долги/Авансы <i class="fa fa-rub"></i> (старое)</a><br>';

							if (!$allPayed)
								echo '<i style="color:red;">Есть не погашенное</i>';					
										
							echo '
									</div>';
					
							echo '
							</div>';
						}
					}	
					
					//--> Счёт
					
					
						
						/*echo '				
										<div class="cellsBlock2">';
						/*if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
							echo '
											<a href="#" id="showDiv1" class="b">Стоматология</a>';
						}
						if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
							echo '
											<a href="#" id="showDiv2" class="b">Косметология</a>';
						}
						echo '
										</div>';*/
						
					//Стоматология -->
						
					if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '
							<div id="tabs-3">';	
						/*echo '
										<div id="div1">';*/
						if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || ($god_mode)){
							echo '	
								<!--<a href="add_error.php" class="b">Добавить осмотр</a>-->';
						}
						
						if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
							echo '	
								<a href="stom_history.php?client='.$client_j[0]['id'].'" class="b">История</a>';
						}
						/*if (($clients['close'] == 1) || $god_mode){
							echo '
								<a href="stom_move.php?id='.$client_j[0]['id'].'" class="b">Переместить</a>';
						}*/




						//Выберем из базы последнюю запись
						$t_f_data_db = array();
						
						/*require 'config.php';*/
						/*mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");*/

						$time = time();

						$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` DESC LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($t_f_data_db, $arr);
							}
						}else
							$t_f_data_db = 0;
						
						
						if ($t_f_data_db != 0){
							//var_dump ($t_f_data_db);

							//echo '							<script src="js/init.js" type="text/javascript"></script>';
							//Выберем из базы первую запись
							$t_f_data_db_first = array();
							
							/*require 'config.php';
							mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
							mysql_select_db($dbName) or die(mysql_error()); 
							mysql_query("SET NAMES 'utf8'");
							$time = time();
							$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` ASC LIMIT 1";
							$res = mysql_query($query) or die($q);
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
									array_push($t_f_data_db_first, $arr);
								}
							}else
								$t_f_data_db_first = 0;
							mysql_close();*/
							
							//$t_f_data_db = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');								
							//var_dump ($t_f_data_db);
							//var_dump ($t_f_data_db_first);
							/*if ($t_f_data_db_first !=0){
								if ($t_f_data_db_first[0]['id'] != $t_f_data_db[0]['id']){
									$t_f_data_db[count($t_f_data_db)] = $t_f_data_db_first[0];
								}
							}*/
									
							for ($z = 0; $z < count ($t_f_data_db); $z++){
								$dop = array();
								
								
								//ЗО и тд
								$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$t_f_data_db[$z]['id']}'";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

								$number = mysqli_num_rows($res);
								if ($number != 0){
									while ($arr = mysqli_fetch_assoc($res)){
										array_push($dop, $arr);
									}
									
								}
								
								echo '
								<div class="cellsBlock3">';
								echo '
									<div class="cellLeft">
										<a href="task_stomat_inspection.php?id='.$t_f_data_db[$z]['id'].'" class="ahref">'.date('d.m.y H:i', $t_f_data_db[$z]['create_time']).'</a>
									</div>
									<div class="cellRight">';
										
								include_once 'teeth_map_db.php';
								include_once 't_surface_name.php';
								include_once 't_surface_status.php';

								include_once 'root_status.php';
								include_once 'surface_status.php';
								include_once 't_context_menu.php';
											
								$t_f_data = array();
								
								if ($z == 0){
									$n = '';
								}else{
									$n = $z;
								}
								
								$sw = 0;
								$stat_id = $t_f_data_db[$z]['id'];
								
								unset($t_f_data_db[$z]['id']);
								unset($t_f_data_db[$z]['create_time']);
								//echo "echo$sw";
								//var_dump ($surfaces);
								$t_f_data_temp_refresh = '';
								
								unset($t_f_data_db[$z]['id']);
								unset($t_f_data_db[$z]['office']);
								unset($t_f_data_db[$z]['client']);
								unset($t_f_data_db[$z]['create_time']);
								unset($t_f_data_db[$z]['create_person']);
								unset($t_f_data_db[$z]['last_edit_time']);
								unset($t_f_data_db[$z]['last_edit_person']);
								unset($t_f_data_db[$z]['worker']);
								unset($t_f_data_db[$z]['comment']);
								unset($t_f_data_db[$z]['zapis_date']);
								unset($t_f_data_db[$z]['zapis_id']);
								
								foreach ($t_f_data_db[$z] as $key => $value){
									//$t_f_data_temp_refresh .= $key.'+'.$value.':';
									
									
									//var_dump(json_decode($value, true));
									$surfaces_temp = explode(',', $value);
									//var_dump ($surfaces_temp);
									foreach ($surfaces_temp as $key1 => $value1){
										//$t_f_data[$key] = json_decode($value, true);
										///!!!Еба костыль
										if ($key1 < 13){
											$t_f_data[$key][$surfaces[$key1]] = $value1;
										}
									}
								}
								
								/*unset($t_f_data['id']);
								unset($t_f_data['office']);
								unset($t_f_data['client']);
								unset($t_f_data['create_time']);
								unset($t_f_data['create_person']);
								unset($t_f_data['last_edit_time']);
								unset($t_f_data['last_edit_person']);
								unset($t_f_data['worker']);
								unset($t_f_data['comment']);*/
								
								//unset($dop[0]['id']);
								
								//var_dump ($t_f_data);
								if (!empty($dop[0])){
									//var_dump($dop[0]);
									unset($dop[0]['id']);
									//var_dump($dop[0]);
									foreach($dop[0] as $key => $value){
										//var_dump($value);
										if ($value != '0'){
											//var_dump($value);
											$dop_arr = json_decode($value, true);
											//var_dump($dop_arr);
											foreach ($dop_arr as $n_key => $n_value){
												if ($n_key == 'zo'){
													$t_f_data[$key]['zo'] = $n_value;
													//$t_f_data_draw[$key]['zo'] = $n_value;
												}
												if ($n_key == 'shinir'){
													$t_f_data[$key]['shinir'] = $n_value;
													//$t_f_data_draw[$key]['shinir'] = $n_value;
												}
												if ($n_key == 'podvizh'){
													$t_f_data[$key]['podvizh'] = $n_value;
													//$t_f_data_draw[$key]['podvizh'] = $n_value;
												}
												if ($n_key == 'retein'){
													$t_f_data[$key]['retein'] = $n_value;
													//$t_f_data_draw[$key]['retein'] = $n_value;
												}
												if ($n_key == 'skomplect'){
													$t_f_data[$key]['skomplect'] = $n_value;
													//$t_f_data_draw[$key]['skomplect'] = $n_value;
												}
											}
										}
									}
								}
							
								//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
								//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
								//var_dump($t_f_data);
								//echo $t_f_data_temp_refresh;
								
								
								echo '
										<div class="map'.$n.' map_exist" id="map'.$n.'">
											<div class="text_in_map" style="left: 15px">8</div>
											<div class="text_in_map" style="left: 52px">7</div>
											<div class="text_in_map" style="left: 87px">6</div>
											<div class="text_in_map" style="left: 123px">5</div>
											<div class="text_in_map" style="left: 159px">4</div>
											<div class="text_in_map" style="left: 196px">3</div>
											<div class="text_in_map" style="left: 231px">2</div>
											<div class="text_in_map" style="left: 268px">1</div>
											
											<div class="text_in_map" style="left: 321px">1</div>
											<div class="text_in_map" style="left: 360px">2</div>
											<div class="text_in_map" style="left: 396px">3</div>
											<div class="text_in_map" style="left: 432px">4</div>
											<div class="text_in_map" style="left: 469px">5</div>
											<div class="text_in_map" style="left: 505px">6</div>
											<div class="text_in_map" style="left: 539px">7</div>
											<div class="text_in_map" style="left: 576px">8</div>';

								

								//var_dump ($teeth_map_temp);	
								
								//!!!ТЕСТ ИНКЛУДА ОТРИСОВКИ ЗФ
								//require_once 'for32_teeth_map_svg.php';
								
								
								//$teeth_map_temp = SelDataFromDB('teeth_map', '', '');
								$teeth_map_temp = $teeth_map_db;
								foreach ($teeth_map_temp as $value){
									$teeth_map[mb_substr($value['tooth'], 0, 3)][mb_substr($value['tooth'], 3, strlen($value['tooth'])-3)]=$value['coord'];
								}
								//$teeth_map_d_temp = SelDataFromDB('teeth_map_d', '', '');
								$teeth_map_d_temp = $teeth_map_d_db;
								foreach ($teeth_map_d_temp as $value){
									$teeth_map_d[$value['tooth']]=$value['coord'];
								}
								//$teeth_map_pin_temp = SelDataFromDB('teeth_map_pin', '', '');
								$teeth_map_pin_temp = $teeth_map_pin_db;
								foreach ($teeth_map_pin_temp as $value){
									$teeth_map_pin[$value['tooth']]=$value['coord'];
								}
								
								for ($i=1; $i <= 4; $i++){
									for($j=1; $j <= 8; $j++){
										
										$DrawRoots = TRUE;				
										$menu = 't_menu';
										if (isset($sw)){
											if ($sw == '1'){
												$t_status = 'yes';
											}else{
												$t_status = 'no';
											}
										}else{
											$t_status = 'yes';
										}
										//$t_status = 'yes';
										$color = "#fff";
										$color_stroke = '#74675C';
										$stroke_width = 1;
										$n_zuba = 't'.$i.$j;
										//echo $n_zuba.'<br />';
										if ($t_f_data[$i.$j]['alien'] == '1'){
											$color_stroke = '#F7273F';
											$stroke_width = 3;
										}
										
										foreach($teeth_map[$n_zuba] as $surface => $coordinates){
											
											$color = "#fff";
											//!!!! попытка с молочным зубом
											if ($t_f_data[$i.$j]['status'] == '19'){
												$color_stroke = '#FF9900';
											}
											$DrawMenu = TRUE;
											if (isset($t_f_data[$i.$j][$surface])){
											$s_stat = $t_f_data[$i.$j][$surface];
											}
											//!!! надо как-то получать статус в строку, чтоб писать в описании
											//t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
											
											if ($t_f_data[$i.$j]['status'] == '3'){
												//штифт
												$surface = 'NONE';
												$color = "#9393FF";
												$color_stroke = '#5353FF';
												$coordinates = $teeth_map_pin[$n_zuba];
												$stroke_width = 1;
																	
												echo '
													<div id="'.$n_zuba.$surface.'"
														status-path=\'
														"stroke": "'.$color_stroke.'", 
														"stroke-width": '.$stroke_width.', 
														"fill-opacity": "1"\' 
														class="mapArea'.$n.'" 
														t_status = "'.$t_status.'"
														data-path'.$n.'="'.$coordinates.'"
														fill-color'.$n.'=\'"fill": "'.$color.'"\'
														t_menu'.$n.' = "
																<div class=\'cellsBlock4\'>
																	<div class=\'cellLeft\'>
																		'.t_surface_name($n_zuba.$surface, 2).'<br />';
														
												DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
												
												echo '
																	</div>
																</div>';
												echo					
														'"
														t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
														
												//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
														
												echo					
														'"
													>
													</div>
												';
											}else{
											
											
												//Если надо рисовать корень, но в бд написано, что тут имплант
												if (($t_f_data[$i.$j]['pin'] == '1') && (mb_strstr($surface, 'root') != FALSE)){
													$DrawRoots = FALSE;
												}else{
													if  ((mb_strstr($surface, 'root') == TRUE) && 
														(($t_f_data[$i.$j]['status'] == '1') || ($t_f_data[$i.$j]['status'] == '2') || 
														($t_f_data[$i.$j]['status'] == '18') || ($t_f_data[$i.$j]['status'] == '19') || 
														($t_f_data[$i.$j]['status'] == '9'))){
														$DrawRoots = FALSE;
													}else{
														if (isset($t_f_data[$i.$j][$surface])){
															//echo $i.$j.'<br />';
															//var_dump ($t_f_data[$i.$j][$surface]);
															if  ((mb_strstr($surface, 'root') == TRUE) && ($t_f_data[$i.$j][$surface] != '0') && ($t_f_data[$i.$j][$surface] != '')){
																$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
															}
															$DrawRoots = TRUE;
														}
													}
												}
												//!!!!учим рисовать корни с коронками - начало  - кажется, это все говно. надо иначе
												/*if ($t_f_data[$i.$j]['status'] == '19'){
													$DrawRoots = TRUE;
												}*/
												if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
													//Если в массиве натыкаемся не на корни или если чужой, то корни не рисуем, а рисум кружок просто
													if ((($surface != 'root1') && ($surface != 'root2') && ($surface != 'root3')) || ($t_f_data[$i.$j]['alien'] == '1')){
														//без корней + коронки и всякая херня
														$surface = 'NONE';
														$color = $tooth_status[$t_f_data[$i.$j]['status']]['color'];
														$coordinates = $teeth_map_d[$n_zuba];								
													}
												}else{
													//Если у какой-то из областей зуба есть статус в бд.
													if (isset($t_f_data[$i.$j][$surface])){
													if ($t_f_data[$i.$j][$surface] != '0'){
														if (array_key_exists($t_f_data[$i.$j][$surface], $root_status)){
															$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
														}elseif(array_key_exists($t_f_data[$i.$j][$surface], $surface_status)){
															$color = $surface_status[$t_f_data[$i.$j][$surface]]['color'];
														}else{
															$color = "#fff";
														}
													}
													}
												}
												
												
								//!Костыль для радикса(корень)/статус 34
								if ((($t_f_data[$i.$j]['root1'] == '34') || ($t_f_data[$i.$j]['root2'] == '34') || ($t_f_data[$i.$j]['root3'] == '34')) && 
										(($t_f_data[$i.$j]['status'] != '1') && ($t_f_data[$i.$j]['status'] != '2') && 
										($t_f_data[$i.$j]['status'] != '18') && ($t_f_data[$i.$j]['status'] != '19') &&
										($t_f_data[$i.$j]['status'] != '9')))
								{
													$surface = 'NONE';
													$color = '#FF0000';
													$coordinates = $teeth_map_d[$n_zuba];								
												}
												
												
												if (mb_strstr($surface, 'root') != FALSE){
													$menu = 'r_menu';
												}elseif((mb_strstr($surface, 'surface') != FALSE) || (mb_strstr($surface, 'top') != FALSE)){
													$menu = 's_menu';
												}else{
													$DrawMenu = FALSE;
												}
												
												if ($DrawRoots){
													echo '
														<div id="'.$n_zuba.$surface.'"
															status-path=\'
															"stroke": "'.$color_stroke.'", 
															"stroke-width": '.$stroke_width.', 
															"fill-opacity": "1"\' 
															class="mapArea'.$n.'" 
															t_status = "'.$t_status.'"
															data-path'.$n.'="'.$coordinates.'"
															fill-color'.$n.'=\'"fill": "'.$color.'"\'
															t_menu'.$n.' = "
																<div class=\'cellsBlock4\'>
																	<div class=\'cellLeft\'>
																		'.t_surface_name($n_zuba.'NONE', 1).'<br />';
																
													DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
															echo '
																	</div>
																	<div class=\'cellRight\'>
																		'.t_surface_name($n_zuba.$surface, 0).'<br />';
													if ($DrawMenu){ DrawTeethMapMenu($key, $n_zuba, $surface, $menu);}	
													echo '
																	</div>
																</div>';	
													echo
															'"
															t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
															
													//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
													
													echo					
															'"
															>
															</div>
															';
												}
											}
										}
										
										if ($t_f_data[$i.$j]['pin'] == '1'){
											//штифт
											$surface = 'NONE';
											$color = "#9393FF";
											$color_stroke = '#5353FF';
											$coordinates = $teeth_map_pin[$n_zuba];
											$stroke_width = 1;
											if ($t_f_data[$i.$j]['alien'] == '1'){
												$color_stroke = '#F7273F';
												$stroke_width = 3;
											}				
											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path'.$n.'="'.$coordinates.'"
													fill-color'.$n.'=\'"fill": "'.$color.'"\'
													t_menu'.$n.' = "
														<div class=\'cellsBlock4\'>
															<div class=\'cellLeft\'>
																'.t_surface_name($n_zuba.$surface, 2).'<br />';
													
											DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
											echo '
															</div>
														</div>';
											echo					
													'"
													t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
													
											//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
											
											echo					
													'"
													>
													</div>
													';
										}
										
										
										//Для ЗО и дополнительно
										if (isset($t_f_data[$i.$j]['zo'])){
											$surface = 'NONE';
											if ($t_f_data[$i.$j]['zo'] == '1'){
												$color = "#FF0000";
											}else{
												$color = "#FFF";
											}
											$color_stroke = '#5353FF';
											$coordinates = $teeth_map_zo_db[$i.$j];
											$stroke_width = 1;
										
											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path="'.$coordinates.'"
													fill-color=\'"fill": "'.$color.'"\'
													t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
											echo					
														'
													t_menuA = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
										
											echo					
													'"
													>
													</div>
													';
										}
										
										$text_status_div = '';
										$text_status_div_shinir = '';
										$text_status_div_podvizh = '';
										$text_status_div_retein = '';
										$text_status_div_skomplect = '';
																		
										//Для Шинирования и дополнительно
										if (isset($t_f_data[$i.$j]['shinir'])){
											$text_status_div_shinir = 'ш';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
										}
										//Для Подвижности и дополнительно
										if (isset($t_f_data[$i.$j]['podvizh'])){
											$text_status_div_podvizh = 'A';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										//Для Ретейнер и дополнительно
										if (isset($t_f_data[$i.$j]['retein'])){
											$text_status_div_retein = 'р';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										//Для Сверхкомплекта и дополнительно
										if (isset($t_f_data[$i.$j]['skomplect'])){
											$text_status_div_skomplect = 'c';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										if ((isset($t_f_data[$i.$j]['shinir'])) || (isset($t_f_data[$i.$j]['podvizh'])) || (isset($t_f_data[$i.$j]['retein'])) || (isset($t_f_data[$i.$j]['skomplect']))){
											echo '<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">'.$text_status_div_shinir.''.$text_status_div_podvizh.''.$text_status_div_retein.''.$text_status_div_skomplect.'</div>';
										}
										
									}
								}
								

								
								echo '
										</div>
									</div>
								</div>';
								
								/*echo '
								<div class="cellsBlock3" style="font-size:80%;">
									<div class="cellLeft">';

								//$decription = $t_f_data_db[$z];

								/*$t_f_data = array();
					
								//собрали массив с зубами и статусами по поверхностям
								foreach ($decription as $key => $value){
									$surfaces_temp = explode(',', $value);
									foreach ($surfaces_temp as $key1 => $value1){
										$t_f_data[$key][$surfaces[$key1]] = $value1;
									}
								}
								
								unset($t_f_data['id']);
								unset($t_f_data['office']);
								unset($t_f_data['client']);
								unset($t_f_data['create_time']);
								unset($t_f_data['create_person']);
								unset($t_f_data['last_edit_time']);
								unset($t_f_data['last_edit_person']);
								unset($t_f_data['worker']);
								unset($t_f_data['comment']);
								
								//var_dump ($t_f_data);			
					*/
								/*$descr_rez = '';
								/*echo '
										<div><a href="#open1" onclick="show(\'hidden_'.$z.'\',200,5)">Подробно</a></div>';	*/
								/*echo '
										<div id=hidden_'.$z.' style="display:none;">';		
								foreach($t_f_data as $key => $value){
									//var_dump ($value);
									foreach ($value as $key1 => $value1){
										
										if ($key1 == 'status'){
											//var_dump ($value1);	
											if ($value1 != 0){
												//$descr_rez .= 
												echo t_surface_name('t'.$key.'NONE', 1).' '.t_surface_status($value1, 0).'';
											}
										}elseif($key1 == 'pin'){
											if ($value1 != 0){
												echo t_surface_status(3, 0);
											}
										}elseif($key1 == 'alien'){
											
										}elseif($key1 == 'zo'){
											
										}else{
											if ($value1 != 0){
												echo t_surface_name('t'.$key.$key1, 1).' '.t_surface_status(0, $value1);
											}
										}
									}
						
								}*/
								/*echo '
									</div>';*/
										
								/*echo '
								</div>';*/
                                /*echo '
							</div>';*/
											
							}
							
							$notes = SelDataFromDB ('notes', $client_j[0]['id'], 'client');
							include_once 'WriteNotes.php';
							echo WriteNotes($notes);
							
							$removes = SelDataFromDB ('removes', $client_j[0]['id'], 'client');
							include_once 'WriteRemoves.php';
							echo WriteRemoves($removes);
							
						}else{
							echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											Не было посещений стоматолога
										</div>
									</div>';
						}

                        //Лаборатория
                        $laborder_j = SelDataFromDB ('journal_laborder', $client_j[0]['id'], 'client');
                        //var_dump($laborder_j);

                        $labors_j = SelDataFromDB('spr_labor', '', '');
                        //var_dump ($labors_j);

                        $labors_jarr = array();

                        foreach ($labors_j as $labor_val){
                            $labors_jarr[$labor_val['id']] = $labor_val;
                        }
                        //var_dump ($labors_jrr);


                        if (TRUE) {
                            echo '
                            <ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: Auto; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);">
								
								<li style="margin-bottom: 3px;">
                                    Заказы в лабораторию ';
                            if ($laborder_j == 0) {
                                echo '<span style="font-size: 90%; color: #7D7D7D; margin-bottom: 5px; color: red;">нет заказов</span>';
                            }

                            echo '
                                </li>
								<li style="margin-bottom: 10px;">
                                    <a href="lab_order_add.php?client_id=' . $client_j[0]['id'] . '" class="b" style="font-size: 75%;">Добавить новый</a>
                                </li>';

							if ($laborder_j != 0) {
                                echo '
									<li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>
										<div class="cellOffice" style="text-align: center; background-color:#FEFEFE;">Лаборатория</div>
										<div class="cellText" style="text-align: center; background-color:#FEFEFE;">Описание</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Статус</div>
									</li>';

                                foreach ($laborder_j as $lab_order_data){

                                    if ($lab_order_data['status'] == 1) {
                                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                                        $mark_enter = 'закрыт';
                                    } elseif ($lab_order_data['status'] == 6) {
                                        $back_color = 'background-color: rgba(255, 102, 17, 0.7);';
                                        $mark_enter = 'отправлен в лаб.';
                                    } elseif ($lab_order_data['status'] == 7) {
                                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                                        $mark_enter = 'пришел из лаб.';
                                    } elseif ($lab_order_data['status'] == 8) {
                                        $back_color = 'background-color: rgba(137,0,81, .7);';
                                        $mark_enter = 'удалено';
                                    } else {
                                        //
                                        /*if ($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']) {
                                            $back_color = 'background-color: rgb(119, 255, 250);';
                                            $mark_enter = 'подтвердить';
                                        } else {*/
                                            $back_color = 'background-color: rgba(255,255,0, .5);';
                                            $mark_enter = 'создан';
                                        //s}
                                    }


                                    echo '
                                    <li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
										<a href="lab_order.php?id='.$lab_order_data['id'].'" class="cellName ahref" style="text-align: center; background-color:#FEFEFE;">
                                            '.date('d.m.y' ,strtotime($lab_order_data['create_time'])).'
										</a>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">
										    '.WriteSearchUser('spr_workers', $lab_order_data['worker_id'], 'user', true).'
										</div>
										<a href="labor.php?id='.$lab_order_data['labor_id'].'" class="cellOffice ahref" style="text-align: center; background-color:#FEFEFE;">
                                            '.$labors_jarr[$lab_order_data['labor_id']]['name'].'
										</a>
										<div class="cellText" style="text-align: left; background-color:#FEFEFE;">
										    '.$lab_order_data['descr'].'
										</div>
										<div class="cellName" style="text-align: center; '.$back_color.'">
										    '.$mark_enter.'
										</div>
									</li>';

                                }
							}
							echo '
                            </ul>';
                        }

					
						//mysql_close();
						
						echo '
							</div>';

					}	
						
					//--> Стоматология 
						
					//Косметология  -->
						
					if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
						echo '
							<div id="tabs-4">';

						/*echo '
							<div id="div2">';*/
						
						if (($cosm['add_own'] == 1) || ($cosm['edit'] == 1) || $god_mode){
							echo '
								<!--<a href="add_error.php" class="b">Добавить посещение</a>-->
								<a href="add_kd.php?client='.$client_j[0]['id'].'" class="b">Добавить КД</a>
								<a href="kd.php?client='.$client_j[0]['id'].'" class="b">КД</a>
								<a href="etaps.php?client='.$client_j[0]['id'].'" class="b">Исследования</a>';
							/*if (($clients['close'] == 1) || $god_mode){
								echo '
								<a href="cosm_move.php?id='.$client_j[0]['id'].'" class="b">Переместить</a>';
							}*/
						}
						
						$cosmet_task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'client_cosm_id');
						//var_dump ($cosmet_task);
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
						
						if ($cosmet_task != 0){
							for ($i=0; $i < count($cosmet_task); $i++){
								//!а если нет офиса или работника??
								$worker = SelDataFromDB('spr_workers', $cosmet_task[$i]['worker'], 'worker_id');
								$offices = SelDataFromDB('spr_office', $cosmet_task[$i]['office'], 'offices');
								echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											<a href="task_cosmet.php?id='.$cosmet_task[$i]['id'].'" class="ahref">
												'.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'
												<br />
												'.$worker[0]['name'].'
												<br />
												'.$offices[0]['name'].'
											</a>
										</div>';
								
								$decription = array();
								$decription_temp_arr = array();
								$decription_temp = '';
								
								/*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
								foreach($cosmet_task[$i] as $key => $value){
									if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
										$decription_temp_arr[mb_substr($key, 1)] = $value;
									}
								}
									
									//var_dump ($decription_temp_arr);
									
									$decription = $decription_temp_arr;
									/*$decription = array();
								$decription = json_decode($cosmet_task[$i]['description'], true);
								var_dump ($actions_cosmet);	*/	
								
								echo '<div class="cellLeft">';
								
								for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
									$action = '';
									if (isset($decription[$j])){
										if ($decription[$j] != 0){
											$action = '<div style="margin: 2px; border: 1px solid #CCC; padding-left: 3px; background-color: '.$actions_cosmet[$j-1]['color'].'">'.$actions_cosmet[$j-1]['full_name'].'</div>';
										}else{
											$action = '';
										}
										echo $action;
									}else{
										echo '';
									}
								}
								
								echo '
										</div>
										<div class="cellRight">';
								//!!!!!!if ($SESION_ID == )
								echo $cosmet_task[$i]['comment'];
								echo '
										</div>
									</div>';
								
								//echo ''.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'<br />';
							}
						}else{
								echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											Не было посещений косметолога
										</div>
									</div>';
						}
						echo '
							</div>';
					}
					
					//--> Косметология 
					
					echo '
						</div>
					</div>';
						
			}
				
			echo '					
				<div id="doc_title">Пациент: '.$client_j[0]['full_name'].' - Асмедика</div>
				</div>
			</div>


			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
			 </script>
								
								
								
								';	
					
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