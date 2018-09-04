<?php 

//add_koeff_price_id_in_invoice_f.php
//
//Нигде не используем, удаляю 2018-08-31

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['koeff']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];
					
					foreach ($data as $zub => $invoice_data){
						
						if (!empty($invoice_data)){
							
							foreach ($invoice_data as $key => $items){
								
								$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['koeff'] = $_POST['koeff'];
								
							}
						}
					}
				}
				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>