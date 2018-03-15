<?php

//edit_invoice.php
//Выписываем счёт

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		if (($finances['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
			
				require 'variables.php';
			
				require 'config.php';

				//var_dump($_SESSION['invoice_data']);
				//unset($_SESSION['invoice_data']);
				
				if (isset($_GET['id'])){
					
					$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
					//var_dump($invoice_j);
					
					if ($invoice_j != 0){

                        //Если заднее число
                        if ((strtotime($invoice_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){
                            echo '<h1>Нельзя редактировать задним числом</h1>';
                        }else {
							
							$sheduler_zapis = array();
							$invoice_ex_j = array();
							$invoice_ex_j_mkb = array();
							$temp_arr = array();
							$temp_arr2 = array();

							$client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');
							//var_dump($client_j);
							
							mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
							mysql_select_db($dbName) or die(mysql_error()); 
							mysql_query("SET NAMES 'utf8'");
							
							$query = "SELECT * FROM `zapis` WHERE `id`='".$invoice_j[0]['zapis_id']."'";
							
							$res = mysql_query($query) or die($query);
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
							
								if (!isset($_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']])){
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['filial'] = $invoice_j[0]['office_id'];
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['worker'] = $invoice_j[0]['worker_id'];
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['t_number_active'] = 0;
                                    $_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['discount'] = 0;
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = array();
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['mkb'] = array();
								}
								
								//Хочу получить все данные по этому наряду и захреначить их в сессиию
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
								//var_dump ($invoice_ex_j);

								//Для МКБ
								$query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='".$_GET['id']."';";
								//var_dump ($query);
								
								$res = mysql_query($query) or die(mysql_error().' -> '.$query);
								$number = mysql_num_rows($res);
								if ($number != 0){
									while ($arr = mysql_fetch_assoc($res)){
										if (!isset($invoice_ex_j_mkb[$arr['ind']])){
											$invoice_ex_j_mkb[$arr['ind']] = array();
											array_push($invoice_ex_j_mkb[$arr['ind']], $arr['mkb_id']);
										}else{
											array_push($invoice_ex_j_mkb[$arr['ind']], $arr['mkb_id']);
										}
									}
								}else
									$invoice_ex_j_mkb = 0;
								//var_dump ($invoice_ex_j_mkb);
								
								if ($invoice_ex_j != 0){
									//надо костыльно преобразовать массив
									foreach($invoice_ex_j as $ind => $invoice_ex_j_arr){

										foreach($invoice_ex_j_arr as $invoice_ex_j_val){
											
											$temp_arr2['id'] = (int)$invoice_ex_j_val['price_id'];
											$temp_arr2['quantity'] = (int)$invoice_ex_j_val['quantity'];
											$temp_arr2['insure'] = (int)$invoice_ex_j_val['insure'];
											$temp_arr2['insure_approve'] = (int)$invoice_ex_j_val['insure_approve'];
											$temp_arr2['price'] = (int)$invoice_ex_j_val['price'];
											$temp_arr2['guarantee'] = (int)$invoice_ex_j_val['guarantee'];
											$temp_arr2['spec_koeff'] = $invoice_ex_j_val['spec_koeff'];
											$temp_arr2['discount'] = (int)$invoice_ex_j_val['discount'];
											
											if (!isset($temp_arr[$ind])){
												$temp_arr[$ind] = array();
											}
											
											array_push($temp_arr[$ind], $temp_arr2);
										}
									}
	
									$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data'] = $temp_arr;

                                    //скидку тут добавлю в сесиию
                                    $discount = $_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['discount'] = $invoice_j[0]['discount'];

									if ($invoice_ex_j_mkb != 0){
										$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['mkb'] = $invoice_ex_j_mkb;
									
									}
								}
								
								//var_dump($_SESSION);
								//var_dump($_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['data']);
								//var_dump($_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['mkb']);
								
								if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
								else $month = $sheduler_zapis[0]['month'];
								
								echo '
								<div id="status">
									<header>
										<!--<div class="nav">
											<a href="zapis_full.php?filial='.$invoice_j[0]['office_id'].'&who=stom&d='.$sheduler_zapis[0]['day'].'&m='.$month.'&y='.$sheduler_zapis[0]['year'].'&kab='.$sheduler_zapis[0]['kab'].'" class="">Запись подробно</a>
										</div>-->
										
										<!--<span style="color: red;">Тестовый режим. Уже сохраняется и даже как-то работает</span>-->
										<h2>Редактировать наряд <a href="invoice.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>';
								
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
									
								if(($sheduler_zapis[0]['enter'] != 8) || ($scheduler['see_all'] == 1) || $god_mode){
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
								}

								//Наряды
								echo '
									<ul id="invoices" style="margin-left: 6px; margin-bottom: 10px;">';
								echo '
									</ul>';
								
								echo '
									<div id="data">';

								echo '	
										<input type="hidden" id="invoice_id" name="client" value="'.$invoice_j[0]['id'].'">
										<input type="hidden" id="client" name="client" value="'.$invoice_j[0]['client_id'].'">
										<input type="hidden" id="client_insure" name="client_insure" value="'.$client_j[0]['insure'].'">
										<input type="hidden" id="zapis_id" name="zapis_id" value="'.$invoice_j[0]['zapis_id'].'">
										<input type="hidden" id="zapis_insure" name="zapis_insure" value="'.$sheduler_zapis[0]['insured'].'">
										<input type="hidden" id="filial" name="filial" value="'.$invoice_j[0]['office_id'].'">
										<input type="hidden" id="worker" name="worker" value="'.$invoice_j[0]['worker_id'].'">
										<input type="hidden" id="t_number_active" name="t_number_active" value="'.$_SESSION['invoice_data'][$invoice_j[0]['client_id']][$invoice_j[0]['zapis_id']]['t_number_active'].'">
										<input type="hidden" id="invoice_type" name="invoice_type" value="'.$invoice_j[0]['type'].'">';
										
								if ($sheduler_zapis[0]['type'] == 5){
									//Зубки
									echo '		
										<div style="font-size: 80%; color: #AAA; margin-bottom: 2px;">
											Выберите зуб
										</div>								
										<div style="vertical-align: middle; margin-bottom: 5px;">
											<div id="teeth" style="display: inline-block;">
												<div class="tooth_updown">
													<div class="tooth_left" style="display: inline-block;">
														<div class="sel_tooth">
															18
														</div>
														<div class="sel_tooth">
															17
														</div>
														<div class="sel_tooth">
															16
														</div>
														<div class="sel_tooth">
															15
														</div>
														<div class="sel_tooth">
															14
														</div>
														<div class="sel_tooth">
															13
														</div>
														<div class="sel_tooth">
															12
														</div>
														<div class="sel_tooth">
															11
														</div>
													</div>			
													<div class="tooth_right" style="display: inline-block;">
														<div class="sel_tooth">
															21
														</div>
														<div class="sel_tooth">
															22
														</div>
														<div class="sel_tooth">
															23
														</div>
														<div class="sel_tooth">
															24
														</div>
														<div class="sel_tooth">
															25
														</div>
														<div class="sel_tooth">
															26
														</div>
														<div class="sel_tooth">
															27
														</div>
														<div class="sel_tooth">
															28
														</div>
													</div>
													<div class="tooth_right" style="display: inline-block;">
														<div id="teeth_polost" class="sel_toothp">
															Полость
														</div>
													</div>	
												</div>
												<div class="tooth_updown">
													<div class="tooth_left" style="display: inline-block;">
														<div class="sel_tooth">
															48
														</div>
														<div class="sel_tooth">
															47
														</div>
														<div class="sel_tooth">
															46
														</div>
														<div class="sel_tooth">
															45
														</div>
														<div class="sel_tooth">
															44
														</div>
														<div class="sel_tooth">
															43
														</div>
														<div class="sel_tooth">
															42
														</div>
														<div class="sel_tooth">
															41
														</div>
													</div>			
													<div class="tooth_right" style="display: inline-block;">
														<div class="sel_tooth">
															31
														</div>
														<div class="sel_tooth">
															32
														</div>
														<div class="sel_tooth">
															33
														</div>
														<div class="sel_tooth">
															34
														</div>
														<div class="sel_tooth">
															35
														</div>
														<div class="sel_tooth">
															36
														</div>
														<div class="sel_tooth">
															37
														</div>
														<div class="sel_tooth">
															38
														</div>
													</div>
													<div class="tooth_right" style="display: inline-block;">
														<div id="teeth_moloch" class="sel_toothm">
															Молочные
														</div>
													</div>
												</div>
											</div>
										</div>';
									//Молочные зубы
									echo '
											<div id="teeth_moloch_options" style="display: none;">
												<div class="tooth_updown">
													<div class="tooth_left" style="display: inline-block;">
														<!--<div class="sel_tooth">
															58
														</div>
														<div class="sel_tooth">
															57
														</div>
														<div class="sel_tooth">
															56
														</div>-->
														<div class="sel_tooth">
															55
														</div>
														<div class="sel_tooth">
															54
														</div>
														<div class="sel_tooth">
															53
														</div>
														<div class="sel_tooth">
															52
														</div>
														<div class="sel_tooth">
															51
														</div>
													</div>			
													<div class="tooth_right" style="display: inline-block;">
														<div class="sel_tooth">
															61
														</div>
														<div class="sel_tooth">
															62
														</div>
														<div class="sel_tooth">
															63
														</div>
														<div class="sel_tooth">
															64
														</div>
														<div class="sel_tooth">
															65
														</div>
														<!--<div class="sel_tooth">
															66
														</div>
														<div class="sel_tooth">
															67
														</div>
														<div class="sel_tooth">
															68
														</div>-->
													</div>
												</div>
												<div class="tooth_updown">
													<div class="tooth_left" style="display: inline-block;">
														<!--<div class="sel_tooth">
															88
														</div>
														<div class="sel_tooth">
															87
														</div>
														<div class="sel_tooth">
															86
														</div>-->
														<div class="sel_tooth">
															85
														</div>
														<div class="sel_tooth">
															84
														</div>
														<div class="sel_tooth">
															83
														</div>
														<div class="sel_tooth">
															82
														</div>
														<div class="sel_tooth">
															81
														</div>
													</div>			
													<div class="tooth_right" style="display: inline-block;">
														<div class="sel_tooth">
															71
														</div>
														<div class="sel_tooth">
															72
														</div>
														<div class="sel_tooth">
															73
														</div>
														<div class="sel_tooth">
															74
														</div>
														<div class="sel_tooth">
															75
														</div>
														<!--<div class="sel_tooth">
															76
														</div>
														<div class="sel_tooth">
															77
														</div>
														<div class="sel_tooth">
															78
														</div>-->
													</div>
												</div>
											</div>
									';
										
								}		
													
								echo '			
										<div  style="display: inline-block; width: 400px; height: 600px;">';

								echo '
											<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100%">
												<ul>
													<li><a href="#price">Прайс</a></li>';
								if ($sheduler_zapis[0]['type'] == 5){
									echo '
													<li><a href="#mkb">Диагноз (МКБ)</a></li>';
								}
								echo '
												</ul>
												<div id="price">';
								//Прайс		
								echo '	
													<div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
														<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
													</div>';
								echo '
													<div style=" width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
														<ul class="ul-tree ul-drop" id="lasttree">';

								showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, $invoice_j[0]['type']);		
									
								echo '
														</ul>
													</div>';
								echo '		
												</div>';
								if ($sheduler_zapis[0]['type'] == 5){
									echo '
												<div id="mkb">';
												
									/*echo '			
													<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
														<ul class="ul-tree ul-drop" id="lasttree">';*/
									
									//Вывод справочника МКб
									echo showTree3(NUll, '', 'list', 0, TRUE, 0, FALSE, 'spr_mkb', 0, 0);

									/*echo '
									Comming soon...<br>
									Just test yet...<br><br>
									
												<li>
													<p onclick="checkMKBItem(\'001\')">001.Болезнь N</p>
												</li>		
												<li>
													<p onclick="checkMKBItem(\'777\')">002.Болезнь N2</p>
												</li>';	*/	
									/*echo '
														</ul>
													</div>';*/
									echo '
												</div>';
								}
								echo '
											</div>';
									
								echo '
										</div>';

								//Результат							
								echo '			
										<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';
										
								echo '	
											<div id="errror" class="invoceHeader" style="position: relative;">
												<div>
													<div style="">К оплате: <div id="calculateInvoice" style="">0</div> руб.</div>
												</div>';
								if ($sheduler_zapis[0]['type'] == 5){
									echo '
												<div>
													<div style="">Страховка: <div id="calculateInsInvoice" style="">0</div> руб.</div>
												</div>';
								}
                                /*echo '
                                            <div>
												<div style="">Скидка: <div id="discountValue" class="calculateInvoice" style="color: rgb(255, 0, 198);">'.$discount.'</div><span  class="calculateInvoice" style="color: rgb(255, 0, 198);">%</span></div>
											</div>';*/
                                echo '
											<div style="position: absolute; bottom: 0; right: 2px; vertical-align: middle; font-size: 11px;">
												<div>	
									                <input type="button" class="b" value="Сохранить" onclick="showInvoiceAdd('.$sheduler_zapis[0]['type'].', \'edit\')">
								                </div>
											</div>';
								echo '
												<div style="position: absolute; top: 0; left: 200px; vertical-align: middle; font-size: 11px; width: 300px;">
												    <div style="display: inline-block; vertical-align: top;">
														Настройки: 
													</div>
													<div style="display: inline-block; vertical-align: top;">
														<div style="margin-bottom: 2px;">
															<div style="display: inline-block; vertical-align: top;">
																 <div id="spec_koeff" class="settings_text" >Коэфф.</div>
															</div> /
															<div style="display: inline-block; vertical-align: top;">
																 <div id="guarantee" class="settings_text">По гарантии</div>
															</div> /
															<div style="display: inline-block; vertical-align: top;">
																 <div class="settings_text" onclick="clearInvoice();">Очистить всё</div>
															</div>
														</div>';
								if ($sheduler_zapis[0]['type'] == 5){
									echo '
														<div style="margin-bottom: 2px;">
															<div style="display: inline-block; vertical-align: top;">
																<div id="insure" class="settings_text" >Страховая</div>
															</div> / 
															<div style="display: inline-block; vertical-align: top;">
																<div id="insure_approve" class="settings_text">Согласовано</div>
															</div>
														</div>';
								}
								echo '
														<div style="margin-bottom: 2px;">
															<div style="display: inline-block; vertical-align: top;">
																<div id="discounts" class="settings_text">Скидки (Акции)</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											';
								
								echo '
											<div id="invoice_rezult" style="width: 700px; height: 500px; overflow: scroll; float: none">
											</div>';
								echo '
										</div>';
								
								echo '
									<div>	
										<input type="button" class="b" value="Сохранить" onclick="showInvoiceAdd('.$sheduler_zapis[0]['type'].', \'edit\')">
									</div>
								</div>
			
								<!-- Подложка только одна -->
								<div id="overlay"></div>
								
								
								
								<script>
								
									$(document).ready(function(){

										//получим активный зуб
										var t_number_active = document.getElementById("t_number_active").value;
										
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
										
										fillInvoiseRez(true);
									});
									
								</script>
								
								
								
								
								
								
								';
							}else{
								echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
							}
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
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>