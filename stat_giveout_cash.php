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

            echo '<input type="checkbox" id="filialsAll" name="filialsAll" class="filialChoice" value="0" checked> Все<br>';

            foreach ($filials_j as $filial_item) {

                echo '
                <input type="checkbox" id="filial_'.$filial_item['id'].'" name="filial_'.$filial_item['id'].'" class="filialChoice" value="'.$filial_item['id'].'" checked> ' . $filial_item['name'] . '<br>';
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

                    echo '<tr id="row_f_'.$filial_id.'" class="cellsBlockHover">';
                    echo '<td style="border: 1px solid #BFBCB5; padding: 2px;">'.$filials_j[$filial_id]['name2'].'</td>';

                    foreach ($give_out_cash_types_j as $type_id => $type_name){

                        echo '<td class="filialSumm_'.$filial_id.'" type_id="'.$type_id.'" style="border: 1px solid #BFBCB5; padding: 2px; text-align: right;">';

                        if (isset($giveoutcash_j[$filial_id][$type_id])) {
                            echo $giveoutcash_j[$filial_id][$type_id];
                        }else{
                            echo 0;
                        }

                        echo '</td>';


                    }

                    //Прочее
                    echo '<td class="filialSumm_'.$filial_id.'" type_id="0" style="border: 1px solid #BFBCB5; padding: 2px; text-align: right;">';

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

                    echo '<td id="typeSumm_'.$type_id.'" class="typeSumm" style="width: 80px; border: 1px solid #BFBCB5; padding: 2px; text-align: right; font-weight: bold;">';

                    if (isset($giveoutcash_summs[$type_id])) {
                        echo $giveoutcash_summs[$type_id];
                    }else{
                        echo 0;
                    }

                    echo '</td>';
                }

                //Прочее
                echo '<td id="typeSumm_0" class="typeSumm" style="width: 80px; outline: 1px solid #BFBCB5; padding: 2px; text-align: right; font-weight: bold;">';

                if (isset($giveoutcash_summs[0])) {
                    echo $giveoutcash_summs[0];
                }else{
                    echo 0;
                }
                echo '</td>';

                echo '</tr>';

                echo '</table>';
            }else{
                echo '<span style="color: red;">Ничего не найдено</span>';
            }

            echo '
                    </div>
                </div>
                <div id="doc_title">Расходы по филиалам - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
			echo '

				<script type="text/javascript">

                    //Проверка и установка checkbox
                    $(".filialChoice").click(function() {
                        
                        var checked_status = $(this).is(":checked");
                        var thisId = $(this).attr("id");
                        var pin_status = false;
                        var allCheckStatus = false;
                        
                        if (thisId == "filialsAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".filialChoice").each(function() {
                                $(this).prop("checked", pin_status);
                                
                                //Если это какой-то филиал, а не "Все"
                                if ($(this).val() > 0){
                                    //console.log($(this).val());
                                    
                                    //Если отметка есть, показываем, если нет, то прячем
                                    if (pin_status){
                                        $("#row_f_"+$(this).val()).show();
                                    }else{
                                        $("#row_f_"+$(this).val()).hide();
                                    }
                                }
                            });
                        }else{
                            if (!checked_status){
                                $("#filialsAll").prop("checked", false);
                                
                                if ($(this).val() > 0){
                                    //console.log($(this).val());
                                    
                                    $("#row_f_"+$(this).val()).hide();
                                }
                            }else{
                                allCheckStatus = true; 
                                $(".filialChoice").each(function() {
                                    if ($(this).attr("id") != "filialsAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false;
                                            
                                            if ($(this).val() > 0){
                                                //console.log($(this).val());
                                    
                                                $("#row_f_"+$(this).val()).hide();
                                            }
                                        }else{
                                            if ($(this).val() > 0){
                                                //console.log($(this).val());
                                    
                                                $("#row_f_"+$(this).val()).show();
                                            }
                                        }
                                        

                                    }
                                });
                                if (allCheckStatus){
                                    $("#filialsAll").prop("checked", true);
                                    
                                }
                            }
                        }
                        
                        calculateGiveoutCashAll();
                        
                    });
                    
                    
                    //Функция посчета сумм по филиалам, которые выбраны
                    function calculateGiveoutCashAll(){
                        //Обнулим сумму
                        $(".typeSumm").each(function() {
                            $(this).html(0);
                        });
                        
                        $(".filialChoice").each(function() {
                            if ($(this).attr("id") != "filialsAll"){
                                //Если отметка стоит
                                if ($(this).is(":checked")){
                                    //console.log("-------------------------");
                                    //console.log($(this).val());
                                    
                                    $(".filialSumm_"+$(this).val()).each(function() {
                                        //console.log($(this).html());
                                        //console.log(Number($(this).html()));
                                        
                                        //console.log("*******");
                                        var type = $(this).attr("type_id");
                                        //console.log(type);
                                        
                                        var typeSumm = Number($("#typeSumm_"+type).html());
                                        //console.log(type + " => " + Number($(this).html()) + " / " + typeSumm);
                                        
                                        //var tempSumm = typeSumm + Number($(this).html());
                                        
                                        $("#typeSumm_"+type).html(number_format(typeSumm + Number($(this).html()), 2, ".", ""));
        
//                                        if (type == 0){
//                                            console.log(typeSumm + " + " + Number($(this).html()) + " = " + tempSumm);
//                                            console.log("+++++++++++++++++++++++++++++");
//                                            console.log($("#typeSumm_"+type).html());
//                                        }
                                        
                                     });

                                    
                                    //$("#summ_"+type).html();
                                }
                            }
                        });
                    }
                
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>