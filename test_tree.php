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
		
		if ($_GET){
			if (isset($_GET['client'])){
		
				echo '
					<div id="data">';
				
				//Зубки
				echo '		
						<div id="teeth">
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
						</div>';
						
				//Результат	
				echo '
						<div id="invoice_rezult">
						</div>
						';
						
				//Прайс	
				echo '	
						<div style="margin: 10px 0 5px; font-size: 11px;">
							<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action 	">раскрыть всё</span>
						</div>';
					
				echo '
						<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
							<ul class="ul-tree ul-drop" id="lasttree">';

				showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0);		
					
				echo '
							</ul>
						</div>
						';	
				
				echo '
					</div>';
					
			}
		}

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>