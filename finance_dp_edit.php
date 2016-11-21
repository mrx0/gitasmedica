<?php

//finance_dp_edit.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$clientDP = SelDataFromDB('journal_debts_prepayments', $_GET['id'], 'id');
			
			if ($clientDP !=0){
				
				if ((time() < $clientDP[0]['create_time'] + 60*60*24) || ($finances['reopen'] == 1) || $god_mode){
				
					if ($clientDP[0]['type'] == 3){
						$descr = '<span style="color: green;">Аванс</span>';
						//$url = 'finance_prepayment.php';
					}
					if ($clientDP[0]['type'] == 4){
						$descr = '<span style="color: red">Долг</span>';
						//$url = 'finance_debt.php';
					}

								
					$bgColor = '';
					/*if ($clientDP[0]['date_expires'] - time() <= 60*60*24*3){
						$bgColor = 'background-color: rgba(254, 63, 63, 0.69);';
					}*/
					
					
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
								<h2>Редактировать '.$descr.' <a href="finance_dp.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
								<!--<a href="finance_edit_date.php?id='.$_GET['id'].'" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: #999; background-color: rgba(252, 252, 0, 0.3);">Изменить дату внесения</a>-->
							</header>';

						echo '
							<div id="data">';
						echo '
							<form action="finance_edit_f.php">
								
								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">
										'.WriteSearchUser('spr_clients', $clientDP[0]['client'], 'user_full', true).'
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
									<div class="cellRight">
										<input type="text" size="50" name="summ" id="summ" placeholder="0" value="'.$clientDP[0]['summ'].'" autocomplete="off">
										<label id="summ_error" class="error"></label>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Срок истечения</div>
									<div class="cellRight">
										<input type="text" id="dataend" name="dataend" class="dateс" value="'.date('d.m.Y', $clientDP[0]['date_expires']).'" onfocus="this.select();_Calendar.lcs(this)"
											onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
									</div>
								</div>';
								
										
						echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2">'.$clientDP[0]['comment'].'</textarea></div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Удалить</div>
									<div class="cellRight">
										<div style="float: right;" class="delFinanceItem"><img src="img/delete.png" title="Удалить"></div>
									</div>
								</div>
								
								';
				echo '
							</form>
							<br>';	
					
				echo '
						</div>';
								
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
						<br><br>';
									
					echo '	
							<div id="errror"></div>				
							<input type="button" class="b" value="Применить" onclick="Ajax_finance_debt_edit('.$_GET['id'].', '.$_SESSION['id'].')">
								</form>';	
							echo '
							
							</div>
						</div>';
										
							echo '
								<script type="text/javascript">
									$(document).ready(function(){
										$(\'.delFinanceItem\').on(\'click\', function(data){
											var rys = confirm("Вы хотите удалить платёж. \nЕго невозможно будет восстановить. \n\nВы уверены?");
											if (rys){
												var id = $(this).attr(\'clientid\');
												alert("Не активно");
												/*ajax({
													url: "del_FinanceItem_f.php",
													method: "POST",
													
													data:
													{
														id: '.$_GET['id'].'
													},
													success: function(req){
														//document.getElementById("request").innerHTML = req;
														alert(req);
														window.location.replace("finances.php");
													}
												})*/
											}
										})
									});
								</script>';
					}else{
						echo '<h1>Прошло более 24 часов с момента создания.</h1><a href="index.php">Вернуться на главную</a>';
					}
						
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>