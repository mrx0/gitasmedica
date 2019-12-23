<?php

//clients_birth_search.php
//Список пациентов у кого др в указанный месяц

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'variables.php';

			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список пациентов, у которых ДР в указанный месяц</h1>';

			if (isset($_GET['m'])){
                $month = $_GET['m'];
			}else{
                $month = '01';
			}

			echo '
				</header>';


			//Выбираем пациентов
            $clients_j = array();

            $msql_cnnct = ConnectToDB ('config');

            $query = "SELECT * FROM `spr_clients` WHERE MONTH(`birthday2`) = '{$month}' AND `birthday` <> '-1577934000' AND `birthday` <> '-1577923200'  ORDER BY `full_name` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    array_push($clients_j, $arr);
                }
            }
            //var_dump($clients_j);

            CloseDB ($msql_cnnct);


            echo '				
                        <div id="errrror"></div>';

            //Выбор месяц
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Месяц: ';
            echo '
                        <span id="getThisCalendar">
                            <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == (int)$month){
                    $selected = 'selected';
                }
                echo '
                                <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }

            echo '
                            </select>
                        </span>
                        <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisDate(\'clients_birth_search.php?m='. $month . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Показать</span>
                        <div style="font-size: 90%; color: rgb(125, 125, 125); float: right;">Сегодня: <a href="clients_birth_search.php?m='.date("m").'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>';

			if (!empty($clients_j)){
				echo '
				
					<div id="data">
					    <span>
					    Всего: '.$number.'
					    </span>
                        <table>
							<tr class="cellsBlock" style="font-weight:bold;">	
								<td class="cellFullName" style="text-align: center">
                                    Полное имя';
                //echo $block_fast_filter;
                echo '
								</td>';

//                echo '
//								<td class="cellCosmAct" style="text-align: center" title="Страховое">Стр.</td>';

				echo '
								<td class="cellCosmAct" style="text-align: center; font-size: 120%;"><i class="fa fa-info" aria-hidden="true"></i></td>
								<td class="cellCosmAct" style="text-align: center">Пол</td>
								<td class="cellTime" style="text-align: center">Д. рож.</td>
								<td class="cellCosmAct" style="text-align: center">Лет</td>
								<td class="cellFullName" style="text-align: center">Контакты</td>
								<td class="cellText" style="text-align: center">Комментарий</td>
							</tr>';

				for ($i = 0; $i < count($clients_j); $i++) { 

                    $bgcolor = '';

                    echo '
							<tr class="cellsBlock cellsBlockHover" style="'.$bgcolor.'">
								<td class="cellFullName">'.$clients_j[$i]['full_name'].'</td>';
                    //var_dump($clients_j[$i]['id']);
								
//                    echo '
//								<td class="cellCosmAct" style="text-align: center">';
//                    if (($clients_j[$i]['polis'] != '') && ($clients_j[$i]['insure'] != '')){
//                        echo '<img src="img/insured.png" title="Страховое">';
//                    }
//
//                    echo '
//                                </td>';

                    echo '
                                <td class="cellCosmAct" style="text-align: center; font-size: 120%;">
                                    <a href="client.php?id='.$clients_j[$i]['id'].'" class="ahref" style="color: rgb(0, 176, 255);" target="_blank" rel="nofollow noopener"><i class="fa fa-user-secret" aria-hidden="true"></i></a>
                                </td>';

                    echo '
                                <td class="cellCosmAct" style="text-align: center">';
                    if ($clients_j[$i]['sex'] != 0){
                        if ($clients_j[$i]['sex'] == 1){
                            echo 'М';
                        }
                        if ($clients_j[$i]['sex'] == 2){
                            echo 'Ж';
                        }
                    }else{
                        echo '-';
                    }

                    echo '
                                </td>';

                    echo '
                                <td class="cellTime" style="text-align: center">';
                    if ($clients_j[$i]['birthday2'] == '0000-00-00'){
                        echo 'не указана';
                    }else{
                        echo date('d.m.Y', strtotime($clients_j[$i]['birthday2']));
                    }
                    echo '				
                                </td>';
                    echo '
                                <td class="cellCosmAct" style="text-align: center">';
                    if ($clients_j[$i]['birthday2'] == '0000-00-00'){
                        echo '-';
                    }else{
                        echo '<b>'.getyeardiff(strtotime($clients_j[$i]['birthday2']), 0).'</b>';
                    }
                    echo '
                                </td>';
									
                    echo '
                                <td class="cellFullName">'.$clients_j[$i]['telephone'];
                    if ($clients_j[$i]['htelephone'] != ''){
                        echo '
										дом. '.$clients_j[$i]['htelephone'].'';
                    }
                    if ($clients_j[$i]['telephoneo'] != ''){
                        echo '
										тел.оп. '.$clients_j[$i]['telephoneo'].'';
                    }
                    if ($clients_j[$i]['htelephoneo'] != ''){
                        echo '
										дом.тел.оп. '.$clients_j[$i]['htelephoneo'].'';
                    }
                    echo '
                                </td>
                                <td class="cellText">'.$clients_j[$i]['comment'].'</td>
                            </tr>';
								
								

				}

				echo '</table>';

			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			echo '
					<div id="doc_title">Пациенты (выборка по месяцу рождения) - Асмедика</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>