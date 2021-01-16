<?php

//abonements.php 
//Список абонементов

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){

            include_once 'functions.php';

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 20;
            $pages = 0;
            $dop = '';
            $dop_link_str = '';

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //Для выделения кнопки
            $option1_color = 'background-color: #fff261;';
            $option2_color = '';
            $option3_color = '';
            $option4_color = '';
            $option5_color = '';

            $current_page = '';

            $msql_cnnct = ConnectToDB();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
                $current_page = 'page=' . $_GET['page'];
            }else{
                $_GET['page'] = 1;
            }


			echo '
				<header>
					<h1>Абонементы</h1>
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo $block_fast_search_abonement;

            echo '
					</div>';

		    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
				echo '
					<a href="abonement_add.php" class="b">Добавить</a>';
		    }

            if (isset($_GET['option'])) {
                //Проданные, не потраченные
                if ($_GET['option'] == 2) {
                    $dop = "WHERE `status`='7' AND `min_count` <> `debited_min` AND `expires_time` > '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=2';
                    $option1_color = '';
                    $option2_color = 'background-color: #fff261;';
                }
                //Проданные истёк срок
                if ($_GET['option'] == 3) {
                    $dop = "WHERE `status`='7' AND `min_count` <> `debited_min` AND `expires_time` < '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=3';
                    $option1_color = '';
                    $option3_color = 'background-color: #fff261;';
                }
                //Проданные, истёк срок, не потраченные
                if ($_GET['option'] == 5) {
                    $dop = "WHERE `status`='7' AND `min_count` <> `debited_min` AND `debited_min` <> '0' AND `expires_time` < '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=5';
                    $option1_color = '';
                    $option5_color = 'background-color: #fff261;';
                }
                //Закрытые
                if ($_GET['option'] == 4) {
                    $dop = "WHERE `status`='5' OR `min_count`=`debited_min`";
                    $dop_link_str = 'option=4';
                    $option1_color = '';
                    $option4_color = 'background-color: #fff261;';
                }
            }

			echo '
						<div id="data">';

            echo '
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите опцию просмотра</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?" class="b" style="' . $option1_color . '">Все</a>
								<a href="?&option=2" class="b" style="' . $option2_color . '">Проданные, не потраченные</a>
								<a href="?&option=3" class="b" style="' . $option3_color . '">Проданные, истёк срок</a>
								<!--<a href="?&option=5" class="b" style="' . $option5_color . '">Проданные, истёк срок, потраченные частично</a>-->
								<a href="?&option=4" class="b" style="' . $option4_color . '">Закрытые</a>
								<!--<a href="?&option=5" class="b" style="' . $option5_color . '"></a>-->
							</li>';

            //Пагинатор
            echo paginationCreate ($limit_pos[1], $_GET['page'], 'journal_abonement_solar', 'abonements.php', $msql_cnnct, $dop, $dop_link_str);

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3" style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellPriority" style="font-size: 80%; text-align: center"></div>
							<div class="cellOffice" style="font-size: 80%; text-align: center; width: 180px; min-width: 180px;">Номер';
            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellOffice" style="font-size: 80%; text-align: center;">Название</div>
							<div class="cellOffice" style="font-size: 80%; text-align: center;">Минут<br><span style="font-size: 80%;">(всего/потрачено/ост.)</span></div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Статус</div>
						</li>';
			
			include_once 'DBWork.php';

            $abonements_j = array();
