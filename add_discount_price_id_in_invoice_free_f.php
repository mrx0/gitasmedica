<?php 

//add_discount_price_id_in_invoice_free_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['discount']) || !isset($_POST['invoice_type']) || !isset($_POST['filial'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data']['free_invoice']['data'])){
					$data = $_SESSION['invoice_data']['free_invoice']['data'];
					
					foreach ($data as $zub => $invoice_data){
						
						if (!empty($invoice_data)){
							if ($_POST['invoice_type'] == 88){

                                $data = $_SESSION['invoice_data']['free_invoice']['data'][$zub]['discount'] = (int)$_POST['discount'];

							}
						}
					}

                    //$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'] = (int)$_POST['discount'];
				}
				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>