<?php

//task_cosmet.php
//Описание задачи косметолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'task_cosmet');
				//var_dump($task);
				
				//$closed = FALSE;
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_filials', $task[0]['office'], 'offices');
						//var_dump ($offices);
						$office = $offices[0]['name'];
						
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
						//var_dump ($actions_cosmet);
					}	
					echo '
						<div id="status">
							<header>
								<h2>Посещение #'.$task[0]['id'].'';
					if ($task[0]['status'] != 9){
						if (($cosm['edit'] == 1) || $god_mode){
							echo '
									<a href="edit_task_cosmet.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
                        if (($cosm['close'] == 1) || $god_mode) {
                            echo '
									<a href="#" onclick="Ajax_del_task_cosmet(' . $_GET['id'] . ')" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                        }
                    }else{
                        if (($cosm['close'] == 1) || $god_mode) {
                            echo '
							        <a href="#" onclick="Ajax_reopen_task_cosmet(' . $_GET['id'] . ')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                        }
                    }
					echo '			
								</h2>';

                    if ($task[0]['status'] == 9){
                        echo '<i style="color:red;">Удалено (заблокировано).</i><br>';
                    }
                    echo '
							</header>';

					echo '
							<div id="data">';
							
					/*if ($task[0]['end_time'] == 0){
						$ended = 'Нет';
						$closed = FALSE;
					}else{
						$ended = date('d.m.y H:i', $task[0]['end_time']);
						$closed = TRUE;
					}*/
					echo '
								<form>';
					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">
											Время посещения<br>
											<span style="font-size:70%;">
												Согласно записи
											</span>
										</div>
										<div class="cellRight">';
					if ($task[0]['zapis_date'] != 0){
							echo date('d.m.y H:i', $task[0]['zapis_date']);
					}else{
						echo 'не было привязано к записи';
					}
					echo '
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">Филиал</div>
										<div class="cellRight">'.$office.'</div>
									</div>

									<div class="cellsBlock2">
										<div class="cellLeft">Пациент</div>
										<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user', true).'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Описание</div>
										<div class="cellRight">';
					

					$arr = array();
					
					foreach ($task[0] as $key => $value){
						/*if (mb_strstr($key, 'c') != FALSE){
							//array_push ($arr, $value);
							$key = str_replace('c', '', $key);
							//echo $key.'<br />';
							$arr[$key] = $value;
						}	*/			
						//!!! Лайфхак
						if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && 
						($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
							$key = str_replace('c', '', $key);
							$arr[$key] = $value;
						}
					}
					
					$decription = array();
					//$decription = json_decode($task[0]['description'], true);
					$decription = $arr;
					
					//var_dump ($decription);		
						
					for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						$action = '';
						if (isset($decription[$j])){
							if ($decription[$j] != 0){
								$action = '<div style="margin: 2px; border: 1px solid #CCC; padding-left: 3px; background-color: '.$actions_cosmet[$j-1]['color'].'">'.$actions_cosmet[$j-1]['full_name'].'</div>';
							}else{
								$action = '';
							}
							echo $action;
						}else{
							echo '';
						}
					}
					
					echo '	
										</div>
									</div>
									

									<div class="cellsBlock2">
										<div class="cellLeft">Комментарий</div>
										<div class="cellRight">'.$task[0]['comment'].'</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Врач</div>
										<div class="cellRight">'.WriteSearchUser('spr_workers', $task[0]['worker'], 'user', true).'</div>
									</div>
									
									<div class="cellsBlock2">
										<span style="font-size: 80%; color: #999;">
											Создан: '.date('d.m.y H:i', $task[0]['create_time']).' пользователем
											'.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user', true).'';
					if ((($task[0]['last_edit_time'] != 0) || ($task[0]['last_edit_person'] !=0)) && (($task[0]['create_time'] != $task[0]['last_edit_time']))){
						echo '
											<br>
											Редактировался: '.date('d.m.y H:i', $task[0]['last_edit_time']).' пользователем
											'.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user', true).'';
					}
					echo '
										</span>
									</div>
									
									<!--<input type="hidden" id="ended" name="ended" value="">-->
									<input type="hidden" id="task_id" name="task_id" value="'.$_GET['id'].'">
									<input type="hidden" id="worker" name="worker" value="'.$_SESSION['id'].'">';


                    //Наряд (!!!2020-11-22 тест для косметологов)

                    $msql_cnnct = ConnectToDB ();

                    if ($task[0]['zapis_id'] != 0) {

                        $summ = 0;
                        $summins = 0;

                        $sheduler_zapis = array();
                        $invoice_ex_j = array();
                        $invoice_ex_j_mkb = array();

                        //Выберем запись
                        $query = "SELECT * FROM `zapis` WHERE `id`='" . $task[0]['zapis_id'] . "'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($sheduler_zapis, $arr);
                            }
                        } else {
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //if ($client !=0){

                        if (!empty($sheduler_zapis)) {

                            //Выберем наряды
                            //$query = "SELECT * FROM `journal_invoice`  WHERE `zapis_id`='" . $task[0]['zapis_id'] . "';";
                            $query = "SELECT * FROM `journal_invoice` ji
                            LEFT JOIN `journal_invoice_ex` jiex 
                            ON ji.id = jiex.invoice_id
                            WHERE ji.zapis_id = '" . $task[0]['zapis_id'] . "' AND ji.status <> 9;";


                            //$invoice_j = SelDataFromDB('journal_invoice', $task[0]['zapis_id'], 'id');

                            //$query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='" . $task[0]['zapis_id'] . "';";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    if (!isset($invoice_ex_j[$arr['invoice_id']])) {
                                        $invoice_ex_j[$arr['invoice_id']] = array();
                                    }
                                    if (!isset($invoice_ex_j[$arr['invoice_id']][$arr['ind']])) {
                                        $invoice_ex_j[$arr['invoice_id']][$arr['ind']] = array();
                                    }


                                    array_push($invoice_ex_j[$arr['invoice_id']][$arr['ind']], $arr);

                                }
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($invoice_ex_j, $arr);
                                }*/
                            } //else
                            //$invoice_ex_j = 0;


                            //сортируем зубы по порядку
                            /*if (!empty($invoice_ex_j)) {
                                foreach ($invoice_ex_j as $keyy => $invoice_ex_j_data){
                                    ksort($invoice_ex_j_data);
                                }

                            }*/

                            //var_dump($invoice_ex_j);

                            if (!empty($invoice_ex_j)) {

                                foreach ($invoice_ex_j as $invoice_ex_j_id => $invoice_ex_j_data) {

                                    $invoice_ex_j_mkb = array();

                                    //Для МКБ
                                    $query = "SELECT * FROM `journal_invoice_ex_mkb` WHERE `invoice_id`='" . $invoice_ex_j_id . "';";
                                    //var_dump ($query);

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                    $number = mysqli_num_rows($res);
                                    if ($number != 0) {
                                        while ($arr = mysqli_fetch_assoc($res)) {
                                            if (!isset($invoice_ex_j_mkb[$arr['ind']])) {
                                                $invoice_ex_j_mkb[$arr['ind']] = array();
                                                array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                            } else {
                                                array_push($invoice_ex_j_mkb[$arr['ind']], $arr);
                                            }
                                        }
                                    }

                                    echo '			
                                        <div class="invoice_rezult" style="margin-top: 30px; display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">
                                            <div id="errror" class="invoceHeader" style="">
                                                 <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                    <div>
                                                        <a href="invoice.php?id='.$invoice_ex_j_id.'" class="ahref" style="">Наряд #' . $invoice_ex_j_id . '</a>
                                                    </div>
                                                </div>
                                            </div>';

                                    echo '
                                            <div id="invoice_rezult" style="float: none; width: 850px;">';

                                    echo '
                                                <div class="cellsBlock">
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center;">';
                                    if ($sheduler_zapis[0]['type'] == 5) {
                                        echo '
                                                        <i><b>Зуб</b></i>';
                                    }
                                    if (($sheduler_zapis[0]['type'] == 6) || ($sheduler_zapis[0]['type'] == 10)) {
                                        echo '
                                                        <i><b>№</b></i>';
                                    }
                                    echo '
                                                    </div>
                                                    <div class="cellText2" style="font-size: 100%; text-align: center;">
                                                        <i><b>Наименование</b></i>
                                                    </div>';

                                    echo '
                                                </div>';


                                    foreach ($invoice_ex_j_data as $ind => $invoice_data) {

//                                        var_dump($invoice_data);
                                        echo '
                                        <div class="cellsBlock">
                                            <div class="cellCosmAct toothInInvoice" style="text-align: center;">';
                                        if ($ind == 99) {
                                            echo 'П';
                                        } else {
                                            echo $ind+1;
                                        }
                                        echo '
                                            </div>';

                                        //Диагноз
                                        //if ($sheduler_zapis[0]['type'] == 5) {

                                            if (!empty($invoice_ex_j_mkb) && isset($invoice_ex_j_mkb[$ind])) {
                                                echo '
                                                <div class="cellsBlock" style="font-size: 100%;" >
                                                    <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.16) none repeat scroll 0% 0%;">
                                                        <b>';
                                                if ($ind == 99) {
                                                    echo '<i>Полость</i>';
                                                } else {
                                                    echo '<i>Зуб</i>: ' . $ind;
                                                }
                                                echo '
                                                        </b>. <i>Диагноз</i>: ';

                                                foreach ($invoice_ex_j_mkb[$ind] as $mkb_key => $mkb_data_val) {
                                                    $rez = array();
                                                    //$rezult2 = array();

                                                    $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb_data_val['mkb_id']}'";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                    $number = mysqli_num_rows($res);
                                                    if ($number != 0) {
                                                        while ($arr = mysqli_fetch_assoc($res)) {
                                                            $rez[$mkb_data_val['mkb_id']] = $arr;
                                                        }
                                                    } else {
                                                        $rez = 0;
                                                    }
                                                    if ($rez != 0) {
                                                        foreach ($rez as $mkb_name_val) {
                                                            echo '
                                                            <div class="mkb_val" style="background: rgb(239, 255, 255); border: 1px dotted #bababa;"><b>' . $mkb_name_val['code'] . '</b> ' . $mkb_name_val['name'] . '
            
                                                            </div>';
                                                        }
                                                    } else {
                                                        echo '<div class="mkb_val">???</div>';
                                                    }

                                                }

                                                echo '
                                                    </div>
                                                </div>';
                                            }


                                            /*if (isset($invoice_ex_j_mkb[''])){
                                                echo '
                                                    <div class="cellsBlock" style="font-size: 100%;" >
                                                        <div class="cellText2" style="padding: 2px 4px; background: rgba(83, 219, 185, 0.14) none repeat scroll 0% 0%;">
                                                            <b>';
                                                if ($ind == 99){
                                                    echo '<i>Полость</i>';
                                                }else{
                                                    echo '<i>Зуб</i>: '.$ind;
                                                }
                                                echo '
                                                            </b>. <i>Диагноз</i>: '.$invoice_data[0]['mkb_id'].'
                                                        </div>
                                                    </div>';
                                            }*/

                                            /*            }


                                                        }
                                                    }
                                                }
                                            }*/

                                            foreach ($invoice_data as $item) {
                                                //var_dump($item);

                                                //часть прайса
                                                //if (!empty($invoice_data)){

                                                //foreach ($invoice_data as $key => $items){
                                                echo '
                                                            <div class="cellsBlock" style="font-size: 100%;" >
                                                            <!--<div class="cellCosmAct" style="">
                                                                -
                                                            </div>-->
                                                                <div class="cellText2" style="">';

                                                //Хочу имя позиции в прайсе
                                                $arr = array();
                                                $rez = array();

                                                $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$item['price_id']}'";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                $number = mysqli_num_rows($res);
                                                if ($number != 0) {
                                                    while ($arr = mysqli_fetch_assoc($res)) {
                                                        array_push($rez, $arr);
                                                    }
                                                    $rezult2 = $rez;
                                                } else {
                                                    $rezult2 = 0;
                                                }

                                                if ($rezult2 != 0) {

                                                    echo $rezult2[0]['name'];

                                                    //Узнать цену
                                                    /*$arr = array();
                                                    $rez = array();
                                                    $price = 0;
                                                    $stoim_item = 0;
                                                    //Для отбора цены по времени создания наряда
                                                    $price_arr = array();



                                                    $query = "SELECT `date_from`, `price` FROM `spr_priceprices` WHERE `item`='{$item['price_id']}' ORDER BY `date_from` DESC, `create_time`";

                                                    if ($item['insure'] != 0){
                                                        $query = "SELECT `date_from`, `price` FROM `spr_priceprices_insure` WHERE `item`='{$item['price_id']}' AND `insure`='".$item['insure']."' ORDER BY `date_from` DESC, `create_time`";
                                                    }

                                                    $res = mysql_query($query) or die(mysql_error().' -> '.$query);
                                                    $number = mysql_num_rows($res);
                                                    if ($number != 0){
                                                        //если кол-во цен == 1
                                                        if ($number == 1){
                                                            $arr = mysql_fetch_assoc($res);
                                                            $price = $arr['price'];
                                                        //если > 1
                                                        }else{
                                                            while ($arr = mysql_fetch_assoc($res)){
                                                                $price_arr[$arr['date_from']] = $arr;
                                                            }
                                                            //обратная сортировка
                                                            krsort($price_arr);
                                                            //var_dump($price_arr);
                                                            //var_dump(strtotime($invoice_j[0]['create_time']));

                                                            foreach($price_arr as $date_from => $value_arr){
                                                                if (strtotime($invoice_j[0]['create_time']) > $date_from){
                                                                    $price = $value_arr['price'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        $price = '?';
                                                    }*/

                                                } else {
                                                    echo '?';
                                                }

                                                echo '
                                                     </div>
                                                </div>';
                                            }
                                            echo '
                                                </div>';
                                            /*    $price = $item['price'];

                                                if ($sheduler_zapis[0]['type'] == 5) {
                                                    if ($item['insure'] != 0) {
                                                        //Написать страховую
                                                        $insure_j = SelDataFromDB('spr_insure', $item['insure'], 'id');

                                                        if ($insure_j != 0) {
                                                            $insure_name = $insure_j[0]['name'];
                                                        } else {
                                                            $insure_name = '?';
                                                        }
                                                    } else {
                                                        $insure_name = 'нет';
                                                    }
                                                }

                                                if ($sheduler_zapis[0]['type'] == 5) {
                                                    /*echo '
                                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;">
                                                                    ' . $insure_name . '
                                                                </div>';*/


                                            /*                   if ($item['insure'] != 0) {
                                                                   if ($item['insure_approve'] == 1) {
                                                                       /*echo '
                                                                                       <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                                                           <i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
                                                                                       </div>';*/
                                            /*                        } else {
                                                                        /*echo '
                                                                                    <div class="cellCosmAct" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;">
                                                                                        <i class="fa fa-ban" aria-hidden="true"></i>
                                                                                    </div>';*/
                                            /*                        }

                                                                } else {
                                                                    /*echo '
                                                                                <div class="cellCosmAct" insureapprove="' . $item['insure_approve'] . '" style="font-size: 70%; text-align: center;">
                                                                                    -
                                                                                </div>';*/
                                            /*                    }
                                                            }

                                                            /*echo '
                                                                        <div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                                            <b>' . $price . '</b>
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            ' . $item['spec_koeff'] . '
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            <b>' . $item['quantity'] . '</b>
                                                                        </div>
                                                                        <div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                                            ' . $item['discount'] . '
                                                                        </div>
                                                                        <div class="cellCosmAct settings_text" guarantee="' . $item['guarantee'] . '" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">';
                                                            if ($item['guarantee'] != 0) {
                                                                echo '
                                                                                <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                                            } else {
                                                                echo '-';
                                                            }*/
                                            /*echo '
                                                        </div>
                                                        <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                            <b>';*/

                                            /*
                                                                                if (($item['itog_price'] != 0) && ($price != 0)) {

                                                                                    $stoim_item = $item['itog_price'];

                                                                                } else {
                                                                                    //вычисляем стоимость
                                                                                    //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                                                                    $stoim_item = $item['quantity'] * $price;

                                                                                    //с учетом скидки акции
                                                                                    if ($item['insure'] == 0) {
                                                                                        //$stoim_item = $stoim_item - ($stoim_item * $invoice_j[0]['discount'] / 100);
                                                                                        $stoim_item = $stoim_item - ($stoim_item * $item['discount'] / 100);
                                                                                        //$stoim_item = round($stoim_item/10) * 10;
                                                                                        $stoim_item = round($stoim_item);
                                                                                    }
                                                                                    //$stoim_item = round($stoim_item/10) * 10;
                                                                                }

                                                                                if ($item['guarantee'] == 0) {
                                                                                    //echo $stoim_item;
                                                                                } else {
                                                                                    //echo 0;
                                                                                }

                                                                                //Общая стоимость
                                                                                if ($item['guarantee'] == 0) {
                                                                                    if ($item['insure'] != 0) {
                                                                                        if ($item['insure_approve'] != 0) {
                                                                                            $summins += $stoim_item;
                                                                                        }
                                                                                    } else {
                                                                                        $summ += $stoim_item;
                                                                                    }
                                                                                }


                                                                                echo '</b>
                                                                                            <!--</div>-->
                                                                                        </div>';
                                                                            }
                                                                            echo '
                                                                                    </div>';
                                                                        }


                                                                        echo '
                                                                                        <div class="cellsBlock" style="font-size: 90%;" >
                                                                                            <div class="cellText2" style="padding: 2px 4px;">
                                                                                            </div>
                                                                                            <!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
                                                                                                Итого:-->';
                                                                        if (($summ != $invoice_j[0]['summ']) || ($summins != $invoice_j[0]['summins'])) {
                                                                            /*echo '<br>
                                                                                <span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
                                            /*               }

                                                           echo '

                                                                               <!--</div>
                                                                               <div class="cellName" style="padding: 2px 4px;">
                                                                                   <div>
                                                                                       <div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">' . $summ . '</div> руб.</div>
                                                                                   </div>-->';
                                                           if ($sheduler_zapis[0]['type'] == 5) {
                                                               echo '
                                                                                   <!--<div>
                                                                                       <div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">' . $summins . '</div> руб.</div>
                                                                                   </div>-->';
                                                           }
                                                           echo '
                                                                               </div>';

                                                       }
                                                   }else{
                                                       //echo 'не было привязано к записи';
                                                   }
                               */



                                        //}
                                    }
                                    echo '</div></div>';
                                }

                            }

                        }
                    }



					/*if (!$closed){
						echo '
									<input type=\'button\' class="b" value=\'Назначить исполнителя\' onclick=\'
										ajax({
											url:"task_add_worker_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}*/

					/*if ($closed){
						echo '
									<input type=\'button\' class="b" value=\'Вернуть в работу\' onclick=\'
										ajax({
											url:"task_reopen_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}else{
						echo '
									<input type=\'button\' class="b" value=\'Закрыть\' onclick=\'
										ajax({
											url:"task_close_f.php",
											statbox:"status",
											method:"POST",
											data:
											{
												ended:document.getElementById("ended").value,
												task_id:document.getElementById("task_id").value,
												worker:document.getElementById("worker").value,
											},
											success:function(data){document.getElementById("status").innerHTML=data;}
										})\'
									>';
					}*/
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