<?php 

//add_invoice_free_in_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['t_number']) || !isset($_POST['filial'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				$t_number = $_POST['t_number'];
				$filial = $_POST['filial'];

				if (!isset($_SESSION['invoice_data']['free_invoice']['data'])){
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}else{
                    $_SESSION['invoice_data']['free_invoice']['t_number_active'] = (int)$t_number;
					if (!isset($_SESSION['invoice_data']['free_invoice']['data'][$t_number])){
						$_SESSION['invoice_data']['free_invoice']['data'][$t_number] = array();
					}
					echo json_encode(array('result' => 'success', 'data' => 'OK'));
				}
			}
		}
	}
?>