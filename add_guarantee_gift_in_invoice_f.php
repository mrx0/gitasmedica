<?php 

//add_guarantee_gift_in_invoice_f.php
//Добавление гарантии или подарка в сессионную переменную наряда

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['guaranteeOrGift']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];
					
					foreach ($data as $zub => $invoice_data){
						
						if (!empty($invoice_data)){
							if ($_POST['invoice_type'] == 5){
								foreach ($invoice_data as $key => $items){
									
									//$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['guarantee'] = (int)$_POST['guarantee'];

                                    if ($_POST['guaranteeOrGift'] == 1) {
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['guarantee'] = 1;
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['gift'] = 0;
                                    }elseif ($_POST['guaranteeOrGift'] == 2){
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['guarantee'] = 0;
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['gift'] = 1;
                                    }else{
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['guarantee'] = 0;
                                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub][$key]['gift'] = 0;
                                    }

								}
							}
							if ($_POST['invoice_type'] == 6){

									//$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['guarantee'] = (int)$_POST['guarantee'];

                                if ($_POST['guaranteeOrGift'] == 1) {
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['guarantee'] = 1;
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['gift'] = 0;
                                }elseif ($_POST['guaranteeOrGift'] == 2){
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['guarantee'] = 0;
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['gift'] = 1;
                                }else{
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['guarantee'] = 0;
                                    $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$zub]['gift'] = 0;
                                }

							}
						}
					}
				}
				
				echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'], 'guaranteeOrGift' => $_POST['guaranteeOrGift']));
			}
		}
	}
?>