<?php

//admin.php
//Админка

	require_once 'header.php';

	if ($enter_ok){
		if ($god_mode){
			include_once 'DBWork.php';
			//$offices = SelDataFromDB('spr_office', '', '');
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Админка</h1>
				</header>		
				<div id="data">';
				
			echo '<a href="/sxd">SXD</a><br />';
			echo '<a href="logs.php">LOGS</a>';
			$permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump ($permissions);
			if ($permissions != 0){
				for ($i=0; $i<count($permissions); $i++){
					echo '
						<div class="cellsBlock2">
							<div class="cellLeft" style="width:20px; min-width:20px;"><b>'.$permissions[$i]['id'].'</b></div>
							<div class="cellLeft"><b>'.$permissions[$i]['name'].'</b></div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								IT
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$it = json_decode($permissions[$i]['it'], true);
					echo $permissions[$i]['it'];
					//var_dump ($it);
					foreach ($it as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo
							'</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								cosmet
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$cosm = json_decode($permissions[$i]['cosm'], true);
					foreach ($cosm as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo
							'</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								stomat
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$stom = json_decode($permissions[$i]['stom'], true);
					foreach ($stom as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo
							'</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								clients
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$clients = json_decode($permissions[$i]['clients'], true);
					foreach ($clients as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo
							'</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								workers
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$workers = json_decode($permissions[$i]['workers'], true);
					foreach ($workers as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo
							'</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">
								offices
							</div>
						</div>
						<div class="cellsBlock2">
							<div class="cellLeft">';
					$offices = json_decode($permissions[$i]['offices'], true);
					foreach ($offices as $key => $value){
						echo $key.'->'.$value.'<br />';					
					}
					echo 
							'</div>
						</div>
					';
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