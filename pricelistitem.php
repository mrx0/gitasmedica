<?php

//pricelistitem.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$rezult = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
			//var_dump($rezult);
			
			$price = 0;
			
			if ($rezult != 0){

                $msql_cnnct = ConnectToDB ();
			
				$arr = array();
				$rez = array();
				$price = 0;
				$price2 = 0;
				$price3 = 0;

				//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC LIMIT 1";
				$query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					$arr = mysqli_fetch_assoc($res);
					$price = $arr['price'];
					$price2 = $arr['price2'];
					$price3 = $arr['price3'];
				}else{
					$price = 0;
					$price2 = 0;
					$price3 = 0;
				}

				//mysql_close();
				
				echo '
					<div id="status">
						<header>
							<div class="nav">
								<a href="pricelist.php" class="b">Основной прайс</a>
							</div>
							<h2>Карточка позиции';
							
				/*if (($items['edit'] == 1) || $god_mode){
					echo '
								<a href="pricelistitem_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
				}*/
				
				if (($items['edit'] == 1) || $god_mode){
					if ($rezult[0]['status'] != 9){
						echo '
									<a href="pricelistitem_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					if ($rezult[0]['status'] == 9){
						echo '
							<a href="#" onclick="Ajax_reopen_pricelistitem('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
					}
				}
				if (($items['close'] == 1) || $god_mode){
					if ($rezult[0]['status'] != 9){
						echo '
									<a href="pricelistitem_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
					}
				}
				
				echo '
							</h2>';
							
				if ($rezult[0]['status'] == 9){
					echo '<i style="color:red;">Позиция удалена (заблокирована).</i><br>';												
				}
				
				echo '
						</header>';
						
				echo '
						<div id="data">';

				echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Код</div>
								<div class="cellRight">'.$rezult[0]['code'].'</div>
							</div>';

				echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Название</div>
								<div class="cellRight">'.$rezult[0]['name'].'</div>
							</div>';
                echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Категория</div>
								<div class="cellRight">'.WriteSearchUser('fl_spr_percents', $rezult[0]['category'], 'id', false).'</div>
							</div>';
				echo '
							<div class="cellsBlock2">
								<div class="cellLeft" style="font-size: 90%;">
								    Цена/ цена 2/ цена 3
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">
                                        <li style="width: auto; color:#777; font-size: 80%;">
                                            Цены 2 и 3 указываются явно для тех позиций, для которых цены фиксированы
                                        </li>
                                    </ul>
								</div>
								<div class="cellRight">'.$price.' | '.$price2.' | '.$price3.'   руб. ';
				if (($items['edit'] == 1) || $god_mode){
					if ($rezult[0]['status'] != 9){
						echo '
									<a href="priceprice_edit.php?id='.$_GET['id'].'" class="info b2" style="font-size: 100%;" title="Редактировать цену"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
				}
				echo '
								</div>
							</div>';

				//В других прайсах

                $arr = array();
                $rez = array();

                $insure_price_arr = array();

                $query = "SELECT * FROM `spr_pricelists_insure` WHERE `item`= '".$_GET['id']."'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $insure_price_arr[$arr['insure']] = $arr;
                    }
                }else{
                    $rez = 0;
                }
                //var_dump($insure_price_arr);

                echo '
							<ul style="margin-bottom: 10px; margin-top: 20px;">
								<li style="width: auto; color:#777; font-size: 90%;">
									Эта позиция в других прайсах
								</li>
							</ul>
							<div style="margin-bottom: 20px;">
								<div class="cellsBlock">';

                $insure_j = SelDataFromDB('spr_insure', '', '');

                if ($insure_j != 0){
                    foreach ($insure_j as $insure_item){
                        //var_dump($insure_item);
                        echo '
                            <li class="cellsBlock" style="width: auto;">
                                <div class="cellOrder" style="position: relative;">
                                    <a href="insure.php?id='.$insure_item['id'].'" class="ahref">'.$insure_item['name'].'</a>
                                </div>';
                        if (isset($insure_price_arr[$insure_item['id']])){
                            if (($items['edit'] == 1) || $god_mode){
                                 echo '
                                <div class="cellName" style="background: rgb(157,255,134);">
                                    <a href="pricelistitem_insure.php?insure='.$insure_item['id'].'&id='.$_GET['id'].'" class="ahref">открыть в страховой</a>
                                </div>';
                            }else{
                                echo '
                                <div class="cellName" style="background: rgb(157,255,134);">
                                    
                                </div>';
                            }
                        }else{
                            if ($rezult[0]['status'] != 9){
                                echo '
                                    <div class="cellName" style="cursor: pointer; background: rgba(255,132,113,0.73);" onclick="Ajax_add_pricelistitem_insure('.$_GET['id'].', '.$insure_item['id'].');">
                                        добавить в эту страховую
                                    </div>';
                            }else{
                                echo '
                                    <div class="cellName" style="cursor: pointer; background: rgba(255,132,113,0.73);">
                                        нет в этой страховой
                                    </div>';
                            }
                        }
                        echo '           
                            </li>   
                        ';
                    }
                }
                echo '
								</div>
							</div>
							';



				echo '
							<div class="cellsBlock2">
								<span style="font-size:80%;">';
									
				if (($rezult[0]['create_time'] != 0) || ($rezult[0]['create_person'] != 0)){
					echo '
										Добавлен: '.date('d.m.y H:i', $rezult[0]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $rezult[0]['create_person'], 'user', true).'<br>';
				}else{
					echo 'Добавлен: не указано<br>';
				}
				if (($rezult[0]['last_edit_time'] != 0) || ($rezult[0]['last_edit_person'] != 0)){
					echo '
										Последний раз редактировался: '.date('d.m.y H:i', $rezult[0]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $rezult[0]['last_edit_person'], 'user', true).'';
				}
				echo '
								</span>
							</div>';
					
				$arr = array();
				$rez = array();
					

				$query = "SELECT * FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC";
				//$query = "SELECT * FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					while ($arr = mysqli_fetch_assoc($res)){
						array_push($rez, $arr);
					}
				}else{
					$rez = 0;
				}

                CloseDB ($msql_cnnct);
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
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>