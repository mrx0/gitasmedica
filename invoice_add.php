<?php

//
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		include_once 'DBWork.php';
		include_once 'functions.php';
	
		require 'config.php';

		//var_dump($_SESSION);
		//unset($_SESSION['invoice_data']);
		
		if ($_GET){
			if (isset($_GET['client']) && isset($_GET['id']) && isset($_GET['filial']) && isset($_GET['worker'])){
		
				//array_push($_SESSION['invoice_data'], $_GET['client']);
				//$_SESSION['invoice_data'] = $_GET['client'];
				if (!isset($_SESSION['invoice_data'][$_GET['client']][$_GET['id']])){
					$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['filial'] = $_GET['filial'];
					$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['worker'] = $_GET['worker'];
					$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['t_number_active'] = 0;
					$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data'] = array();
				}
				//сортируем зубы по порядку
				ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['data']);
				
				//var_dump($_SESSION);
				//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['id']]);
				
				echo '
					<div id="data">';
				
				//Зубки
				echo '	
						<input type="hidden" id="client" name="client" value="'.$_GET['client'].'">
						<input type="hidden" id="zapis_id" name="zapis_id" value="'.$_GET['id'].'">
						<input type="hidden" id="filial" name="filial" value="'.$_GET['filial'].'">
						<input type="hidden" id="worker" name="worker" value="'.$_GET['worker'].'">
						<input type="hidden" id="t_number_active" name="t_number_active" value="'.$_SESSION['invoice_data'][$_GET['client']][$_GET['id']]['t_number_active'].'">
						
						<div style="vertical-align: middle;">
							<div id="teeth" style="display: inline-block;">
								<div class="tooth_updown">
									<div class="tooth_left" style="display: inline-block;">
										<div class="sel_tooth">
											18
										</div>
										<div class="sel_tooth">
											17
										</div>
										<div class="sel_tooth">
											16
										</div>
										<div class="sel_tooth">
											15
										</div>
										<div class="sel_tooth">
											14
										</div>
										<div class="sel_tooth">
											13
										</div>
										<div class="sel_tooth">
											12
										</div>
										<div class="sel_tooth">
											11
										</div>
									</div>			
									<div class="tooth_right" style="display: inline-block;">
										<div class="sel_tooth">
											21
										</div>
										<div class="sel_tooth">
											22
										</div>
										<div class="sel_tooth">
											23
										</div>
										<div class="sel_tooth">
											24
										</div>
										<div class="sel_tooth">
											25
										</div>
										<div class="sel_tooth">
											26
										</div>
										<div class="sel_tooth">
											27
										</div>
										<div class="sel_tooth">
											28
										</div>
									</div>
								</div>
								<div class="tooth_updown">
									<div class="tooth_left" style="display: inline-block;">
										<div class="sel_tooth">
											48
										</div>
										<div class="sel_tooth">
											47
										</div>
										<div class="sel_tooth">
											46
										</div>
										<div class="sel_tooth">
											45
										</div>
										<div class="sel_tooth">
											44
										</div>
										<div class="sel_tooth">
											43
										</div>
										<div class="sel_tooth">
											42
										</div>
										<div class="sel_tooth">
											41
										</div>
									</div>			
									<div class="tooth_right" style="display: inline-block;">
										<div class="sel_tooth">
											31
										</div>
										<div class="sel_tooth">
											32
										</div>
										<div class="sel_tooth">
											33
										</div>
										<div class="sel_tooth">
											34
										</div>
										<div class="sel_tooth">
											35
										</div>
										<div class="sel_tooth">
											36
										</div>
										<div class="sel_tooth">
											37
										</div>
										<div class="sel_tooth">
											38
										</div>
									</div>
								</div>
							</div>
							<div id="teeth_polost" class="sel_toothp" style="display: inline-block; vertical-align: middle; text-align: center; margin: 2px; padding: 5px;">
								Полость
							</div>
						</div>';
						
				//Прайс							
				echo '			
						<div  style="display: inline-block;">';

				echo '	
							<div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
								<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
							</div>';
					
				echo '
							<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
								<ul class="ul-tree ul-drop" id="lasttree">';

				showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0);		
					
				echo '
								</ul>
							</div>';					
				echo '
						</div>';

				//Результат							
				echo '			
						<div class="invoice_rezult" style="display: inline-block;">';
						
				echo '	
							<div id="errror" style="margin: 10px 0 5px; font-size: 11px; padding-left: 5px;">
								
							</div>
							<div id="calculateInvoice">0</div>';
				
				echo '
							<div id="invoice_rezult" style="width: 500px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
							</div>';
				echo '
						</div>';
				
				echo '
					</div>';
					
			}
		}

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>