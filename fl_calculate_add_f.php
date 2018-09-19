<?php 

//fl_calculate_add_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){



			if (!isset($_POST['invoice_type']) || !isset($_POST['summ']) || !isset($_POST['summins']) || !isset($_POST['client_id']) || !isset($_POST['zapis_id']) || !isset($_POST['invoice_id']) || !isset($_POST['filial_id']) || !isset($_POST['worker_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

				if (isset($_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'])){
					if (!empty($_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'])) {
                        $data = $_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['data'];

                        $discount = $_SESSION['calculate_data'][$_POST['client_id']][$_POST['zapis_id']]['discount'];

                        //!!! @@@
                        include_once 'ffun.php';

                        $calculateSaveResult = calculateCalculateSave ($data, $_POST['zapis_id'], $_POST['invoice_id'], $_POST['filial_id'], $_POST['client_id'], $_POST['worker_id'], $_POST['invoice_type'], $_POST['summ'], $discount, $_SESSION['id']);

						echo json_encode(array('result' => $calculateSaveResult['result'], 'data' => $calculateSaveResult['data']));
					}
				}
			}
		}
	}

?>