<?php

//lab_order.php
//Заказ в лабораторию

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){

			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            if ($_GET){
                if (isset($_GET['id'])){

                    $lab_order_j = SelDataFromDB('journal_laborder', $_GET['id'], 'id');
                    //var_dump( $lab_order_j);

                    $closed = FALSE;
                    $dop = array();

                    if ($lab_order_j !=0){

                        $offices_j = SelDataFromDB('spr_filials', $lab_order_j[0]['office_id'], 'offices');

                        $lab_order_ex_j = SelDataFromDB('journal_laborder_ex', $lab_order_j[0]['id'], 'laborder_id');
                        //var_dump( $lab_order_ex_j);

                        echo '
                            <div id="status">
								<header>

									<h2>Заказ в лабораторию #'.$_GET['id'].' от '.date('d.m.y' ,strtotime($lab_order_j[0]['create_time'])).'';

                        if (($finances['edit'] == 1) || $god_mode){
                            if ($lab_order_j[0]['status'] != 9){
                                echo '
                                        <a href="edit_lab_order.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                            }
                            if (($lab_order_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                                echo '
                                        <a href="#" onclick="Ajax_reopen_lab_order('.$_GET['id'].', '.$lab_order_j[0]['client_id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                            }
                        }
                        /*if (($finances['close'] == 1) || $god_mode){
                            if ($lab_order_j[0]['status'] != 9){
                                echo '
                                        <a href="lab_order_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                            }
                        }*/

                        echo '			
                                    </h2>';



                        if ($lab_order_j[0]['status'] == 9){
                            echo '<i style="color:red;">Заказ удалён (заблокирован).</i><br>';
                        }

                        echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                        if (($lab_order_j[0]['create_time'] != 0) || ($lab_order_j[0]['create_person'] != 0)){
                            echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($lab_order_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $lab_order_j[0]['create_person'], 'user', true).'<br>';
                        }else{
                            echo 'Добавлен: не указано<br>';
                        }
                        if (($lab_order_j[0]['last_edit_time'] != 0) || ($lab_order_j[0]['last_edit_person'] != 0)){
                            echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($lab_order_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $lab_order_j[0]['last_edit_person'], 'user', true).'';
                        }
                        echo '
											</span>
										</div>';

                        echo '
								</header>';

                        echo '
                                <div id="data">';

                        if ($lab_order_j[0]['status'] == 1) {
                            $back_color = 'background-color: rgba(119, 255, 135, 1);';
                            $mark_enter = 'закрыт';
                        } elseif ($lab_order_j[0]['status'] == 5) {
                            $back_color = 'background-color: rgba(183, 41, 240, 0.7);';
                            $mark_enter = 'отменён';
                        } elseif ($lab_order_j[0]['status'] == 6) {
                            $back_color = 'background-color: rgba(255, 102, 17, 0.7);';
                            $mark_enter = 'отправлен в лаб.';
                        } elseif ($lab_order_j[0]['status'] == 7) {
                            $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                            $mark_enter = 'пришел из лаб.';
                        } elseif ($lab_order_j[0]['status'] == 8) {
                            $back_color = 'background-color: rgba(137,0,81, .7);';
                            $mark_enter = 'удалено';
                        } else {
                            $back_color = 'background-color: rgba(255,255,0, .5);';
                            $mark_enter = 'создан';
                        }


                        echo '
                                    <div id="errror"></div>
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   Статус (нажмите, чтобы изменить)
                                                </li>
                                                <li class="cellsBlock" style="margin-bottom: 5px;">
                                                    <div id="lab_order_status" class="cellName ahref" style="text-align: center; '.$back_color.'">
										                '.$mark_enter.'
										            </div>
										            <input type="hidden" id="status_now" value="'.$lab_order_j[0]['status'].'">
										            <input type="hidden" id="lab_order_id" value="'.$lab_order_j[0]['id'].'">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>';

                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   Пациент
                                                </li>
                                                <li style="margin-bottom: 5px;">
                                                    '.WriteSearchUser('spr_clients', $lab_order_j[0]['client_id'], 'user_full', true).'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>';

                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   Врач
                                                </li>
                                                <li style="margin-bottom: 5px;">
                                                    '.WriteSearchUser('spr_workers', $lab_order_j[0]['worker_id'], 'user_full', true).'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>';

                        $labor_j = SelDataFromDB('spr_labor', $lab_order_j[0]['labor_id'], 'id');

                        echo '
                                    <div class="cellsBlock2"> 
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   Лаборатория
                                                </li>
                                                <li style="margin-bottom: 5px;">
                                                    <a href="labor.php?id='.$lab_order_j[0]['labor_id'].'" class="ahref">'.$labor_j[0]['name'].'</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>';

                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   Описание
                                                </li>
                                                <li style="margin-bottom: 5px;">
                                                    '.$lab_order_j[0]['descr'].'
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
                                                <li style="margin-bottom: 5px;">
                                                    '.$lab_order_j[0]['comment'].'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>';

                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                   История изменения статуса
                                                </li>

                                                <li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
                                                    <div class="cellName" style="text-align: center; background-color:#FEFEFE;">
                                                        '.date('d.m.y H:i' ,strtotime($lab_order_j[0]['create_time'])).'
                                                    </div>
                                                    <div class="cellName" style="text-align: center; background-color:#FEFEFE;">
                                                        '.WriteSearchUser('spr_workers', $lab_order_j[0]['create_person'], 'user', true).'<br>';
                        if ($lab_order_j[0]['office_id'] != 0){
                            echo $offices_j[0]['name'];
                        }else{
                            echo '-';
                        }
                        echo '
                                                    </div>
                                                    <div class="cellName" style="text-align: center; background-color: rgba(255,255,0, .5);">
                                                        создан
                                                    </div>
                                                </li>';
                        if ($lab_order_ex_j != 0){
                            for ($i=0; $i < count($lab_order_ex_j); $i++){

                                if ($lab_order_ex_j[$i]['status'] == 1) {
                                    $back_color = 'background-color: rgba(119, 255, 135, 1);';
                                    $mark_enter = 'закрыт';
                                } elseif ($lab_order_ex_j[$i]['status'] == 5) {
                                    $back_color = 'background-color: rgba(183, 41, 240, 0.7);';
                                    $mark_enter = 'отменён <span style="font-size: 120%; "><i class="fa fa-times" aria-hidden="true"></i></span>';
                                }  elseif ($lab_order_ex_j[$i]['status'] == 6) {
                                    $back_color = 'background-color: rgba(255, 102, 17, 0.7);';
                                    $mark_enter = 'отправлен в лаб.  <span style="font-size: 120%; "><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span>';
                                } elseif ($lab_order_ex_j[$i]['status'] == 7) {
                                    $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                                    $mark_enter = '<span style="font-size: 120%; "><i class="fa fa-long-arrow-left" aria-hidden="true"></i></span>  пришел из лаб.';
                                } elseif ($lab_order_ex_j[$i]['status'] == 8) {
                                    $back_color = 'background-color: rgba(137,0,81, .7);';
                                    $mark_enter = 'удалено';
                                } else {
                                    $back_color = 'background-color: rgba(255,255,0, .5);';
                                    $mark_enter = 'создан';
                                }

                                echo '
                                                <li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
                                                    <div class="cellName" style="text-align: center; background-color:#FEFEFE;">
                                                        '.date('d.m.y H:i' ,strtotime($lab_order_ex_j[$i]['create_time'])).'
                                                    </div>
                                                    <div class="cellName" style="text-align: center; background-color:#FEFEFE;">
                                                        '.WriteSearchUser('spr_workers', $lab_order_ex_j[$i]['create_person'], 'user', true).'<br>';
                                if ($lab_order_ex_j[$i]['office_id'] != 0){
                                    $offices_j = SelDataFromDB('spr_filials', $lab_order_ex_j[$i]['office_id'], 'offices');
                                    if ($offices_j != 0) {
                                        echo $offices_j[0]['name'];
                                    }
                                }else{
                                    echo '-';
                                }
                                echo '
                                                    </div>
                                                    <div class="cellName" style="text-align: center; '.$back_color  .'">
                                                        '.$mark_enter.'
                                                    </div>
                                                </li>';
                            }
                        }


                        echo '
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