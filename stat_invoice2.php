<?php

//stat_invoice2.php
//Отчёт по оплатам по закрытым нарядам на текущем филиале за период

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode) {
        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'widget_calendar.php';
        include_once 'variables.php';

        $dop = '';
        $dopWho = '';
        $dopDate = '';
        $dopFilial = '';
        $di = 0;

        if (!isset($_GET['filial'])){
            //Филиал
            if (isset($_SESSION['filial'])){
                $_GET['filial'] = $_SESSION['filial'];
            }else{
                $_GET['filial'] = 15;
            }
        }

        //тип (космет/стомат/...)
        if (isset($_GET['who'])) {
            $getWho = returnGetWho($_GET['who'], 4, array(4,7));
        }else{
            $getWho = returnGetWho(4, 4, array(4,7));
        }
        //var_dump($getWho);

        $who = $getWho['who'];
        $whose = $getWho['whose'];
        $selected_stom = $getWho['selected_stom'];
        $selected_cosm = $getWho['selected_cosm'];
        $datatable = $getWho['datatable'];
        $kabsForDoctor = $getWho['kabsForDoctor'];
        $type = $getWho['type'];

        $stom_color = $getWho['stom_color'];
        $cosm_color = $getWho['cosm_color'];
        $somat_color = $getWho['somat_color'];
        $admin_color = $getWho['admin_color'];
        $assist_color = $getWho['assist_color'];
        $sanit_color = $getWho['sanit_color'];
        $ubor_color = $getWho['ubor_color'];
        $dvornik_color = $getWho['dvornik_color'];
        $other_color = $getWho['other_color'];
        $all_color = $getWho['all_color'];

        if (isset($_GET['m']) && isset($_GET['y'])){
            //операции со временем
            $month = $_GET['m'];
            $year = $_GET['y'];
        }else{
            //операции со временем
            $month = date('m');

            $year = date('Y');
        }
        //var_dump($month);

        foreach ($_GET as $key => $value){
            if (($key == 'd') || ($key == 'm') || ($key == 'y'))
                $dopDate  .= '&'.$key.'='.$value;
            if ($key == 'filial'){
                $dopFilial .= '&'.$key.'='.$value;
                $dop .= '&'.$key.'='.$value;
            }
            if ($key == 'who'){
                $dopWho .= '&'.$key.'='.$value;
                $dop .= '&'.$key.'='.$value;
            }
        }

        $filials_j = getAllFilials(false, true, true);

        $msql_cnnct = ConnectToDB ();

        //Получаем данные по оплатам на этом филиале за месяц
        $payments_j = array();
        //Сумма оплат
        $payments_j_summ = 0;
        //Массив с ID нарядов, по которым были оплаты
        $invoices_ids_arr = array();




        $query = "SELECT jp.*, z.noch 
        FROM `journal_payment` jp
        LEFT JOIN `journal_invoice` ji ON ji.id = jp.invoice_id
        LEFT JOIN `zapis` z ON ji.zapis_id = z.id
        WHERE jp.filial_id = '{$_GET['filial']}' AND MONTH(jp.date_in) = '{$month}' AND YEAR(jp.date_in) = '{$year}'";

        if ($type == 7){
            $query = "SELECT jp.*, z.noch 
            FROM `journal_payment` jp
            INNER JOIN `journal_invoice` ji ON ji.id = jp.invoice_id AND ji.type = '5'
            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
            WHERE jp.filial_id = '{$_GET['filial']}' AND MONTH(jp.date_in) = '{$month}' AND YEAR(jp.date_in) = '{$year}'";
        }
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
//            $arr = mysqli_fetch_assoc($res);
//            var_dump($arr['summ']);
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                //Исключаем ночные
                if ($arr['noch'] != 1) {
                    array_push($payments_j, $arr);

                    $payments_j_summ += $arr['summ'];
                    array_push($invoices_ids_arr, "`id`='" . $arr['invoice_id'] . "'");
                }
            }
        }
        //var_dump($payments_j);
        //var_dump(count($payments_j));;

        //Сумма без страховых нарядов
        //var_dump($payments_j_summ);

        if (!empty($payments_j)) {

            $invoices_ids_arr = array_unique($invoices_ids_arr);
            //var_dump($invoices_ids_arr);

            $invoices_ids_str = implode(' OR ', $invoices_ids_arr);
            //var_dump(implode(' OR ', $invoices_ids_arr));

            //Соберём наряды, по которым были оплаты
            $invoices_j = array();

            $query = "SELECT * FROM `journal_invoice` WHERE {$invoices_ids_str}";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    //Раскидываем в массив
                    //array_push($invoices_j, $arr);
                    $invoices_j[$arr['id']] = $arr;
                }
            }
            //var_dump($invoices_j);

