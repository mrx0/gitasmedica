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
				//Коэффициент общий
				if ($_POST['mark'] == 'spec_koeff'){
					$data = '
						<li><div onclick="spec_koeffInvoice(0)">нет</div></li>'.
						'<li><div onclick="spec_koeffInvoice(10)">Ведущий сп-т +10%</div></li>'.
						'<li><div onclick="spec_koeffInvoice(20)">Главный сп-т +20%</div></li>'.
						'<li><div><input type="number" size="2" name="koeff" id="koeff" min="1" max="99" value="1" class="mod"><div style="display: inline;" onclick="spec_koeffInvoice(document.getElementById(\'koeff\').value)"> Применить</div></div></li>';
				}
				//По гарантии общий
				if ($_POST['mark'] == 'guarantee'){
					$data = '
						<li><div onclick="guaranteeInvoice(0)">нет</div></li>'.
						'<li><div onclick="guaranteeInvoice(1)">По гарантии</div></li>';
				}
				//Страховая общее
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
				//Страховая согласовано общее
				if ($_POST['mark'] == 'insure_approve'){
					$data = '
						<li><div onclick="insureApproveInvoice(0)">нет</div></li>'.
						'<li><div onclick="insureApproveInvoice(1)">Согласовано</div></li>';
				}	
				//Скидка акция общее
				if ($_POST['mark'] == 'discounts'){
					$data = '
						<li><div onclick="discountInvoice(0)">нет</div></li>'.
						'<li><div><input type="number" size="2" name="discount" id="discount" min="1" max="100" value="1" class="mod"><div style="display: inline;" onclick="discountInvoice(document.getElementById(\'discount\').value)"> Применить</div></div></li>';
				}
				//Страховая согласовано позиция
				if ($_POST['mark'] == 'insure_approveItem'){
					$data = '
						<li><div onclick="insureApproveItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 0)">нет</div></li>'.
						'<li><div onclick="insureApproveItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 1)">Согласовано</div></li>';
				}
				//Гарантия позиция
				if ($_POST['mark'] == 'guaranteeItem'){
					$data = '
						<li><div onclick="guaranteeItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 0)">нет</div></li>'.
						'<li><div onclick="guaranteeItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 1)">По гарантии</div></li>';
				}
				//Коэффициент позиция
				if ($_POST['mark'] == 'spec_koeffItem'){
					$data = '
						<li><div onclick="spec_koeffItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 0)">нет</div></li>'.
						'<li><div onclick="spec_koeffItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 10)">Ведущий сп-т +10%</div></li>'.
						'<li><div onclick="spec_koeffItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 20)">Главный сп-т +20%</div></li>'.
						'<li><div><input type="number" size="2" name="koeff" id="koeff" min="1" max="100" value="1" class="mod"><div style="display: inline;" onclick="spec_koeffItemInvoice('.$_POST['zub'].', '.$_POST['key'].', document.getElementById(\'koeff\').value)"> Применить</div></div></li>';
				}
				//Скидки акции позиция
				if ($_POST['mark'] == 'discountItem'){
					$data = '
						<li><div onclick="discountItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 0)">нет</div></li>'.
						'<li><div><input type="number" size="2" name="discount" id="discount" min="1" max="100" value="1" class="mod"><div style="display: inline;" onclick="discountItemInvoice('.$_POST['zub'].', '.$_POST['key'].', document.getElementById(\'discount\').value)"> Применить</div></div></li>';
				}
				//Страховка позиция
				if ($_POST['mark'] == 'insureItem'){
					include_once 'DBWork.php';
					
					$data .= '
						<li><div onclick="insureItemInvoice('.$_POST['zub'].', '.$_POST['key'].', 0)">не страховой</div></li>';
					
					$insures_j = SelDataFromDB('spr_insure', '', '');
					
					if ($insures_j != 0){
						for ($i=0;$i<count($insures_j);$i++){
							$data .= '
								<li><div onclick="insureItemInvoice('.$_POST['zub'].', '.$_POST['key'].', '.$insures_j[$i]['id'].')">'.$insures_j[$i]['name'].'</div></li>';
						}
					}
				}

				echo json_encode(array('result' => 'success', 'data' => $data));

			}
		}
	}
?>