<?php

//wrights.php
//Права

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			include_once 'DBWork.php';
			$temp_arr = array();
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Права</h1>
				</header>		
				<!--p style="margin: 5px 0; padding: 1px; font-size:80%;">
					Быстрый поиск: 
					<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
				</p>-->
				<div id="data">';
				
			$permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump ($permissions);
			if ($permissions != 0){
				for ($i=0; $i<count($permissions); $i++){
					echo '
						<div class="cellsBlock2">
							<div class="cellLeft" style="width:20px; min-width:20px;"><b>'.$permissions[$i]['id'].'</b></div>
							<div class="cellLeft"><b>'.$permissions[$i]['name'].'</b></div>
						</div>';
						
					foreach ($permissions[$i] as $key1 => $value1){
						if (($key1 != 'id') && ($key1 != 'name')){
							echo '
								<div class="cellsBlock2">
									<div class="cellLeft">
										'.$key1.'
									</div>
								</div>		
								<div class="cellsBlock2">
									<div class="cellLeft">';
							
							$temp_arr = json_decode($value1, true);
							foreach ($temp_arr as $key => $value){
								echo $key.'-> ', ($value > 0) ? '<span style="font-weight: bold; color: green">да</span>' : '<span style="font-weight: bold; color: red">нет</span>' ,'<br />';					
							}
							
							echo '
									</div>
								</div>';
						}
					}
				}
			}
			echo '	
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>