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
                        //var_dump("1 - ".(microtime(true) - $script_start));

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
                            //var_dump("2 - ".(microtime(true) - $script_start));
                            //Долг контрагента
                            $client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);
                            //var_dump("3 - ".(microtime(true) - $script_start));
                            //Выдачи контрагенту
                            //$client_refund = json_decode(calculateRefund ($client_j[0]['id']), true);

                            //var_dump($client_balance);

                            echo '
                                    <ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; /*margin-top: 10px;*/">
                                            Всего внесено:
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
                                            '.$client_balance['summ'].' руб.
                                        </li>
                                    </ul>

                                    <ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; /*margin-top: 10px;*/">
                                            Всего возвращено пациенту:
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold; color: red;">
                                            '.$client_balance['withdraw'].' руб.
                                        </li>
                                    </ul>';
                        echo '
                                </div>';
                        echo '
                                <div>';
                        echo '
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Доступный остаток средств:
                                        </li>
                                        <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                            <div class="availableBalance" id="availableBalance"  draggable="true" ondragstart="return dragStart(event)" style="display: inline;">'.($client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund']).'</div><div style="display: inline;"> руб.</div>
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
                        //var_dump("3.5 - ".(microtime(true) - $script_start));
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
                        //var_dump("3.6 - ".(microtime(true) - $script_start));
                        //var_dump ($invoice_j);

                        if (($finances['see_all'] != 0) || $god_mode){
                            $rezultInvoices = showInvoiceDivRezult($invoice_j, false, false, true, true, true, false);
                        }else{
                            $rezultInvoices = showInvoiceDivRezult($invoice_j, false, false, true, true, false, false);
                        }
                        //$data, $minimal, $show_categories, $show_absent, $show_deleted

                        echo $rezultInvoices['data'];
                        //var_dump("4 - ".(microtime(true) - $script_start));
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


                            echo '
								</ul>';



                        //Выписанные выдачи денег
                        $arr = array();
                        $refund_j = array();
                        //var_dump("5 - ".(microtime(true) - $script_start));
                        echo '
								<ul id="refunds" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
									    Выдачи ';
                        if (($finances['see_all'] == 1) || $god_mode){
                            echo '
                            <a href="withdraw_add.php?client_id='.$client_j[0]['id'].'" class="b">Добавить новую</a>';
                        }
                        echo '
									</li>';


                        $query = "SELECT * FROM `journal_withdraw` WHERE `client_id`='".$client_j[0]['id']."' ORDER BY `create_time` DESC ";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($refund_j, $arr);
                            }
                        }
                        //var_dump ($order_j);


                        if (($finances['see_all'] != 0) || $god_mode){
                            $rezultWithdraw = showWithdrawDivRezult($refund_j, false, true, true);
                        }else{
                            //$rezultRefunds = showWithdrawDivRezult($refund_j, false, true, false);
                            $rezultWithdraw = showWithdrawDivRezult($refund_j, false, true, false);
                        }
                        //$data, $minimal, $show_absent, $show_deleted

                        echo $rezultWithdraw['data'];

                        //var_dump("6 - ".(microtime(true) - $script_start));
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