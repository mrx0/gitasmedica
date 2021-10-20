<?php

//phone_calls.php
//Статистика обзвона

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($zapis['see_all'] == 1) || $god_mode){

            include_once 'functions.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 40;
            $pages = 0;
            $dop = '';
            $dop_link_str = '';
            //Ссылка страницы для пагинатора
            $link = 'phone_calls.php';
            //Для чекбокса выбора данных за последний месяц
            $check_date = '';
            //Для фио исполнителя
            $worker = '';

            //$filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();
            $db = new DB();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }else{
                $_GET['page'] = 1;
            }

            $individual = '';
            $dopQuery = $dop = 'WHERE TRUE';

            $args = [];

            if (isset($_GET['client_id'])) {
                $args['client_id'] = $_GET['client_id'];

                $dopQuery .= ' AND j_pc.client_id = :client_id';
                $dop .=  ' AND client_id = '.$_GET['client_id'];

                $individual = 'пациенту: '.WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full', false);
                $dop_link_str .= '&client_id='.$_GET['client_id'];
            }

            if (isset($_GET['create_person'])) {
                $create_person = SelDataFromDB ('spr_workers', $_GET['create_person'], 'worker_full_name');
                //var_dump($create_person);

                if($create_person != 0) {
                    $args['create_person'] = $create_person[0]['id'];

                    $dopQuery .= ' AND j_pc.create_person = :create_person';
                    $dop .= ' AND create_person = ' . $create_person[0]['id'];

                    $dop_link_str .= '&create_person=' . $create_person[0]['id'];

                    $worker = $_GET['create_person'];
                }
            }

            if (isset($_GET['date'])) {
                $args['data_start'] = date('Y-m-d', strtotime(date('Y-m-d', strtotime($_GET['date'])).' -1 month')).' 00:00:01';
                $args['data_end'] = explode('.', $_GET['date'])[2].'-'.explode('.', $_GET['date'])[1].'-'.explode('.', $_GET['date'])[0].' 23:59:59';
//                var_dump($args);

                $dopQuery .= ' AND j_pc.create_time BETWEEN :data_start AND :data_end';
                $dop .=  ' AND `create_time` BETWEEN "'.$args['data_start'].'" AND "'.$args['data_end'].'"';

                $dop_link_str .= '&date='.$_GET['date'];

                $check_date = 'checked';
            }
//            var_dump($dop);

			echo '
				<header>
					<h1>Статистика звонков ';

            echo $individual;

            echo '
                    </h1>
				</header>';

            //if (isset($_GET['client_id'])) {
                echo '
					<a href="phone_calls.php" class="b">Все звонки</a>';
            //}
			echo '
                    <div id="data" style="margin-top: 5px;">';

            if (!isset($_GET['client_id'])) {
                echo '
                            <!--<div class="no_print"> -->
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 3px 5px 3px; /*width: 420px*/; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
								
								<li style="margin-bottom: 3px;">
									Фильтр
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Данные за последний месяц
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<!--<div style="margin-bottom: 10px;">
											C <input type="text" id="datastart" name="datastart" class="dateс" value="' . date("01.m.Y") . '" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="' . date("d.m.Y") . '" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
										</div>-->
										<div style="vertical-align: middle; color: #333;">
											<input type="checkbox" name="all_time" value="1" '.$check_date.'> <span style="font-size:80%;"></span>
										</div>
									</div>
								</li>
                                <li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
                                        Кто внёс запись<br>
										<!--<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>-->
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">';
                if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {
                    echo '
                                        <input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="'.$worker.'" class="who4" autocomplete="off">
										<ul id="search_result4" class="search_result4"></ul><br />';
                } else {
                    echo WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false) . '
                                        <input type="hidden" id="search_client4" name="searchdata4" value="' . WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false) . '">';
                }
                echo '
									</div>
								</li>';

                echo '
                        <div class="no_print"> 
						<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_phone_calls()">
						</div>';

                echo '</ul>';
            }

            if (!isset($_GET['client_id'])) {
                //Пагинатор
                echo paginationCreate2($limit_pos[1], $_GET['page'], 'journal_phone_calling', $link, $db, $dop, $dop_link_str);
            }

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3 " style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellTime" style="font-size: 80%; text-align: center"></div>
							<div class="cellOffice" style="font-size: 80%; text-align: center; width: 100px; min-width: 100px;">Дата звонка';
//            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellFullName" style="font-size: 80%; text-align: center;">Пациент</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Контакты</div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Комментарий</div>
							<div class="cellTime" style="font-size: 80%; text-align: center; width: 150px; min-width: 150px;">Добавил</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Время доб.</div>
						</li>';

            $query = "
            SELECT j_pc.*, s_c.full_name, s_c.telephone, s_c.htelephone, s_c.telephoneo, s_c.htelephoneo, s_w.name AS worker_name
            FROM `journal_phone_calling` j_pc 
            LEFT JOIN `spr_clients` s_c ON s_c.id = j_pc.client_id
            LEFT JOIN `spr_workers` s_w ON s_w.id = j_pc.create_person
            $dopQuery
            ORDER BY j_pc.call_time DESC, j_pc.create_time DESC  LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
