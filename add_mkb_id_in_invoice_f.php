<?php 

//add_mkb_id_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			$iExist = false;
			$existID = 0;
			
			if (!isset($_POST['mkb_id']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] != 0){
					$t_number_active = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'];
					
					//$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active] = (int)$_POST['mkb_id'];
					
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active])){
						//проверим, нет ли уже такой позиции в диагнозе
						if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active])){
							$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active];
							foreach ($data as $mkb_data){
								if ($mkb_data == $_POST['mkb_id']){
									$iExist = true;
									//$existID = $key;
								}
							}
						}
					}else{
						$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active] = array();
					}

					if ($iExist){
						//
					}else{
						array_push($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active], (int)$_POST['mkb_id']);
					}
					
					
					
					/*if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$t_number_active])){
						
						//проверим, нет ли уже такой позиции в акте
						if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active])){
							$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active];
							foreach ($data as $key => $invoice_data){
								if ($invoice_data['id'] == $_POST['price_id']){
									$iExist = true;
									$existID = $key;
								}
							}
						}
						
						if ($iExist){
							$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active][$existID]['quantity']++;
						}else{
							$temp_arr['id'] = (int)$_POST['price_id'];
							$temp_arr['quantity'] = 1;
							$temp_arr['insure'] = 0;
							$temp_arr['insure_approve'] = 0;
							$temp_arr['price'] = 0;
							$temp_arr['guarantee'] = 0;
							$temp_arr['spec_koeff'] = 0;
							$temp_arr['discount'] = 0;
							
							//Если посещение страховое и у пациента прописана страховая
							if (isset($_POST['zapis_insure'])){
								if (isset($_POST['client_insure'])){
									if ($_POST['zapis_insure'] != 0){
										if ($_POST['client_insure'] != 0){
											$temp_arr['insure'] = (int)$_POST['client_insure'];
										}
									}
								}
							}
							
							array_push($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active], $temp_arr);
						}
					}*/
					
				}

				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>