<?php 

//add_price_price_id_in_item_invoice_free_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
        //echo json_encode(array('result' => 'success', 'data' => $_POST));
		
		if ($_POST){

			$temp_arr = array();
			
			if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['price']) || !isset($_POST['invoice_type']) || !isset($_POST['filial'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data']['free_invoice']['data'])){

						if (isset($_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']])){
                            $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['price'] = (int)$_POST['price'];
                            $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]['manual_itog_price'] = (int)$_POST['price'];
						}
				}
				
				echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data']['free_invoice']['data'][$_POST['ind']]));
			}
		}
	}
?>