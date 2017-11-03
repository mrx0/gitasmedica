<?php

//add_spec_koeff_price_id_in_invoice_f.php
//

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['spec_koeff']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);

				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];
					//переменная для цены
                    $price['price'] = 0;
                    $price['start_price'] = 0;
					//переменная для массива цен
                    $prices = array();
                    //!!! @@@
                    include_once 'ffun.php';

                    $spec_koeff = $_POST['spec_koeff'];
                    //var_dump($prices);
					foreach ($data as $ind => $invoice_data){

						if (!empty($invoice_data)){
							if ($_POST['invoice_type'] == 5){
								foreach ($invoice_data as $key => $items){
                                    $item = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['id'];
                                    $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['insure'];

								    //получим цены
                                    $prices = takePrices ($item, $insure);
                                    //var_dump($prices);

                                    if (!empty($prices)) {

                                        $price = returnPriceWithKoeff($spec_koeff, $prices, $insure, false, 0);

                                    }

									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['spec_koeff'] = $spec_koeff;
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['price'] = $price['price'];
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['start_price'] = $price['start_price'];
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['manual_price'] = false;
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['manual_itog_price'] = $price['price'];

								}
							}
							if ($_POST['invoice_type'] == 6){
                                $item =  $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['id'];
                                $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure'];

                                //получим цены
                                $prices = takePrices ($item, $insure);
                                //var_dump($prices);

                                if (!empty($prices)) {

                                    $price = returnPriceWithKoeff($spec_koeff, $prices, $insure, false, 0);

                                }

								$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['spec_koeff'] = $spec_koeff;
								$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price'] = $price['price'];
								$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['start_price'] = $price['start_price'];
                                $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['manual_price'] = false;
                                $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['manual_itog_price'] = $price['price'];

							}
						}
					}
				}

				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>