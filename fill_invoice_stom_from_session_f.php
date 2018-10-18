<?php 

//fill_invoice_stom_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		$request = '
						<div class="cellsBlock">
							<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
								<i><b>Зуб</b></i>
							</div>
							<div class="cellText2" style="font-size: 100%; text-align: center;">
								<i><b>Наименование</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
								<div id="insure" class="settings_text" title="Страховая"><i>Страх.</i></div>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
								<div id="insure_approve" class="settings_text" title="Согласовано"><i>Сог.</i></div>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
								<i><b>Цена, руб.</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<div id="spec_koeff" class="settings_text" title="Коэффициент"><i>Коэфф.</i></div>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<i><b>Кол-во</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<div id="discounts" class="settings_text" title="Скидки (Акции)"><i>Скидка</i></div>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 30px; min-width: 30px; max-width: 30px;">
								<div id="guaranteegift" class="settings_text" title="По гарантии | Подарок"><i>Гар.<br>Под.</i></div>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
								<i><b>Всего, руб.</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
                                <div id="percent_cats" class="settings_text" onclick="contextMenuShow(0, ' . $_POST['invoice_type'] . ', event, \'percent_cats\');">
                                    <i><b>Категория</b></i>
                                </div>
							</div>
							<div class="cellCosmAct" style="font-size: 70%; text-align: center;">
								<i><b>-</b></i>
							</div>
						</div>';
		
		if ($_POST){
			if (!isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker']) || !isset($_POST['invoice_type'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				include_once 'DBWork.php';

                //!!! @@@
                include_once 'ffun.php';

                $msql_cnnct = ConnectToDB2();

				$client = $_POST['client'];
				$zapis_id = $_POST['zapis_id'];
				$filial = $_POST['filial'];
				$worker = $_POST['worker'];
				
				$price['price'] = 0;
                $price['start_price'] = 0;
				
				if (!isset($_SESSION['invoice_data'][$client][$zapis_id]['data'])){
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}else{
					//берем из сесии данные
					$data = $_SESSION['invoice_data'][$client][$zapis_id]['data'];
					$discount = $_SESSION['invoice_data'][$client][$zapis_id]['discount'];

					ksort($data);
					
					$t_number_active = $_SESSION['invoice_data'][$client][$zapis_id]['t_number_active'];
					$mkb_data = $_SESSION['invoice_data'][$client][$zapis_id]['mkb'];


                    //Категории процентов
                    $percent_cats_j = SelDataFromDB('fl_spr_percents', $_POST['invoice_type'], 'type');
                    //var_dump( $percent_cats_j);

                    //Надо отсортировать по названию
                    $percent_cats_j_names = array();

                    //Определяющий массив из названий для сортировки
                    foreach ($percent_cats_j as $key => $arr) {
                        array_push($percent_cats_j_names, $arr['name']);
                    }

                    //Сортируем по названию
                    array_multisort($percent_cats_j_names, SORT_LOCALE_STRING, $percent_cats_j);


					foreach ($data as $ind => $invoice_data){
						if ($t_number_active == $ind){
							$bg_col = 'background: rgba(131, 219, 83, 0.5) none repeat scroll 0% 0%;';
							$bg_col2 = 'background: rgba(83, 219, 185, 0.5) none repeat scroll 0% 0%;';
							$bg_col3 = 'background: rgba(131, 219, 83, 0.5) none repeat scroll 0% 0%;';
						}else{
							$bg_col = '';
							$bg_col2 = 'background: rgba(83, 219, 185, 0.14) none repeat scroll 0% 0%;';
							$bg_col3 = 'background: rgba(83, 219, 185, 0.14) none repeat scroll 0% 0%;';
						}
						$request .= '
							<div class="cellsBlock">
								<div class="cellCosmAct toothInInvoice" style="'.$bg_col3.'" onclick="toothInInvoice('.$ind.')">';
						if ($ind == 99){
							$request .= 'П';
						}else{
							$request .= $ind;
						}
						$request .= '
								</div>';
								
						if (!empty($mkb_data) && isset($mkb_data[$ind])){
							$request .= '
								<div class="cellsBlock" style="font-size: 100%;" >
									<div class="cellText2" style="padding: 2px 4px; '.$bg_col2.'">
										<b>';
							if ($ind == 99){
								$request .= '<i>Полость</i>';
							}else{
								$request .= '<i>Зуб</i>: '.$ind;
							}
							$request .= '
										</b>. <i>Диагноз</i>: ';
										
							foreach ($mkb_data[$ind] as $mkb_key => $mkb_data_val){
								$rez = array();
								$rezult2 = array();
								
								$query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val}'";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
								$number = mysqli_num_rows($res);
								if ($number != 0){
									while ($arr = mysqli_fetch_assoc($res)){
										$rez[$mkb_data_val] = $arr;
									}
								}else{
									$rez = 0;
								}
								if ($rez != 0){
									foreach ($rez as $mkb_name_val){
										$request .= '
											<div class="mkb_val"><b>'.$mkb_name_val['code'].'</b> '.$mkb_name_val['name'].'
												<div class="mkb_val_del" onclick="deleteMKBItemID('.$ind.', '.$mkb_data_val.')">
													<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
												</div>
											</div>';
									}
								}else{
									$request .= '<div class="mkb_val">???</div>';
								}
								
							}
							
							$request .= '
									</div>
									<div class="cellCosmAct info" style="font-size: 100%; text-align: center; padding: 2px 4px; '.$bg_col2.'" onclick="deleteMKBItem('.$ind.');">
										<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
									</div>
								</div>';
						}


						//часть прайса		
						if (!empty($invoice_data)){
						    //var_dump($invoice_data);

							foreach ($invoice_data as $key => $items){
								$request .= '
								<div class="cellsBlock" style="font-size: 100%; height: 45px; min-height: 45px;">
								<!--<div class="cellCosmAct" style=" '.$bg_col.'">
									-
								</div>-->
								<div class="cellText2" style=" '.$bg_col.'"><div style="text-overflow: ellipsis; overflow: hidden; white-space: inherit;  width: 120px;">';
								
								//Хочу имя позиции в прайсе
								$arr = array();
								$rez = array();

								$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$items['id']}'";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
								$number = mysqli_num_rows($res);
								if ($number != 0){
									while ($arr = mysqli_fetch_assoc($res)){
										array_push($rez, $arr);
									}
									$rezult2 = $rez;
								}else{
									$rezult2 = 0;
								}
								
								if ($rezult2 != 0){
									$request .= $rezult2[0]['name'];
									
									//Узнать цену
                                    //переменная для цены
                                    $price['price'] = 0;
                                    $price['start_price'] = 0;

                                    //переменная для массива цен
                                    $prices = array();

                                    $spec_koeff = $items['spec_koeff'];

                                    //получим цены
                                    $prices = takePrices ($items['id'], $items['insure']);
                                    //var_dump($prices);

                                    if (!empty($prices)) {
                                        if (isset($items['price'])){
                                            if (!isset($items['manual_price'])){
                                                $items['manual_price'] = false;
                                            }
                                            if (!isset($items['start_price'])){
                                                $items['start_price'] = 0;
                                            }
                                            /*if ($items['manual_price']){
                                                $price['price'] = $items['price'];
                                                $price['start_price'] = $items['start_price'];*/
                                            //}else {
                                                $price = returnPriceWithKoeff($spec_koeff, $prices, $items['insure'], $items['manual_price'], $items['price']);
                                            //}
                                        }

                                    }

									/*$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='{$items['id']}' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
									
									if ($items['insure'] != 0){
										$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='{$items['id']}' AND `insure`='".$items['insure']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
									}
									
									$res = mysql_query($query) or die(mysql_error().' -> '.$query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										$arr = mysql_fetch_assoc($res);
										$price = $arr['price'];
									}else{
										$price = '?';
									}*/
									
								}else{
									$request .= '?';
								}
								
								if ($items['insure'] != 0){
									//Написать страховую
									$insure_j = SelDataFromDB('spr_insure', $items['insure'], 'id');
									
									if ($insure_j != 0){
										$insure_name = $insure_j[0]['name'];
									}else{
										$insure_name = '?';
									}
								}else{
									$insure_name = 'нет';
								}
								
								$request .= '
								</div>
								</div>
								<div class="cellCosmAct settings_text" insure="'.$items['insure'].'" style="font-size: 80%; text-align: center; '.$bg_col.' width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;" onclick="contextMenuShow('.$ind.', '.$key.', event, \'insureItem\');">
									'.$insure_name.'
								</div>';
								
								if ($items['insure'] != 0){
									if ($items['insure_approve'] == 1){
										$request .= '
											<div class="cellCosmAct settings_text" insureapprove="'.$items['insure_approve'].'" style="font-size: 70%; text-align: center; '.$bg_col.'" onclick="contextMenuShow('.$ind.', '.$key.', event, \'insure_approveItem\');">
												<i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
											</div>';
									}else{
										$request .= '
										<div class="cellCosmAct settings_text" insureapprove="'.$items['insure_approve'].'" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;" onclick="contextMenuShow('.$ind.', '.$key.', event, \'insure_approveItem\');">
											<i class="fa fa-ban" aria-hidden="true"></i>
										</div>';
									}

								}else{
									$request .= '
									<div class="cellCosmAct" insureapprove="'.$items['insure_approve'].'" style="font-size: 70%; text-align: center; '.$bg_col.'">
										-
									</div>';
								}
								
								$request .= '
								<div class="cellCosmAct invoiceItemPrice settings_text" ind="'.$ind.'" key="'.$key.'" price="'.$price['price'].'" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; position: relative; '.$bg_col.'">
                                    <div start_price="'.$price['start_price'].'"  onclick="contextMenuShow('.$ind.', '.$key.', event, \'priceItem\');">'.$price['price'].'</div>
                                    <div class="invPriceUpOne" style="top: 0;" onclick="invPriceUpDownOne('.$ind.', '.$key.', '.$price['price'].', '.$price['start_price'].', \'up\');">
                                        <i class="fa fa-caret-up" aria-hidden="true"></i>
                                    </div>
                                    <div class="invPriceDownOne" style="bottom: 0;" onclick="invPriceUpDownOne('.$ind.', '.$key.', '.$price['price'].', '.$price['start_price'].', \'down\');">
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </div>
								</div>
								<div class="cellCosmAct spec_koeffInvoice settings_text"  speckoeff="'.$items['spec_koeff'].'" style="font-size: 90%; text-align: center; '.$bg_col.' width: 40px; min-width: 40px; max-width: 40px;" onclick="contextMenuShow('.$ind.', '.$key.', event, \'spec_koeffItem\');">
									'.$items['spec_koeff'].'
								</div>
								<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; '.$bg_col.'">
									<input type="number" size="2" name="quantity" id="quantity" min="1" max="99" value="'.$items['quantity'].'" class="mod" onchange="changeQuantityInvoice('.$ind.', '.$key.', this);">
								</div>
								<div class="cellCosmAct settings_text"  discount="'.$items['discount'].'" style="font-size: 90%; text-align: center; '.$bg_col.' width: 40px; min-width: 40px; max-width: 40px;" onclick="contextMenuShow('.$ind.', '.$key.', event, \'discountItem\');">
									'.$items['discount'].'
								</div>
								
								<div class="cellCosmAct settings_text" guarantee="'.$items['guarantee'].'" gift="'.$items['gift'].'" style="font-size: 80%; text-align: center; width: 30px; min-width: 30px; max-width: 30px; '.$bg_col.'" onclick="contextMenuShow('.$ind.', '.$key.', event, \'guaranteeGiftItem\');">';
								if ($items['guarantee'] != 0){
									$request .= '
										<i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
								}elseif ($items['gift'] != 0){
                                    $request .= '
                                        <i class="fa fa-gift" aria-hidden="true" style="color: blue; font-size: 150%;"></i>';
								}else{
                                    $request .= '-';
                                }
								$request .= '
								</div>
								
								<div class="cellCosmAct invoiceItemPriceItog" manual_itog_price="'.$items['itog_price'].'" style="font-size: 105%; font-weight: bold; text-align: center; '.$bg_col.' width: 60px; min-width: 60px; max-width: 60px; cursor: pointer" onclick="contextMenuShow('.$ind.', '.$key.', event, \'priceItemItog\');">';



                                //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                                /*if ($items['guarantee'] == 0) {
                                    //if (isset($items['manual_itog_price'])){
                                    if (isset($items['itog_price'])) {
                                        if ($items['itog_price'] > 0) {
                                            $request .= $items['itog_price'];
                                        } else {
                                            $request .= '0';
                                        }
                                    } else {
                                        $request .= '0';
                                    }
                                    //}else{
                                    //$request .= '0';
                                    //}
                                }else{
                                    $request .= '0';
                                }*/

                                $request .= $items['itog_price'];


                                $request .= '	
								</div>
                                <div class="cellCosmAct" style="font-size: 100%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; '.$bg_col.'">';

                                $request .= '
                                    <select name="percent_cats" id="percent_cats" style="width: 80px; max-width: 80px;" onchange="changeItemPerCatInvoice(' . $ind . ', ' . $key . ', this.value);">';

                                if ($percent_cats_j != 0) {
                                    for ($i = 0; $i < count($percent_cats_j); $i++) {
                                        if ($items['percent_cats'] == $percent_cats_j[$i]['id']) {
                                            $selected = ' selected';
                                        } else {
                                            $selected = '';
                                        }

                                        $request .= "<option value='" . $percent_cats_j[$i]['id'] . "' " . $selected . "><i>" . $percent_cats_j[$i]['name'] . "</i></option>";
                                    }
                                }
                                $request .= '
                                                    </select>';

                                $request .= '
                                </div>
								<div invoiceitemid="'.$key.'" class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteInvoiceItem('.$ind.', this);">
									<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
							}
						}else{
							$request .= '
							<div class="cellsBlock" style="font-size: 100%;" >
								<div class="cellText2" style="text-align: center; '.$bg_col.' border: 1px dotted #DDD;">
									<span style="color: rgba(255, 0, 0, 0.62);">не заполнено</span>
								</div>
								<div class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteInvoiceItem('.$ind.', this);">
									<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
						}
							$request .= '
							</div>';
					}

					echo json_encode(array('result' => 'success', 'data' => $request, 'data2' => $_SESSION['invoice_data'][$client][$zapis_id]['data']));
				}
				
				/*include_once 'functions.php';
				
				require 'config.php';
				//Вставка

				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "INSERT INTO `journal_etaps` (
					`name`, `client_id`)
					VALUES (
					'{$_POST['name']}', '{$_POST['client']}') ";
				//mysql_query($query) or die(mysql_error());
				
				$mysql_insert_id = mysql_insert_id();
				
				mysql_close();
				
				//логирование
				AddLog (GetRealIp(), $_POST['session_id'], '', 'Добавлено исследование. ['.date('d.m.y H:i', $time).']. Пациент ['.$_POST['client'].'] ID ['.$mysql_insert_id.']');

				echo '
							<h1>Добавлено исследование</h1>
							<a href="etap.php?id='.$mysql_insert_id.'" class="b">Перейти</a>
							';*/
			}
		}
	}
?>