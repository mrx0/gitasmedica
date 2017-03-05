<?php 

//context_menu_show_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$data = '';
			
			if (!isset($_POST['mark'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

				if ($_POST['mark'] == 'koeff'){
					$data = '
						<li><div onclick="koeffInvoice(0)">нет</div></li>'.
						'<li><div onclick="koeffInvoice(10)">Ведущий сп-т +10%</div></li>'.
						'<li><div onclick="koeffInvoice(20)">Главный сп-т +20%</div></li>';
				}
				
				if ($_POST['mark'] == 'insure'){
					include_once 'DBWork.php';
					
					$data .= '
						<li><div onclick="insureInvoice(0)">не страховой</div></li>';
					
					$insures_j = SelDataFromDB('spr_insure', '', '');
					
					if ($insures_j != 0){
						for ($i=0;$i<count($insures_j);$i++){
							$data .= '
								<li><div onclick="insureInvoice('.$insures_j[$i]['id'].')">'.$insures_j[$i]['name'].'</div></li>';
						}
					}
				}

				echo json_encode(array('result' => 'success', 'data' => $data));

			}
		}
	}
?>