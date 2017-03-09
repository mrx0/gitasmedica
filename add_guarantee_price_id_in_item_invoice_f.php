<?php 

//add_guarantee_price_id_in_item_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['zub']) || !isset($_POST['key']) || !isset($_POST['guarantee']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					if ($_POST['invoice_type'] == 5){
						if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']])){
							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]['guarantee'] = (int)$_POST['guarantee'];
						}
					}
					if ($_POST['invoice_type'] == 6){
						if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']])){
							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']]['guarantee'] = (int)$_POST['guarantee'];
						}
					}
				}
				
				//echo json_encode(array('result' => 'success', 'data' => $_POST['key']));
			}
		}
	}
?>