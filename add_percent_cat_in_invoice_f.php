<?php 

//add_percent_cat_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['percent_cats']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'];
					
					foreach ($data as $zub => $invoice_data){
						
						if (!empty($invoice_data)){
							if ($_POST['invoice_type'] == 5){
								foreach ($invoice_data as $key => $items){
									
									$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['guarantee'] = (int)$_POST['guarantee'];
									
								}
							}
							if ($_POST['invoice_type'] == 6){

									$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['guarantee'] = (int)$_POST['guarantee'];

							}
						}
					}
				}
				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>