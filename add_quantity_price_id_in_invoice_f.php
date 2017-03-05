<?php 

//add_koeff_price_id_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['quantity']) || !isset($_POST['key']) || !isset($_POST['zub']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']])){
					
						$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]['quantity'] = $_POST['quantity'];
						//$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active]
					}
				}
				
				//echo json_encode(array('result' => 'success', 'data' => $_POST['key']));
			}
		}
	}
?>