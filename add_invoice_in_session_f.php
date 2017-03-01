<?php 

//add_invoice_in_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['client']) || !isset($_POST['t_number']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				$client = $_POST['client'];
				$t_number = $_POST['t_number'];
				$zapis_id = $_POST['zapis_id'];
				$filial = $_POST['filial'];
				$worker = $_POST['worker'];
				
				if (!isset($_SESSION['invoice_data'][$client][$zapis_id]['data'])){
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}else{
					$_SESSION['invoice_data'][$client][$zapis_id]['t_number_active'] = $t_number;
					if (!isset($_SESSION['invoice_data'][$client][$zapis_id]['data'][$t_number])){
						$_SESSION['invoice_data'][$client][$zapis_id]['data'][$t_number] = array();
					}
					echo json_encode(array('result' => 'success', 'data' => 'OK'));
				}
			}
		}
	}
?>