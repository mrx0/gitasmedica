<?php

//fl_tabels_noch.php
//Важный отчёт ночь

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';
            include_once 'widget_calendar.php';

            $dop = '';
            $dopWho = '';
            $dopDate = '';
            $dopFilial = '';
            //$di = 0;

            //тип график (космет/стомат/...)
            $who = '&who=4';
            $whose = 'Администраторов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_admin';
            $type = 0;

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

            //Сегодняшняя дата
            $day = date("d");
            $cur_month = date("m");
            $cur_year = date("Y");

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

            $today = date("Y-m-d");

			$workers_j = array();

			//$offices_j = SelDataFromDB('spr_filials', '', '');
            //$permissions_j = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(false, true, true);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            $msql_cnnct = ConnectToDB ();

            if (!isset($_SESSION['fl_calcs_tabels'])){
                $_SESSION['fl_calcs_tabels'] = array();
            }

            //var_dump($_SESSION['fl_calcs_tabels']);

			if ($_POST){
			}else{
				echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>Важный отчёт ночь</h1>';
                echo '
                        <div>
						    <a href="fl_tabel_print_choice.php?type='.$type.'" class="b4">Печать пачки</a>
						</div>';
                echo '    
					</header>
					</div>';

				echo '
                    <div id="data" style="margin: 10px 0 0;">';
                echo '
					    <div id="errrror"></div>';
                echo '
                        <ul style="margin-left: 6px; margin-bottom: 20px;">
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <a href="fl_tabels.php?who=5" class="b" style="">Стоматологи</a>
                                <a href="fl_tabels.php?who=6" class="b" style="">Косметологи</a>
                                <a href="fl_tabels.php?who=10" class="b" style="">Специалисты</a>
                                <a href="fl_tabels2.php?who=4" class="b" style="">Администраторы</a>
                                <a href="fl_tabels2.php?who=7" class="b" style="">Ассистенты</a>
                                <a href="fl_tabels3.php?who=11" class="b" style="">Прочие</a>
                                <a href="fl_tabels_noch.php" class="b" style="background-color: #fff261;">Ночь</a>
                            </li>';

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_tabels_noch.php', $dop);
                echo '</div>';

                echo '
                        </ul>';


                $rezultShed = array();

                $query = "SELECT `day`, `filial`, `worker` FROM `scheduler` WHERE `smena`='3' AND `month` = '$month' AND `year` = '$year' ORDER BY `day`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //Раскидываем в массив
                        //array_push($rezultShed, $arr);

//                        if (!isset($rezultShed[$arr['day']])) {
//                            $rezultShed[$arr['day']] = array();
//                        }
//                        if (!isset($rezultShed[$arr['day']][$arr['worker']])) {
//                            $rezultShed[$arr['day']][$arr['worker']] = array();
//                        }

                        if (!isset($rezultShed[$arr['day']])) {
                            $rezultShed[$arr['day']] = array();
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial']])) {
                            $rezultShed[$arr['day']][$arr['filial']] = array();
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial']][$arr['worker']])) {
                            $rezultShed[$arr['day']][$arr['filial']][$arr['worker']] = array();
                        }
                        //$arr['in_shed'] = TRUE;
                        //array_push($rezultShed[$arr['day']], $arr);
                    }
                }
                //var_dump($rezultShed[2][15]);


                //Выберем данные по записи в ночные смены
                //$zapis_j = array();
                //$invoice_j = array();

                $query = "SELECT
                    z.office AS filial_id, z.worker AS worker_id, z.day,
                    ji.id, ji.summ, ji.summins, ji.paid, ji.status, ji.create_time, ji.create_person, ji.last_edit_time, ji.last_edit_person
                            FROM `zapis` z
                            LEFT JOIN `journal_invoice` ji ON ji.zapis_id = z.id
                            WHERE z.noch='1' AND z.enter='1' AND z.month='$month' AND z.year='$year';";

                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){

                        //Сравниваем полученные данные по записи с тем, что у нас есть по графику
                        //Дополняем данные, если есть соответствие
                        //Вставляем новые данные, если соответствия нет

                        //Метка, были ли эти данные в графике
                        $arr['in_shed'] = TRUE;

                        if (!isset($rezultShed[$arr['day']])){
                            $rezultShed[$arr['day']] = array();
                            $arr['in_shed'] = FALSE;
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']])) {
                            $rezultShed[$arr['day']][$arr['filial_id']] = array();
                            $arr['in_shed'] = FALSE;
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']])) {
                            $rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']] = array();
                            $arr['in_shed'] = FALSE;
                        }

                        array_push($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']], $arr);
                        //array_push($invoice_j, $arr);
                    }
                }
                //var_dump($rezultShed[2][15]);

                //Табличка с данными графика работы
                echo '
                        <table style="table-layout: fixed; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                            <tr class="<!--sticky f-sticky-->">
                                <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Дата</i></b></td>
                                <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Закрыто работ на сумму, руб.</i></td>
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>ФИО</i></td>
                                <!--<td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>% от выручки</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>К выплате, руб.</i></td>
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Ассистент</i></td>
                                <!--<td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>% от выручки</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>К выплате, руб.</i></td>
                                <!--<td style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;">-</td>-->
                                ';
                echo '
                            </tr>';


                if (!empty($rezultShed)) {

                    //Просто маркер для блоков показать/скрыть наряды
                    $markerInd = 0;

                    //Выводим дни, в которые у нас врачи были по графику
                    foreach ($rezultShed as $day => $day_data) {

                        if ((int)$day < 10) $day = '0'.(int)$day;
                        if ((int)$month < 10) $month = '0'.(int)$month;

                        echo '
                            <!--<tr class="cellsBlockHover workerItem" worker_id="$worker_data[id]" style="$bgColor">-->
                            <tr class="workerItem" worker_id="" style="box-shadow: 3px -2px 2px rgba(125, 125, 125, 0.27);">
                                <td colspan="8" style="background-color:rgba(234, 253, 194, 0.22); border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">
                                    '.$day.'.'.$month.'.'.$year.'
                                </td>
                            </tr>';


                        foreach ($day_data as $filial_id => $filial_data) {

                            //Выбрать ассистов по графику
                            //!!! попробовать переделать это потом, чтоб данные сразу брались в первом запросе
                            $assist_j = array();

                            $query = "SELECT `worker` FROM `scheduler` WHERE `type` = '7' AND `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `filial`='{$filial_id}'";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    if (!array_key_exists($arr['worker'], $assist_j)) {
                                        $assist_j[$arr['worker']] = WriteSearchUser('spr_workers', $arr['worker'], 'user_full', false);
                                    }
                                }
                            }
                            //var_dump($day . '.' . $month . '.' . $year);
                            //var_dump($assist_j);

                            foreach ($filial_data as $worker_id => $worker_data) {
                                //var_dump($worker_data);

                                $rezultInvoices = showInvoiceDivRezult($worker_data, true, true, true, false, false, false);
                                //var_dump($rezultInvoices);

                                $summ = 0;
                                $bgColor = '';
                                $notInShedStr = '';

                                foreach ($worker_data as $data) {
                                    //var_dump($data);
                                    //var_dump($data['in_shed']);

                                    //fl_tabels_noch.phpСумма нарядов
                                    $summ += $data['summ'] + $data['summins'];

                                    //var_dump($data['in_shed']);
                                    if (!$data['in_shed']){
                                        $bgColor = 'background-color: rgba(255, 85, 85, 0.25);';
                                        $notInShedStr = '<span style="color: red; font-size: 85%;">Ошибка #47. Нет в графике</span>';
                                    }

                                }

                                echo '
                                            <tr class="workerItem" worker_id="" style="box-shadow: 3px -2px 2px rgba(125, 125, 125, 0.27); '.$bgColor.'">
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;">
                                                    '.$notInShedStr.'
                                                </td>';

                                //Выручка
                                echo '
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                                    ' . $summ . '
                                                </td>';

                                echo '
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;">
                                                    <b>' . WriteSearchUser('spr_workers', $worker_id, 'user_full', false) . '</b><br><br>
                                                    <span style=""><a href="zapis.php?filial='.$filial_id.'&who=stom&d='.$day.'&m='.$month.'&y='.$year.'#tabs-3" class="ahref button_tiny">К записи</a></span>
                                                </td>';

                                //% от выручки
//                                echo '
//                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//                                                    %
//                                               </td>';

                                //Итого
                                echo '
                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                                    <b>17771</b>
                                               </td>';

                                //Ассистент
                                echo '
                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding-left: 5px; text-align: left;" colspan="3">';
                                if (!empty($assist_j)) {
                                    echo '
                                                    <table style="">';
                                    foreach ($assist_j as $assist_id => $full_name) {
                                        echo '
                                                        <tr class="">
                                                            <td style="width: 276px; border-bottom: 1px solid #BFBCB5; padding-left: 5px; text-align: left;">
                                                                <b>' . $full_name . '</b>
                                                            </td>';
//                                        //% от выручки
//                                        echo '
//                                                            <td style="width: 70px; border-bottom: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//                                                                %
//                                                            </td>';

                                        //Итого
                                        echo '
                                                            <td style="width: 70px; border-bottom: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                                                <b>7070</b>
                                                            </td>';
                                        echo '
                                                        </tr>';
                                    }

                                    echo '
                                                    </table>';
                                }else{
                                    echo '<span style="color: red; font-size: 85%;">Нет ассистентов в графике</span>';
                                }
                                echo'
                                                </td>';


                                //---
//                                        echo '
//                                               <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//
//                                               </td>';


                                echo '
                                            </tr>';

                                if ($rezultInvoices['count'] > 0) {
                                    echo '
                                            <tr class="workerItem" worker_id="" style="' . $bgColor . ' box-shadow: 2px 1px 4px rgba(125, 125, 125, 0.27);">   
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;" colspan="8">
                                                    <div id="allINvoicesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allINvoicesIsHere_'.$markerInd.'\');">показать/скрыть наряды</div>
                                                    <div id="allINvoicesIsHere_'.$markerInd.'" style="display: none;">
                                                    ' . $rezultInvoices['data'] . '
                                                    </div>
                                                </td>
                                            </tr>';
                                }

                                $markerInd++;
                            }

                            $markerInd++;
                        }

                        $markerInd++;
                    }
                }

                echo '
                        </table>';


                //!!! Дальше доделывать надо и то, что ниже - либо переделывать либо выкидывать

                //Процент с выручки для этого типа
                $revenue_percent_j = array();

                $arr = array();
                $rez = array();

                $query = "SELECT * FROM `fl_spr_revenue_percent` WHERE `permission` = '{$type}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($revenue_percent_j[$arr['filial_id']])){
                            $revenue_percent_j[$arr['filial_id']] = array();
                        }
                        if (!isset($revenue_percent_j[$arr['filial_id']][$arr['category']])){
                            $revenue_percent_j[$arr['filial_id']][$arr['category']] = array();
                        }
                        $revenue_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                    }
                }
                //var_dump($revenue_percent_j);


                echo '
                        </table>';
                echo '
                    </div>';

                echo '
		            <div id="doc_title">Важный отчёт ночь - Асмедика</div>';

				echo '

				<script type="text/javascript">

				$(document).ready(function() {
				    //Соберём выручку филиала
                    //fl_calculateZP ('.$month.', '.$year.',0);
				    //setTimeout(fl_getAllTabels ('.$month.', '.$year.', '.$type.'), 7000);
				    
				    
                    wait(function(runNext){

                        setTimeout(function(){
            
                            fl_calculateZP ('.$month.', '.$year.', '.$type.');
            
                            runNext();
            
                        }, 100);
            
                    }).wait(function(){
            
                        setTimeout(function(){
            
                            fl_getAllTabels ('.$month.', '.$year.', '.$type.')
            
                        }, 1000);
            
                       
            
                    });
				    
				    
                    //$.when(fl_calculateZP ('.$month.', '.$year.',0)).then(fl_getAllTabels ('.$month.', '.$year.', '.$type.'));  
                    
                    //!!! из файла fl_tabels.php
                    //посмотреть по ходу, надо ли это тут будет
				    var ids = "0_0_0";
				    var ids_arr = {};
				    var permission = 0;
				    var worker = 0;
				    var office = 0;


                    //Табели
				    $(".tableTabels").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getTabelsfunc (thisObj, certData);
                    });

				    //Необработанные расчеты
				    $(".tableDataNPaidCalcs").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getCalculatesfunc (thisObj, certData);
                    });
                    
				});
				
                
				</script>';
			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>