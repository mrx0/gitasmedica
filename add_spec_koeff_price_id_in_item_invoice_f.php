<?php 

//add_spec_koeff_price_id_in_item_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();

			if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['spec_koeff']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);

				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){

				    //переменная для цены
                    $price['price'] = 0;
                    $price['start_price'] = 0;
                    //переменная для массива цен
                    $prices = array();
                    //!!! @@@
                    include_once 'ffun.php';

                    $spec_koeff = $_POST['spec_koeff'];

					if ($_POST['invoice_type'] == 5){
						if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']])){
                            $item = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['id'];
                            $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['insure'];

                            //получим цены
                            $prices = takePrices ($item, $insure);
                            //var_dump($prices);

                            if (!empty($prices)) {

                                $price = returnPriceWithKoeff($spec_koeff, $prices, $insure, false, 0);

                            }

							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['spec_koeff'] = $_POST['spec_koeff'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['price'] = $price['price'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['start_price'] = $price['start_price'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['manual_price'] = false;
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['manual_itog_price'] = $price['price'];
						}
					}
					if (($_POST['invoice_type'] == 6) || ($_POST['invoice_type'] == 10) || ($_POST['invoice_type'] == 88)){
						if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
                            $item =  $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['id'];
                            $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['insure'];


                            //получим цены
                            $prices = takePrices ($item, $insure);
                            //var_dump($prices);

                            if (!empty($prices)) {

                                $price = returnPriceWithKoeff($spec_koeff, $prices, $insure, false, 0);

                            }

							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['spec_koeff'] = $_POST['spec_koeff'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['price'] = $price['price'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['start_price'] = $price['start_price'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['manual_price'] = false;
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['manual_itog_price'] = $price['price'];
						}
					}
				}

				echo json_encode(array('result' => 'success'));
			}
		}
	}
?>