<?php 

//fl_add_percent_cat_in_invoice_f.php
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
					
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];

                    include_once 'DBWork.php';

                    //Категории процентов
                    $percents_j = SelDataFromDB('fl_spr_percents', $_POST['percent_cats'], 'id');

                    $work_percent = (int)$percents_j[0]['work_percent'];
                    $material_percent = (int)$percents_j[0]['material_percent'];

					foreach ($data as $ind => $invoice_data){

						if (!empty($invoice_data)){
							if ($_POST['invoice_type'] == 5){
								foreach ($invoice_data as $key => $items){

									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['percent_cats'] = (int)$_POST['percent_cats'];
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['work_percent'] = $work_percent;
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['material_percent'] = $material_percent;

								}
							}
							if ($_POST['invoice_type'] == 6){

									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['percent_cats'] = (int)$_POST['percent_cats'];
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['work_percent'] = $work_percent;
									$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['material_percent'] = $material_percent;

							}
						}
					}
                    echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data']));
				}
			}
		}
	}
?>