//        $rezultInvoices = showInvoiceDivRezult($invoices_j, false, false, false, true, true, false);

//        echo $rezultInvoices['data'];
//        echo $rezultInvoices['data_deleted'];

        }

        //Наряды по стоматологическим страховым работам
        $invoices_ins_j = array();
        //Сумма по наряды по стоматологическим страховым работам
        $invoices_ins_summ = 0;

        $query = "
            SELECT ji.*, z.noch 
            FROM `journal_invoice` ji
            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
            WHERE ji.type='5' AND ji.office_id='{$_GET['filial']}' AND MONTH(ji.closed_time) = '{$month}' AND YEAR(ji.closed_time) = '{$year}'
            AND ji.summins <> '0' AND ji.status = '5'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //Раскидываем в массив
                //Исключаем ночные
                if ($arr['noch'] != 1) {
                    array_push($invoices_ins_j, $arr);
                    $invoices_ins_summ += $arr['summins'];
                    //$invoices_ins_j[$arr['id']] = $arr ;
                }
            }
        }
//        var_dump($invoices_ins_summ);
//        var_dump($invoices_ins_j);



//        //Наряды
//        $query = "
//        SELECT ji.summ, ji.summins, ji.office_id, z.noch
//        FROM `journal_invoice` ji
//        LEFT JOIN `zapis` z ON ji.zapis_id = z.id
//        WHERE ji.status='5' AND ji.office_id = '{$_GET['filial']}' AND ji.closed_time  BETWEEN '2019-06-01' AND '2019-06-30'
//        ";

        //Если ассистент, то только стоматология
//        if ($_POST['typeW'] == 7){
////            $query = "
////            SELECT `summ`,`summins`, `office_id`
////            FROM `journal_invoice`
////            WHERE `type` ='5' AND `status`='5' AND `closed_time` BETWEEN '{$datastart}' AND '{$dataend}'
////            ";
//
//            $query = "
//            SELECT ji.summ, ji.summins, ji.office_id, z.noch
//            FROM `journal_invoice` ji
//            LEFT JOIN `zapis` z ON ji.zapis_id = z.id
//            WHERE ji.status='5' AND ji.type ='5' AND ji.closed_time BETWEEN '{$datastart}' AND '{$dataend}'
//            ";
//        }

        //var_dump($query);

