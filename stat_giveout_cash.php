<?php

//stat_giveout_cash.php
//Расходный ордер

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';
            require 'variables.php';

            $have_target_filial = false;
            $href_str = '';

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();

            //Дата
            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            //Или если мы смотрим другой месяц
            if (isset($_GET['m']) && isset($_GET['y']) && isset($_GET['d'])) {
                $day = $_GET['d'];
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
                        </div>
                        <h2 style="">Расходные ордеры</h2>
                        <div>
						    <a href="fl_give_out_cash_add.php'.$href_str.'" class="b4">Добавить расход</a>
						</div>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            //Выбор филиала
            echo '
                        <div style="font-size: 90%; margin-bottom: 20px;">
                            Филиал: ';

            if (($finances['see_all'] == 1) || $god_mode) {

                echo '
                            <select name="SelectFilial" id="SelectFilial">';

                foreach ($filials_j as $filial_item) {

                    $selected = '';

                    if ($filial_id == $filial_item['id']) {
                        $selected = 'selected';
                    }

                    echo '
                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                }

                echo '
                            </select>';
            } else {

                echo $filials_j[$_SESSION['filial']]['name'] . '<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $_SESSION['filial'] . '">';

            }

//            //Выбор месяц и год
//            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: ';
//            echo '
//			                <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
//            foreach ($monthsName as $mNumber => $mName){
//                $selected = '';
//                if ((int)$mNumber == (int)$month){
//                    $selected = 'selected';
//                }
//                echo '
//				                <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
//            }
//            echo '
//			                </select>
//			                <select name="iWantThisYear" id="iWantThisYear">';
//            for ($i = 2017; $i <= (int)date('Y')+2; $i++){
//                $selected = '';
//                if ($i == (int)date('Y')){
//                    $selected = 'selected';
//                }
//                echo '
//				                <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
//            }
//            echo '
//			                </select>
//			                <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisDate(\'fl_consolidated_report_admin.php?filial_id='. $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
//			                <div style="font-size: 90%; color: rgb(125, 125, 125); float: right;">Сегодня: <a href="fl_consolidated_report_admin.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
//			            </div>';


            echo '
                        </div>';

            if ($filial_id > 0) {
                //Календарик
                echo '
	
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
                            <div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="stat_giveout_cash.php?filial_id=' . $filial_id . '" class="ahref">' . date("d") . ' ' . $monthsName[date("m")] . ' ' . date("Y") . '</a></div>
                            <div>
                                <span style="color: rgb(125, 125, 125);">
                                    Изменить дату:
                                    <a href="stat_giveout_cash.php?filial_id=' . $filial_id . '&d='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="Пред. день"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
                                    <a href="stat_giveout_cash.php?filial_id=' . $filial_id . '&d='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="След. день"><i class="fa fa-caret-right" aria-hidden="true"></i></a>
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)" 
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                    <span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'stat_giveout_cash.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
                                </span>
                            </div>
                        </li>';
            }

            //Если определён филиал
            if ($have_target_filial) {

                $rezult = array();

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


                //Даты от и до
                $datastart = $year.'-'.$month.'-'.$day.' 00:00:00';
                $dataend = $year.'-'.$month.'-'.$day.' 23:59:59';

                //Поехали собирать расходные ордера
                $query = "SELECT * FROM `journal_giveoutcash` WHERE
                    `date_in` BETWEEN 
                    STR_TO_DATE('".$datastart." 00:00:00', '%Y-%m-%d %H:%i:%s')
                    AND 
                    STR_TO_DATE('".$dataend." 23:59:59', '%Y-%m-%d %H:%i:%s') 
                    AND `office_id`='{$filial_id}'
                    ORDER BY `id` ASC";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rezult, $arr);
                    }
                }

                if (!empty($rezult)){
                    //var_dump($rezult);

                    $result = '';
                    $deleted_orders = '';

                    echo '
                        <div class="" style="">
                            <ul style="margin-left: 6px; margin-bottom: 10px; font-size: 14px;">
                                <li style="font-size: 110%; margin-bottom: 5px;">
                                    Найдены расходные ордеры:
                                </li>';
                    echo '
                                <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                    echo '
                                    <div class="cellOrder" style="text-align: center; border-right: none;">
                                        <b>№</b>
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Тип</b>           
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Сумма</b>
                                                 </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Комментарий</b>
                                    </div>
                                    <div class="cellCosmAct" style="text-align: center;">
                                        <b>-</b>
                                    </div>';
                    echo '
                                </li>';

                    foreach ($rezult as $item){

                        //Если удалён, то меняем цвет на серый
                        if ( $item['status'] != 9){
                            $bgColor = '';
                        }else{
                            $bgColor = 'background-color: rgba(199, 199, 199, 1);';
                        }

                        $result_temp = '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
                        $result_temp .= '
                                    <div class="cellOrder" style="position: relative; border-right: none; border-top: none;">
                                        <b>Расходный ордер #' . $item['id'] . '</b><br>от ' . date('d.m.y', strtotime($item['date_in'])) . '<br>
                                        <span style="font-size: 90%;  color: #555;">';

                        if (($item['create_time'] != 0) || ($item['create_person'] != 0)) {
                            $result_temp .= '
                                            Добавлен: ' . date('d.m.y H:i', strtotime($item['create_time'])) . '<br>
                                            Автор: ' . WriteSearchUser('spr_workers', $item['create_person'], 'user', true) . '<br>';
                        } else {
                            $result_temp .= 'Добавлен: не указано<br>';
                        }
                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            echo'
                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }*/
                        $result_temp .= '
                                        </span>
                                                        
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">';
                        if ($item['type'] != 0) {
                            $result_temp .= $give_out_cash_types_j[$item['type']];
                        }else{
                            $result_temp .= 'Прочее';

                            if ($item['additional_info'] != '') {
                                $result_temp .= ':<br><i>' . $item['additional_info'] . '</i>';
                            }
                            //var_dump($item);
                        }

                        $result_temp .= '                              
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
                                        </div>
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="margin: 1px 0; padding: 1px 3px;">
                                            <span class="" style="font-size: 13px">' . $item['comment'] . '</span>
                                        </div>
                                    </div>';

                        //Удалить или восстановить
                        if ( $item['status'] != 9) {
                            $result_temp .= ' 
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;">
                                        <a href="giveout_cash_edit.php?id='.$item['id'].'" class="ahref"><i class="fa fa-pencil-square-o" aria-hidden="true" style="cursor: pointer;"  title="Редактировать"></i></a><br><br>
                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить" onclick="fl_deleteGiveout_cash(' . $item['id'] . ');"></i>
                                    </div>';
                        }else {
                            $result_temp .= '
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenGiveout_cash('.$item['id'].');">
                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
                                    </div>';
                        }

                        $result_temp .= '
                                </li>';

                        //Если не удалённый
                        if ( $item['status'] != 9){
                            $result .= $result_temp;
                        }else{
                            $deleted_orders .= $result_temp;
                        }

                    }

                    //Выводим
                    echo $result;

                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo $deleted_orders;
                    }

                    echo '
                            </ul>
                        </div>';
                }else{
                    echo '<span style="color: red;">Ничего не найдено</span>';
                }

            }

            echo '
                    </div>
                </div>
                <div id="doc_title">Расходные ордеры - Асмедика</div>';

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