<?php

//fl_percent_cat.php
//карточка категории процентов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

                $filials_j = getAllFilials(true, false, false);
                //var_dump($filials_j);

				$percent_j = SelDataFromDB('fl_spr_percents', $_GET['id'], 'id');
				//var_dump($percent_j);
				
				if ($percent_j != 0){

                    $permission_j = SelDataFromDB('spr_permissions', $percent_j[0]['type'], 'id');
                    //var_dump($permissions_j);

					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="fl_percent_cats.php?who='.$percent_j[0]['type'].'" class="b">Категории процентов общие</a>
                                </div>
								<h2>
									Категория процентов '.$percent_j[0]['name'];

                    if (($finances['see_all'] == 1) || $god_mode){
						if ($percent_j[0]['status'] != 9){
							echo '
										<a href="fl_percent_cat_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
/*						if (($percent_j[0]['status'] == 9) && (($finances['see_all'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_percent_cat('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}*/
					}
/*                    if (($finances['see_all'] == 1) || $god_mode){
						if ($percent_j[0]['status'] != 9){
							echo '
										<a href="fl_percent_cat_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}*/

					echo '
								</h2>
								<div id="errror"></div>';
								
					if ($percent_j[0]['status'] == 9){
						echo '<i style="color:red;">Категория удалена (заблокирована).</i><br>';
					}

                    echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size: 80%;  color: #555;">';

                    if (($percent_j[0]['create_time'] != 0) || ($percent_j[0]['create_person'] != 0)){
                        echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($percent_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $percent_j[0]['create_person'], 'user', true).'<br>';
                    }else{
                        echo 'Добавлен: не указано<br>';
                    }
                    if (($percent_j[0]['last_edit_time'] != 0) || ($percent_j[0]['last_edit_person'] != 0)){
                        echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($percent_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $percent_j[0]['last_edit_person'], 'user', true).'';
                    }
                    echo '
											</span>
										</div>';

					echo '
							</header>';

					echo '
							<div id="data">';
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$percent_j[0]['name'].' <div style="float: right; width: 20px; height: 20px; background-color: rgb('.$percent_j[0]['color'].'); border: 1px solid grey;"></div></div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Процент за работу (общий)</div>
									<div class="cellRight">'.$percent_j[0]['work_percent'].'%</div>
								</div>
  								<div class="cellsBlock2">
									<div class="cellLeft">Процент за материал (общий)</div>
									<div class="cellRight">'.$percent_j[0]['material_percent'].'%</div>
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Спец. цена фиксированная</div>
									<div class="cellRight">'.$percent_j[0]['summ_special'].' руб.</div>
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Персонал (тип)</div>
									<div class="cellRight">'.$permission_j[0]['name'].'</div>
								</div>';

                    echo '
                    <div id="doc_title">Категория процентов #'.$percent_j[0]['id'].' - '.$percent_j[0]['name'].'</div>';
                    
					echo '			
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