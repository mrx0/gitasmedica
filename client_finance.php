<?php

//client_finance.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
				
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
                            <div>
                                <div style="display: inline-block; color: red;">Запрещено создавать новые долги и авансы через данное меню. Необходимо закрыть все уже созданные.</div>
                            </div>
							<ul style="margin-left: 6px; margin-bottom: 20px;">';
					echo '
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
									<!--<a href="finance_debt_add.php?client='.$client[0]['id'].'" class="b"><span style="color: red;"><i class="fa fa-rub"></i></span> Зафиксировать долг</a>
									<a href="finance_prepayment_add.php?client='.$client[0]['id'].'" class="b"><span style="color: green;"><i class="fa fa-rub"></i></span> Зафиксировать аванс</a>-->
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
							$allpayed = false;
							$descr = '';
							$url = 'finance_dp.php';
							$descrPayed = '';
							
							if ($clientDP[$i]['type'] == 3){
								$descr = '<span style="color: green;">Аванс</span>';
								//$url = 'finance_prepayment.php';
							}
							if ($clientDP[$i]['type'] == 4){
								$descr = '<span style="color: red">Долг</span>';
								//$url = 'finance_debt.php';
							}
							
							$repayments = Repayments($clientDP[$i]['id']);
							if ($repayments != 0){
								$ostatok = 0;
								$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto;">
										Погашения
									</li>';
								foreach($repayments as $value){
									$ostatok += $value['summ'];
									$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto; background-color: rgba(255, 255, 0, 0.3);">	
										<a href="finance_dp_repayment.php?id='.$value['id'].'" class="cellTime ahref" style="text-align: center; border: 0;">'.date('d.m.y H:i', $value['create_time']).'</a>
										<div class="cellName" style="text-align: right; border: 0;">'.$value['summ'].' руб.</div>
										<div class="cellText" style="text-align: right; border: 0; max-width: 250px;">'.$value['comment'].'</div>
									</li>';
								}
								$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto; margin-bottom: 10px;  margin-top: 2px; background-color: rgba(6, 255, 0, 0.3);">
										<div class="cellText" style="text-align: left; max-width: 250px;">
											Остаток: '.($clientDP[$i]['summ'] - $ostatok).' руб. ';
								if ($clientDP[$i]['summ'] - $ostatok == 0){
									$descrPayed .= '- <i>ЗАКРЫТО</i>';
									$allpayed = true;
								}
								$descrPayed .= '
										</div>
									</li>';
							}
							
							$bgColor = '';
							if ($clientDP[$i]['date_expires'] - time() <= 60*60*24*3){
								$bgColor = 'background-color: rgba(254, 63, 63, 0.69);';
							}
							if ($allpayed){
								$bgColor = 'background-color: rgba(101, 254, 63, 0.7);';
							}								
							
							echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">	
									<div class="cellPriority" style="text-align: center"></div>
									<a href="'.$url.'?id='.$clientDP[$i]['id'].'" class="cellTime ahref" style="text-align: center">'.date('d.m.y H:i', $clientDP[$i]['create_time']).'</a>
									<div class="cellTime" style="text-align: center">'.$descr.'</div>
									<div class="cellName" style="text-align: right;">'.$clientDP[$i]['summ'].' руб.</div>
									<div class="cellName" style="text-align: right; '.$bgColor.'">до '.date('d.m.y', $clientDP[$i]['date_expires']).'</div>
									<div class="cellText" style="text-align: right; max-width: 250px;">'.$clientDP[$i]['comment'].'</div>
								</li>';
								
							echo $descrPayed;
							/*echo '
								</li>';*/
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