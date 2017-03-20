<?php 

//serviceitem_edit_f.php
//Изменение

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
					if (isset($_POST['price']) && isset($_POST['price2']) && isset($_POST['price3']) && isset($_POST['id'])){
						if ((is_numeric($_POST['price'])) && (is_numeric($_POST['price'])) && (is_numeric($_POST['price']))){
							if (($_POST['price'] >= 0) && ($_POST['price2'] >= 0) && ($_POST['price3'] >= 0)){
								//var_dump(strtotime($_POST['iWantThisDate2']));
								//var_dump(date('d.m.y H:i', time()));
								//var_dump(date('d.m.y H:i', strtotime($_POST['iWantThisDate2'])));
								//$PriceName = WriteToDB_EditPriceName ($name, $_SESSION['id']);
							
								//операции со временем						
								$iWantThisDate2 = strtotime($_POST['iWantThisDate2']." 09:00:00");
								$_time = time();
								$start_day = mktime(9, 0, 0, date("m", $_time), date("d", $_time), date("y", $_time));
								
								//var_dump($start_day);
								//var_dump($iWantThisDate2);
								
								if ($iWantThisDate2 >= $start_day){
									WriteToDB_EditPricePrice ($_POST['id'], $_POST['price'], $_POST['price2'], $_POST['price3'], $iWantThisDate2, $_SESSION['id']);
								
									echo '
										<div class="query_ok">
											Цена изменена.<br><br>
										</div>';
								}else{
									echo '
										<div class="query_neok">
											Задним числом изменять нельзя.<br><br>
										</div>';
								}
							}else{
								echo '
									<div class="query_neok">
										Ошибка цены.<br><br>
									</div>';
							}
						}else{
							echo '
								<div class="query_neok">
									Ошибка цены.<br><br>
								</div>';
						}
					}else{
						echo '
							<div class="query_neok">
								Не указана цена.<br><br>
							</div>';
					}
		}
	}
?>