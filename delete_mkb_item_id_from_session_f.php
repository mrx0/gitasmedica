<?php 

//delete_mkb_item_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				
				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$_POST['ind']])){
					foreach ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$_POST['ind']] as $index => $value){
						if ($value == $_POST['key']){
							unset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$_POST['ind']][$index]);
						}
					}
				}

			}
		}
	}
?>