//        $journal = array();
//
//        $arr = array();
//
//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//        $number = mysqli_num_rows($res);
//
//        if ($number != 0) {
//            while ($arr = mysqli_fetch_assoc($res)) {
//                //Исключаем ночные
//                if ($arr['noch'] != 1) {
//                    if (!isset($journal[$arr['office_id']])){
//                        $journal[$arr['office_id']] = array();
//                    }
//                    array_push($journal[$arr['office_id']], $arr);
//                }
//            }
//        }
//        //var_dump($journal);
//
//        $summ_arr = array();
//        $all_summ = 0;
//
//        foreach ($journal as $filial_id => $filial_journal){
//            //var_dump($item);
//            if (!isset($summ_arr[$filial_id])){
//                $summ_arr[$filial_id] = 0;
//            }
//
//            foreach ($filial_journal as $item){
//                $summ_arr[$filial_id] += $item['summ'] + $item['summins'];
//            }
//        }
        //Сумма без страхововых
        //var_dump($summ_arr);



        echo '
			
				<div id="status">
					<div class="no_print"> 
                        <header>
                            <div class="nav">
                                <!--<a href="scheduler_template.php" class="b">График план</a>
                                <a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>-->
                            </div>
                            
                            <h2>Отчёт отображает сумму по оплатам, которая идёт в расчёт ЗП</h2>
                        </header>
					</div>';
        echo '
					<div id="data" style="margin-top: 5px;">
					    <input type="hidden" id="type" value="'.$type.'">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
        echo '			
							<div class="no_print"> 
                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                        <!--<a href="scheduler.php?'.$dopFilial.$dopDate.'&who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                        <a href="scheduler.php?'.$dopFilial.$dopDate.'&who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                        <a href="scheduler.php?'.$dopFilial.$dopDate.'&who=10" class="b" style="'.$somat_color.'">Специалисты</a>-->
                                        <a href="stat_invoice2.php?'.$dopFilial.$dopDate.'&who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                        <a href="stat_invoice2.php?'.$dopFilial.$dopDate.'&who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                        <!--<a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                        <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                        <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=15" class="b" style="'.$dvornik_color.'">Дворники</a>
                                        <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=11" class="b" style="'.$other_color.'">Прочие</a>-->
                                </li>
                                <li style="width: auto; margin-bottom: 20px;">
                                    <div style="display: inline-block; margin-right: 20px;">
                                        <div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Филиалы
                                        </div>
                                        <div>
                                            <select name="SelectFilial" id="SelectFilial">
											';

        foreach ($filials_j as $f_id => $filial_item){
            $selected = '';
            if (isset($_GET['filial'])){
                if ($f_id== $_GET['filial']){
                    $selected = 'selected';
                }
            }
            echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
        }

        echo '
                                            </select>
                                        </div>
                                    </div>
                                    <div style="display: inline-block; margin-right: 20px;">
    
                                        <div style="display: inline-block; margin-right: 20px;">
                                            <a href="?'.$who.'" class="dotyel" style="font-size: 70%;">Сбросить</a>
                                        </div>
                                    </div>
                                </li>
                            </div>';

        echo '<div class="no_print">';
        echo widget_calendar ($month, $year, 'stat_invoice2.php', $dop);
        echo '</div>';



        echo '
                        </ul>';
        echo '
                        <div class="cellsBlock2" style="width: auto; background: rgb(253, 244, 250);">
                            Сумма по всем оплатам: 
                            <span class="calculateOrder">'.number_format($payments_j_summ, 0, '.', ' ').'</span> руб.
                        </div>
                        <div class="cellsBlock2" style="width: auto; background: rgb(253, 244, 250);">
                            Сумма по страховым нарядам: 
                            <span class="calculateInsInvoice">'.number_format($invoices_ins_summ, 0, '.', ' ').'</span> руб.
                        </div>
                        <div class="cellsBlock2" style="width: auto; background: rgb(253, 244, 250);">
                            Итого: 
                            <span class="calculateInvoice">'.number_format($payments_j_summ + $invoices_ins_summ, 0, '.', ' ').'</span> руб.
                        </div>
                        ';



        //Выведем все оплаты, которые получили
        if (!empty($payments_j)){

            echo '
                ';

            foreach ($payments_j as $payment_item) {
                $pay_type_mark = '';
                $cert_num = '';

                if ($payment_item['type'] == 1){
                    $pay_type_mark = '<i class="fa fa-certificate" aria-hidden="true" title="Оплата сертификатом"></i>';
                    //Найдем сертификат по его id
                    $query = "SELECT `num` FROM `journal_cert` WHERE `id`='".$payment_item['cert_id']."' LIMIT 1";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);
                        $cert_num = 'Сертификатом №'.$arr['num'];
                    } else {
                        $cert_num = 'Ошибка сертификата';
                    }
                }

                //var_dump($payment_item['filial_id']);

                echo '
                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                echo '
                        <div class="cellOrder" style="position: relative; border-right: 0;"">
                            <b>Оплата #' . $payment_item['id'] . '</b> от ' . date('d.m.y', strtotime($payment_item['date_in'])) . ' '.$cert_num.'<br>
                            <span style="font-size:90%;  color: #555;">
                                Филиал: ';
//                if (($finances['see_all'] == 1) || $god_mode) {
//                    echo '
//                              <div id="change_payment_filial" class="ahref change_payment_filial" payment_id="'.$payment_item['id'].'" filial_id="'.$payment_item['filial_id'].'" style="display: inline;">';
//                }
                if ($payment_item['filial_id'] > 0){
                    echo $filials_j[$payment_item['filial_id']]['name'].'<br>';
                }else{
                    echo '<span style="color: red;">не указан</span><br>';
                }
//                if (($finances['see_all'] == 1) || $god_mode) {
//                    echo '
//                              </div>';
//                }

                if (($payment_item['create_time'] != 0) || ($payment_item['create_person'] != 0)) {
                    echo '
                                Добавлен: ' . date('d.m.y H:i', strtotime($payment_item['create_time'])) . '<br>
                                Автор: ' . WriteSearchUser('spr_workers', $payment_item['create_person'], 'user', false) . '<br>';
                } else {
                    echo 'Добавлен: не указано<br>';
                }
                /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                    echo'
                                Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                }*/
                echo '
                            </span>
                            <span style="position: absolute; top: 2px; right: 3px;">'. $pay_type_mark .'</span>
                        </div>
                        <div class="cellName" style="border-left: 0;">
                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                Сумма:<br>
                                <span class="calculateOrder" style="font-size: 13px">' . $payment_item['summ'] . '</span> руб.
                            </div>
                        </div>';



                //О наряде

                //Цвет если оплачено или нет
                $paycolor = "color: red;";
                if ($invoices_j[$payment_item['invoice_id']]['summ'] == $invoices_j[$payment_item['invoice_id']]['paid']) {
                    $paycolor = 'color: #333333;';
                }
                echo '
                        <div class="cellOrder" style="position: relative; border-right: 0;">
                            <a href="invoice.php?id='.$payment_item['invoice_id'].'" class="ahref" style="font-weight: bold;">Наряд #' . $payment_item['invoice_id'] . '</a> от ' . date('d.m.y', strtotime($invoices_j[$payment_item['invoice_id']]['create_time'])) . '<br>
                            <span style="font-size:90%;  color: #555;">
                    Филиал: ';

