<?php

//withdraw_del.php
//Удаление выдачи

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($finances['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$withdraw_j = SelDataFromDB('journal_withdraw', $_GET['id'], 'id');
				//var_dump($withdraw_j);
				
				if ($withdraw_j !=0){
					echo '
						<div id="status">
							<header>
								<h2>Удалить выдачу <a href="withdraw.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							</header>';


                    echo '
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                 <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                     Контрагент: '.WriteSearchUser('spr_clients',  $withdraw_j[0]['client_id'], 'user_full', true).'
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
                                            <div style="">Сумма: <div id="calculateInvoice" class="calculateOrder">'.$withdraw_j[0]['summ'].'</div> руб.</div>
                                        </div>';
					echo '
								    </div>
								</div>

								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Удалить" onclick="Ajax_del_withdraw('.$_GET['id'].', '.$withdraw_j[0]['client_id'].')">
								
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