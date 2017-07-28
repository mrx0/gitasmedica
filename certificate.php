<?php

//certificate.php
//карточка сертификата

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$cert_j = SelDataFromDB('journal_cert', $_GET['id'], 'id');
				//var_dump($cert_j);
				
				if ($cert_j != 0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="certificates.php" class="b">Сертификаты</a>
                                </div>
								<h2>
									Карточка сертификата';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($cert_j[0]['status'] != 9){
							echo '
										<a href="cert_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($cert_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_cert('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($cert_j[0]['status'] != 9){
							echo '
										<a href="cert_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>';
								
					if ($cert_j[0]['status'] == 9){
						echo '<i style="color:red;">Сертификат удален (заблокирован).</i><br>';
					}

                    echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                    if (($cert_j[0]['create_time'] != 0) || ($cert_j[0]['create_person'] != 0)){
                        echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($cert_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $cert_j[0]['create_person'], 'user', true).'<br>';
                    }else{
                        echo 'Добавлен: не указано<br>';
                    }
                    if (($cert_j[0]['last_edit_time'] != 0) || ($cert_j[0]['last_edit_person'] != 0)){
                        echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($cert_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $cert_j[0]['last_edit_person'], 'user', true).'';
                    }
                    echo '
											</span>
										</div>';

					echo '
							</header>';

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
									<div class="cellLeft">Продан</div>';
					if (($cert_j[0]['cell_time'] == '0000-00-00 00:00:00') && ($cert_j[0]['status'] != 7)){
					    echo '
                                    <div class="cellRight">нет</div>';
                    }else {
					    echo '
					        <div class="cellRight" style="background-color: rgba(47, 186, 239, 0.7);">'
                                . date('d.m.y H:i', strtotime($cert_j[0]['cell_time'])) . ' за ' . $cert_j[0]['cell_price'] . ' руб.
                            </div>';
                    }
                    echo '
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Потрачено</div>
									<div class="cellRight">'.$cert_j[0]['debited'].' руб.</div>
								</div>';
					if ($cert_j[0]['status'] == 5) {
                        echo '
           					    <div class="cellsBlock2">
									<div class="cellLeft">Закрыт (полностью потрачен)</div>
									<div class="cellRight" style="background-color: rgba(119, 255, 135, 1);">' . date('d.m.y H:i', strtotime($cert_j[0]['closed_time'])) . '</div>
								</div>';
                    }

                    $expired_color = '';

                    if ($cert_j[0]['expires_time'] != '0000-00-00') {
                        //время истечения срока годности
                        $sd = $cert_j[0]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        //сравнение не прошла ли гарантия
                        /*var_dump(strtotime($sd));
                        var_dump(strtotime($cd)); */
                        if (strtotime($sd) > strtotime($cd)) {
                        } else {
                            echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Истёк срок</div>
                                <div class="cellRight" style="background-color: rgba(239,47,55, .7);">' . date('d.m.y', strtotime($cert_j[0]['expires_time'])) . '</div>
                            </div>';
                        }
                    }


					//Если не удалён
                    if ($cert_j[0]['status'] != 9){
                        //Если ещё не продан
                        if (($cert_j[0]['status'] != 7) && ($cert_j[0]['cell_time'] == '0000-00-00 00:00:00')){
                                echo '
                                <a href="cert_cell.php?id=' . $cert_j[0]['id'] . '" class="b">Продать сертификат</a>';
                        }else{
                            //Если ничего не потрачено с него
                            if ($cert_j[0]['debited'] != 0) {

                            }
                        }
                    }
					echo '			
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