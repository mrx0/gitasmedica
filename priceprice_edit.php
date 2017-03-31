<?php

//serviceitem_edit.php
//Редактирование краточки товара

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				//операции со временем						
				$day = date('d');		
				$month = date('m');		
				$year = date('Y');
				
				$items_j = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
				//var_dump($items_j);
				
				if ($items_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="pricelist.php" class="b">Основной прайс</a>
                                </div>
								<h2>Изменить цену</h2>
                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                    <li style="width: auto; color:#777; font-size: 80%;">
                                        Цены 2 и 3 указываются явно для тех позиций, для которых цены фиксированы
                                    </li>
                                </ul>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errror"></div>';
					echo '
								<form action="priceprice_edit_f.php">
					
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<a href="pricelistitem.php?id='.$_GET['id'].'" class="ahref">'.$items_j[0]['name'].'</a>
										</div>
									</div>';
									
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
				
					$arr = array();
					$rez = array();
					$price = 0;
					$price2 = 0;
					$price3 = 0;

					//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC LIMIT 1";
					$query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";
											
					$res = mysql_query($query) or die($query);

					$number = mysql_num_rows($res);
					if ($number != 0){
						$arr = mysql_fetch_assoc($res);
						$price = $arr['price'];
						$price2 = $arr['price2'];
						$price3 = $arr['price3'];
					}else{
						$price = 0;
						$price2 = 0;
						$price3 = 0;
					}

					mysql_close();
					
					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Цена</div>
										<div class="cellRight">
											<input type="text" name="price" id="price" value="'.$price.'"  style="width: 50px;"> руб.
										</div>
									</div>';
									
					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Цена2</div>
										<div class="cellRight">
											<input type="text" name="price2" id="price2" value="'.$price2.'"  style="width: 50px;"> руб.
										</div>
									</div>';

					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Цена3</div>
										<div class="cellRight">
											<input type="text" name="price3" id="price3" value="'.$price3.'"  style="width: 50px;"> руб.
										</div>
									</div>';

					//Календарик
					echo '
	
								<div class="cellsBlock2">
									<div class="cellLeft">С какого числа:</div>
									<div class="cellRight">
										<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
										onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
									</div>
								</div>';
								
					echo '				
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_price('.$_GET['id'].', '.$_SESSION['id'].')">
									<label id="price_error" class="error"></label>
								</form>';

						
					echo '
								<div class="cellsBlock2">
									<span style="font-size:80%;">';
										
					if (($items_j[0]['create_time'] != 0) || ($items_j[0]['create_person'] != 0)){
						echo '
											Добавлен: '.date('d.m.y H:i', $items_j[0]['create_time']).'<br>
											Кем: '.WriteSearchUser('spr_workers', $items_j[0]['create_person'], 'user', true).'<br>';
					}else{
						echo 'Добавлен: не указано<br>';
					}
					if (($items_j[0]['last_edit_time'] != 0) || ($items_j[0]['last_edit_person'] != 0)){
						echo '
											Последний раз редактировался: '.date('d.m.y H:i', $items_j[0]['last_edit_time']).'<br>
											Кем: '.WriteSearchUser('spr_workers', $items_j[0]['last_edit_person'], 'user', true).'';
					}
					echo '
									</span>
								</div>';
						
					$arr = array();
					$rez = array();
						
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					$query = "SELECT * FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC";
										
					$res = mysql_query($query) or die($query);

					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
					}else{
						$rez = 0;
					}
					
					mysql_close();
					//var_dump($rez);				
					
					echo '
								<ul style="margin-bottom: 10px; margin-top: 20px;">
									<li style="width: auto; color:#777; font-size: 90%;">
										История изменений и применений цен
									</li>
								</ul>
								<div style="margin-bottom: 20px;">
									<div class="cellsBlock">';

                    if ($rez != 0){
                        for($i=0; $i < count($rez); $i++){
                            echo '
                            <div>';
                            if ((($items['close'] == 1) && ($finances['close'] == 1)) || $god_mode){
                                echo '
						        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer; color: red;"  title="Удалить"></i>';
                            }
                            echo '
                            '.$rez[$i]['price'].'/'.$rez[$i]['price2'].'/'.$rez[$i]['price3'].' руб. c '.date('d.m.y H:i', $rez[$i]['date_from']).' | '.date('d.m.y H:i', $rez[$i]['create_time']).'  |  '.WriteSearchUser('spr_workers', $rez[$i]['create_person'], 'user', true).'';
                            echo '
						    </div>';
                            //echo '<div>'.$rez[$i]['price'].' руб. |  '.date('d.m.y H:i', $rez[$i]['create_time']).'  |  '.WriteSearchUser('spr_workers', $rez[$i]['create_person'], 'user', true).'</div>';
                        }
                    }
					
					echo '
									</div>
								</div>';
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>