//            var_dump($query);

            //Выбрать все
            $calls_j = $db::getRows($query, $args);
            //var_dump($calls_j);
			
			if (!empty($calls_j)){
			    //var_dump($calls_j);

                //Соберем данные по типам абонементов
//                $abon_types_j = array();
//
//                $msql_cnnct = ConnectToDB ();
//
//                $query = "SELECT `id`, `name` FROM `spr_solar_abonements` WHERE `status` <> '9'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //array_push($abon_types_j, $arr);
//
//                        $abon_types_j[$arr['id']] = $arr;
//                    }
//                }
                //var_dump($abon_types_j);

				for ($i = 0; $i < count($calls_j); $i++) {

				    //Метка
                    $call_mark = '';
                    //Массив для подсчёта
//                    $res =

					if ($calls_j[$i]['status'] == 8) {
                        $call_mark = '<i class="fa fa-phone-square" style="color: red; font-size: 160%;" title="Не звонить"></i> Не звонить';
                    }elseif ($calls_j[$i]['status'] == 6){
                        $call_mark = '<i class="fa fa-phone-square" style="color: orange; font-size: 160%;" title="Не дозвонились"></i> Не дозвонились';
                    }elseif ($calls_j[$i]['status'] == 7){
                        $call_mark = '<i class="fa fa-phone-square" style="color: blue; font-size: 160%;" title="Записались"></i> Записались';
					}elseif ($calls_j[$i]['status'] == 5){
                        $call_mark = '<i class="fa fa-phone-square" style="color: #b35bff; font-size: 160%;" title="Перезвонить"></i> Перезвонить';
					}elseif ($calls_j[$i]['status'] == 4){
                        $call_mark = '<i class="fa fa-phone-square" style="color: #93021e; font-size: 160%;" title="Плохой отзыв"></i> Плохой отзыв';
					}elseif ($calls_j[$i]['status'] == 3){
                        $call_mark = '<i class="fa fa-phone-square" style="color: #b1ffad; font-size: 160%;" title="Хороший отзыв"></i> Хороший отзыв';
					}else{
                        $call_mark = '<i class="fa fa-phone-square" style="color: #dcdcdc; font-size: 160%;" title="Нет отметки"></i> Нет отметки';
					}

//                    $expired_color = '';
//                    $expired_txt = '';
//
//                    if (($calls_j[$i]['expires_time'] != '0000-00-00') && ($calls_j[$i]['status'] != 5)) {
//                        //время истечения срока годности
//                        $sd = $calls_j[$i]['expires_time'];
//                        //текущее
//                        $cd = date('Y-m-d', time());
//                        /*var_dump(strtotime($sd));
//                        var_dump(strtotime($cd));*/
//                        //сравнение не прошла ли гарантия
//                        if (strtotime($sd) > strtotime($cd)) {
//                            $expired_txt .= '';
//                        } else {
//                            $expired_color = 'background-color: rgba(239,47,55, .7)';
//                            $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
//                            $status = 'Истёк срок '.date('d.m.y', strtotime($calls_j[$i]['expires_time']));
//                        }
//
//                    }


					echo '
							<li class="cellsBlock3 cellsBlockHover" style="">
								<div class="cellTime" style="text-align: left; font-size: 70%; font-style: italic;">'.$call_mark.'</div>
								<div class="cellOffice" style="text-align: center; width: 100px; min-width: 100px; font-size: 80%;">'.date('d.m.Y', strtotime($calls_j[$i]['call_time'])).'</div>
								<a href="client.php?id='.$calls_j[$i]['client_id'].'" class="cellFullName ahref" style="text-align: left; font-size: 80%;" target="_blank" rel="nofollow noopener">'.$calls_j[$i]['full_name'].'</a>
								<div class="cellTime" style="text-align: left; font-size: 80%;">';

                    echo $calls_j[$i]['telephone'].''.$calls_j[$i]['htelephone'].''.$calls_j[$i]['telephoneo'].''.$calls_j[$i]['htelephoneo'];

					echo '
                                </div>
								<div class="cellText" style="text-align: left; font-size: 80%;">';

                    if ($calls_j[$i]['status'] == 5){
                        echo '<span style="/*color: red;*/">Перезвонить: '.date('d.m.Y', strtotime($calls_j[$i]['recall_date'])).'</span><br>';
                    }
					echo $calls_j[$i]['comment'];
                    echo '
                                </div>
                                <div class="cellTime" style="font-size: 80%; text-align: center; width: 150px; min-width: 150px;">'.$calls_j[$i]['worker_name'].'</div>
                                <div class="cellTime" style="font-size: 70%; text-align: center;">'.date('d.m.Y H:i', strtotime($calls_j[$i]['create_time'])).'</div>
							</li>';
				}
			}

			echo '
					</ul>
					
					<div id="doc_title">Статистика звонков</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>