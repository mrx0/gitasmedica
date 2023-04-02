<?php

//move_all.php
//Перенос всех отметок этого пациента к другому

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
			//var_dump($_SESSION);
			if ($client !=0){
				echo '
					<div id="status">
						<header>
							<h2>Перенос всех посещений и данных другому пациенту</h2>
						</header>';
				echo '
						<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;">
							<i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> Данное действие невозможно будет отменить. Будут перенесены ВСЕ данные:<br>
							<b>Записи пациента, поcещения косметологов, стоматологов и т.д.</b>
						</span><br>';
				
				echo '
						<div id="data">';
				echo '
						<div id="errrror"></div>';

				echo '
							<form action="client_edit_f.php">
								<div class="cellsBlock2">
									<div class="cellLeft">
										ФИО <span style="font-size: 80%;">от кого переносим</span>';
				echo '
									</div>
									<div class="cellRight">
										<a href="client.php?id='.$_GET['client'].'" class="ahref">#'.$_GET['client'].' '.$client[0]['full_name'].'</a>
									</div>
								</div>';

				echo '				
								<div class="cellsBlock2" style="/*width: 600px;*/">
									<div class="cellLeft">
										Кому перенести<br />
									</div>
									<div class="cellRight">
										<input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off" style="width: 90%; font-size: 110%;"> 
										<ul id="search_result" class="search_result"></ul><br>
										<!--<span class="button_tiny" style="background: rgb(7 255 106); font-size: 80%; cursor: pointer;" onclick="checkBeforeMoveAll();"><i class="fa fa-check-square" style="color: green; "></i> Проверить</span><br>-->
									</div>
								</div>';
                echo '	
                                <ul class="move_description" style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); /*display: inline-table;*/ display: none;">
                                    <li style="margin-bottom: 10px;">
                                        <i>От кого переносим</i>
                                    </li>
                                    <li id="move_from_descr" class="filterBlock">
                                        
                                    </li>				
                                </ul>
							
                                <ul class="move_description" style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); /*display: inline-table;*/ display: none;">
                                    <li style="margin-bottom: 10px;">
                                        <b>Кому переносим</b>
                                    </li>
                                    <li id="move_to_descr" class="filterBlock">
    
                                    </li>
                                </ul>';

				echo '				
								<input type="hidden" id="id" name="id" value="'.$_GET['client'].'">
								<input type="hidden" id="move_to_id" name="move_to_id" value="0">
								<div id="errror"></div>
								<input type="button" class="b" value="Применить" onclick="Ajax_move_all('.$_GET['client'].')">
							</form>';	
				echo '
						</div>
					</div>';


				echo '
				<script type="text/javascript">
                    $(".search_result").on("click", "li", function(){

                        let move_to_client_id = $(this).attr("client_id");
                        //console.log(move_to_client_id);
                        
                        $("#move_to_id").val(move_to_client_id);
                        
                        checkBeforeMoveAll();
                        
                    });
				</script>
				';
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