<?php

//fl_tabels_simple_pay.php
//Проверка выплат по табелям...

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'widget_calendar.php';
			include_once 'variables.php';

            $filials_j = getAllFilials(false, false, false);
			//var_dump ($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);
			
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			$dop = '';
			$dopWho = '';
			$dopDate = '';
			$dopFilial = '';

			$typeQ = '';
			$filialQ = '';

            $type = 0;

            $getWho = array();

            $filial_id = 0;

            if (isset($_GET['filial'])) {
                $filial_id = $_GET['filial'];

            }else {
                if (isset($_SESSION['filial'])){
                    $filial_id = $_SESSION['filial'];
                } else {
                    $filial_id = 15;
                }
            }
            //var_dump($filial_id);

            if($filial_id != 0) {
                $filialQ = " AND fl_jt.office_id = '$filial_id'";
            }

			//тип (космет/стомат/...)
            if (isset($_GET['who'])) {
			    if ($_GET['who'] != 0) {
                    $getWho = returnGetWho($_GET['who'], 5, array(5, 6, 10, 4, 7, 13, 14, 15, 11));
                }
            }
            //var_dump($getWho);

			if (!empty($getWho)) {
                $who = $getWho['who'];
                $whose = $getWho['whose'];
                $selected_stom = $getWho['selected_stom'];
                $selected_cosm = $getWho['selected_cosm'];
                $datatable = $getWho['datatable'];
                $kabsForDoctor = $getWho['kabsForDoctor'];
                $type = $getWho['type'];

                $typeQ = " AND fl_jt.type = '$type' ";

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
            }else{
                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = '';
                $assist_color = '';
                $sanit_color = '';
                $ubor_color = '';
                $dvornik_color = '';
                $other_color = '';
                $all_color = 'background-color: #fff261;';

                $who = '';
            }

			if (isset($_GET['m']) && isset($_GET['y'])){
				//операции со временем						
				$month = $_GET['m'];
				$year = $_GET['y'];
			}else{
				//операции со временем						
				$month = date('m');		
				$year = date('Y');
			}

			foreach ($_GET as $key => $value){
				if (($key == 'd') || ($key == 'm') || ($key == 'y'))
					$dopDate  .= '&'.$key.'='.$value;
				if ($key == 'filial'){
                    if ($value != 0) {
                        $dopFilial .= '&' . $key . '=' . $value;
                        $dop .= '&' . $key . '=' . $value;
                    }
				}
				if ($key == 'who'){
				    if ($value != 0) {
                        $dopWho .= '&' . $key . '=' . $value;
                        $dop .= '&' . $key . '=' . $value;
                    }
				}
			}

            $msql_cnnct = ConnectToDB ();

			//Получаем табели
			$markSheduler = 0;

			$arr = array();
			$rez = array();

            $query = "SELECT fl_jt.*, sw.name FROM `fl_journal_tabels` fl_jt
                      LEFT JOIN `spr_workers` sw ON sw.id = fl_jt.worker_id
                      WHERE fl_jt.month = '$month' AND fl_jt.year = '$year'
                       AND fl_jt.status <> '9' 
                       AND ((fl_jt.status <> '7') 
                        OR ((fl_jt.summ + fl_jt.surcharge + fl_jt.night_smena + fl_jt.empty_smena - fl_jt.paidout - fl_jt.deduction) <> '0'))
                         ".$typeQ.$filialQ;

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					//Раскидываем в массив
					//$rez[$arr['day']][$arr['smena']][$arr['kab']] = $arr;
                    //array_push($rez, $arr);
                    //var_dump($arr);
                    if (!isset($rez[$arr['name']])){
                        $rez[$arr['name']] = array();
                    }

                    array_push($rez[$arr['name']], $arr);
				}
			}
			var_dump($rez);
			//var_dump($query);

            //Сортируем по имени
            ksort($rez);

			echo '
			
				<div id="status">
					<div class="no_print"> 
					<header>
						<div class="nav">
							<a href="fl_tabels.php" class="b">Важный отчёт</a>
							<a href="fl_tabels2.php" class="b">Отчёт по часам</a>
							<a href="fl_tabels_check.php?&filial='.$filial_id.'&m='.$month.'&y='.$year.''.$dopWho.'" class="b">Проверка табелей</a>
						</div>
						
						<h2>Проверка табелей 2</h2>
						
						<span style="font-size: 85%; color: #7D7D7D;">Отображаются только <span style="color: red;">не закрытые (не выплаченные до конца, не проведёные)</span> табели.</span>
					</header>';
			echo '
					</div>';
			echo '
					<div id="data" style="margin-top: 5px;">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
			echo '			
							<div class="no_print"> 
                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=0" class="b" style="'.$all_color.'">Все</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=15" class="b" style="'.$dvornik_color.'">Дворники</a>
                                        <a href="fl_tabels_simple_pay.php?'.$dopFilial.$dopDate.'&who=11" class="b" style="'.$other_color.'">Прочие</a>
                                </li>
                                <li style="width: auto; margin-bottom: 20px;">
                                    <div style="display: inline-block; margin-right: 20px;">
                                        <div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Филиалы
                                        </div>
                                        <div>
                                            <select name="SelectFilial" id="SelectFilial">
                                                <option value="0">Все</option>
											';
                foreach ($filials_j as $f_id => $filial_item){
					$selected = '';

						if ($f_id == $filial_id){
							$selected = 'selected';
						}

					echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
				}
			echo '
                                            </select>
                                        </div>
                                    </div>
                                    <!--<div style="display: inline-block; margin-right: 20px;">
    
                                        <div style="display: inline-block; margin-right: 20px;">
                                            <a href="?'.$dopWho.$dopFilial.'" class="dotyel" style="font-size: 70%;">Сбросить</a>
                                        </div>
                                    </div>-->
                                </li>
                                
                            </div>';
								
			echo '<div class="no_print">';
			echo widget_calendar ($month, $year, 'fl_tabels_simple_pay.php', $dop);
			echo '</div>';
			
			echo '
                        </ul>';
			

	
			echo '
                    </div>';

            if (true){
                foreach($rez as $worker_name => $workerData) {

                    //var_dump($worker_name);

                    foreach ($workerData as $rezData){

//                        if ($rezData['type'] != 777) {
                        if ((($rezData['type'] != 1) && ($rezData['type'] != 9) && ($rezData['type'] != 11) && ($rezData['type'] != 12) && ($rezData['type'] != 777)) ||
                            ($_SESSION['id'] == $rezData['worker_id']) || ($_SESSION['id'] == 270) || $god_mode || ($rezData['type'] == 777)
                        ){

                            //var_dump($rezData);

                            $bgcolor = "background-color: rgba(73, 252, 78, 0.17);";

                            if (($rezData['month'] == date('m')) && ($rezData['year'] == date('Y'))) {
                                $bgcolor = "background-color: rgba(73, 252, 78, 0.17);";
                            } else {
                                $bgcolor = "background-color: rgba(245, 4, 66, 0.17);";
                            }


                            //никак не используются
                            $invoice_rez_str = '';

                            //Общая сумма, которую начислили
                            $summItog = $rezData['summ'] + $rezData['surcharge'] + $rezData['night_smena'] + $rezData['empty_smena'];

                            //Если ассистент, то плюсуем сумму за РЛ
                            if ($rezData['type'] == 7) {
                                $summItog += $rezData['summ_calc'];
                            }
                            //var_dump(intval($summItog - $rezData['paidout'] - $rezData['deduction']));

                            if ((intval($summItog - $rezData['paidout'] - $rezData['deduction']) != 0) || ($rezData['status'] != 7)) {

                                echo '
                                            <div class="cellsBlockHover" style="width: 500px; ' . $bgcolor . ' border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                                <div style="width: 500px; box-shadow: -2px -1px 3px 1px rgb(137, 137, 137);">';
                                echo '
                                                    <a href="fl_tabel.php?id=' . $rezData['id'] . '" class="ahref">';

                                echo '
                                                        <div style="display: inline-block; vertical-align: top;">
                                                            <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                            </div>
                                                            <div style="display: inline-block; vertical-align: middle; font-size: 100%; width: 160px;">';


                                echo '<b><i>Табель #' . $rezData['id'] . '</i></b><br>';
                                echo '<span style="font-size: 77%; color: #7D7D7D;">' . $monthsName[$month], ' ', $year . '</span><br>';
                                echo '<i style="font-size: 80%;">' . WriteSearchUser('spr_workers', $rezData['worker_id'], 'user', false) . '</i><br>';
                                echo '<i style="font-size: 80%;">' , ($rezData['type'] == 777) ? '' : $permissions_j[$rezData['type']]['name'] , '</i> / ';
                                echo '<span style="font-size: 70%;">' . $filials_j[$rezData['office_id']]['name2'] . '</span>';
                                echo '
                                                            </div>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: top;">
                                                            <table style="width: 180px; border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 11px">
                                                                <tr>
                                                                    <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        Начислено:
                                                                    </td>
                                                                    <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        <span class="calculateOrder calculateCalculateN" style="font-size: 13px;">
                                                                            ' . intval($summItog) . '
                                                                        </span> руб.
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        Удержано: 
                                                                    </td>
                                                                    <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        <span class="calculateInvoice calculateCalculateN" style="font-size: 13px">
                                                                            ' . $rezData['deduction'] . '
                                                                        </span> руб.
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        Выплачено:
                                                                    </td>
                                                                    <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        <span class="calculateInvoice calculateCalculateN" style="font-size: 13px; color: rgb(12, 0, 167);">
                                                                            ' . $rezData['paidout'] . '
                                                                        </span> руб.
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        Осталось: 
                                                                    </td>
                                                                    <td style="text-align: right; border-bottom: 1px solid rgba(191, 188, 181, 0.4);">
                                                                        <div class="markForTabelDeploy" tabel_id="' . $rezData['id'] . '" rez_summ="' . intval($summItog - $rezData['paidout'] - $rezData['deduction']) . '" style="display: none;"></div>
                                                                        <span class="calculateInvoice calculateCalculateN" style="font-size: 13px">
                                                                            ' . intval($summItog - $rezData['paidout'] - $rezData['deduction']) . '
                                                                        </span> руб.
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        
                                                    </a>
                                                    ' . $invoice_rez_str . '
               
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px;">';
                                if (intval($summItog - $rezData['paidout'] - $rezData['deduction']) > 0) {
                                    echo '
                                                        <a href="fl_paidout_in_tabel_add.php?tabel_id=' . $rezData['id'] . '&type=7&filial_id=' . $filial_id . '&ref=' . urlencode('fl_tabels_simple_pay.php?&filial=' . $filial_id . '&m=' . $month . '&y=' . $year . '&who=' . $rezData['type']) . '" class="b" style = "font-size: 80%;">ЗП +</a>';
                                }
                                if ((intval($summItog - $rezData['paidout'] - $rezData['deduction']) == 0) && ($rezData['status'] == 0)) {
                                    echo '
                                                        <button class="b" style="font-size: 80%;" onclick="deployTabel(' . $rezData['id'] . ');">Провести</button>';
                                }
                                //var_dump($rezData['status']);
                                echo '
                                                    </div>
                                                    
                                                </div>';
                                if ($rezData['status'] == 7) {
                                    echo '
                                                <div style="display: inline-block; vertical-align: top; font-size: 180%;">
                                                    <!--<div style="border: 1px solid #CCC; padding: 3px; margin: 1px;">-->
                                                        <i class="fa fa-check" aria-hidden="true" style="color: green;" title="Проведён"></i>
                                                    <!--</div>-->
                                                </div>';

                                } else {
                                    //$notDeployCount++;
                                }

                                echo '
                                            </div>';
                            }
                        }
                    }
                }

                echo '
                                        <input type="hidden" name="ref" id="ref" value="fl_tabels_simple_pay.php?&filial=' . $filial_id . '&m=' . $month . '&y=' . $year . '&who=' . $type .'">';
            }


			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';



			echo '
					<script>
					
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
                                    if (key.indexOf("filial") == -1){
                                        get_data_str = get_data_str + "&" + key + "=" + params[key];
                                    }
                                }
                                //console.log(get_data_str);
							    
								document.location.href = "?filial="+$(this).val() + get_data_str;
								
							});
							
							$("#SelectDayW").change(function(){
							
							    blockWhileWaiting (true);
							    
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$dopWho.'";
							});
						});
						
					</script>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
		echo '
		    <div id="doc_title">Проверка табелей 2/',$monthsName[$month],' ',$year,' - Асмедика</div>';
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>