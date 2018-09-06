<?php 

//fl_calculate_reset_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['invoice_type']) || !isset($_POST['summ']) || !isset($_POST['summins']) || !isset($_POST['client_id']) || !isset($_POST['zapis_id']) || !isset($_POST['filial_id']) || !isset($_POST['worker_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

				if (isset($_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'])){
					if (!empty($_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'])){

						unset($_SESSION['calculate_data']);

						echo json_encode(array('result' => 'success', 'data' => ''));
					}
				}
			}
		}
	}
?>