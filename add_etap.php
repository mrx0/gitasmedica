<?php

//add_etap.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($cosm['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			/*$offices = SelDataFromDB('spr_filials', '', '');*/
			
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
							<h2>Добавить исследование</h2>

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
				echo '
							<form action=""add_etap_f.php">
					
							<div class="cellsBlock3">
								<div class="cellLeft">Название исследования</div>
								<div class="cellRight">
									<input type="text" size="50" name="name" id="name" value="">
								</div>
							</div>
	
							
							<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
								ajax({
									url:"add_etap_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										name:document.getElementById("name").value,
										client:'.$_GET['client'].',
										
										session_id:'.$_SESSION['id'].',
									},
									success:function(data){document.getElementById("status").innerHTML=data;}
								})\'
							>
						</form>';
				echo '
				</div>';
				
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