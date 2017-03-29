<?php

//finance_dp_repayment_add.php
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
					
					if ($clientDP[0]['type'] == 3){
						$descr = '<span style="color: green;">Аванс</span>';
						//$url = 'finance_prepayment.php';
					}
					if ($clientDP[0]['type'] == 4){
						$descr = '<span style="color: red">Долг</span>';
						//$url = 'finance_debt.php';
					}
					
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
								<h2>Погашение '.$descr.' <a href="finance_dp.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
								Сумма '.$clientDP[0]['summ'].'
							</header>					
					
							<div id="data">';

					echo '
						<form action="add_finance_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">
									'.WriteSearchUser('spr_clients', $clientDP[0]['client'], 'user_full', true).'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма погашения <i class="fa fa-rub"></i></div>
								<div class="cellRight">
									<input type="text" size="50" name="summ" id="summ" placeholder="0" value="" autocomplete="off">
									<input type="hidden" name="type" id="type" value="4" autocomplete="off">
									<label id="summ_error" class="error"></label>
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Срок истечения</div>
								<div class="cellRight">
									'.date('d.m.Y', $clientDP[0]['date_expires']).'
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2"></textarea></div>
							</div>
							';
					
			echo '
							<div id="errror" style="margin: 10px;"></div>
							<input type="button" class="b" value="Применить" onclick="Ajax_finance_dp_repayment_add('.$_GET['id'].')">
						</form>';	
				
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