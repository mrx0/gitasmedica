<?php

//pricelistitem_insure_del.php
//Удаление

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$pricelistitem_insure = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
				//var_dump($pricelistitem_insure);
			
				$insure_j = SelDataFromDB('spr_insure', $_GET['insure'], 'id');
			
				if ($insure_j != 0){
			
					if ($pricelistitem_insure !=0){
						echo '
							<div id="status">
								<header>
                                    <div class="nav">
                                        <a href=insure_price.php?id='.$_GET['insure'].'" class="b">Прайс компании</a>
                                        <a href="pricelist.php" class="b">Основной прайс</a>
                                        <a href="pricelistitem.php?id='.$_GET['id'].'" class="b">Эта позиция в основном прайсе</a>
                                    </div>
									<h2>Удалить позицию из прайса страховой</h2>
									<a href="insure.php?id='.$_GET['insure'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a>
								</header>
								<a href="insure_price.php?id='.$_GET['insure'].'" class="b">В прайс</a><br>';

						echo '
								<div id="data">';
						echo '
								<div id="errrror"></div>';
						echo '
									<div style="font-size: 85%; color: #FF0202; margin: 15px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i>
										Позиция будет полностью удалена из прайса страховой,<br>
										но останется в основном прайсе.<br>
										При необходимости позицию можно будет<br>
										добавить повторно.<br><br>
									</div>';
						echo '
						
						
									<form action="pricelistitem_del_f.php">
										<div class="cellsBlock2" style="">
											<div class="cellLeft">
												Название
											</div>
											<div class="cellRight">
												<a href="pricelistitem_insure.php?insure='.$_GET['insure'].'&id='.$_GET['id'].'" class="ahref">'.$pricelistitem_insure[0]['name'].'</a>
											</div>
										</div>';
										
						echo '				
									<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
									<div id="errror"></div>
									<input type="button" class="b" value="Удалить" onclick="Ajax_del_pricelistitem_insure('.$_GET['id'].', '.$_GET['insure'].')">';



						echo '				
									</form>';	
						echo '
								</div>
							</div>';
					}else{
						echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
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