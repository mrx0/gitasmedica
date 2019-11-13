<?php

//stat_giveout_cash.php
//Расходный ордер

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';
			include_once 'widget_calendar.php';
            require 'variables.php';

            $have_target_filial = false;
            $href_str = '';

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();

            //Дата
            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            //Или если мы смотрим другой месяц
            if (isset($_GET['m']) && isset($_GET['y'])) {
                //$day = $_GET['d'];
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            //Филиал
            if (isset($_SESSION['filial'])) {
                $filial_id = $_SESSION['filial'];
                $have_target_filial = true;
            } else {
                $filial_id = 0;
            }

            if (($finances['see_all'] == 1) || $god_mode) {
                if (isset($_GET['filial_id'])) {
                    $filial_id = $_GET['filial_id'];
                    $have_target_filial = true;
                }
            }

            if ($have_target_filial) {
                $href_str = '?filial_id=' . $filial_id . '&d=' . $day . '&m=' . $month . '&y=' . $year;
                //var_dump($href_str);
            }

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="stat_cashbox.php" class="b">Касса</a>
                            <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт</a>
                            <a href="giveout_cash_all.php" class="b">Расходные ордеры</a>
                        </div>
                        <h2 style="">Расходы по филиалам</h2>
                        <!--<div>
						    <a href="fl_give_out_cash_add.php'.$href_str.'" class="b4">Добавить расход</a>
						</div>-->
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';


            $dop = '';

            echo '<div class="no_print">';
            echo widget_calendar ($month, $year, 'stat_giveout_cash.php', $dop);
            echo '</div>';




            //Выбор филиала
            echo '
                        <div style="font-size: 90%; margin: 10px 0;">
                            Филиалы: <br>';

            echo '<input type="checkbox" id="fullAll" name="fullAll" class="fullType" value="0" checked> Все<br>';

            foreach ($filials_j as $filial_item) {

                echo '
                <input type="checkbox" id="fullAll" name="fullAll" class="fullType" value="0" checked disabled> ' . $filial_item['name'] . '<br>';
            }

            echo '
                        </div>';


            $msql_cnnct = ConnectToDB ();


            //Типы расходов
            $give_out_cash_types_j = array();

            $query = "SELECT `id`,`name` FROM `spr_cashout_types`";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $give_out_cash_types_j[$arr['id']] = $arr['name'];
                }
            }
            //var_dump( $give_out_cash_types_j);


            $rezult = array();

            //Расходы, выдано из кассы
            $giveoutcash_j = array();
            //Суммы расходов по типам
            $giveoutcash_summs = array();
            //Сумма расходов
            $giveoutcash_summ = 0;


            //Поехали собирать расходные ордера
            $query = "SELECT * FROM `journal_giveoutcash` WHERE
            MONTH(`date_in`) = '$month' AND YEAR(`date_in`) = '$year' AND `status` <> '9' 
            ORDER BY `date_in` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    //var_dump($arr);

                    if (!isset($giveoutcash_j[$arr['office_id']])) {
                        $giveoutcash_j[$arr['office_id']] = array();
                    }

                    if (!isset($giveoutcash_j[$arr['office_id']][$arr['type']])) {
                        $giveoutcash_j[$arr['office_id']][$arr['type']] = 0;
                    }
                    $giveoutcash_j[$arr['office_id']][$arr['type']] += $arr['summ'];

                    //Cуммы по типам со всех филиалов
                    if (!isset($giveoutcash_summs[$arr['type']])) {
                        $giveoutcash_summs[$arr['type']] = 0;
                    }
                    $giveoutcash_summs[$arr['type']] += $arr['summ'];

                }
            }
            //var_dump($giveoutcash_j);
            //var_dump($giveoutcash_summs);

            if (!empty($giveoutcash_j)){

                echo '<table style="border: 1px solid #BFBCB5; margin: 5px; font-size: 90%;">';

                echo '<tr>';
                echo '<td style="outline: 1px solid #BFBCB5; padding: 2px;"></td>';

                foreach ($give_out_cash_types_j as $type_id => $type_name){
                    echo '<td style="width: 80px; border: 1px solid #BFBCB5; padding: 2px; text-align: center;"><i>'.$type_name.'</i></td>';
                }
                echo '<td style="width: 80px; outline: 1px solid #BFBCB5; padding: 2px; text-align: center;"><i>Прочее</i></td>';

                echo '</tr>';

                foreach ($giveoutcash_j as $filial_id => $filial_giveoutcash_data){

                    echo '<tr>';
                    echo '<td style="border: 1px solid #BFBCB5; padding: 2px;">'.$filials_j[$filial_id]['name2'].'</td>';

                    foreach ($give_out_cash_types_j as $type_id => $type_name){

                        echo '<td style="border: 1px solid #BFBCB5; padding: 2px; text-align: right;">';

                        if (isset($giveoutcash_j[$filial_id][$type_id])) {
                            echo $giveoutcash_j[$filial_id][$type_id];
                        }else{
                            echo 0;
                        }

                        echo '</td>';


                    }

                    //Прочее
                    echo '<td style="border: 1px solid #BFBCB5; padding: 2px; text-align: right;">';

                    if (isset($giveoutcash_j[$filial_id][0])) {
                        echo $giveoutcash_j[$filial_id][0];
                    }else{
                        echo 0;
                    }

                    echo '</td>';

                    echo '</tr>';
                }

                //Суммы
                echo '<tr>';
                echo '<td style="outline: 1px solid #BFBCB5; padding: 2px;"><b>Всего</b></td>';

                foreach ($give_out_cash_types_j as $type_id => $type_name){

                    echo '<td style="width: 80px; border: 1px solid #BFBCB5; padding: 2px; text-align: right;"><b>';

                    if (isset($giveoutcash_summs[$type_id])) {
                        echo $giveoutcash_summs[$type_id];
                    }else{
                        echo 0;
                    }

                    echo '</b></td>';
                }

                //Прочее
                echo '<td style="width: 80px; outline: 1px solid #BFBCB5; padding: 2px; text-align: right;"><b>';

                if (isset($giveoutcash_summs[0])) {
                    echo $giveoutcash_summs[0];
                }else{
                    echo 0;
                }
                echo '</b></td>';

                echo '</tr>';

                echo '</table>';
            }else{
                echo '<span style="color: red;">Ничего не найдено</span>';
            }

