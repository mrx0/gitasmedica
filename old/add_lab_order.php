<?php

//add_lab_order.php
//Заказ в лабораторию добавить
//!!! переделать права

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

			//Если у нас по GET передали клиента
			if (isset($_GET['client_id'])){
				$client = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
				if ($client !=0){

                    $invoice_id = 0;

                    if (isset($_GET['invoice_id'])){
                        $invoice_id = $_GET['invoice_id'];
                    }

                    echo '
                    <div id="status">
                        <header>
                            <h2>Новый заказ в лабораторию</h2>
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
								<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								    Пациент: '.WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full', true).'
							    </li>';
					echo '
							</ul>   
					    </header>';

                    echo '
                        <div id="data">';

                    //Филиал
                    if (isset($_SESSION['filial'])){

                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								           Врач <label id="search_client2_error" class="error"></label>
                                        </li>
                                        <li style="margin-bottom: 5px;">
                                            <input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2"  autocomplete="off">
                                            <ul id="search_result2" class="search_result2"></ul>
									    </li>
							        </ul>
								</div>
							</div>';

                        echo '		
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Лаборатория <label id="lab_error" class="error">
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">';
                        //if (($finances['see_all'] == 1) || $god_mode){
                        	$lab_j = SelDataFromDB('spr_labor', '', '');
                            echo '
                                        <select name="lab" id="lab">
                                            <option value="0">Выберите лабораторию</option>';
                            if ($lab_j != 0){
                                for ($i=0;$i<count($lab_j);$i++){
                                    echo "<option value='".$lab_j[$i]['id']."'>".$lab_j[$i]['name']."</option>";
                                }
                            }
                            echo '
									    </select>';
                       /* }else{
                            $offices_j = SelDataFromDB('spr_filials', $_SESSION['filial'], 'offices');
                            if ($offices_j != 0) {
                                echo $offices_j[0]['name'].'
                                <input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                            }
                        }*/
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
								            Описание <label id="descr_error" class="error">
                                        <li style="font-size: 90%; margin-bottom: 5px;">
                                            <textarea name="descr" id="descr" cols="35" rows="3"></textarea>
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
                                            <textarea name="comment" id="comment" cols="35" rows="2"></textarea>
									    </li>
								    </ul>
								</div>
							</div>';


                    }else{
                        echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                    }


                    echo '
                            <div>
                                <div id="errror"></div>
                                <input type="hidden" id="client_id" name="client_id" value="'.$_GET['client_id'].'">
                                <input type="button" class="b" value="Сохранить" onclick="showLabOrderAdd(\'add\')">
                            </div>
                        </div>

						
                    </div>
					<!-- Подложка только одна -->
					<div id="overlay"></div>';

                }else{
                    echo '<h1>Такого контрагента нет в базе</h1><a href="index.php">Вернуться на главную</a>';
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