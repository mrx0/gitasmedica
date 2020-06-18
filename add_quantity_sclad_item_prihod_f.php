<?php 

//add_quantity_sclad_item_prihod_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['quantity']) || !isset($_POST['item_id']) || !isset($_POST['ind'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['item_id']]);
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['item_id']]);

				if (isset($_SESSION['sclad']['items_prihod_data'])){
                    if (isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']])){
                        $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]['quantity'] = (int)$_POST['quantity'];
//                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['item_id']]['manual_itog_price'] = (int)$_POST['quantity'] * $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['price'];
                    }
				}
				
				echo json_encode(array('result' => 'success', 'data' => $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]));
			}
		}
	}
?>