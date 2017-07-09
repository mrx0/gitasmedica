<?php

//cert_del.php
//Удаление(блокирование) сертификата

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$cert_j = SelDataFromDB('journal_cert', $_GET['id'], 'id');
				//var_dump($cert_j);
				
				if ($cert_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="certificates.php" class="b">Сертификаты</a>
                                </div>
								<h2>Удалить(заблокировать) сертификат <a href="certificate.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>';
                    if ($cert_j[0]['status'] == 7){
                        echo '<i style="color:red;">Сертификат уже продан. Удалять нельзя.</i><br>';
                    }
                    echo '
							</header>';

					echo '
							<div id="data">';
					echo '
							<div id="errrror"></div>';
                    echo '
							<div id="data">';
                    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Номер</div>
									<div class="cellRight">'.$cert_j[0]['num'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Номинал</div>
									<div class="cellRight">'.$cert_j[0]['nominal'].' руб.</div>
								</div>
  								<div class="cellsBlock2">
									<div class="cellLeft">Продан</div>
									<div class="cellRight">';
                    if ($cert_j[0]['cell_time'] == '0000-00-00 00:00:00'){
                        echo 'нет';
                    }else {
                        echo date('d.m.y H:i', strtotime($cert_j[0]['cell_time']));
                    }
                    echo '
                                    </div>
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Потрачено</div>
									<div class="cellRight">'.$cert_j[0]['debited'].' руб.</div>
								</div>';
                    echo '			
							</div>';
                    
                    if (($cert_j[0]['status'] != 9) && ($cert_j[0]['status'] != 7)){
                        echo '				
									<input type="hidden" id="id" name="id" value="' . $_GET['id'] . '">
									<div id="errror"></div>
									<input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_cert(' . $_GET['id'] . ')">';
                    }
					echo '				
								</form>';	
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>