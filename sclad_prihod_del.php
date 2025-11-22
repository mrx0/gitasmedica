<?php

//sclad_prihod_del.php
//Удаление(блокирование) накладной

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
                $prihod_j = SelDataFromDB('sclad_prihod', $_GET['id'], 'id');
				//var_dump($prihod_j);
				
				if ($prihod_j !=0){
					echo '
						<div id="status">
							<header>
                                    <div class="nav">
                                        <a href="sclad.php" class="b">Склад</a>
                                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                                        <a href="test_parsing.php" class="b">Сканировать накладную</a>
                                    </div>
								<h2>Удалить(заблокировать) накладную <a href="sclad_prihod.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>';
                    if ($prihod_j[0]['status'] == 7){
                        echo '<i style="color:#ff0000;">Накладная проведена. Удалять нельзя.</i><br>';
                    }
                    echo '
							</header>';

					echo '
							<div id="data">';
					echo '
							<div id="errrror"></div>';
                    echo '
							<div id="data">';

                    echo '
                                <div class="cellsBlock2" style="margin-bottom: 10px;">
                                    <span style="font-size:90%;">
                                        <i>Поставщик: <b>'.$prihod_j[0]['provider_name'].'</b></i><br>
                                        <i>№ / дата документа поставщика: <b>'.$prihod_j[0]['prov_doc'].'</b></i><br>
                                    </span>
                                </div>';
                    echo '
                                <div class="cellsBlock2" style="margin-bottom: 10px;">
                                    <span style="font-size:80%;  color: #555;">';

                    if (($prihod_j[0]['create_time'] != 0) || ($prihod_j[0]['create_person'] != 0)){
                        echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($prihod_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $prihod_j[0]['create_person'], 'user', true).'<br>';
                    }else{
                        echo 'Добавлен: не указано<br>';
                    }
                    if (($prihod_j[0]['last_edit_time'] != 0) || ($prihod_j[0]['last_edit_person'] != 0)){
                        echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($prihod_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $prihod_j[0]['last_edit_person'], 'user', true).'';
                    }
                    echo '
                                    </span>
                                </div>';
                    
                    if (($prihod_j[0]['status'] != 9) && ($prihod_j[0]['status'] != 7)){
                        echo '				
									<input type="hidden" id="id" name="id" value="' . $_GET['id'] . '">
									<div id="errror"></div>
									<input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_prihod(' . $_GET['id'] . ')">';
                    }
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>