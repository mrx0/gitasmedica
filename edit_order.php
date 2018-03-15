<?php

//edit_order.php
//Редактируем ордер

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		if (($finances['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
			
				require 'variables.php';
			
				require 'config.php';

				if (isset($_GET['id'])){
					
					$order_j = SelDataFromDB('journal_order', $_GET['id'], 'id');
					//var_dump($order_j);
					
					if ($order_j != 0){

                        $day = date('d', strtotime($order_j[0]['date_in']));
                        $month = date('m', strtotime($order_j[0]['date_in']));
                        $year = date('Y', strtotime($order_j[0]['date_in']));

                        //var_dump (strtotime($order_j[0]['date_in']) + 12*60*60);
                        //var_dump (time());

                        //Если заднее число
                        if ((strtotime($order_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){
                            echo '<h1>Нельзя редактировать задним числом</h1>';
                        }else {

                            echo '
                                <div id="status">
                                    <header>
                                        <h2>Редактировать ордер <a href="order.php?id=' . $_GET['id'] . '" class="ahref">#' . $_GET['id'] . '</a></h2>
                                        <ul style="margin-left: 6px; margin-bottom: 10px;">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Контрагент: ' . WriteSearchUser('spr_clients', $order_j[0]['client_id'], 'user_full', true) . '
                                            </li>';
                            echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:80%;  color: #555;">';

                            if (($order_j[0]['create_time'] != 0) || ($order_j[0]['create_person'] != 0)) {
                                echo '
                                                    Добавлен: ' . date('d.m.y H:i', strtotime($order_j[0]['create_time'])) . '<br>
                                                    Автор: ' . WriteSearchUser('spr_workers', $order_j[0]['create_person'], 'user', true) . '<br>';
                            } else {
                                echo 'Добавлен: не указано<br>';
                            }
                            if (($order_j[0]['last_edit_time'] != 0) || ($order_j[0]['last_edit_person'] != 0)) {
                                echo '
                                                    Последний раз редактировался: ' . date('d.m.y H:i', strtotime($order_j[0]['last_edit_time'])) . '<br>
                                                    Кем: ' . WriteSearchUser('spr_workers', $order_j[0]['last_edit_person'], 'user', true) . '';
                            }
                            echo '
                                                </span>
                                            </div>';

                            //Календарик
                            echo '
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                <span style="color: rgb(125, 125, 125);">
                                                    Дата внесения: <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="' . $day . '.' . $month . '.' . $year . '" onfocus="this.select();_Calendar.lcs(this)" 
                                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                                                </span>
                                            </li>';
                            echo '
                                        </ul>';

                            echo '		
                                    </header>';

                            echo '
                                    <div id="data">';


                            //Филиал
                            if (isset($_SESSION['filial'])) {


                                echo '
                                        <div class="cellsBlock2">
                                            <div class="cellRight">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Сумма (руб.) <label id="summ_error" class="error"></label>
                                                    </li>
                                                    <li style="margin-bottom: 5px;">
                                                        <input type="text" size="15" name="summ" id="summ" placeholder="Введите сумму" value="' . $order_j[0]['summ'] . '" class="who2"  autocomplete="off">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>';

                                echo '
                                        <div class="cellsBlock2">
                                            <div class="cellRight">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Способ внесения  <label id="summ_type_error" class="error"></label>
                                                    </li>
                                                    <li style="font-size: 90%; margin-bottom: 5px;">
                                                        <input id="summ_type" name="summ_type" value="1" type="radio" ', $order_j[0]['summ_type'] == 1 ? 'checked' : '', '> Наличный<br>
                                                        <input id="summ_type" name="summ_type" value="2" type="radio" ', $order_j[0]['summ_type'] == 2 ? 'checked' : '', '> Безналичный
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>';

                                echo '		
                                        <div class="cellsBlock2">
                                            <div class="cellRight">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Филиал <label id="filial_error" class="error">
                                                    </li>
                                                    <li style="font-size: 90%; margin-bottom: 5px;">';
                                if (($finances['see_all'] == 1) || $god_mode) {
                                    $offices_j = SelDataFromDB('spr_filials', '', '');
                                    echo '
                                                         <select name="filial" id="filial">';
                                    if ($offices_j != 0) {
                                        for ($i = 0; $i < count($offices_j); $i++) {
                                            echo "<option value='" . $offices_j[$i]['id'] . "' ", $order_j[0]['office_id'] == $offices_j[$i]['id'] ? "selected" : "", ">" . $offices_j[$i]['name'] . "</option>";
                                        }
                                    }
                                    echo '
                                                         </select>';
                                } else {
                                    $offices_j = SelDataFromDB('spr_filials', $order_j[0]['office_id'], 'offices');
                                    if ($offices_j != 0) {
                                        echo $offices_j[0]['name'] . '
                                        <input type="hidden" id="filial" name="filial" value="' . $order_j[0]['office_id'] . '">';
                                    }
                                }
                                echo '
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
                                                        <textarea name="comment" id="comment" cols="35" rows="2">' . $order_j[0]['comment'] . '</textarea>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>';


                            } else {
                                echo '
                                        <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                            }

                            echo '
                                <div>
                                    <div id="errror"></div>
                                    <input type="hidden" id="order_id" name="order_id" value="' . $_GET['id'] . '">
                                    <input type="hidden" id="client_id" name="client_id" value="' . $order_j[0]['client_id'] . '">
                                    <input type="button" class="b" value="Сохранить" onclick="showOrderAdd(\'edit\')">
                                </div>
                            </div>
					
                            </div>
                            <!-- Подложка только одна -->
                            <div id="overlay"></div>';
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
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>