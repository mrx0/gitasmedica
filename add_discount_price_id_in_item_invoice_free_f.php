<?php 

//add_discount_price_id_in_item_invoice_free_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['discount']) || !isset($_POST['invoice_type']) || !isset($_POST['filial'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				if (isset($_SESSION['invoice_data']['free_invoice']['data'])){

						if (isset($_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']])){
                            $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['discount'] = (int)$_POST['discount'];

                            $discount = (int)$_POST['discount'];
                            $price = $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['price'];
                            $quantity = $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['quantity'];

                            $price = $price * $quantity;

                            $quantity = $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['quantity'];

                            $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['manual_itog_price'] = round(($price - ($price * $discount / 100)), 0);
						}
				}
				
				echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]));
			}
		}
	}
?>