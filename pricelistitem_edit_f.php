<?php 

//pricelistitem_edit_f.php
//Изменение

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['pricelistitemname'] == ''){
				echo '
					<div class="query_neok">
						Что-то не заполнено.<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				//$name = trim($_POST['pricelistitemname']);
				
				$name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistitemname']))));
				$code = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistitemcode']))));
				$codemkb = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistitemcodemkb']))));
                $code_u = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistitemcode_u']))));
                $code_nom = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistitemcode_nom']))));

				//Проверяем есть ли такая услуга
				$rezult = SelDataFromDB('spr_pricelist_template', $name, 'name');
				//var_dump($rezult);

                //!!! 20220309 - Убрал проверку на одинаковые имена
				/*if (($rezult != 0) && ($rezult[0]['id'] != $_POST['id'])){
					echo '
						<div class="query_neok">
							Такая позиция уже есть.<br><br>
						</div>';
				}else{*/

					WriteToDB_UpdatePriceItem ($name, $code, $codemkb, $code_u, $code_nom, $_POST['category_id'], $_POST['id'], $_SESSION['id']);

					if (isset($_POST['group'])){
						if ($_POST['group'] != 0){
							WriteToDB_UpdatePriceItemInGroup($_POST['id'], $_POST['group'], $_SESSION['id']);
						}
					}
					echo '
						<div class="query_ok">
							Изменено.<br><br>
                            <a href="pricelistitem.php?id='.$_POST['id'].'" class="b">Перейти к позиции</a>
						</div>';
//				}
			}
		}
	}
?>