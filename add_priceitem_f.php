<?php 

//add_priceitem_f.php
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
				$pricecode = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricecode']))));
				$pricecodemkb = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricecodemkb']))));
				$pricecode_u = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricecode_u']))));
				$pricecode_nom = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricecode_nom']))));

				//Проверяем есть ли такая услуга
				$rezult = SelDataFromDB('spr_pricelist_template', addslashes($name), 'name');
				//var_dump($rezult);
				
				if ($rezult == 0){
					if (isset($_POST['price'])){
						if (is_numeric($_POST['price'])){
							if ($_POST['price'] >= 0){
								
								//операции со временем						
								$iWantThisDate2 = strtotime($_POST['iWantThisDate2']." 09:00:00");
								$_time = time();
								$start_day = mktime(9, 0, 0, date("m", $_time), date("d", $_time), date("y", $_time));
								
								//if ($iWantThisDate2 >= $start_day){
								    //Добавим в базу и вернем id
									$PriceNameId = WriteToDB_EditPriceName (addslashes($name), $pricecode, $pricecodemkb, $pricecode_u, $pricecode_nom, $_POST['category_id'], $_SESSION['id']);

									WriteToDB_EditPricePrice ($PriceNameId, $_POST['price'], $_POST['price2'], $_POST['price3'], $iWantThisDate2, $_SESSION['id']);

									if ($_POST['group'] != 0){
										WriteToDB_UpdatePriceItemInGroup($PriceNameId, $_POST['group'], $_SESSION['id']);
									}
									echo '
										<div class="query_ok">
											Позиция добавлена.<br><br>
											<a href="pricelistitem.php?id='.$PriceNameId.'" class="b">Перейти к позиции</a> <a href="add_pricelist_item.php" class="b">Добавить ещё</a>
										</div>';
//								}else{
//									echo '
//										<div class="query_neok">
//											Задним числом добавлять нельзя.<br><br>
//										</div>';
//								}
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
							Такая позиция уже есть.<br><br>
						</div>';
				}

			}
		}
	}
?>