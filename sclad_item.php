<?php

//sclad_item.php
//карточка позиции на складе

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

                $filials_j = getAllFilials(true, false, false);
                //var_dump($filials_j);

				$sclad_item_j = SelDataFromDB('spr_sclad_items', $_GET['id'], 'id');
				var_dump($abonement_j);
				
				if ($sclad_item_j != 0){

                    $abon_type_name = '';

                    $msql_cnnct = ConnectToDB ();

                    $query = "SELECT `name` FROM `spr_solar_abonements` WHERE `id` = '{$abonement_j[0]['abon_type']}' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);

                        $abon_type_name = $arr['name'];
                    }
                    //var_dump($abon_types_j);

					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="abonements.php" class="b">Абонементы</a>
                                </div>
								<h2>
									Карточка абонемента';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($abonement_j[0]['status'] != 9){
							echo '
										<a href="abonement_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($abonement_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_abonement('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($abonement_j[0]['status'] != 9){
							echo '
										<a href="abonement_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>
								<div id="errror"></div>';
								
					if ($abonement_j[0]['status'] == 9){
						echo '<i style="color:red;">Абонемент удален (заблокирован).</i><br>';
					}

                    echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                    if (($abonement_j[0]['create_time'] != 0) || ($abonement_j[0]['create_person'] != 0)){
                        echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($abonement_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $abonement_j[0]['create_person'], 'user', true).'<br>';
                    }else{
                        echo 'Добавлен: не указано<br>';
                    }
                    if (($abonement_j[0]['last_edit_time'] != 0) || ($abonement_j[0]['last_edit_person'] != 0)){
                        echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($abonement_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $abonement_j[0]['last_edit_person'], 'user', true).'';
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
									<div class="cellRight">'.$abonement_j[0]['num'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$abon_type_name.'</div>
								</div>
  								<div class="cellsBlock2">
									<div class="cellLeft">Продан</div>';
					if (($abonement_j[0]['cell_time'] == '0000-00-00 00:00:00') && ($abonement_j[0]['status'] != 7)){
					    echo '
                                    <div class="cellRight">нет</div>';
                    }else {
					    echo '
					                <div class="cellRight" style="background-color: rgba(47, 186, 239, 0.7);">'
                                        . date('d.m.y H:i', strtotime($abonement_j[0]['cell_time'])) . ' за ' . $abonement_j[0]['cell_price'] . ' руб.<br>';
                        if ($abonement_j[0]['filial_id'] != 0){
                            //$offices_j = SelDataFromDB('spr_filials', $abonement_j[0]['office_id'], 'offices');
                            if (!empty($filials_j)) {
                                echo '<span style="font-size: 70%;">'.$filials_j[$abonement_j[0]['filial_id']]['name'].'</span>';
                            }
                        }else{
                            echo '-';
                        }
                        //Удалить продажу
                        if (($finances['see_all'] == 1) || $god_mode) {
                            echo '
                                    <div style="float: right; cursor: pointer;" onclick="Ajax_abonement_celling_del('.$_GET['id'].');" title="Отменить продажу"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 130%;"></i></div>';
                        }

					    echo '
                                    </div>';
                    }
                    echo '
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Всего минут</div>
									<div class="cellRight">'.$abonement_j[0]['min_count'].'</div>
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Потрачено минут</div>
									<div class="cellRight">'.$abonement_j[0]['debited_min'].'</div>
								</div>
           					    <div class="cellsBlock2">
									<div class="cellLeft">Осталось минут</div>
									<div class="cellRight">'.($abonement_j[0]['min_count'] - $abonement_j[0]['debited_min']).'</div>
								</div>';
					if ($abonement_j[0]['status'] == 5) {
                        echo '
           					    <div class="cellsBlock2">
									<div class="cellLeft">Закрыт (полностью потрачен)</div>
									<div class="cellRight" style="background-color: rgba(119, 255, 135, 1);">' . date('d.m.y H:i', strtotime($abonement_j[0]['closed_time'])) . '</div>
								</div>';
                    }

                    $expired_color = '';

                    if ($abonement_j[0]['expires_time'] != '0000-00-00') {
                        //время истечения срока годности
                        $sd = $abonement_j[0]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        //сравнение не прошла ли гарантия
                        /*var_dump(strtotime($sd));
                        var_dump(strtotime($cd)); */
                        if (strtotime($sd) > strtotime($cd)) {
                            echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Срок истечёт</div>
                                <div class="cellRight">
                                    ' . date('d.m.Y', strtotime($abonement_j[0]['expires_time'])) . '
                                </div>
                            </div>';
                        } else {
                            echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Истёк срок</div>
                                <div class="cellRight" style="background-color: rgba(239,47,55, .7);">' . date('d.m.y', strtotime($abonement_j[0]['expires_time']));
                            if ((($finances['see_all'] == 1) || $god_mode) && ($abonement_j[0]['status'] != 5) && ($abonement_j[0]['status'] != 9)) {
                                echo '
                                    <div style="float: right;">
                                        <span style="font-size: 80%;">Изменить срок <i class="fa fa-calendar" aria-hidden="true"></i></span><br>
                                        <input type="text" id="dataCertEnd" name="dataCertEnd" class="dateс" value="'.date('d.m.Y', strtotime($abonement_j[0]['expires_time'])).'" onfocus="this.select();_Calendar.lcs(this)"
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                        <i class="fa fa-check" aria-hidden="true" style="color: green; cursor: pointer;" title="Применить" onclick="Ajax_change_expiresTime(\'abon\','.$_GET['id'].');"></i>
                                    </div>';
                            }
                            echo '    
                                </div>
                            </div>';
                        }
                    }


					//Если не удалён
                    if ($abonement_j[0]['status'] != 9){
                        //Если ещё не продан
                        if (($abonement_j[0]['status'] != 7) && ($abonement_j[0]['cell_time'] == '0000-00-00 00:00:00')){
                                echo '
                                <a href="abonement_cell.php?id=' . $abonement_j[0]['id'] . '" class="b">Продать абонемент</a>';
                        }else{
                            //Если ничего не потрачено с него
                            if ($abonement_j[0]['debited_min'] != 0) {

                            }
                        }
                    }

                    //Список кому использовали
//                    $abonementPayList = array();
//
//                    $msql_cnnct = ConnectToDB ();
//
//                    $query = "SELECT * FROM `journal_payment` WHERE `cert_id`='{$abonement_j[0]['id']}'";
//
//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                    $number = mysqli_num_rows($res);
//
//                    if ($number != 0){
//                        while ($arr = mysqli_fetch_assoc($res)){
//                            array_push($abonementPayList, $arr);
//                        }
//
//                        if (!empty($abonementPayList)){
//                            //var_dump($abonementPayList);
//
//                            echo '
//								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
//									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Проведённые оплаты</li>';
//
//                            foreach ($abonementPayList as $abonementPayListData) {
//                                echo '<li class="cellsBlock" style="width: auto;">';
//
//
//                                echo '
//											<a href="invoice.php?id=' . $abonementPayListData['invoice_id'] . '" class="cellName ahref" style="position: relative;">
//												<b>Наряд #' . $abonementPayListData['invoice_id'] . '</b><br>
//											</a>
//											<div class="cellName">
//												<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//													Сумма:<br>
//													<span class="calculateInvoice" style="font-size: 13px">' . $abonementPayListData['summ'] . '</span> руб.
//												</div>
//											</div>
//											<div class="cellName">
//											    Пациент:<br>
//											    '.WriteSearchUser('spr_clients', $abonementPayListData['client_id'], 'user', true).'
//											</div>';
//
//                                echo '</li>';
//                            }
//
//                            echo '
//								</ul>';
//                        }
//
//                    }

                    echo '
                    <div id="doc_title">Абонемент #'.$abonement_j[0]['id'].'</div>';
                    
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