<?php

//sclad_prihod.php
//Приходная накладная

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';
			
			require 'config.php';

//            $edit_options = false;
//            $upr_edit = false;
//            $admin_edit = false;
//            $stom_edit = false;
//            $cosm_edit = false;
//            $finance_edit = false;

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['id'])){
					
					$prihod_j = SelDataFromDB('sclad_prihod', $_GET['id'], 'id');
					
					if ($prihod_j != 0){
                            //var_dump($prihod_j);

                            $filials_j = getAllFilials(false, false, true);
                            //var_dump($filials_j);

							echo '
							<div id="status">
								<header>
                                    <div class="nav">
                                        <a href="sclad.php" class="b">Склад</a>
                                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                                    </div>

									<h2>Приходная накладная #'.$_GET['id'].'';

							if (($finances['edit'] == 1) || $god_mode){
								if ($prihod_j[0]['status'] != 9){
									echo '
												<a href="sclad_prihod_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
								}
								if (($prihod_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
									echo '
										<a href="#" onclick="Ajax_reopen_prihod('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
								}
							}
							//Изменить дату внесения
//							if (($finances['see_all'] == 1) || $god_mode){
//								if ($prihod_j[0]['status'] != 9){
//									echo '
//												<a href="prihod_time_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Изменить дату"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
//								}
//							}
							if (($finances['close'] == 1) || $god_mode){
								if ($prihod_j[0]['status'] != 9){
									echo '
												<a href="sclad_prihod_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
								}
							}

							echo '			
										</h2>';

							if ($prihod_j[0]['status'] == 9){
								echo '<i style="color:red;">Приходная накладная удалена.</i><br>';
							}


                            echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:90%;">
                                                    <i>Поставщик: <b>'.$prihod_j[0]['provider_name'].'</b></i><br>
                                                    <i>№ / дата документа поставщика: <b>'.$prihod_j[0]['prov_doc'].'</b></i><br>
                                                </span>
                                            </div>';


							echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

							if (($prihod_j[0]['create_time'] != 0) || ($prihod_j[0]['create_person'] != 0)){
								echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($prihod_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $prihod_j[0]['create_person'], 'user', true).'<br>';
							}else{
								echo 'Добавлен: не указано<br>';
							}
							if (($prihod_j[0]['last_edit_time'] != 0) || ($prihod_j[0]['last_edit_person'] != 0)){
								echo '
													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($prihod_j[0]['last_edit_time'])).'<br>
													Кем: '.WriteSearchUser('spr_workers', $prihod_j[0]['last_edit_person'], 'user', true).'';
							}
							echo '
											</span>
										</div>';



							echo '
									</header>';

                            //$back_color = '';

                            //Что в накладной
                            $prihod_ex_j = array();

                            //Сумма накладной
                            $summ = 0;

                            $msql_cnnct = ConnectToDB ();

							$query = "
                            SELECT sp_ex.*, ssi.name, ssi.unit FROM `sclad_prihod_ex` sp_ex
                            LEFT JOIN `spr_sclad_items` ssi ON ssi.id = sp_ex.sclad_item_id
                            WHERE sp_ex.prihod_id='".$_GET['id']."';";
							//var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);

							if ($number != 0){
								while ($arr = mysqli_fetch_assoc($res)){
									if (!isset($prihod_ex_j[$arr['sclad_item_id']])){
										$prihod_ex_j[$arr['sclad_item_id']] = array();
									}
                                    if (!isset($prihod_ex_j[$arr['sclad_item_id']][$arr['ind']])){
                                        $prihod_ex_j[$arr['sclad_item_id']][$arr['ind']] = array();
                                    }

                                    $prihod_ex_j[$arr['sclad_item_id']][$arr['ind']] = $arr;

									$summ += $arr['price'] * $arr['quantity'];
								}
							}
							//var_dump($prihod_ex_j);

							echo '
								<div id="data">';

							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

							echo '	
										<div id="errror" class="invoceHeader" style="padding: 3px 10px;">
                                            <div>
                                                <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <div style="">Сумма: <div id="calculateInvoice" style="">'.$summ.'</div> руб.</div>
                                                    </div>
                                                    <div>
                                                        <div style="">На склад: 
                                                            <b>';
							if ($prihod_j[0]['filial_id'] == 0) {
                                echo 'Главный склад [ПР21]';
                            }else{
                                echo $filials_j[$prihod_j[0]['filial_id']]['name'];
                            }

                            echo '                           </b>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="">Дата прихода: 
                                                            <i style="font-size: 115%">';
                           echo date('d.m.Y', strtotime($prihod_j[0]['prihod_time']));
                        echo '                            </i>
                                                        </div>
                                                    </div>
                                                    ';

                            echo '
                                                </div>';


                            echo '
                                                <div style="display: inline-block; vertical-align: top;">';



                            //Если статус не равен 7 то есть не проведено
                            if ($prihod_j[0]['status'] != 7) {
                                echo '
                                                    <div style="color: red; ">
                                                        Накладная не проведена
                                                    </div>';

                                echo '
                                                    <div style="display: inline-block;">
                                                        <!--<a href="invoice_status_close.php?invoice_id=' . $prihod_j[0]['id'] . '" class="b">Закрыть работу</a>-->
                                                        <input type="button" class="b" value="Провести" onclick="showPrihodClose(' . $prihod_j[0]['id'] . ')">
                                                    </div>';

                            }else{
                                echo '
                                                    <div style="margin-top: 5px;">
                                                        <div style="display: inline-block; color: green;">
                                                            Накладная проведена';


                                if (($finances['see_all'] == 1) || $god_mode){
                                    echo '
                                                            <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%; cursor: pointer;" title="Распровести" onclick="showPrihodOpen(' . $prihod_j[0]['id'] . ')"></i>';
                                }
                                echo '
                                                        </div>
                                                    </div>';
                            }

                            echo '
										        </div>
                                            </div>
										</div>';




							echo '
										<div id="invoice_rezult" style="float: none; width: 900px;">';

							echo '
											<div class="cellsBlock">
                                                <div class="cellText2" style="font-size: 80%; text-align: center; width: 20px; min-width: 20px; max-width: 20px;">
                                                    №
                                                </div>
												<div class="cellText2" style="font-size: 80%; text-align: center;">
													<i><b>Наименование</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 90px; min-width: 90px; max-width: 90px;">
													<i><b>Цена, руб.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 70px; min-width: 70px; max-width: 70px;">
													<i><b>Кол-во</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 110px; min-width: 110px; max-width: 110px;">
													<i><b>Всего, руб.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 90px; min-width: 90px; max-width: 90px;">
													<i><b>Срок годн./Гар.</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">
													<i><b>Дата</b></i>
												</div>
											</div>';

											
							if (!empty($prihod_ex_j)) {

                                //Номер по порядку
                                $num = 1;

                                foreach ($prihod_ex_j as $ind => $prihod_data) {

                                    foreach ($prihod_data as $item) {
//                                        var_dump($item);

                                        echo '
                                            <div class="cellsBlock" style="font-size: 100%;" >
                                                <div class="cellText2" style="font-size: 80%; text-align: center; width: 20px; min-width: 20px; max-width: 20px;">
                                                    '.$num.'
                                                </div>
                                                <div class="cellText2" style="font-size: 80%; text-align: left;">';

                                        echo $item['name'];

                                        echo '
                                                </div>';

                                        echo '
                                                <div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: right; width: 90px; min-width: 90px; max-width: 90px;">
                                                    ' . $item['price'] . '
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: left; width: 70px; min-width: 70px; max-width: 70px;">
                                                    ' . $item['quantity'];

                                        if (isset($units[$item['unit']])) {
                                            //echo 'item_unit_' . $item_id . '_'.$ind.'="' . $items_arr_j[$item_id]['unit'] . '">' . $units[$items_arr_j[$item_id]['unit']];
                                            echo ' ' . $units[$item['unit']];
                                        } else {
                                            //echo 'item_unit_' . $item_id . '_'.$ind.'="0"><i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                                            echo '<i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                                        }

                                        echo '
                                         
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: right; width: 110px; min-width: 110px; max-width: 110px;">
                                                    ';

                                        echo $item['price'] * $item['quantity'];



                                        echo '
                                                    
                                                </div>
                                                <div class="cellCosmAct" style="font-size: 70%; padding: 2px 4px; text-align: left; width: 90px; min-width: 90px; max-width: 90px;">';

                                        if ($item['exp_garant_type'] == 1){
                                            echo 'Гарантия до';
                                        }elseif($item['exp_garant_type'] == 2){
                                            echo 'Срок годности до';
                                        }else{
                                            echo 'не указано';
                                        }

                                        echo '
												</div>
												<div class="cellCosmAct" style="font-size: 80%; padding: 2px 4px; text-align: center; width: 80px; min-width: 80px; max-width: 80px;">';

                                        if ($item['exp_garant_type'] != 0){
                                            echo date('d.m.Y', strtotime($item['exp_garant_date']));
                                        }else{
                                            echo '-';
                                        }

                                        echo '
												</div>';


                                        echo '
                                            </div>';
                                    }
                                    $num++;
                                }
                            }
					
							echo '
									    </div>
                                    </div>';


                            echo '
		                            <div id="doc_title">Приходная накладная #'.$_GET['id'].' - Асмедика</div>';
							echo '
								</div>
							';
						/*}else{
							echo '<h1>Что-то пошло не так_4</h1><a href="index.php">Вернуться на главную</a>';
						}*/
					}else{
						echo '<h1>Что-то пошло не так_3</h1><a href="index.php">Вернуться на главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так_2</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так_1</h1><a href="index.php">Вернуться на главную</a>';
			}
//		}else{
//			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
//		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>