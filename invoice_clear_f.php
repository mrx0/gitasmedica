<?php 

//invoice_clear_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			/*if (!isset($_POST['client']) || !isset($_POST['zapis_id'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{*/
				$client = $_POST['client'];
				$zapis_id = $_POST['zapis_id'];
				
				if (isset($_SESSION['invoice_data'][$client][$zapis_id]['data'])){
					
					$_SESSION['invoice_data'][$client][$zapis_id]['data'] = array();
					$_SESSION['invoice_data'][$client][$zapis_id]['mkb'] = array();
					$_SESSION['invoice_data'][$client][$zapis_id]['t_number_active'] = 0;
					
					echo json_encode(array('result' => 'success', 'data' => 'OK'));
					
				}

                if (isset($_SESSION['invoice_data']['free_invoice'])){

                    $_SESSION['invoice_data']['free_invoice']['data'] = array();
                    $_SESSION['invoice_data']['free_invoice']['mkb'] = array();
                    $_SESSION['invoice_data']['free_invoice']['t_number_active'] = 0;

                    echo json_encode(array('result' => 'success', 'data' => 'OK'));

                }
			//}
		}
	}
?>