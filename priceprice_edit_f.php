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
			
					if (isset($_POST['price']) && isset($_POST['id'])){
						if (is_numeric($_POST['price'])){
							if ($_POST['price'] > 0){
								//$PriceName = WriteToDB_EditPriceName ($name, $_SESSION['id']);
								WriteToDB_EditPricePrice ($_POST['id'], $_POST['price'], $_SESSION['id']);
								echo '
									<div class="query_ok">
										Цена изменена.<br><br>
									</div>';
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