<?php 

//delete_invoice_item_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['key']) || !isset($_POST['ind']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);
				if ($_POST['target'] === 'item'){
					unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);
				}elseif ($_POST['target'] === 'ind'){
					unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]);
					unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$_POST['ind']]);
				}
				
				if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] == $_POST['ind']){
					if (empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
						$keys = array_keys($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data']);
						//$firstKey = $keys[0];
                        if (($_POST['ind'] != 0) && !empty($keys)) {
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] = $keys[0];
                        }else{
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] = 0;
                        }

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