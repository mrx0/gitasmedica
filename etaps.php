<?php

//etap.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($cosm['add_own'] == 1) || ($cosm['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			/*$offices = SelDataFromDB('spr_filials', '', '');*/
			
			//clear_dir('uploads');
			
			$post_data = '';
			$js_data = '';
			
			//Если у нас по GET передали клиента
			$get_client = '';
			if (isset($_GET['client']) && ($_GET['client'] != '')){
				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
				if ($client !=0){
					$get_client = $client[0]['full_name'];
				}
				
				echo '
					<div id="status">
						<header>
							<h2>Исследования</h2>

						</header>';

				echo '
						<div id="data">';
				echo '
								<div class="cellsBlock3">
									<div class="cellLeft">Пациент</div>
									<div class="cellRight">
										'.$get_client.'
									</div>
								</div>';
								
				$issled = SelDataFromDB('journal_etaps', $client[0]['id'], 'client');	

				echo '
					<a href="add_etap.php?client='.$client[0]['id'].'" class="b" style="margin-bottom: 40px;">Добавить исследование</a>
				';
				
				if ($issled != 0){
					echo '
						<ul style="margin-left:6px;">
					';
					foreach($issled as $value){
						
						echo '	<li class="cellsBlock">	
									<a href="etap.php?id='.$value['id'].'" class="cellFullName ahref" style="margin-bottom: 10px;">'.$value['name'].'</a>
								</li>';
					}
					echo '
						</ul>
					';
				}else{
					echo '
						<h3>У пациента нет исследований</h3>
					';
				}

					
					
					
	
			}else{
				echo '<h1>Не выбран пациент.</h1><a href="index.php">На главную</a>';
			}

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>