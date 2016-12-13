<?php 

// !!!!!! Доделать !!! add_priceitem_f.php
//Функция для добавления новой услуги

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['pricename'] == ''){
				echo '
					<div class="query_neok">
						Что-то не заполнено.<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';

				//$name = trim($_POST['pricename']);
				
				$name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricename']))));
				
				//Проверяем есть ли такая услуга
				$rezult = SelDataFromDB('spr_pricelist', $name, 'name');
				//var_dump($rezult);
				
				if ($rezult == 0){
					if (isset($_POST['price'])){
						if (is_numeric($_POST['price'])){
							if ($_POST['price'] > 0){
								$PriceName = WriteToDB_EditPriceName ($name, $_SESSION['id']);
								WriteToDB_EditPricePrice ($PriceName, $_POST['price'], $_SESSION['id']);
								echo '
									<div class="query_ok">
										Услуга добавлена в базу.<br><br>
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
				}else{
					echo '
						<div class="query_neok">
							Такая услуга уже есть.<br><br>
						</div>';
				}

			}
		}
	}
?>