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

                        echo '
								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Выписанные наряды</li>';

                        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$client_j[0]['id']."'";

                        $res = mysql_query($query) or die($query);
                        $number = mysql_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysql_fetch_assoc($res)){
                                array_push($invoice_j, $arr);
                            }
                        }else
                            $invoice_j = 0;
                        //var_dump ($invoice_j);

                        $invoiceAll_str = '';
                        $invoiceClose_str = '';

                        if ($invoice_j != 0) {
                            //var_dump ($invoice_j);

                            foreach ($invoice_j as $invoice_item) {

                                $invoiceTemp_str = '';

                                //Отметка об объеме оплат
                                $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не закрыт"></i>';

                                if ($invoice_item['summ'] == $invoice_item['paid']) {
                                    $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Закрыт"></i>';
                                }

                                $invoiceTemp_str .= '
                                            <li class="cellsBlock" style="width: auto;">';
                                if ($invoice_item['status'] != 9) {
                                    $invoiceTemp_str .= '
                                                <div class="cellName" style="position: relative;" invoice_attrib="true" invoice_id="' . $invoice_item['id'] . '"
                                                ondragenter="return dragEnter(event)"
                                                ondrop="return dragDrop(event)" 
                                                ondragover="return dragOver(event)" 
                                                >';
                                }else{
                                        $invoiceTemp_str .= '<div class="cellName" style="position: relative;"';
                                }
                                $invoiceTemp_str .= '
                                                <a href="invoice.php?id=' . $invoice_item['id'] . '" class="ahref">
                                                    <b>Наряд #' . $invoice_item['id'] . '</b><br>
                                                    <span style="font-size:80%;  color: #555;">';

                                if (($invoice_item['create_time'] != 0) || ($invoice_item['create_person'] != 0)) {
                                    $invoiceTemp_str .= '
                                                            Добавлен: ' . date('d.m.y H:i', strtotime($invoice_item['create_time'])) . '<br>
                                                            <!--Автор: ' . WriteSearchUser('spr_workers', $invoice_item['create_person'], 'user', true) . '<br>-->';
                                } else {
                                    $invoiceTemp_str .= 'Добавлен: не указано<br>';
                                }
                                if (($invoice_item['last_edit_time'] != 0) || ($invoice_item['last_edit_person'] != 0)) {
                                    $invoiceTemp_str .= '
                                                            Последний раз редактировался: ' . date('d.m.y H:i', strtotime($invoice_item['last_edit_time'])) . '<br>
                                                            <!--Кем: ' . WriteSearchUser('spr_workers', $invoice_item['last_edit_person'], 'user', true) . '-->';
                                }
                                $invoiceTemp_str .= '
                                                    </span>
                                                </a>
                                                <div style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</div>
                                                </div>
                                                <div class="cellName">
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Сумма:<br>
                                                        <span class="calculateInvoice" style="font-size: 13px">' . $invoice_item['summ'] . '</span> руб.
                                                    </div>';
                                if ($invoice_item['summins'] != 0) {
                                    $invoiceTemp_str .= '
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Страховка:<br>
                                                        <span class="calculateInsInvoice" style="font-size: 13px">' . $invoice_item['summins'] . '</span> руб.
                                                    </div>';
                                }
                                $invoiceTemp_str .= '
                                                </div>';

                                $invoiceTemp_str .= '
                                                <div class="cellName">
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Оплачено: <br>
                                                        <span class="calculateInvoice" style="font-size: 13px; color: #333;">' . $invoice_item['paid'] . '</span> руб.
                                                    </div>';
                                if ($invoice_item['summ'] != $invoice_item['paid']) {
                                    $invoiceTemp_str .= '
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Осталось <a href="payment_add.php?invoice_id='.$invoice_item['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                                        <span class="calculateInvoice" style="font-size: 13px">'.($invoice_item['summ'] - $invoice_item['paid']).'</span> руб.
                                                    </div>';
                                }

                                $invoiceTemp_str .= '
                                                </div>';
                                $invoiceTemp_str .= '
                                            </li>';

                                if ($invoice_item['status'] != 9) {
                                    $invoiceAll_str .= $invoiceTemp_str;
                                } else {
                                    $invoiceClose_str .= $invoiceTemp_str;
                                }

                            }

                            if (strlen($invoiceAll_str) > 1){
                                echo $invoiceAll_str;
                            }else{
                                echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;">Нет нарядов</li>';
                            }

                            //Удалённые
                            if ((strlen($invoiceClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                                echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                                echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы наряды</li>';
                                echo $invoiceClose_str;
                                echo '</div>';
                            }

                        }else{
                            echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</li>';
                        }

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

                            $query = "SELECT * FROM `journal_order` WHERE `client_id`='".$client_j[0]['id']."'";

                            $res = mysql_query($query) or die($query);
                            $number = mysql_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysql_fetch_assoc($res)){
                                    array_push($order_j, $arr);
                                }
                            }else
                                $order_j = 0;
                            //var_dump ($order_j);

                            $orderAll_str = '';
                            $orderClose_str = '';

                            if ($order_j != 0){
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
                                            <li class="cellsBlock" style="width: auto;">';
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
                                    $orderTemp_str .= '
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
                            }

                            echo '
								</ul>';




                            echo '</div>';

                            echo '				
								<div class="cellsBlock2">
									<a href="client_finance.php?client='.$client_j[0]['id'].'" class="b">Долги/Авансы <i class="fa fa-rub"></i> (старое)</a><br>';

                            if (!$allPayed)
                                echo '<i style="color:red;">Есть не погашенное</i>';

                            echo '
									</div>';

                            echo '
							</div>';

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