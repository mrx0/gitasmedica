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
                    $price = 0;
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

                                $price = returnPriceWithKoeff($spec_koeff, $prices, $insure);

                            }

							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['spec_koeff'] = $_POST['spec_koeff'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['price'] = $price;
						}
					}
					if ($_POST['invoice_type'] == 6){
						if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
                            $item =  $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['id'];
                            $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['insure'];


                            //получим цены
                            $prices = takePrices ($item, $insure);
                            //var_dump($prices);

                            if (!empty($prices)) {

                                $price = returnPriceWithKoeff($spec_koeff, $prices, $insure);

                            }

							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['spec_koeff'] = $_POST['spec_koeff'];
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['price'] = $price;
						}
					}
				}
				
				//echo json_encode(array('result' => 'success', 'data' => $_POST['key']));
			}
		}
	}
?>