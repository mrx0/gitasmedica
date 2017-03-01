<?php 

//add_price_id_in_invoice_f
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['price_id']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] != 0){
					$t_number_active = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'];
					
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active])){
						array_push($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active], $_POST['price_id']);
					}
					
				}

				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>