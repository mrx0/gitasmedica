<?php

//sclad_item.php
//карточка позиции на складе

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			if ($_GET){

                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

				include_once 'DBWork.php';
				include_once 'functions.php';

                require 'variables.php';

                $filials_j = getAllFilials(true, false, false);
                //var_dump($filials_j);

				//$sclad_item_j = SelDataFromDB('spr_sclad_items', $_GET['id'], 'id');
				//var_dump($sclad_item_j);

                //$msql_cnnct = ConnectToDB ();
                $db = new DB();

                $query = "SELECT ssi.*, scc.name AS cat_name
                FROM `spr_sclad_items` ssi
                RIGHT JOIN `spr_sclad_category` scc
                ON scc.id = ssi.parent_id
                WHERE ssi.id = :id LIMIT 1
                ";

                $args = [
                    'id' => $_GET['id']
                ];

                //Выбрать все категории
                $sclad_item_j = $db::getRows($query, $args);
                //var_dump($sclad_item_j);

				if (!empty($sclad_item_j)){

					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="sclad.php" class="b">Склад</a>
                                </div>
								<h2>
									Карточка складской позиции #'.$sclad_item_j[0]['id'].' ';
					
					/*if (($items['edit'] == 1) || $god_mode){
						if ($sclad_item_j[0]['status'] != 9){
							echo '
										<a href="abonement_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($sclad_item_j[0]['status'] == 9) && (($items['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_abonement('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($items['close'] == 1) || $god_mode){
						if ($sclad_item_j[0]['status'] != 9){
							echo '
										<a href="abonement_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}*/

					echo '
								</h2>
								<div id="errror"></div>';
								
/*					if ($sclad_item_j[0]['status'] == 9){
						echo '<i style="color:red;">Позиция удалена (заблокирована).</i><br>';
					}*/

                    /*echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                    if (($sclad_item_j[0]['create_time'] != 0) || ($sclad_item_j[0]['create_person'] != 0)){
                        echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($sclad_item_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $sclad_item_j[0]['create_person'], 'user', true).'<br>';
                    }else{
                        echo 'Добавлен: не указано<br>';
                    }
                    if (($sclad_item_j[0]['last_edit_time'] != 0) || ($sclad_item_j[0]['last_edit_person'] != 0)){
                        echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($sclad_item_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $sclad_item_j[0]['last_edit_person'], 'user', true).'';
                    }
                    echo '
											</span>
										</div>';*/

					echo '
							</header>';

					echo '
							<div id="data">';
					echo '
								<!--<div class="cellsBlock2">
									<div class="cellLeft">Категория</div>
									<div class="cellRight">'.$sclad_item_j[0]['cat_name'].'</div>
								</div>-->
									
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$sclad_item_j[0]['name'].'</div>
								</div>';




                    echo '    
                        </div>';




                    //Наличие на складах
                    $availability_j = array();

                    echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">
									    Наличие
                                    </li>';

                    if (!empty( $availability_j)){
//                        foreach ($last_prihod_j as $items) {
//
//                            echo '
//                                    <li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36);">';
//                            echo '
//                                        <a href="sclad_prihod.php?id=' . $items['prihod_id'] . '" class="cellOrder ahref" style="position: relative;">
//                                            <div style="font-weight: bold;">
//                                                Приход #' . $items['prihod_id'] . '
//                                            </div>
//                                            <div style="margin: 3px;">';
//                            echo '
//                                            </div>
//                                            <div style="font-size:80%; color: #555; border-top: 1px dashed rgb(179, 179, 179); margin-top: 5px;">';
//
//
//                            echo '
//                                            </div>';
//
//                            echo '
//                                                        <div style="font-size: 100%; color: #171919;">
//                                                            <i>' . $items['provider_name'] . '</i>
//                                                        </div>';
//                            echo '
//                                                    </a>';
//
//                            echo '
//													<div class="cellName">
//														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//															Количество:<br>
//															<span class="calculateOrder" style="font-size: 13px; color: black">' . $items['quantity'] . '</span> '.$units[$sclad_item_j[0]['unit']].'
//														</div>';
//                            echo '
//													</div>';
//
//                            echo '
//													<div class="cellName">
//														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//															Филиал / дата:<br>
//															<b>' . $filials_j[$items['filial_id']]['name2'] . '</b> / '.date('d.m.Y' ,strtotime($items['prihod_time'])).'
//														</div>';
//                            echo '
//													</div>
//												</li>';
//                        }

                    }else{
                        echo '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Ничего не найдено</i>';
                    }


                    echo '
								</ul>';




                    //Последние приходы (проведённые)
                    $query = "SELECT sp_ex.*, sp.provider_id, sp.provider_name, sp.prihod_time, sp.filial_id
                    FROM `sclad_prihod_ex` sp_ex
                    LEFT JOIN `sclad_prihod` sp
                    ON sp.id = sp_ex.prihod_id
                    WHERE sp_ex.sclad_item_id = :id
                    AND sp.status = '7'
                    LIMIT 10
                    ";

                    $args = [
                        'id' => $_GET['id']
                    ];

                    $last_prihod_j = $db::getRows($query, $args);
                    //var_dump($last_prihod_j);


                    echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">
									    Последние приходы
                                    </li>';

                    if (!empty($last_prihod_j)){
                        foreach ($last_prihod_j as $items) {

                            echo '
												<li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36);">';
                            echo '
													<a href="sclad_prihod.php?id=' . $items['prihod_id'] . '" class="cellOrder ahref" style="position: relative;">
														<div style="font-weight: bold;">
															Приход #' . $items['prihod_id'] . '
														</div>
														<div style="margin: 3px;">';
                            echo '
														</div>
														<div style="font-size:80%; color: #555; border-top: 1px dashed rgb(179, 179, 179); margin-top: 5px;">';

//                            echo '
//                                                            <div style="font-size: 100%; color: #171919;">
//                                                                '.date('d.m.Y' ,strtotime($items['prihod_time'])).'
//                                                            </div>';
//                            echo '
//                                                            <div style="font-size: 98%; color: #171919;">
//                                                                <b>' . $filials_j[$items['filial_id']]['name2'] . '</b> / '.date('d.m.Y' ,strtotime($items['prihod_time'])).'
//                                                            </div>';

//                            if (($items['create_time'] != 0) || ($items['create_person'] != 0)) {
//                                echo '
//																Добавлен: ' . date('d.m.y H:i', strtotime($items['create_time'])) . '<br>
//																<!--Автор: ' . WriteSearchUser('spr_workers', $items['create_person'], 'user', true) . '<br>-->';
//                            } else {
//                                echo 'Добавлен: не указано<br>';
//                            }
//                            if (($items['last_edit_time'] != 0) || ($items['last_edit_person'] != 0)) {
//                                echo '
//																Редактировался: ' . date('d.m.y H:i', strtotime($items['last_edit_time'])) . '<br>
//																<!--Кем: ' . WriteSearchUser('spr_workers', $items['last_edit_person'], 'user', true) . '-->';
//                            }

                            echo '
														</div>';


                            //Цвет если оплачено или нет
//                            $paycolor = "color: red;";
//                            if ($items['summ'] == $items['paid']) {
//                                $paycolor = 'color: #333333;';
//                            }

//                            echo '
//														<span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . ' ' . $status_mark . ' ' . $calculate_mark . '</span>';
                            echo '
                                                        <div style="font-size: 100%; color: #171919;">
                                                            <i>' . $items['provider_name'] . '</i>
                                                        </div>';
                            echo '
                                                    </a>';

                            echo '
													<div class="cellName">
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Количество:<br>
															<span class="calculateOrder" style="font-size: 13px; color: black">' . $items['quantity'] . '</span> '.$units[$sclad_item_j[0]['unit']].'
														</div>';
                            echo '
													</div>';

                            echo '
													<div class="cellName">
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Филиал / дата:<br>
															<b>' . $filials_j[$items['filial_id']]['name2'] . '</b> / '.date('d.m.Y' ,strtotime($items['prihod_time'])).'
														</div>';
                            echo '
													</div>
												</li>';
                        }
                        
                        
                        
                    }else{
                        echo '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Ничего не найдено</i>';
                    }


                    echo '
								</ul>';



                    //Последние перемещения
                    $last_move_j = array();

                    echo '
								<ul id="orders" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
									    Последние перемещения
									</li>';
                    if (!empty($last_move_j)){

                    }else{
                        echo '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Ничего не найдено</i>';
                    }

                    echo '
								</ul>';



                    echo '
                                <div id="doc_title">Карточка складской позиции #'.$sclad_item_j[0]['id'].'</div>';
                    
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