//                    echo '
//                <div id="change_payment_filial" class="ahref change_payment_filial" payment_id="'.$payment_item['id'].'" filial_id="'.$payment_item['filial_id'].'" style="display: inline;">';

                if ($invoices_j[$payment_item['invoice_id']]['office_id'] > 0){
                    echo $filials_j[$invoices_j[$payment_item['invoice_id']]['office_id']]['name'].'<br>';
                }else{
                    echo '<span style="color: red;">не указан</span><br>';
                }
//                if (($finances['see_all'] == 1) || $god_mode) {
//                    echo '
//                </div>';
//                }

                if (($invoices_j[$payment_item['invoice_id']]['create_time'] != 0) || ($invoices_j[$payment_item['invoice_id']]['create_person'] != 0)) {
                    echo '
                                Добавлен: ' . date('d.m.y H:i', strtotime($invoices_j[$payment_item['invoice_id']]['create_time'])) . '<br>
                                Автор: ' . WriteSearchUser('spr_workers', $invoices_j[$payment_item['invoice_id']]['create_person'], 'user', false) . '<br>';
                } else {
                    echo 'Добавлен: не указано<br>';
                }
                /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                    echo'
                                Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                }*/
                //$invoices_j[$payment_item['invoice_id']]
                echo '
                            </span>
                        </div>
                        
                        <div class="cellName" style="border-left: 0;">
                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                    Сумма:<br>
                                    <span class="calculateInvoice" style="font-size: 13px; ' . $paycolor . '">' . $invoices_j[$payment_item['invoice_id']]['summ'] . '</span> руб.
                                </div>';
                if ($invoices_j[$payment_item['invoice_id']]['summins'] != 0) {
                    echo '
                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                    Страховка:<br>
                                    <span class="calculateInsInvoice" style="font-size: 13px">' . $invoices_j[$payment_item['invoice_id']]['summins'] . '</span> руб.
                                </div>';
                }
                echo '
                            </div>';

                if ($invoices_j[$payment_item['invoice_id']]['summ'] != $invoices_j[$payment_item['invoice_id']]['paid']) {
                    echo '
                            <div class="cellName">';
                    echo '
                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                    Оплачено:<br>
                                    <span class="calculateInvoice" style="font-weight: normal; font-size: 13px; color: #333;">' . $invoices_j[$payment_item['invoice_id']]['paid'] . '</span> руб.
                                </div>';

                    if ($invoices_j[$payment_item['invoice_id']]['summ'] != $invoices_j[$payment_item['invoice_id']]['paid']) {
                        echo '
                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                    Осталось внести<br>
                                    <span class="calculateInvoice" style="font-size: 13px">' . ($invoices_j[$payment_item['invoice_id']]['summ'] - $invoices_j[$payment_item['invoice_id']]['paid']) . '</span> руб.
                                </div>';
                    }
                }


                echo '
                            </div>
                        </div>
                        
                        ';
                echo '
                    </li>';
            }

            echo '';
        }


        //Выведем страховые наряды
        $rezultInvoices = showInvoiceDivRezult($invoices_ins_j, false, false, false, true, true, false);

        echo $rezultInvoices['data'];
        //echo $rezultInvoices['data_deleted'];

        echo '
                    </div>
                </div>';


        echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    //console.log("'.$month.'");
							    //console.log("?filial="+$(this).val()+"'.$who.'&m='.$month.'&y='.$year.'");

								document.location.href = "?filial="+$(this).val()+"'.$who.'&m='.$month.'&y='.$year.'";
							    
								//var dayW = document.getElementById("SelectDayW").value;
								//document.location.href = "?filial="+$(this).val()+"'.$who.'";
							});
//							$("#SelectDayW").change(function(){
//							
//							    blockWhileWaiting (true);
//							    
//								var filial = document.getElementById("SelectFilial").value;
//								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
//							});
						});
						
					</script>';
    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
    echo '
		    <div id="doc_title">График '.$whose.'/',$monthsName[$month],' ',$year,'/Филиал ... - Асмедика</div>';
}else{
    header("location: enter.php");
}

require_once 'footer.php';
?>