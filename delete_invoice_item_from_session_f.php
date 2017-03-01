<?php 

//delete_invoice_item_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['key']) || !isset($_POST['zub']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				if ($_POST['target'] === 'item'){
					unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				}elseif ($_POST['target'] === 'zub'){
					unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']]);
				}
				
				if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] == $_POST['zub']){
					if (empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']])){
						$keys = array_keys($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data']);
						//$firstKey = $keys[0];
						$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] = $keys[0];
					}
				}
				
				if (empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] = 0;
				}
				
				ksort($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data']);
				
				echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>