//                if (!empty($rezult)){
//                    //var_dump($rezult);
//
//                    $result = '';
//                    $deleted_orders = '';
//
//                    echo '
//                        <div class="" style="">
//                            <ul style="margin-left: 6px; margin-bottom: 10px; font-size: 14px;">
//                                <li style="font-size: 110%; margin-bottom: 5px;">
//                                    Найдены расходные ордеры:
//                                </li>';
//                    echo '
//                                <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
//                    echo '
//                                    <div class="cellOrder" style="text-align: center; border-right: none;">
//                                        <b>№</b>
//                                    </div>
//                                    <div class="cellName" style="text-align: center; border-right: none;">
//                                        <b>Тип</b>
//                                    </div>
//                                    <div class="cellName" style="text-align: center; border-right: none;">
//                                        <b>Сумма</b>
//                                                 </div>
//                                    <div class="cellName" style="text-align: center; border-right: none;">
//                                        <b>Комментарий</b>
//                                    </div>
//                                    <div class="cellCosmAct" style="text-align: center;">
//                                        <b>-</b>
//                                    </div>';
//                    echo '
//                                </li>';
//
//                    foreach ($rezult as $item){
//
//                        //Если удалён, то меняем цвет на серый
//                        if ( $item['status'] != 9){
//                            $bgColor = '';
//                        }else{
//                            $bgColor = 'background-color: rgba(199, 199, 199, 1);';
//                        }
//
//                        $result_temp = '
//                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
//                        $result_temp .= '
//                                    <div class="cellOrder" style="position: relative; border-right: none; border-top: none;">
//                                        <b>Расходный ордер #' . $item['id'] . '</b><br>от ' . date('d.m.y', strtotime($item['date_in'])) . '<br>
//                                        <span style="font-size: 90%;  color: #555;">';
//
//                        if (($item['create_time'] != 0) || ($item['create_person'] != 0)) {
//                            $result_temp .= '
//                                            Добавлен: ' . date('d.m.y H:i', strtotime($item['create_time'])) . '<br>
//                                            Автор: ' . WriteSearchUser('spr_workers', $item['create_person'], 'user', true) . '<br>';
//                        } else {
//                            $result_temp .= 'Добавлен: не указано<br>';
//                        }
//                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
//                            echo'
//                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
//                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
//                        }*/
//                        $result_temp .= '
//                                        </span>
//
//                                    </div>
//                                    <div class="cellName" style="border-right: none; border-top: none;">';
//                        if ($item['type'] != 0) {
//                            $result_temp .= $give_out_cash_types_j[$item['type']];
//                        }else{
//                            $result_temp .= 'Прочее';
//
//                            if ($item['additional_info'] != '') {
//                                $result_temp .= ':<br><i>' . $item['additional_info'] . '</i>';
//                            }
//                            //var_dump($item);
//                        }
//
//                        $result_temp .= '
//                                    </div>
//                                    <div class="cellName" style="border-right: none; border-top: none;">
//                                        <div style="text-align: right;">
//                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
//                                        </div>
//                                    </div>
//                                    <div class="cellName" style="border-right: none; border-top: none;">
//                                        <div style="margin: 1px 0; padding: 1px 3px;">
//                                            <span class="" style="font-size: 13px">' . $item['comment'] . '</span>
//                                        </div>
//                                    </div>';
//
//                        //Удалить или восстановить
//                        if ( $item['status'] != 9) {
//                            $result_temp .= '
//                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;">
//                                        <a href="giveout_cash_edit.php?id='.$item['id'].'" class="ahref"><i class="fa fa-pencil-square-o" aria-hidden="true" style="cursor: pointer;"  title="Редактировать"></i></a><br><br>
//                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить" onclick="fl_deleteGiveout_cash(' . $item['id'] . ');"></i>
//                                    </div>';
//                        }else {
//                            $result_temp .= '
//                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenGiveout_cash('.$item['id'].');">
//                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
//                                    </div>';
//                        }
//
//                        $result_temp .= '
//                                </li>';
//
//                        //Если не удалённый
//                        if ( $item['status'] != 9){
//                            $result .= $result_temp;
//                        }else{
//                            $deleted_orders .= $result_temp;
//                        }
//
//                    }
//
//                    //Выводим
//                    echo $result;
//
//                    if (($finances['see_all'] == 1) || $god_mode) {
//                        echo $deleted_orders;
//                    }
//
//                    echo '
//                            </ul>
//                        </div>';
//                }else{
//                    echo '<span style="color: red;">Ничего не найдено</span>';
//                }

            //}

            echo '
                    </div>
                </div>
                <div id="doc_title">Расходы по филиалам - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
			echo '

				<script type="text/javascript">

                    $(function() {
                        $("#SelectFilial").change(function(){
                            
                            blockWhileWaiting (true);
                            
                            var get_data_str = "";
                            
                            var params = window
                                .location
                                .search
                                .replace("?","")
                                .split("&")
                                .reduce(
                                    function(p,e){
                                        var a = e.split(\'=\');
                                        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                        return p;
                                    },
                                    {}
                                );
                            //console.log(params);
                                                            
                            for (key in params) {
                                //console.log(key.length);  
                                                              
                                if (key.length > 0){
                                    if (key.indexOf("filial_id") == -1){
                                        get_data_str = get_data_str + "&" + key + "=" + params[key];
                                    }
                                }
                            }
                            //console.log(get_data_str);
                            
                            document.location.href = "?filial_id="+$(this).val() + "&" + get_data_str;
                        });
                    });
                    
                    			
                
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>