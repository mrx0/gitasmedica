<?php 

//add_price_id_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['price_id']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] != 0){
					$t_number_active = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'];
					
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active])){
						
						$temp_arr['id'] = $_POST['price_id'];
						$temp_arr['quantity'] = 1;
						$temp_arr['insure'] = 0;
						$temp_arr['insure_approve'] = 0;
						$temp_arr['price'] = 0;
						$temp_arr['guarantee'] = 0;
						$temp_arr['koeff'] = 0;
						
						array_push($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active], $temp_arr);
					}
					
				}

				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>