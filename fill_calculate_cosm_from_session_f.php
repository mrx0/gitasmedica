<?php 

//fill_calculate_cosm_from_session_f.php
//на 2018.08.06 точная копия fill_calculate_stom_from_session_f.php

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		$request = '';

		if ($_POST){
			if (!isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				echo json_encode(array('result' => 'error', 'data' => 'Ошибка #9'));
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
					echo json_encode(array('result' => 'error', 'data' => 'Ошибка #10 ['.$client.'/'.$zapis_id.']'));
				}else{
					//берем из сесии данные
					$data = $_SESSION['calculate_data'][$client][$zapis_id]['data'];

                    if (!empty($data)){

                        $discount = $_SESSION['calculate_data'][$client][$zapis_id]['discount'];

                        ksort($data);

                        $t_number_active = $_SESSION['calculate_data'][$client][$zapis_id]['t_number_active'];
                        $mkb_data = $_SESSION['calculate_data'][$client][$zapis_id]['mkb'];

                        foreach ($data as $ind => $items) {
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

                            $request .= $ind + 1;

                            $request .= '
                                    </div>';

                            //часть прайса

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

                            //foreach ($invoice_data as $key => $items){

                            if ($items['guarantee'] == 0) {

                                $percent_cat = $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind]['percent_cats'];

                                $percents_j = getPercents($_POST['worker'], $percent_cat);

                                $percent_cat_name = $percents_j[$percent_cat]['name'];
                                $work_percent = (int)$percents_j[$percent_cat]['work_percent'];
                                $material_percent = (int)$percents_j[$percent_cat]['material_percent'];

                                $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind]['work_percent'] = $work_percent;
                                $_SESSION['calculate_data'][$client][$zapis_id]['data'][$ind]['material_percent'] = $material_percent;

                                $request .= '
                                        <div class="cellsBlock" style="font-size: 100%;" >
                                        <!--<div class="cellCosmAct" style=" ' . $bg_col . '">
                                            -
                                        </div>-->
                                        <div class="cellText2" style=" ' . $bg_col . '"><div style="">';

                                //Хочу имя позиции в прайсе
                                $arr = array();
                                $rez = array();

                                $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$items['price_id']}'";

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
                                    //$request .= $rezult2[0]['name'];
                                    $request .= '<i>'.$rezult2[0]['code'].'</i>'.$rezult2[0]['name'].' <span style="font-size: 90%; background: rgba(197, 197, 197, 0.41);">[#'.$rezult2[0]['id'].']</span>';

                                    $spec_koeff = $items['spec_koeff'];

                                    //получим цены
                                    $prices = takePrices($items['price_id'], $items['insure']);
                                    //var_dump($prices);

                                    if (!empty($prices)) {
                                        if (isset($items['price'])) {
                                            if (!isset($items['manual_price'])) {
                                                $items['manual_price'] = false;
                                            }
                                            if (!isset($items['start_price'])) {
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

                                } else {
                                    $request .= '?';
                                }

                                $request .= '
                                        </div>
                                        </div>';

                                $price = $items['price'];

                                //вычисляем стоимость
                                //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                $stoim_item = $items['quantity'] * $price;

                                //с учетом скидки акции
                                if ($items['insure'] == 0) {
                                    //$stoim_item = $stoim_item - ($stoim_item * $invoice_j[0]['discount'] / 100);
                                    $stoim_item = $stoim_item - ($stoim_item * $items['discount'] / 100);
                                    //$stoim_item = round($stoim_item/10) * 10;
                                    $stoim_item = round($stoim_item);
                                }


                                //if (isset($items['manual_itog_price'])){
                                if (isset($items['itog_price'])) {
                                    if ($items['itog_price'] > 0) {
                                        $stoim_item2 = $items['itog_price'];
                                    }else{
                                        $stoim_item2 = '0';
                                    }
                                } else {
                                    $stoim_item2 = '0';
                                }

                                if ($stoim_item2 != 0) {
                                    $stoim_item = $stoim_item2;
                                }


                                $request .= '
      
                                        <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 100%; text-align: center; ' . $bg_col . ' width: 60px; min-width: 60px; max-width: 60px;">
                                            ' . $stoim_item . '
                                        </div>
                                                    <div class="cellName" style="font-size: 90%; text-align: right; width: 120px; max-width: 120px;' . $bg_col . '">';
                                /*$request .= '
                                                    <select name="percent_cat' . $ind . '_0" id="percent_cat' . $ind . '_0" style="width: 110px; max-width: 110px;" onchange="fl_changeItemPercentCat(' . $ind . ', 0, $(\'#percent_cat' . $ind . '_0\').val());">';

                                if ($percent_cats_j != 0) {
                                    for ($i = 0; $i < count($percent_cats_j); $i++) {
                                        if ($percent_cat == $percent_cats_j[$i]['id']) {
                                            $selected = ' selected';
                                        } else {
                                            $selected = '';
                                        }

                                        $request .= "<option value='" . $percent_cats_j[$i]['id'] . "' " . $selected . ">" . $percent_cats_j[$i]['name'] . "</option>";
                                    }
                                }
                                $request .= '
                                                    </select>';*/

                                $request .= '<i>'.$percent_cat_name.'</i>';

                                $request .= '
                                                    </div>
                                            <div class="cellCosmAct info" style="font-size: 100%; text-align: center; ' . $bg_col . '" onclick="deleteCalculateItem(' . $ind . ', this);">
                                                <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                            </div>
                                        </div>';
                            }
                            //}
                            /*}else{
                                $request .= '
                                <div class="cellsBlock" style="font-size: 100%;" >
                                    <div class="cellText2" style="text-align: center; '.$bg_col.' border: 1px dotted #DDD;">
                                        <span style="color: rgba(255, 0, 0, 0.62);">не заполнено</span>
                                    </div>
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteCalculateItem('.$ind.', this);">
                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                    </div>
                                </div>';
                            }*/
                            $request .= '
                                </div>';
                        }
                    }else{
                        $request = '';
                    }

					echo json_encode(array('result' => 'success', 'data' => $request));
				}
			}
		}
	}
?>