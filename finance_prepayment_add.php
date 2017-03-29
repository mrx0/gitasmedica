<?php

//finance_debt_add.php
//Добавить долг

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			
				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
			
				//var_dump($user);
				if ($client != 0){
					
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
								<h2>Зафиксировать аванс</h2>
								Заполните поля
							</header>					
					
							<div id="data">';

					echo '
						<form action="add_finance_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">
									<a href="client.php?id='.$client[0]['id'].'" class="ahref">'.$client[0]['full_name'].'</a>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight">
									<input type="text" size="50" name="summ" id="summ" placeholder="0" value="" autocomplete="off">
									<input type="hidden" name="type" id="type" value="3" autocomplete="off">
									<label id="summ_error" class="error"></label>
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Срок истечения</div>
								<div class="cellRight">
									<input type="text" id="dataend" name="dataend" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
										onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2"></textarea></div>
							</div>
							';
					
			echo '
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick="Ajax_finance_debt_add('.$client[0]['id'].', '.$_SESSION['id'].')">
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