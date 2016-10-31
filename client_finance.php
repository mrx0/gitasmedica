<?php

//client_finance.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($finances['see_all'] == 1) || $god_mode){
				
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'widget_calendar.php';

				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
				
				if ($client != 0){
					echo '
						<header style="margin-bottom: 5px;">
							<h1><a href="client.php?id='.$client[0]['id'].'" class="ahref">'.$client[0]['full_name'].'</a></h1>
						</header>';
					echo '
						<div id="data" style="margin: 0;">
							<ul style="margin-left: 6px; margin-bottom: 20px;">';
					echo '
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
									<a href="finance_debt_add.php?client='.$client[0]['id'].'" class="b"><span style="color: red;"><i class="fa fa-rub"></i></span> Зафиксировать долг</a>
									<a href="finance_prepayment_add.php?client='.$client[0]['id'].'" class="b"><span style="color: green;"><i class="fa fa-rub"></i></span> Зафиксировать аванс</a>
								</li>
							</ul>';
							
					echo '
							<ul style="margin-left: 6px; margin-bottom: 20px; padding: 7px;">';
					
					//Долги/авансы					
					$clientDP = DebtsPrepayments ($client[0]['id']);
					
					if ($clientDP != 0){
						echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto; margin-bottom: 5px;">	
									Авансы / долги
								</li>';
						for ($i=0; $i<count($clientDP); $i++){
							$descr = '';
							if ($clientDP[$i]['type'] == 3){
								$descr = '<span style="color: green;">Аванс</span>';
							}
							if ($clientDP[$i]['type'] == 4){
								$descr = '<span style="color: red">Долг</span>';
							}
							
							$bgColor = '';
							if ($clientDP[$i]['date_expires'] - time() <= 60*60*24*3){
								$bgColor = 'background-color: red;';
							}
							
							echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">	
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $clientDP[$i]['create_time']).'</div>
									<div class="cellTime" style="text-align: center">'.$descr.'</div>
									<div class="cellName" style="text-align: right;">'.$clientDP[$i]['summ'].' руб.</div>
									<div class="cellName" style="text-align: right; '.$bgColor.'">до '.date('d.m.y', $clientDP[$i]['date_expires']).'</div>
									<div class="cellText" style="text-align: right; max-width: 250px;">'.$clientDP[$i]['comment'].'</div>
								</li>';
				
							echo '
								</li>';
						}
					}else{
						echo '
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
									Нет авансов и долгов.
								</li>
						';
						
					}
					
					echo '
							</ul>';
					echo '
						</div>';
				}else{
					echo '<h1>Что-то пошло не так</h1>';
				}
			}else{
				echo '<h1>Не хватает прав доступа.</h1>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>