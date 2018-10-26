<?php

//money_get_f.php
//Функция для выдачи финансовой истории клиента в карточку

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';
            require 'variables.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            if (!isset($_POST['client_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $rezult = '';

                //Выписанные наряды
                $invoice_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$_POST['client_id']."' ORDER BY `create_time` DESC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($invoice_j, $arr);
                    }
                }

                $rezultInvoices = showInvoiceDivRezult($invoice_j, false, false, true, true, true);
                //$data, $minimal, $show_categories, $show_absent, $show_deleted
                //var_dump (count($rezultInvoices));

                if ($rezultInvoices['count'] > 0) {
                    $rezult .= '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Выписанные <span style="color: red;">не закрытые</span> наряды</li>';

                    $rezult .= $rezultInvoices['data'];

                    $rezult .= '    
                                    </li>
                                </ul>';
                }else{
                    /*$rezult .= '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">
									    Все выписанные наряды <span style="color: darkgreen;">закрыты</span>
									</li>';

                    $rezult .= $rezultInvoices['data'];

                    $rezult .= '    
                                    </li>
                                </ul>';*/

                    $rezult = '';
                }

                //Внесенные оплаты/ордеры
                $arr = array();
                $order_j = array();

                $rezult .= '
                            <ul id="orders" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
                                    Внесенные оплаты/ордеры	<span style="color: red;">(последние 5)</span> <a href="add_order.php?client_id='.$_POST['client_id'].'" class="b">Добавить новый</a>
                                </li>';


                $query = "SELECT * FROM `journal_order` WHERE `client_id`='".$_POST['client_id']."' ORDER BY `create_time` DESC LIMIT 5";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($order_j, $arr);
                    }
                }
                //var_dump ($order_j);

                $rezultOrders = showOrderDivRezult($order_j, false, true, false);
                //$data, $minimal, $show_absent, $show_deleted

                $rezult .= $rezultOrders['data'];

                /*$orderAll_str = '';
                $orderClose_str = '';

                if (!empty($order_j)){
                    //var_dump ($order_j);

                    foreach($order_j as $order_item){

                        $order_type_mark = '';

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
												<b>Ордер #'.$order_item['id'].'</b> от '.date('d.m.y' ,strtotime($order_item['date_in'])).'<br>
												<span style="font-size:80%;  color: #555;">';

                        if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)){
                            $orderTemp_str .= '
														Добавлен: '.date('d.m.y H:i' ,strtotime($order_item['create_time'])).'<br>
														<!--Автор: '.WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true).'<br>-->';
                        }else{
                            $orderTemp_str .= 'Добавлен: не указано<br>';
                        }
                        if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            $orderTemp_str .= '
														Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
														<!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }
                        $orderTemp_str .= '
												</span>
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
                        $rezult .= $orderAll_str;
                    }else{
                        $rezult .= '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;"><i>Нет ордеров</i></li>';
                    }

                    //Удалённые
                    /*if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                        echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                        echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                        echo $orderClose_str;
                        echo '</div>';
                    }*/

                /*}else{
                    $rezult .= '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;"><i>Нет ордеров</i></li>';
                }*/

                $rezult .= '
                            </ul>';


                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }
?>