<?php

//finance_dp.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$clientDP = SelDataFromDB('journal_debts_prepayments', $_GET['id'], 'id');
			//var_dump($clientDP);
			
			if ($clientDP != 0){
				
				if ($clientDP[0]['type'] == 3){
					$descr = '<span style="color: green;">Аванс</span>';
					//$url = 'finance_prepayment.php';
				}
				if ($clientDP[0]['type'] == 4){
					$descr = '<span style="color: red">Долг</span>';
					//$url = 'finance_debt.php';
				}

							
				$bgColor = '';
				if ($clientDP[0]['date_expires'] - time() <= 60*60*24*3){
					$bgColor = 'background-color: rgba(254, 63, 63, 0.69);';
				}
				
				echo '
					<div id="status">
						<header>
							<h2>'.$descr.' #'.$clientDP[0]['id'].'';
				if (($finances['edit'] == 1) || $god_mode){
					echo '
						<a href="finance_dp_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
				}
				echo '
							</h2>	
						</header>';
				if (($finances['see_all'] == 1) || $god_mode){
					
					//Массив с месяцами
					$monthsName = array(
					'01' => 'Январь',
					'02' => 'Февраль',
					'03' => 'Март',
					'04' => 'Апрель',
					'05' => 'Май',
					'06' => 'Июнь',
					'07'=> 'Июль',
					'08' => 'Август',
					'09' => 'Сентябрь',
					'10' => 'Октябрь',
					'11' => 'Ноябрь',
					'12' => 'Декабрь'
					);
					
					$backSummColor = '';
					if ($clientDP[0]['type'] == 2){
						$backSummColor = "background-color: rgba(0, 201, 255, 0.5)";
					}
					
					echo '
						<div id="data">';
					echo '

							<div class="cellsBlock2">
								<div class="cellLeft">Пациент</div>
								<div class="cellRight">
									'.WriteSearchUser('spr_clients', $clientDP[0]['client'], 'user_full', true).'
								</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight" style="font-weight: bold; text-align: center; '.$backSummColor.'">'.$clientDP[0]['summ'].'</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Дата истечения</div>
								<div class="cellRight" style="text-align: right; '.$bgColor.'">'.date('d.m.y', $clientDP[0]['date_expires']).'</div>
							</div>	
							
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight">'.$clientDP[0]['comment'].'</div>
							</div>
							
							<br>';
							
					echo '
						<span style="font-size: 80%; color: #999;">
							Создан '.date('d.m.y H:i', $clientDP[0]['create_time']).' пользователем 
							'.WriteSearchUser('spr_workers', $clientDP[0]['create_person'], 'user', true).'
						</span>';
						
					if ($clientDP[0]['last_edit_time'] != 0){
						echo '
						<br>
						<span style="font-size: 80%; color: #999;">
							Редактировался '.date('d.m.y H:i', $clientDP[0]['last_edit_time']).' пользователем 
							'.WriteSearchUser('spr_workers', $clientDP[0]['last_edit_person'], 'user', true).'
						</span>';
					}
					
					echo '
					</div>';
				}else{
					echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';
?>