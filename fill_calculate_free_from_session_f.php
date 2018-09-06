<?php 

//fill_calculate_free_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		$request = '';

		if ($_POST){
			if (!isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				echo json_encode(array('result' => 'error', 'data' => 'Ошибка #2'));
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

				if (!isset($_SESSION['calculate_data'][$client][$zapis_id]['data'])){
					echo json_encode(array('result' => 'error', 'data' => 'Ошибка #1 ['.$client.'/'.$zapis_id.']'));
				}else{
					//берем из сесии данные
					$data = $_SESSION['calculate_data'][$client][$zapis_id]['data'];
					$discount = $_SESSION['calculate_data'][$client][$zapis_id]['discount'];

					ksort($data);

					$t_number_active = $_SESSION['calculate_data'][$client][$zapis_id]['t_number_active'];
					$mkb_data = $_SESSION['calculate_data'][$client][$zapis_id]['mkb'];

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

						/*if (!empty($mkb_data) && isset($mkb_data[$ind])){
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
						}*/


						//часть прайса
						if (!empty($invoice_data)){

                            //Категории процентов
                            $percent_cats_j = SelDataFromDB('fl_spr_percents', 88, 'type');
                            //var_dump( $percent_cats_j);

							foreach ($invoice_data as $key => $items){
                                $percent_cat = $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind][$key]['percent_cats'];

                                $percents_j = getPercents($_POST['worker'], $percent_cat);

                                $work_percent = (int)$percents_j[$percent_cat]['work_percent'];
                                $material_percent = (int)$percents_j[$percent_cat]['material_percent'];

                                $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind][$key]['work_percent'] = $work_percent;
                                $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind][$key]['material_percent'] = $material_percent;

								$request .= '
								<div class="cellsBlock" style="font-size: 100%;" >
								<!--<div class="cellCosmAct" style=" '.$bg_col.'">
									-
								</div>-->
								<div class="cellText2" style=" '.$bg_col.'"><div style="">';

								//Хочу имя позиции в прайсе
								$arr = array();
								$rez = array();

								$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$items['price_id']}'";

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
                                    //$request .= $rezult2[0]['name'];
                                    $request .= '<i>'.$rezult2[0]['code'].'</i>'.$rezult2[0]['name'].' <span style="font-size: 90%; background: rgba(197, 197, 197, 0.41);">[#'.$rezult2[0]['id'].']</span>';

									//Узнать цену
                                    //переменная для цены
                                    //$price['price'] = 0;
                                    //$price['start_price'] = 0;

                                    //переменная для массива цен
                                    //$prices = array();

                                    $spec_koeff = $items['spec_koeff'];

                                    //получим цены
                                    $prices = takePrices ($items['price_id'], $items['insure']);
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

									/*$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='{$items['price_id']}' ORDER BY `date_from`, `create_time` DESC LIMIT 1";

									if ($items['insure'] != 0){
										$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='{$items['price_id']}' AND `insure`='".$items['insure']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
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

								/*if ($items['insure'] != 0){
									//Написать страховую
									$insure_j = SelDataFromDB('spr_insure', $items['insure'], 'id');

									if ($insure_j != 0){
										$insure_name = $insure_j[0]['name'];
									}else{
										$insure_name = '?';
									}
								}else{
									$insure_name = 'нет';
								}*/

								$request .= '
								</div>
							    </div>
								<!--<div class="cellCosmAct settings_text" insure="'.$items['insure'].'" style="font-size: 80%; text-align: center; '.$bg_col.' width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;" onclick="contextMenuShow('.$ind.', '.$key.', event, \'insureItem\');">
									$insure_name
								</div>-->';

                                $price = $items['price'];

                                //вычисляем стоимость
                                //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                $stoim_item = $items['quantity'] * $price;

                                //с учетом скидки акции
                                if ($items['insure'] == 0){
                                    //$stoim_item = $stoim_item - ($stoim_item * $invoice_j[0]['discount'] / 100);
                                    $stoim_item = $stoim_item - ($stoim_item * $items['discount'] / 100);
                                    //$stoim_item = round($stoim_item/10) * 10;
                                    $stoim_item = round($stoim_item);
                                }


                                //if (isset($items['manual_itog_price'])){
                                if (isset($items['itog_price'])){
                                    if ($items['itog_price'] > 0){
                                        $stoim_item2 = $items['itog_price'];
                                    }else{
                                        $stoim_item2 = '0';
                                    }
                                }else{
                                    $stoim_item2 = '0';
                                }
                                //}else{
                                //$request .= '0';
                                //}

                                if ($stoim_item2 != 0){
                                    $stoim_item = $stoim_item2;
                                }

                                //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                                /*if ($items['guarantee'] != 0){
                                    $stoim_item = $stoim_item2 = '0';
                                }*/


                                //$stoim_item = round($stoim_item/10) * 10;

								/*if ($items['insure'] != 0){
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
								}*/

								/*$request .= '
								<div class="cellCosmAct invoiceItemPrice settings_text" ind="'.$ind.'" key="'.$key.'" start_price="'.$price['start_price'].'" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; '.$bg_col.'" onclick="contextMenuShow('.$ind.', '.$key.', event, \'priceItem\');">
                                    '.$price['price'].'
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
								<div class="cellCosmAct settings_text" guarantee="'.$items['guarantee'].'" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; '.$bg_col.'" onclick="contextMenuShow('.$ind.', '.$key.', event, \'guaranteeItem\');">';
								if ($items['guarantee'] != 0){
									$request .= '
										<i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
								}else{
									$request .= '-';
								}*/
								$request .= '
								<!--</div>-->
								<div class="cellCosmAct invoiceItemPriceItog" style="font-size: 100%; text-align: center; '.$bg_col.' width: 60px; min-width: 60px; max-width: 60px;">
									'.$stoim_item.'
								</div>
                                            <div class="cellName" style="font-size: 80%; width: 120px; max-width: 120px;">';
                                $request .= '
                                            <select name="percent_cat'.$ind.'_'.$key.'" id="percent_cat'.$ind.'_'.$key.'" style="width: 110px; max-width: 110px;" onchange="fl_changeItemPercentCat('.$ind.', '.$key.', $(\'#percent_cat'.$ind.'_'.$key.'\').val());">';

                                                if ( $percent_cats_j != 0){
                                                    for ($i=0;$i<count( $percent_cats_j);$i++){
                                                        if ($percent_cat ==  $percent_cats_j[$i]['id']){
                                                            $selected = ' selected';
                                                        }else{
                                                            $selected = '';
                                                        }

                                                        $request .= "<option value='". $percent_cats_j[$i]['id']."' ".$selected.">". $percent_cats_j[$i]['name']."</option>";
                                                    }
                                                }
                                $request .= '
                                            </select>';
                                $request .= '
					                        </div>
								<div invoiceitemid="'.$key.'" class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteCalculateItem('.$ind.', this);">
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
								<div class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteCalculateItem('.$ind.', this);">
									<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
						}
							$request .= '
							</div>';
					}

					echo json_encode(array('result' => 'success', 'data' => $request));
				}
			}
		}
	}
?>