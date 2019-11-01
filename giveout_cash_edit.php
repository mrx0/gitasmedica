<?php

//giveout_cash_edit.php
//Редактируем расходный ордер

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode) {
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
			
				require 'variables.php';
			
				require 'config.php';

				if (isset($_GET['id'])){

				    $filials_j = getAllFilials(false, false, false);
                    //var_dump($filials_j);
					
					$giveoutcash_j = SelDataFromDB('journal_giveoutcash', $_GET['id'], 'id');
//					var_dump($giveoutcash_j);
					
					if ($giveoutcash_j != 0){

                        $day = date('d', strtotime($giveoutcash_j[0]['date_in']));
                        $month = date('m', strtotime($giveoutcash_j[0]['date_in']));
                        $year = date('Y', strtotime($giveoutcash_j[0]['date_in']));

                        //var_dump (strtotime($giveoutcash_j[0]['date_in']) + 12*60*60);
                        //var_dump (time());

                        //Если заднее число
                        if ((strtotime($giveoutcash_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){
                            echo '<h1>Нельзя редактировать задним числом</h1>';
                        }else {

                            echo '
                                <div id="status">
                                    <header>
                                        <h2>Редактировать расходный ордер #' . $_GET['id'] . '</h2>
                                        <ul style="margin-left: 6px; margin-bottom: 10px;">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Филиал: ' . $filials_j[$giveoutcash_j[0]['office_id']]['name'] . '
                                            </li>';
                            echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:80%;  color: #555;">';

                            if (($giveoutcash_j[0]['create_time'] != 0) || ($giveoutcash_j[0]['create_person'] != 0)) {
                                echo '
                                                    Добавлен: ' . date('d.m.y H:i', strtotime($giveoutcash_j[0]['create_time'])) . '<br>
                                                    Автор: ' . WriteSearchUser('spr_workers', $giveoutcash_j[0]['create_person'], 'user', true) . '<br>';
                            } else {
                                echo 'Добавлен: не указано<br>';
                            }
                            if (($giveoutcash_j[0]['last_edit_time'] != 0) || ($giveoutcash_j[0]['last_edit_person'] != 0)) {
                                echo '
                                                    Последний раз редактировался: ' . date('d.m.y H:i', strtotime($giveoutcash_j[0]['last_edit_time'])) . '<br>
                                                    Кем: ' . WriteSearchUser('spr_workers', $giveoutcash_j[0]['last_edit_person'], 'user', true) . '';
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
                                                        <input type="text" size="15" name="summ" id="summ" placeholder="Введите сумму" value="' . $giveoutcash_j[0]['summ'] . '" class="who2"  autocomplete="off">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>';


                                $give_out_cash_types_j = SelDataFromDB('spr_cashout_types', '', '');
                                //var_dump($give_out_cash_j);

                                echo '		
                                    <div class="cellsBlock2">
                                        <div class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                    Тип <label id="type_error" class="error">
                                                </li>
                                                <li style="font-size: 90%; margin-bottom: 5px;">';

                                                echo '
                                                    <select name="type" id="type">';
                                                echo '
                                                        <option value="0" selected>Прочее</option>';
                                                if ($give_out_cash_types_j != 0){
                                                    for ($i=0; $i<count($give_out_cash_types_j); $i++){
                                                        $selected = '';
                                                        if ($giveoutcash_j[0]['type'] == $give_out_cash_types_j[$i]['id']){
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option value='".$give_out_cash_types_j[$i]['id']."' $selected>".$give_out_cash_types_j[$i]['name']."</option>";

                                                    }
                                                }

                                                echo '
                                                    </select>';
                                                echo '
                                                </li>
                                            </ul>
                                        </div>';

                                echo '
                   
                                        <div id="additional_info_block" class="cellRight">
                                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                    Описание <!--если выбрано "Прочее"-->
                                                </li>
                                                <li style="font-size: 90%; margin-bottom: 5px;">
                                                    <textarea name="additional_info" id="additional_info" cols="35" rows="2">'.$giveoutcash_j[0]['additional_info'].'</textarea>
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

                                                    if (($finances['see_all'] == 1) || $god_mode){
                                                        echo '
                                                        <select name="filial" id="filial">';
                                                        if (!empty($filials_j)){
                                                            foreach ($filials_j as $f_id => $f_item){
                                                                echo "<option value='".$f_id."' ", $giveoutcash_j[0]['office_id'] == $f_id ? "selected" : "" ,">".$f_item['name']."</option>";
                                                            }
                                                        }
                                                        echo '
                                                        </select>';
                                                    }else{

                                                        echo $filials_j[$giveoutcash_j[0]['office_id']]['name'].'
                                                        <input type="hidden" id="filial" name="filial" value="'.$giveoutcash_j[0]['office_id'].'">';
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
                                                        <textarea name="comment" id="comment" cols="35" rows="2">' . $giveoutcash_j[0]['comment'] . '</textarea>
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
                                            <input type="hidden" id="giveoutcash_id" name="giveoutcash_id" value="' . $_GET['id'] . '">
                                            <input type="hidden" id="client_id" name="client_id" value="">
                                            
                                            <input type="button" class="b" value="Сохранить" onclick="showGiveOutCashAdd(\'edit\')">
                                        </div>
                                    </div>
					
                                </div>
                                
                                
                                <div id="doc_title">Редактировать расходный ордер #' . $_GET['id'] . ' - Асмедика</div>
                                
                                <!-- Подложка только одна -->
                                <div id="overlay"></div>';
                        }

                        //Скрипты которые грузят данные при загрузке
                        echo '
                            <script>
                                $(document).ready(function() {
                                    if ($(\'#type\').val() != 0){
                                        $(\'#additional_info_block\').hide();
                                        $(\'#additional_info_block\').removeClass(\'cellRight\');
                                    }else{
                                        $(\'#additional_info_block\').show();
                                        $(\'#additional_info_block\').addClass(\'cellRight\');
                                    }
                                    
                                    
                                    $(\'#type\').change(function(){
                                        //console.log($(\'#type\').val());
                                        
                                        if ($(\'#type\').val() != 0){
                                            $(\'#additional_info_block\').hide();
                                            $(\'#additional_info_block\').removeClass(\'cellRight\');
                                        }else{
                                            $(\'#additional_info_block\').show();
                                            $(\'#additional_info_block\').addClass(\'cellRight\');
                                        }
                                    });
                                })
                            </script>';

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