<?php

//invoice_time_edit.php
//Изменить дату внесения

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($finances['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
				//var_dump($invoice_j);
				
				if ($invoice_j !=0){
					echo '
						<div id="status">
							<header>
								<h2>Изменить дату внесения наряда <a href="invoice.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>';
                    echo '				
				            </header>';

                    if ($invoice_j[0]['status'] == 9){
                        echo '<i style="color:red;">Наряд удалён (заблокирован).</i><br>';
                    }else {
                        if ($invoice_j[0]['status'] == 5){
                            echo '<i style="color:red;">Работа по наряду закрыта. Нельзя изменить время внесения</i><br>';
                        }else {
                            echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">
                                         <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                             Пациент: ' . WriteSearchUser('spr_clients', $invoice_j[0]['client_id'], 'user_full', true) . '
                                         </li> 
                                    </ul>';

                            echo '
                                    <div id="data">';
                            echo '
                                        <div id="errrror"></div>';

                            echo '		
                                        <div style="font-size: 85%; color: #FF0202; margin: 15px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i>
                                            Внимание!<br>
                                            Изменение даты добавления может значительно повлиять на все отчёты.<br>
                                        </div>';

                            echo '
                                        <div class="cellsBlock2" style="margin-bottom: 10px;">
                                            <span style="font-size:80%;  color: #555;">';

                            if (($invoice_j[0]['create_time'] != 0) || ($invoice_j[0]['create_person'] != 0)) {
                                echo '
                                Добавлен: ' . date('d.m.y H:i', strtotime($invoice_j[0]['create_time'])) . '<br>
                                Автор: ' . WriteSearchUser('spr_workers', $invoice_j[0]['create_person'], 'user', true) . '<br>';
                            } else {
                                echo 'Добавлен: не указано<br>';
                            }
                            if (($invoice_j[0]['last_edit_time'] != 0) || ($invoice_j[0]['last_edit_person'] != 0)) {
                                echo '
                                Последний раз редактировался: ' . date('d.m.y H:i', strtotime($invoice_j[0]['last_edit_time'])) . '<br>
                                Кем: ' . WriteSearchUser('spr_workers', $invoice_j[0]['last_edit_person'], 'user', true) . '';
                            }

                            echo '
                                            </span>
                                        </div>
                                        <div class="filterBlock">
                                            <div class="filtercellLeft" style="width: 120px; min-width: 120px;">
                                                Укажите новую дату
                                            </div>
                                            <div class="filtercellRight" style="width: 245px; min-width: 245px;">
                                                <input type="text" id="datanew" name="datanew" class="dateс" value="' . date('d.m.Y', strtotime($invoice_j[0]['create_time'])) . '" onfocus="this.select();_Calendar.lcs(this)"
                                                onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" id="id" name="id" value="' . $_GET['id'] . '">
                                        <div id="errror"></div>
                                        <input type="button" class="b" value="Применить" onclick="Ajax_invoice_time_edit(' . $_GET['id'] . ')">
                                        
                                    </div>
                                </div>';
                        }
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