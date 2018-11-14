<?php

//finance_account.php
//Счёт пациента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            //переменная для просроченных
            $allPayed = true;

            if ($_GET){
                if (isset($_GET['client_id'])){

                    $client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'id');

                    if ($client_j != 0){

                        //!!! Долги/авансы старое
                        $clientDP = DebtsPrepayments ($client_j[0]['id']);

                        if ($clientDP != 0){
                            //var_dump ($clientDP);
                            $allPayed = false;
                            for ($i=0; $i<count($clientDP); $i++){
                                $repayments = Repayments($clientDP[$i]['id']);
                                //var_dump ($repayments);

                                if ($repayments != 0){
                                    //var_dump ($repayments);

                                    $ostatok = 0;
                                    foreach($repayments as $value){
                                        $ostatok += $value['summ'];
                                    }
                                    if ($clientDP[$i]['summ'] - $ostatok == 0){
                                        $allPayed = true;
                                    }else{
                                        $allPayed = false;
                                    }
                                }

                            }
                        }

                        echo '
                            <div id="status">
								<header>
								    <h2>Счет</h2>
								</header>';

                        echo '
                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                        Контрагент: '.WriteSearchUser('spr_clients',   $client_j[0]['id'], 'user_full', true).'
                                    </li> 
                                </ul>';
                            echo '
                                <div id="data">';


                            echo '<div>';

                            //!!! @@@
                            //Баланс контрагента
                            include_once 'ffun.php';
                            $client_balance = json_decode(calculateBalance ($client_j[0]['id']), true);
                            //Долг контрагента
                            $client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);

                            //var_dump(json_decode($client_balance, true));
                            echo '
                                    <ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px;">
                                            Всего внесено:
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
                                            '.$client_balance['summ'].' руб.
                                        </li>
                                    </ul>
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Доступный остаток средств:
                                        </li>
                                        <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                            <div class="availableBalance" id="availableBalance"  draggable="true" ondragstart="return dragStart(event)" style="display: inline;">'.($client_balance['summ'] - $client_balance['debited']).'</div><div style="display: inline;"> руб.</div>
                                        </li>
                                    </ul>
                        
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Общий долг составляет:
                                        </li>
                                        <li class="calculateInvoice" style="font-size: 110%; font-weight: bold;">
                                             '.$client_debt['summ'].' руб.
                                        </li>
                                      
                                     </ul>';

                            echo '
                                </div>';

                            echo '
                                <div>';

                        //Выписанные наряды
                        $arr = array();
                        $invoice_j = array();

                        $msql_cnnct = ConnectToDB ();

                        echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Выписанные наряды</li>';

                        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$client_j[0]['id']."' ORDER BY `create_time` DESC ";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($invoice_j, $arr);
                            }
                        }
                        //var_dump ($invoice_j);

                        if (($finances['see_all'] != 0) || $god_mode){
                            $rezultInvoices = showInvoiceDivRezult($invoice_j, false, true, true, true, false);
                        }else{
                            $rezultInvoices = showInvoiceDivRezult($invoice_j, false, true, true, false, false);
                        }
                        //$data, $minimal, $show_categories, $show_absent, $show_deleted

                        echo $rezultInvoices['data'];

                        echo '
								</ul>';



                        //Внесенные оплаты/ордеры
                        $arr = array();
                        $order_j = array();



                        echo '
								<ul id="orders" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
									    Внесенные оплаты/ордеры	<a href="add_order.php?client_id='.$client_j[0]['id'].'" class="b">Добавить новый</a>
									</li>';

                        $query = "SELECT * FROM `journal_order` WHERE `client_id`='".$client_j[0]['id']."' ORDER BY `create_time` DESC ";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($order_j, $arr);
                            }
                        }
                        //var_dump ($order_j);


                        if (($finances['see_all'] != 0) || $god_mode){
                            $rezultOrders = showOrderDivRezult($order_j, false, true, true);
                        }else{
                            $rezultOrders = showOrderDivRezult($order_j, false, true, false);
                        }
                        //$data, $minimal, $show_absent, $show_deleted

                        echo $rezultOrders['data'];

                            /*$orderAll_str = '';
                            $orderClose_str = '';

                            /*if ($order_j != 0){
                                //var_dump ($order_j);

                                foreach($order_j as $order_item){

                                /*    $order_type_mark = '';

                                    if ($order_item['summ_type'] == 1){
                                        $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал"></i>';
                                    }

                                    if ($order_item['summ_type'] == 2){
                                        $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                                    }
                                    $orderTemp_str = '';

                                    $orderTemp_str .= '
                                            <li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(225, 255, 67, 0.69);">';
                                    $orderTemp_str .= '
                                                <a href="order.php?id='.$order_item['id'].'" class="cellOrder ahref" style="position: relative;">
                                                    <div style="font-weight: bold;">Ордер #'.$order_item['id'].'<span style="font-weight: normal;"> от '.date('d.m.y' ,strtotime($order_item['date_in'])).'</span></div>
                                                    <div style="margin: 3px;">';

                                    $orderTemp_str .= 'Филиал: '.$offices_j[$order_item['office_id']]['name'];

                                    $orderTemp_str .= '
                                                    </div>
                                                    <div style="font-size:80%;  color: #555;">';

                                    /*if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)){
                                        $orderTemp_str .= '
                                                            Добавлен: '.date('d.m.y H:i' ,strtotime($order_item['create_time'])).'<br>
                                                            <!--Автор: '.WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true).'<br>-->';
                                    }else{
                                        $orderTemp_str .= 'Добавлен: не указано<br>';
                                    }*/
                             /*       if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                        $orderTemp_str .= '
                                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                    }
                                    $orderTemp_str .= '
                                                    </div>
                                                    <span style="position: absolute; top: 2px; right: 3px;">'. $order_type_mark.'</span>
                                                </a>
                                                <div class="cellName">
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Сумма:<br>
                                                        <span class="calculateOrder" style="font-size: 13px">'.$order_item['summ'].'</span> руб.
                                                    </div>';
                                    /*if ($order_item['summins'] != 0){
                                        echo '
                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                    Страховка:<br>
                                                    <span class="calculateInsInvoice" style="font-size: 13px">'.$order_item['summins'].'</span> руб.
                                                </div>';
                                    }*/
                            /*        $orderTemp_str .= '
                                                </div>';
                                    $orderTemp_str .= '
                                            </li>';

                                    if ($order_item['status'] != 9) {
                                        $orderAll_str .= $orderTemp_str;
                                    } else {
                                        $orderClose_str .= $orderTemp_str;
                                    }

                                }


                                if (strlen($orderAll_str) > 1){
                                    echo $orderAll_str;
                                }else{
                                    echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;">Нет ордеров</li>';
                                }

                                //Удалённые
                                if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                                    echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                                    echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                                    echo $orderClose_str;
                                    echo '</div>';
                                }

                            }else{
                                echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет ордеров</li>';
                            }*/

                            echo '
								</ul>';




                            echo '</div>';

                            echo '				
								<div class="cellsBlock2">
									<!--<a href="client_finance.php?client='.$client_j[0]['id'].'" class="b">Долги/Авансы <i class="fa fa-rub"></i> (старое)</a><br>-->';

                            /*if (!$allPayed)
                                echo '<i style="color:red;">Есть не погашенное</i>';*/

                            echo '
									</div>';

                            echo '
							</div>';


                            echo '
		                            <div id="doc_title">Счёт пациента '.WriteSearchUser('spr_clients',   $client_j[0]['id'], 'user', false).' - Асмедика</div>';


                            echo '<script src="js/dds.js" type="text/javascript"></script>';


                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
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