<?php

//individuals2.php
//Индивидуальные занятия для админов

	require_once 'header.php';
    require_once 'blocks_dom.php';

	//var_dump($_SESSION);
//	var_dump($_GET);
	//var_dump($_SESSION['calculate_data']);

	if ($enter_ok){
		require_once 'header_tags.php';

		//include_once 'DBWork.php';
        include_once('DBWorkPDO.php');
		include_once 'functions.php';

        include_once 'variables.php';

        //!!!Массив тех, кому видно по умолчанию, потому надо будет вывести это в базу или в другой файл
        $permissionsWhoCanSee_arr = array(2, 3, 9);

        //Деление на странички пагинатор paginator
        $limit_pos[0] = 0;
        $limit_pos[1] = 50;
        $pages = 0;
        $dop = '';
        $dop_link_str = '';
        //Ссылка страницы для пагинатора
        $link = 'individuals2.php';
        //Для чекбокса выбора данных за последний месяц
        $check_date = '';
        //Для фио исполнителя
        $worker = '';

        $db = new DB();

        if (isset($_GET['page'])){
            $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
        }else{
            $_GET['page'] = 1;
        }

        $individual = '';
        $dopQuery = $dop = 'WHERE TRUE';

        $args = [];

        //За период
        if (isset($_GET['date'])) {
            if ($_GET['date'] > 0) {
                $args['data_start'] = date('Y-m-d', strtotime(' -6 month')) . ' 00:00:01';
                $args['data_end'] = date('Y-m-d 23:59:59');
//                var_dump($args['data_start']);
//                var_dump($args['data_end']);
//                var_dump($args);

                $dopQuery .= ' AND j_ind.create_time BETWEEN :data_start AND :data_end';
                $dop .= ' AND `create_time` BETWEEN "' . $args['data_start'] . '" AND "' . $args['data_end'] . '"';

                $dop_link_str .= '&date=' . $_GET['date'];

                //$check_date = 'checked';
            }
        }else{
            $_GET['date'] = 0;
        }

        //Только по одному сотруднику
        if (isset($_GET['worker_id'])) {
            if($_GET['worker_id'] != 0) {
                $args['worker_id'] = $_GET['worker_id'];

                $dopQuery .= " AND j_ind.worker_id = :worker_id";
                $dop .= " AND worker_id = '{$_GET['worker_id']}'";

                $dop_link_str .= '&worker_id=' . $_GET['worker_id'];
            }

        }
        //var_dump($dop);

        if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {
            //$query_dop = '';
        }else{
            //$query_dop = 'AND (j_ind.author_id	 = '.$_SESSION['id'].' OR j_ind.student_id	 = '.$_SESSION['id'].')';
            $dopQuery .= ' AND (j_ind.create_person	 = '.$_SESSION['id'].')';
            $dop .= ' AND create_person	 = '.$_SESSION['id'].'';
        }

        //Выборка
        $query = "SELECT j_ind.*
            FROM `journal_individuals2` j_ind
            {$dopQuery}
            ORDER BY j_ind.date DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";

//        var_dump($query);
//        var_dump($args);

        $tasks_j = $db::getRows($query, $args);
        //var_dump($tasks_j);

        echo '
			<header style="margin-bottom: 1px;">
                <div class="nav">
                    <a href="univer.php" class="b">UNIVER главная</a>
                    <a href="individuals.php" class="b">Индивидуальные занятия</a>
                </div>
				<h1>Работа с админами</h1>';
			echo '
			</header>';


        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
            echo '<a href="individuals2_add.php" class="b">Добавить</a><br>';
        }
			
        echo '
					<a href="individuals2.php" class="paginator_btn">Все записи / сбросить фильтры</a>';

        echo '
            <div id="infoDiv" style="display: none; position: absolute; z-index: 2; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
            </div>

			<div id="data" style="margin-top: 5px">';

        if (!isset($_GET['worker_id'])) {
            echo '
                            <!--<div class="no_print"> -->
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 3px 5px 3px; /*width: 420px*/; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
								
								<li style="margin-bottom: 3px;">
									Фильтр
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Данные за:
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div style="vertical-align: middle; color: #333;">
                                            <input id="date" name="date" value="0" ', $_GET['date'] == 0 ? 'checked': '',' type="radio"> всё время<br>
                                            <input id="date" name="date" value="1" ', $_GET['date'] == 1 ? 'checked': '',' type="radio"> последний месяц  <br>
											<input id="date" name="date" value="3" ', $_GET['date'] == 3 ? 'checked': '',' type="radio"> 3 месяца <br>
											<input id="date" name="date" value="6" ', $_GET['date'] == 6 ? 'checked': '',' type="radio"> полгода <br>
										</div>
									</div>
								</li>
                                <!--<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
                                        ФИО<br>
<!--										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
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
								</li>-->';

            echo '
                        <div class="no_print"> 
						<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_individuals2()">
						</div>';

            echo '</ul>';
        }

        //if (!isset($_GET['worker_id'])) {
            //Пагинатор
            echo paginationCreate2($limit_pos[1], $_GET['page'], 'journal_individuals2', $link, $db, $dop, $dop_link_str);
        //}


//        echo '
//					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 1;">';
//
//        //echo $block_fast_search_client;
//
//        echo '
//					</div>';

//        $stocks_str = '';
//        $news_str = '';
//        $warning_str = '';

        if (!empty($tasks_j)){

            echo '
                            <table width="100%" style="/*border: 1px solid #BEBEBE;*/ margin:5px; font-size: 11px;">
                                <tr style="font-weight: bold; font-size: 11px;">
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">Дата</td>
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">ФИО</td>
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">План работы</td>
                                    <td colspan="2" style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">Ошибки<br> кол-во принятых звонков/ замечание по звонку</td>
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">Работа с  пациентами в холле</td>
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">Коррекция ошибок</td>
                                    <td style="border: 1px solid #BEBEBE; padding: 2px 10px; text-align: center;">Статистика звонков</td>
                                </tr>';

            foreach ($tasks_j as $task) {
//                var_dump($task);

                $bgColor = '';
                if ($task['status'] == 9){
                    $bgColor = "background-color: #8C8C8C;";
                }
                echo '
                            <tr class="cellsBlockHover">
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.explode('-', $task['date'])[2].'.'.explode('-', $task['date'])[1].'.'.explode('-', $task['date'])[0].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;"><a href="?&worker_id='.$task['worker_id'].'" class="ahref" style="">'.WriteSearchUser('spr_workers', $task['worker_id'], 'user', false).'</a></td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['plan'].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['rings_count'].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['rings_review'].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['work_w_patients'].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['error_correction'].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.$task['ring_stat'].'</td>
                            </tr>';
            }

            echo '</table>';
        }

        echo '	
			
			    <div id="doc_title">Работа с админами - Асмедика</div>
				</div>';
		
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>