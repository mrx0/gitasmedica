<?php

//order.php
//Приходный ордер

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            if ($_GET){
                if (isset($_GET['id'])){

                    $order_j = SelDataFromDB('journal_order', $_GET['id'], 'id');

                    if ($order_j != 0){
                       // var_dump($order_j);

                        $offices_j = SelDataFromDB('spr_filials', $order_j[0]['office_id'], 'offices');

                        echo '
                            <div id="status">
								<header>

									<h2>Ордер #'.$_GET['id'].' от '.date('d.m.y' ,strtotime($order_j[0]['date_in'])).'';
									
                        if (($finances['edit'] == 1) || $god_mode){
                            if ($order_j[0]['status'] != 9){
                                echo '
                                        <a href="edit_order.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                            }
                            if (($order_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                                echo '
                                        <a href="#" onclick="Ajax_reopen_order('.$_GET['id'].', '.$order_j[0]['client_id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                            }
                        }
						if (($finances['close'] == 1) || $god_mode){
                            if ($order_j[0]['status'] != 9){
                                echo '
                                        <a href="order_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                            }
                        }
					
					    echo '			
                                    </h2>';

                        $arrival_str = '<span style="color:forestgreen;">Приходный</span>';
                        if ($order_j[0]['arrival'] == 1){
                            $arrival_str = '<span style="color:red;">Расходный</span>';
                        }

                        echo '
                                    <div class="cellsBlock2" style="margin-bottom: 10px; font-size:90%; font-weight: bold;">
                                        '.$arrival_str.'
                                    </div>';
										
                        if ($order_j[0]['status'] == 9){
                            echo '<i style="color:red;">Ордер удалён (заблокирован).</i><br>';
                        }


						echo '
								</header>';

                        echo '
                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                        Контрагент: '.WriteSearchUser('spr_clients',  $order_j[0]['client_id'], 'user_full', true).'
                                    </li> 
                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                        Филиал: <span style="font-size: 105%; color: #333;">'.$offices_j[0]['name'].'</span>
                                    </li>
                                </ul>';


                        echo '
									<div class="cellsBlock2" style="margin-bottom: 10px;">
										<span style="font-size:80%;  color: #555;">';

                        if (($order_j[0]['create_time'] != 0) || ($order_j[0]['create_person'] != 0)){
                            echo '
											Добавлен: '.date('d.m.y H:i' ,strtotime($order_j[0]['create_time'])).'<br>
											Автор: '.WriteSearchUser('spr_workers', $order_j[0]['create_person'], 'user', true).'<br>';
                        }else{
                            echo 'Добавлен: не указано<br>';
                        }
                        if (($order_j[0]['last_edit_time'] != 0) || ($order_j[0]['last_edit_person'] != 0)){
                            echo '
											Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($order_j[0]['last_edit_time'])).'<br>
											Кем: '.WriteSearchUser('spr_workers', $order_j[0]['last_edit_person'], 'user', true).'';
                        }
                        echo '
										</span>
									</div>';


                            echo '
                                <div id="data">';


                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Сумма
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 110%; font-weight: bold;">
									        '.$order_j[0]['summ'].' руб.
									    </li>
							        </ul>
								</div>
							</div>';

                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Способ внесения
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">
                                            ', $order_j[0]['summ_type'] == 1 ? 'Наличный' : 'Безналичный' ,'
									    </li>
								    </ul>
								</div>
							</div>';

                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Комментарий
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">
                                            '.$order_j[0]['comment'].'
									    </li>
								    </ul>
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