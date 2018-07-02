<?php

//invoice_del.php
//Удаление(блокирование) наряда

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($finances['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
				//var_dump($invoice_j);
				
				if ($invoice_j !=0){

                    echo '
                        <div id="status">
                            <header>
                                <h2>Удалить (заблокировать) наряд <a href="invoice.php?id=' . $_GET['id'] . '" class="ahref">#' . $_GET['id'] . '</a></h2>
                            </header>';

                    //Если заднее число
                    if ((strtotime($invoice_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){
                        echo '
                    <div class="query_neok" style="padding-bottom: 10px;">
                        <h3>Нельзя удалять задним числом.</h3>
                    </div>';
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
                                    <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">
                                        <div id="errror" class="invoceHeader" style="">
                                            <div>
                                                <div style="">Сумма: <div id="calculateInvoice" style="">' . $invoice_j[0]['summ'] . '</div> руб.</div>
                                            </div>';
                        if ($invoice_j[0]['summins'] != 0) {
                            echo '
                                            <div>
                                                <div style="">Страховка: <div id="calculateInsInvoice" style="">' . $invoice_j[0]['summins'] . '</div> руб.</div>
                                            </div>';
                        }
                        echo '
                                        </div>
                                    </div>
    
                                    <input type="hidden" id="id" name="id" value="' . $_GET['id'] . '">
                                    <div id="errror"></div>
                                    <input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_invoice(' . $_GET['id'] . ', ' . $invoice_j[0]['client_id'] . ')">
                                    
                                </div>
                            </div>';
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