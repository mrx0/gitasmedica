<?php

//material_costs_test.php
//Расходы на материалы test

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			//include_once 'DBWork.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

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
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            //Филиал
            if (isset($_SESSION['filial'])) {
                $filial_id = $_SESSION['filial'];
                $have_target_filial = true;
            } else {
                $filial_id = 15;
            }

            if (($finances['see_all'] == 1) || $god_mode) {
                if (isset($_GET['filial_id'])) {
                    $filial_id = $_GET['filial_id'];
                    $have_target_filial = true;
                }
            }

            $dop = 'filial_id='.$filial_id;

//            if ($have_target_filial) {
//                $href_str = '?filial_id=' . $filial_id . '&m=' . $month . '&y=' . $year;
//                //var_dump($href_str);
//            }

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                        </div>
                        <h2 style="">Расходы на материалы по филиалам</h2>
                        <div>
						    <a href="material_cost_add_test.php?filial_id='.$filial_id.'" class="b">Добавить расход</a>
						</div>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';


            //$dop = '';

            echo '<div class="no_print">';
            echo widget_calendar ($month, $year, 'material_costs_test.php', $dop);
            echo '</div>';


            //Выбор филиала
            echo '
                        <div style="font-size: 90%; margin: 10px 0;">
                            Филиалы: <br>

            <select name="SelectFilial" id="SelectFilial">
							<option value="0" selected>Выберите филиал</option>';
            if (!empty($filials_j)){
                foreach($filials_j as $f_id => $filial_item){
                    $selected = '';
                    if ($f_id == $filial_id){
                        $selected = 'selected';
                    }
                    echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
                }
            }
				echo '
            </select>


                        </div>';


//            $msql_cnnct = ConnectToDB ();
//
//
//            //Типы расходов
//            $give_out_cash_types_j = array();
//
//            $query = "SELECT `id`,`name` FROM `spr_cashout_types`";
//            //var_dump($query);
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $number = mysqli_num_rows($res);
//            if ($number != 0){
//                while ($arr = mysqli_fetch_assoc($res)){
//                    $give_out_cash_types_j[$arr['id']] = $arr['name'];
//                }
//            }
//            //var_dump( $give_out_cash_types_j);

            //$msql_cnnct = ConnectToDB ();
            $db = new DB();

            //Выбрать все категории
            $query = "
            SELECT j_mc.*, fl_spr_perc.name AS cat_name 
            FROM `journal_material_costs_test` j_mc
            RIGHT JOIN `fl_spr_percents` fl_spr_perc
            ON fl_spr_perc.id = j_mc.category_id
            WHERE j_mc.filial_id = :filial_id AND j_mc.month = :month AND j_mc.year = :year
            ORDER BY j_mc.create_time";

            $args = [
                'month' => $month,
                'year' => $year,
                'filial_id' => $filial_id
            ];

            $material_costs_j = $db::getRows($query, $args);
            //var_dump($material_costs_j);

                //$data = $db::getRows("SELECT `title` FROM `category` WHERE `parent_id` > ?", [$parent_id]);
            //foreach ($data as $item) {
            //echo $item['title'].'<br>';
            //}


//            $rezult = array();
//
//            //Расходы, выдано из кассы
//            $giveoutcash_j = array();
//            //Суммы расходов по типам
//            $giveoutcash_summs = array();
//            //Сумма расходов
//            $giveoutcash_summ = 0;


//            //Поехали собирать расходные ордера
//            $query = "SELECT * FROM `journal_giveoutcash` WHERE
//            MONTH(`date_in`) = '$month' AND YEAR(`date_in`) = '$year' AND `status` <> '9'
//            ORDER BY `date_in` ASC";
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


//            if ($number != 0) {
//                while ($arr = mysqli_fetch_assoc($res)) {
//                    //var_dump($arr);
//
//                    if (!isset($giveoutcash_j[$arr['office_id']])) {
//                        $giveoutcash_j[$arr['office_id']] = array();
//                    }
//
//                    if (!isset($giveoutcash_j[$arr['office_id']][$arr['type']])) {
//                        $giveoutcash_j[$arr['office_id']][$arr['type']] = 0;
//                    }
//                    $giveoutcash_j[$arr['office_id']][$arr['type']] += $arr['summ'];
//
//                    //Cуммы по типам со всех филиалов
//                    if (!isset($giveoutcash_summs[$arr['type']])) {
//                        $giveoutcash_summs[$arr['type']] = 0;
//                    }
//                    $giveoutcash_summs[$arr['type']] += $arr['summ'];
//
//                }
//            }
            //var_dump($giveoutcash_j);
            //var_dump($giveoutcash_summs);

            if (!empty($material_costs_j)){

                echo '
                <table style="border: 1px solid #BFBCB5; margin: 5px; font-size: 90%;">';

                echo '
                    <tr style="text-align: center;">
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Месяц/Год</i></td>  
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Сумма</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Категория</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Филиал</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Дата создания<br>Автор</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 100%"><i class="fa fa-cog" title=""></i></td>
                    </tr>';

                foreach ($material_costs_j as $material_cost_data){

                    echo '
                        <tr class="cellsBlockHover">    
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.$monthsName[$material_cost_data['month']].' '.$material_cost_data['year'].'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.$material_cost_data['summ'].' руб.</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.$material_cost_data['cat_name'].'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.$filials_j[$material_cost_data['filial_id']]['name2'].'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%; text-align: right">
                                '.date('d.m.Y', strtotime($material_cost_data['create_time']."")).'<br>
                                '.WriteSearchUser('spr_workers', $material_cost_data['create_person'], 'user', true).'
                            </td>
                            <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; text-align: center; cursor: pointer;" onclick="Ajax_MaterialCostDelete('.$material_cost_data['id'].');"><i class="fa fa-times"  style="color: red;" title="Удалить"></i></td>
                        </tr>';
                }



                echo '</table>';
            }else{
                echo '<span style="color: red;">Ничего не найдено</span>';
            }

            echo '
                    </div>
                </div>
                <div id="doc_title">Расходы на материалы по филиалам - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';


            echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    if ($(this).val() != 0){
							        
							        blockWhileWaiting (true);
								    
                                    let get_data_str = "";

                                    let params = window
                                        .location
                                        .search
                                        .replace("?","")
                                        .split("&")
                                        .reduce(
                                            function(p,e){
                                                let a = e.split(\'=\');
                                                p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                                return p;
                                            },
                                            {}
                                        );
                                    //console.log(params);
                                                    
                                    for (let key in params) {
//                                        console.log(key.indexOf("filial_id"));
                                        if (key.length > 0){
                                            if (key.indexOf("filial_id") == -1){
                                                get_data_str = get_data_str + "&" + key + "=" + params[key];
                                            }
                                        }
                                    }
                                    //console.log(get_data_str);
                            
                                    //!!! window.location.href - это правильное использование
                                    window.location.href = "material_costs_test.php?filial_id="+$(this).val() + get_data_str;
								}
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