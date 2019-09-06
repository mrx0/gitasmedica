<?php

//report_zapis_daily.php
//Отчёт для ежедневной сверки

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
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

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
                $filial_id = 15;
                $have_target_filial = true;
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
                            <!--<a href="" class="b"></a>-->
                        </div>
                        <h2 style="">Отчёт - сверка</h2>
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


            echo '
                        </div>';

            if ($filial_id > 0) {
                //Календарик
                echo '
	
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
                            <div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="report_zapis_daily.php?filial_id=' . $filial_id . '" class="ahref">' . date("d") . ' ' . $monthsName[date("m")] . ' ' . date("Y") . '</a></div>
                            <div>
                                <span style="color: rgb(125, 125, 125);">
                                    Изменить дату:
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)" 
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                    <span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'report_zapis_daily.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
                                </span>
                            </div>
                        </li>';
            }

            //Если определён филиал
            if ($have_target_filial) {

                $rezult = array();

                $msql_cnnct = ConnectToDB ();

                //запись
                $zapis_j = array();

                $query = "SELECT * FROM `zapis` WHERE `office`='{$filial_id}' AND `year` = '{$year}' AND `month` = '{$month}'  AND `day` = '{$day}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //array_push($zapis_j, $arr);
                        if (!isset($zapis_j[$arr['type']])){
                            $zapis_j[$arr['type']] = array();
                        }
                        if (!isset($zapis_j[$arr['type']][$arr['worker']])){
                            $zapis_j[$arr['type']][$arr['worker']] = array();
                        }
                        array_push($zapis_j[$arr['type']][$arr['worker']], $arr);
                    }
                }
                //var_dump($zapis_j);
                //var_dump($zapis_j[10]);


                echo '<table border="1">';

                if (!empty($zapis_j)){

                    // !!! **** тест с записью
                    include_once 'showZapisRezult.php';

                    foreach ($zapis_j as $type => $type_data){
                        //var_dump($type_data);

                        echo '
                            <tr>
                                <td colspan="6">'.$permissions_j[$type]['name'].'</td>
                            </tr>';
                        foreach ($type_data as $worker_id => $worker_zapis_data) {
                            //var_dump($worker_zapis_data);

                            echo '
                            <tr>
                                <td colspan="6">'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</td>
                            </tr>';

                            foreach ($worker_zapis_data as $item) {
                                //Время начала - конца приема
                                $start_time_h = floor($item['start_time'] / 60);
                                $start_time_m = $item['start_time'] % 60;
                                if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
                                $end_time_h = floor(($item['start_time'] + $item['wt']) / 60);
                                if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
                                $end_time_m = ($item['start_time'] + $item['wt']) % 60;
                                if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;

                                echo '
                                <tr>
                                    <td style="width: 100px; font-size: 80%;">'.$start_time_h . ':' . $start_time_m . ' - ' . $end_time_h . ':' . $end_time_m.'</td>
                                    <td style="width: 180px;">'.WriteSearchUser('spr_clients', $item['patient'], 'user', false).'</td>
                                    <td style="width: 180px;">наряды (+типы работ)</td>
                                    <td style="width: 180px;">ордеры если были</td>
                                    <td style="width: 180px;">проведенные оплаты по наряду</td>
                                    <td style="width: 180px;">р/л + отметка врача</td>
                                </tr>';
                            }

                            echo '
                            <tr>
                                <td colspan="6">';

                            echo showZapisRezult($worker_zapis_data, false, false, false, false, false, false, 0, true, false);

                            echo '
                                </td>
                            </tr>';
                        }
                    }
                }else{
                    echo '<span style="color: red;">В записи ничего не найдено</span>';
                }


                echo '</table>';

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