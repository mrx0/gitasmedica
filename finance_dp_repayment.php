<?php

//finance_dp_repayment.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			
				$clientDP = SelDataFromDB('journal_debts_prepayments', $_GET['id'], 'id');
				
				//var_dump($user);
				if ($clientDP !=0){
					
					$year = date("Y");
					$month = date("m");
					
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
				
					echo '
						<div id="status">
							<header>
								<h2>Погашение <a href="finance_dp.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a>';
					if (($finances['edit'] == 1) || $god_mode){
						echo '
							<a href="finance_dp_repayment_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					echo '
								</h2>
								К документу <a href="finance_dp.php?id='.$clientDP[0]['parent'].'">#'.$clientDP[0]['parent'].'</a>
							</header>					
					
							<div id="data">';

					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">
										'.WriteSearchUser('spr_clients', $clientDP[0]['client'], 'user_full', true).'
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Сумма погашения <i class="fa fa-rub"></i></div>
									<div class="cellRight">
										'.$clientDP[0]['summ'].'
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Внесено</div>
									<div class="cellRight">
										'.date('d.m.Y H:i', $clientDP[0]['create_time']).'
									</div>
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
					</div>
				</div>';

				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>