//			$abonements_j = SelDataFromDB('journal_abonement_solar', '', $limit_pos);
//			var_dump ($abonements_j);

            $query = "SELECT * FROM `journal_abonement_solar` {$dop} ORDER BY `num` ASC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";

            if (isset($_GET['option'])) {
                if ($_GET['option'] == 2) {
                    $query = "SELECT * FROM `journal_abonement_solar` {$dop} ORDER BY `expires_time` ASC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 3) {
                    $query = "SELECT * FROM `journal_abonement_solar` {$dop} ORDER BY `expires_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 5) {
                    $query = "SELECT * FROM `journal_abonement_solar` {$dop} ORDER BY `expires_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 4) {
                    $query = "SELECT * FROM `journal_abonement_solar` {$dop} ORDER BY `cell_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
            }
//            var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($abonements_j, $arr);
                }

            }
            CloseDB ($msql_cnnct);
            //var_dump($abonements_j);

            if (!empty($abonements_j)){

                //Соберем данные по типам абонементов
                $abon_types_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT `id`, `name` FROM `spr_solar_abonements` WHERE `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($abon_types_j, $arr);

                        $abon_types_j[$arr['id']] = $arr;
                    }
                }
                //var_dump($abon_types_j);

				for ($i = 0; $i < count($abonements_j); $i++) {
//				    var_dump($abonements_j[$i]);
//				    var_dump($abonements_j[$i]['min_count'] == $abonements_j[$i]['debited_min']);

                    $status = '';

                    if(($abonements_j[$i]['status'] == 5) || ($abonements_j[$i]['min_count'] == $abonements_j[$i]['debited_min'])){
                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
//                        $status = 'Закрыт '.date('d.m.y H:i', strtotime($abonements_j[$i]['closed_time']));
                        $status = 'Закрыт';
                    }elseif ($abonements_j[$i]['status'] == 9) {
                        $back_color = 'background-color: rgba(161,161,161,1);';
                        $status = 'Удалён';

                    }elseif ($abonements_j[$i]['status'] == 7){
                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                        $status = '<div style="font-size: 90%;">Продан '.date('d.m.y H:i', strtotime($abonements_j[$i]['cell_time'])).'</div>';

                        $expires_time_color = '';

                        $expirestime1weekminus = date('Y-m-d', strtotime(date('Y-m-d', strtotime($abonements_j[$i]['expires_time'])).' -2 weeks'));
                        //var_dump($expirestime1weekminus);

                        if (date('Y-m-d', time()) > $expirestime1weekminus) {
                            $expires_time_color = 'color: rgb(236 62 62);';
                        }

                        $status .= '
                                    <div style="font-size: 85%; '.$expires_time_color.'"><b>Срок истекает: '.date('d.m.Y', strtotime($abonements_j[$i]['expires_time'])).'</b></div>';
                    }else{
                            $back_color = '';
					}

                    $expired_color = '';
                    $expired_txt = '';

                    if (($abonements_j[$i]['expires_time'] != '0000-00-00') && ($abonements_j[$i]['status'] != 5) && ($abonements_j[$i]['min_count'] != $abonements_j[$i]['debited_min'])) {
                        //время истечения срока годности
                        $sd = $abonements_j[$i]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        /*var_dump(strtotime($sd));
                        var_dump(strtotime($cd));*/
                        //сравнение не прошла ли гарантия
                        if (strtotime($sd) > strtotime($cd)) {
                            $expired_txt .= '';
                        } else {
                            $expired_color = 'background-color: rgba(239,47,55, .7)';
                            $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
                            $status = 'Истёк срок '.date('d.m.y', strtotime($abonements_j[$i]['expires_time']));
                        }

                    }


					echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="abonement.php?id='.$abonements_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; font-weight: bold; width: 180px; min-width: 180px;" id="4filter">'.$abonements_j[$i]['num'].'</a>
								<div class="cellOffice" style="text-align: left; font-size: 80%;">'.$abon_types_j[$abonements_j[$i]['abon_type']]['name'].'</div>
								<div class="cellOffice" style="text-align: right">';
//                    if (($abonements_j[$i]['status'] == 7) && ($abonements_j[$i]['status'] != '0000-00-00 00:00:00')) {
//                        echo ($abonements_j[$i]['nominal'] - $abonements_j[$i]['debited']).' руб.';
//                    }

                    echo $abonements_j[$i]['min_count'].' / '.$abonements_j[$i]['debited_min'].' / <span style="color:red; font-weight: bold;">'.($abonements_j[$i]['min_count'] - $abonements_j[$i]['debited_min']).'</span>';

                    echo '
                                 </div>';
                    echo '
								<div class="cellText" style="text-align: center;">'.$status.'';
                    if ($abonements_j[$i]['filial_id'] != 0) {

                        if (!empty($filials_j)) {
                            echo '<div style="font-size: 90%;"><i>'.$filials_j[$abonements_j[$i]['filial_id']]['name'].'</i></div>';
                        }else {
                            echo '-';
                        }
                    }
                    echo '
                                </div>';
                    echo '
							</li>';
				}
			}

			echo '
					</ul>
					
					<div id="doc_title">Абонементы</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>