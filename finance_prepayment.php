<?php

//finance_prepayment.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$finance_j = SelDataFromDB('journal_finance', $_GET['id'], 'id');
			//var_dump($finance_j);
			
			if ($finance_j != 0){
				echo '
					<div id="status">
						<header>
							<h2>Платёж #'.$finance_j[0]['id'].'</h2>
							<a href="finance_remove.php?id='.$_GET['id'].'" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: #999; background-color: rgba(252, 252, 0, 0.3);">Перенести средства</a>
						</header>';
				if (($finance['see_all'] == 1) || $god_mode){
					
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
					if ($finance_j[0]['type'] == 2){
						$backSummColor = "background-color: rgba(0, 201, 255, 0.5)";
					}
					
					echo '
						<div id="data">';
					echo '

							<div class="cellsBlock2">
								<div class="cellLeft">Пациент</div>
								<div class="cellRight">
									'.WriteSearchUser('spr_clients', $finance_j[0]['client'], 'user_full', true).'
								</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight" style="font-weight: bold; text-align: center; '.$backSummColor.'">'.$finance_j[0]['summ'].'</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Месяц</div>
								<div class="cellRight" style="text-align: right;">'.$monthsName[$finance_j[0]['month']].'</div>
							</div>	
							
							<div class="cellsBlock2">
								<div class="cellLeft">Год</div>
								<div class="cellRight" style="text-align: right;">'.$finance_j[0]['year'].'</div>
							</div>
							
							
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight" style="text-align: right;">';
					$filials = SelDataFromDB('spr_filials', $finance_j[0]['filial'], 'offices');
					if ($filials != 0){
						echo '<a href="filial.php?id='.$filials[0]['id'].'" class="ahref">'.$filials[0]['name'].'</a>';	
					}else{
						echo 'Не указан филиал';
					}
					echo '		
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight">'.$finance_j[0]['comment'].'</div>
							</div>
							
							<br>';
							
					echo '
						<span style="font-size: 80%; color: #999;">
							Создан '.date('d.m.y H:i', $finance_j[0]['create_time']).' пользователем 
							'.WriteSearchUser('spr_workers', $finance_j[0]['create_person'], 'user', true).'
						</span>';
						
					if ($finance_j[0]['last_edit_time'] != 0){
						echo '
						<br>
						<span style="font-size: 80%; color: #999;">
							Редактировался '.date('d.m.y H:i', $finance_j[0]['last_edit_time']).' пользователем 
							'.WriteSearchUser('spr_workers', $finance_j[0]['last_edit_person'], 'user', true).'
						</span>';
					}
					
					if (($finance['edit'] == 1) || $god_mode){
						echo '
								<br><br>
								<a href="finance_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
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