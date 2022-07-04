<?php

//reviews.php
//Отзывы

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || $god_mode){

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
            $link = 'reviews.php';
            //Для чекбокса выбора данных за последний месяц
            $check_date = '';
            //Для фио исполнителя
            $worker = '';

            $filials_j = getAllFilials(true, false, false);
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

                $dopQuery .= ' AND j_pc.date BETWEEN :data_start AND :data_end';
                $dop .=  ' AND `date` BETWEEN "'.$args['data_start'].'" AND "'.$args['data_end'].'"';

                $dop_link_str .= '&date='.$_GET['date'];

                $check_date = 'checked';
            }
//            var_dump($dop);

			echo '
				<header>
					<h1>Отзывы ';

            echo $individual;

            echo '
                    </h1>
				</header>';

                echo '
					<a href="review_add.php" class="b">Добавить</a>';

			echo '
                    <div id="data" style="margin-top: 5px;">';

//            if (!isset($_GET['client_id'])) {
//                echo '
//                            <!--<div class="no_print"> -->
//							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 3px 5px 3px; /*width: 420px*/; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
//
//								<li style="margin-bottom: 3px;">
//									Фильтр
//								</li>
//								<li class="filterBlock">
//									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
//										Данные за последний месяц
//									</div>
//									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
//										<!--<div style="margin-bottom: 10px;">
//											C <input type="text" id="datastart" name="datastart" class="dateс" value="' . date("01.m.Y") . '" onfocus="this.select();_Calendar.lcs(this)"
//												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
//											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="' . date("d.m.Y") . '" onfocus="this.select();_Calendar.lcs(this)"
//												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
//										</div>-->
//										<div style="vertical-align: middle; color: #333;">
//											<input type="checkbox" name="all_time" value="1" '.$check_date.'> <span style="font-size:80%;"></span>
//										</div>
//									</div>
//								</li>
//                                <li class="filterBlock">
//									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
//                                        Кто внёс запись<br>
//										<!--<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>-->
//									</div>
//									<div class="filtercellRight" style="width: 245px; min-width: 245px;">';
//                if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {
//                    echo '
//                                        <input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="'.$worker.'" class="who4" autocomplete="off">
//										<ul id="search_result4" class="search_result4"></ul><br />';
//                } else {
//                    echo WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false) . '
//                                        <input type="hidden" id="search_client4" name="searchdata4" value="' . WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false) . '">';
//                }
//                echo '
//									</div>
//								</li>';
//
//                echo '
//                        <div class="no_print">
//						<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_phone_calls()">
//						</div>';
//
//                echo '</ul>';
//            }

            if (!isset($_GET['client_id'])) {
                //Пагинатор
                echo paginationCreate2($limit_pos[1], $_GET['page'], 'journal_reviews', $link, $db, $dop, $dop_link_str);
            }

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3 " style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellTime" style="font-size: 80%; text-align: center; width: 100px; min-width: 100px;">Дата';
//            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellOffice" style="font-size: 80%; text-align: center;">Филиал</div>
							<div class="cellFullName" style="font-size: 80%; text-align: center;">ФИО врача</div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Отзыв</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Добавил</div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Сайты</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center;">-</div>
						</li>';

            $query = "
            SELECT j_r.*, s_w.name AS worker_name
            FROM `journal_reviews` j_r 
            LEFT JOIN `spr_workers` s_w ON s_w.id = j_r.added_person
            $dopQuery
            ORDER BY j_r.date DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
//            var_dump($query);

            //Выбрать все
            $reviews_j = $db::getRows($query, $args);
            //var_dump($reviews_j);
			
			if (!empty($reviews_j)){
			    //var_dump($rreviews_j);

				for ($i = 0; $i < count($reviews_j); $i++) {

				    //Метка
                    $review_status_mark = '';
                    //Массив для подсчёта
//                    $res =

					if ($reviews_j[$i]['status'] == 0) {
                        $review_status_mark = '<i class="fa fa-minus" style="color: red; font-size: 160%;" title=""></i>';
                    }elseif ($reviews_j[$i]['status'] == 1){
                        $review_status_mark = '<i class="fa fa-plus" style="color: green; font-size: 160%;" title=""></i>';
					}else{
                        $review_status_mark = '?';
					}


					echo '
							<li class="cellsBlock3 cellsBlockHover" style="">
								<div class="cellTime" style="font-size: 70%; text-align: center;">'.date('d.m.Y', strtotime($reviews_j[$i]['date'])).'</div>
								<div class="cellOffice" style="font-size: 70%; text-align: center;">'.$filials_j[$reviews_j[$i]['filial_id']]['name2'].'</div>
								<div class="cellFullName" style="text-align: right; font-size: 70%;">'.WriteSearchUser('spr_workers', $reviews_j[$i]['worker_id'], 'user_full', false).'</div>
								<div class="cellText" style="font-size: 70%; text-align: left;">'.$reviews_j[$i]['review_text'].'</div>
								<div class="cellTime" style="text-align: right; font-size: 70%;">'.$reviews_j[$i]['worker_name'].'</div>
								<div class="cellText" style="text-align: left; font-size: 70%;">'.$reviews_j[$i]['sites'].'</div>
                                <div class="cellCosmAct" style="text-align: center; font-size: 70%; font-style: italic;">'.$review_status_mark.'</div>
							</li>';
				}
			}

			echo '
					</ul>
					
					<div id="doc_title">Отзывы</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>