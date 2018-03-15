<?php

//invoice_cosm_add.php
//Выписываем счёт косметология

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		include_once 'DBWork.php';
		include_once 'functions.php';

		//var_dump($_SESSION);
		//unset($_SESSION['invoice_data']);
		
		if ($_GET){
			if (isset($_GET['client']) && isset($_GET['id']) && isset($_GET['filial']) && isset($_GET['worker'])){
		
				if (($invoice['add_new'] == 1) || $god_mode){
					//array_push($_SESSION['invoice_data'], $_GET['client']);
					//$_SESSION['invoice_data'] = $_GET['client'];
					
					$sheduler_zapis = array();
					
					//Массив с месяцами
					$monthsName = array(
						'01' => 'Январь',
						'02' => 'Февраль',
						'03' => 'Март',
						'04' => 'Апрель',
						'05' => 'Май',
						'06' => 'Июнь',
						'07'=> 'Июль',
						'08' => 'Август',
						'09' => 'Сентябрь',
						'10' => 'Октябрь',
						'11' => 'Ноябрь',
						'12' => 'Декабрь'
					);
					
					$client_j = SelDataFromDB('spr_clients', $_GET['client'], 'user');
					//var_dump($client_j);

                    $msql_cnnct = ConnectToDB ();

					$query = "SELECT * FROM `zapis` WHERE `id`='".$_GET['id']."'";

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
					
						if (!isset($_SESSION['invoice_data'][$_GET['client']][$_GET['id']])){
							$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['filial'] = (int)$_GET['filial'];
							$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['worker'] = (int)$_GET['worker'];
							$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['t_number_active'] = 0;
							$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data'] = array();
							$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['mkb'] = array();
						}
						//сортируем зубы по порядку
						ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
						
						//var_dump($_SESSION);
						var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
						var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['mkb']);
						
						echo '
						<div id="status">
							<header>
								<span style="color: red;">Тестовый режим. Уже сохраняется и даже как-то работает</span>
								<h2>Наряд '.WriteSearchUser('spr_clients', $_GET['client'], 'user', true).'</h2>';
								
								
								
							$t_f_data_db = array();
							$cosmet_data_db = array();
							$show_this = FALSE;
							
							if ($sheduler_zapis[0]['type'] == 5){
								if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
									$show_this = TRUE;
								}
							}elseif ($sheduler_zapis[0]['type'] == 6){
								if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
									$show_this = TRUE;
								}
							}
							
							if ($show_this){
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
									
									if ($sheduler_zapis[0]['month'] < 10) $month = '0'.$sheduler_zapis[0]['month'];
									else $month = $sheduler_zapis[0]['month'];
									
									echo 
										'<b>'.$sheduler_zapis[0]['day'].' '.$monthsName[$month].' '.$sheduler_zapis[0]['year'].'</b><br>'.
										$start_time_h.':'.$start_time_m.' - '.$end_time_h.':'.$end_time_m;
														
									echo '
													<div style="position: absolute; top: 1px; right: 1px;">'.$dop_img.'</div>';
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
												</div>';
											
					
									echo '
											</li>';
								}
							}
								
								
								
								
						echo '		
							</header>';
						echo '
							<div id="data">';
						
						//Зубки
						echo '	
								<input type="hidden" id="client" name="client" value="'.$_GET['client'].'">
								<input type="hidden" id="client_insure" name="client_insure" value="'.$client_j[0]['insure'].'">
								<input type="hidden" id="zapis_id" name="zapis_id" value="'.$_GET['id'].'">
								<input type="hidden" id="zapis_insure" name="zapis_insure" value="'.$sheduler_zapis[0]['insured'].'">
								<input type="hidden" id="filial" name="filial" value="'.$_GET['filial'].'">
								<input type="hidden" id="worker" name="worker" value="'.$_GET['worker'].'">
								<input type="hidden" id="t_number_active" name="t_number_active" value="'.$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['t_number_active'].'">
								<input type="hidden" id="invoice_type" name="invoice_type" value="'.$_GET['type'].'">
								<!--
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
										</div>
									</div>
									<div id="teeth_polost" class="sel_toothp" style="display: inline-block; vertical-align: middle; text-align: center; margin: 2px; padding: 5px;">
										Полость
									</div>
								</div>
								-->';
								
											
						echo '			
								<div  style="display: inline-block; width: 400px; height: 600px;">';

						echo '
									<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100%">
										<ul>
											<li><a href="#price">Прайс</a></li>
											<!--<li><a href="#mkb">МКБ</a></li>-->
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

						showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, $_GET['type']);		
							
						echo '
												</ul>
											</div>';
						echo '		
										</div>
										<!--<div id="mkb">
										
											<div style=" width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
												<ul class="ul-tree ul-drop" id="lasttree">

							Comming soon...<br>
							Just test yet...<br><br>
							
										<li>
											<p onclick="checkMKBItem(\'001\')">001.Болезнь N</p>
										</li>		
										<li>
											<p onclick="checkMKBItem(\'777\')">002.Болезнь N2</p>
										</li>		

												</ul>
											</div>

										</div>-->
									</div>';
							
						echo '
								</div>';

						//Результат							
						echo '			
								<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';
								
						echo '	
									<div id="errror" class="invoceHeader" style="">
										<div>
											<div style="">К оплате: <div id="calculateInvoice" style="">0</div> руб.</div>
										</div>
										<!--<div>
											<div style="">Страховка: <div id="calculateInsInvoice" style="">0</div> руб.</div>
										</div>-->
										<div style="position: absolute; top: 3px; right: 5px; vertical-align: middle; font-size: 11px; width: 400px;">
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
												</div>
												<!--<div style="margin-bottom: 2px;">
													<div style="display: inline-block; vertical-align: top;">
														<div id="insure" class="settings_text" >Страховая</div>
													</div> / 
													<div style="display: inline-block; vertical-align: top;">
														<div id="insure_approve" class="settings_text">Согласовано</div>
													</div>
												</div>-->
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
							<input type="button" class="b" value="Сохранить" onclick="showInvoiceAdd('.$sheduler_zapis[0]['type'].')">
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
								$(".sel_tooth").click(function(){
	
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
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>