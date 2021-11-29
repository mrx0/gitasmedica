<?php

//univer.php
//UNIVER

	require_once 'header.php';
    require_once 'blocks_dom.php';

	//var_dump($_SESSION);
	//var_dump($_SESSION['calculate_data']);

	if ($enter_ok){
		require_once 'header_tags.php';

		//include_once 'DBWork.php';
        include_once('DBWorkPDO_2.php');
		include_once 'functions.php';

        include_once 'variables.php';

        //$announcing_arr = array();

		//$offices = SelDataFromDB('spr_filials', '', '');

        //!!!Массив тех, кому видно по умолчанию, потому надо будет вывести это в базу или в другой файл
        $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

        //Деление на странички пагинатор paginator
        $paginator_str = '';
        $limit_pos[0] = 0;
        $limit_pos[1] = 30;
        $pages = 0;

        //$show_option_str = " AND j_task.status <> '9' AND j_task.status <> '1'";
        $show_option_str = " AND j_task.status <> '9'";
        $show_option_str_for_paginator = 'show_option=allopen';
        $show_option_str_for_header = 'Все <span style="color: green">открытые</span>';

        if (isset($_GET)){

            $today = date('Y-m-d', time());
            $today3daysplus = date('Y-m-d', strtotime('+3 days'));
            //var_dump($today3daysplus);

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }

            $bgColor_all = '';
            $bgColor_allopen = '';
            $bgColor_excl = '';
            $bgColor_newtopic = '';
            $bgColor_done = '';
            $bgColor_deleted = '';
            $bgColor_person = '';

            //Че показываем
            if (isset($_GET['show_option'])){
                //Все кроме удалённых
                if ($_GET['show_option'] == 'all'){
                    $show_option_str = " AND j_task.status <> '9'";
                    $show_option_str_for_paginator = 'show_option=all';
                    $show_option_str_for_header = 'Все заявки <span style="color: green">(кроме удалённых)</span>';
                    $bgColor_all = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Все открытые
                if ($_GET['show_option'] == 'allopen'){
                    $show_option_str = " AND j_task.status <> '9' AND j_task.status <> '1'";
                    $show_option_str_for_paginator = 'show_option=allopen';
                    $show_option_str_for_header = 'Все <span style="color: green">открытые</span>';
                    $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Подходит к концу
                /*if ($_GET['show_option'] == 'excl2'){
                    $show_option_str = " AND j_task.status <> '9'";
                    $show_option_str_for_paginator = 'show_option=excl2';
                    $show_option_str_for_header = '<span style="color: red">Просроченные</span> заявки';
                }*/
                //Истёк срок
                if ($_GET['show_option'] == 'excl'){
                    $show_option_str = " AND j_task.status <> '9' AND j_task.status <> '1' AND j_task.plan_date < '{$today3daysplus}'";
                    $show_option_str_for_paginator = 'show_option=excl';
                    $show_option_str_for_header = '<span style="color: red">Просроченные и подходящие по сроку</span> заявки';
                    $bgColor_excl = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Изменения
                if ($_GET['show_option'] == 'newtopic'){
                    $show_option_str = " AND j_task.id NOT IN (SELECT `task_id` FROM `journal_tasks_readmark` jtask_rm2 WHERE j_task.id = jtask_rm2.task_id AND jtask_rm2.create_person = '{$_SESSION['id']}' AND jtask_rm2.status = '1')";
                    $show_option_str_for_paginator = 'show_option=newtopic';
                    $show_option_str_for_header = '<span style="color: forestgreen">Обновлённые</span> заявки';
                    $bgColor_newtopic = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Сделанные
                if ($_GET['show_option'] == 'done'){
                    $show_option_str = " AND j_task.status = '1'";
                    $show_option_str_for_paginator = 'show_option=done';
                    $show_option_str_for_header = '<span style="color: green">Завершенные</span> заявки';
                    $bgColor_done = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Удалённые
                if ($_GET['show_option'] == 'deleted'){
                    $show_option_str = " AND j_task.status = '9'";
                    $show_option_str_for_paginator = 'show_option=deleted';
                    $show_option_str_for_header = '<span style="color: darkslategrey">Удалённые</span> заявки';
                    $bgColor_deleted = 'background-color: rgba(0, 201, 255, 0.5)';
                }
                //Персональные
                if ($_GET['show_option'] == 'person'){
                    $show_option_str = " AND j_tasks_worker.worker_id = '{$_SESSION['id']}' AND j_task.status <> '9'";
                    $show_option_str_for_paginator = 'show_option=person';
                    $show_option_str_for_header = '<span style="color: rgba(124, 0, 255, 0.68);">Персональные</span> заявки';
                    $bgColor_person = 'background-color: rgba(0, 201, 255, 0.5)';
                }
            }else{
                $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
            }
        }

        $query_dop = '';

        //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
        if (($ticket['see_all'] != 1) && (!$god_mode)){
            $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `task_id` = j_task.id)";
        }

        //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
        if (($ticket['see_all'] != 1) && (!$god_mode)){
            if (isset($_SESSION['filial'])) {
                $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `task_id` = j_task.id)";
            }
        }

        //Надо выбрать те, которые относятся к конкретному сотруднику
        if (($ticket['see_all'] != 1) && (!$god_mode)){
            $query_dop .= " OR j_task.id IN (SELECT `task_id` FROM `journal_tasks_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `task_id` = j_task.id)";
        }

        //$msql_cnnct = ConnectToDB ();
        $db = new DB2();

        //Выборка не удалённых (j_task.status <> '9')
        //и плюс статус прочитан он данным сотрудником или нет
        //и плюс если текущий пользователь указан как исполнитель
        $query = "SELECT j_task.*, jtask_rm.status as read_status, j_tasks_worker.worker_id,
            GROUP_CONCAT(DISTINCT j_tasks_filial.filial_id ORDER BY j_tasks_filial.filial_id ASC SEPARATOR \",\") AS filials
            FROM `journal_tasks` j_task
            LEFT JOIN `journal_tasks_readmark` jtask_rm ON j_task.id = jtask_rm.task_id AND jtask_rm.create_person = '{$_SESSION['id']}'
            LEFT JOIN `journal_tasks_workers` j_tasks_worker ON j_task.id = j_tasks_worker.task_id AND j_tasks_worker.worker_id = '{$_SESSION['id']}'
            LEFT JOIN `journal_tasks_filial` j_tasks_filial ON j_tasks_filial.task_id = j_task.id
            WHERE (TRUE
            {$query_dop}
            OR j_task.create_person = '{$_SESSION['id']}')
            {$show_option_str} 
            
            GROUP BY `id` ORDER BY /*`plan_date` ASC,*/ `id` DESC";

        $tasks_j = $db::getRows($query, []);
//        var_dump($tasks_j);

        $filials_j = getAllFilials(false, true, true);

        echo '
			<header style="margin-bottom: 5px;">       
                 <div class="nav">
                    <a href="individuals.php" class="b">Индивидуальные занятия</a>
                </div>
				<h1>UNIVER</h1>'. '(тестовый режим)';
			echo '
			</header>
            
            <div id="infoDiv" style="display: none; position: absolute; z-index: 2; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
            </div>
			
			<div id="data">';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
            echo '<a href="univer_add.php" class="b">Добавить задание</a><br>';
        }


        echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 1;">';

        //echo $block_fast_search_client;

        echo '
					</div>';


//        $arr = array();
//        $rez = array();
//
//        //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
//        if ($_SESSION['permissions'] != 777) {
//            $query_dop = "AND j_ann.id IN (SELECT `annoncing_id` FROM `journal_announcing_worker` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `annoncing_id` = j_ann.id)";
//        }else{
//            $query_dop = '';
//        }
//
//        //Выборка объявлений не удалённых (j_ann.status <> '9')
//        //и плюс статус прочитан он данным сотрудником или нет
//        $query = "SELECT jann.*, jannrm.status AS read_status
//        FROM `journal_announcing_readmark` jannrm
//        RIGHT JOIN (
//          SELECT * FROM `journal_announcing` j_ann  WHERE j_ann.status <> '9' AND (j_ann.type = '1' OR j_ann.type = '2' OR j_ann.type = '3' OR j_ann.type = '4' OR j_ann.type = '5')
//          {$query_dop}
//        ) jann ON jann.id = jannrm.announcing_id
//        AND jannrm.create_person = '{$_SESSION['id']}'
//        ORDER BY `create_time` DESC";

//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//        $number = mysqli_num_rows($res);
//        if ($number != 0){
//            while ($arr = mysqli_fetch_assoc($res)){
//                array_push($announcing_arr, $arr);
//            }
//        }

//        $args = [
//
//        ];
//
//        $announcing_arr = $db::getRows($query, $args);

        //var_dump($announcing_arr);
        //var_dump($query);

        $stocks_str = '';
        $news_str = '';
        $warning_str = '';

        if (!empty($tasks_j)){

            foreach ($tasks_j as $task) {
                //var_dump($task);

                $temp_str = '';

                $annColor = '245, 245, 245';
                $annIco = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                $annColorAlpha = '0.9';
                $readStateClass = '';
                $newTopic = true;
                $topicTheme = nl2br($task['theme']);

                if ($task['type'] == 1){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-bullhorn" aria-hidden="true"></i>';
                    $annColorAlpha = '0.53';
                    if ($topicTheme == ''){
                        $topicTheme = 'Задание';
                    }
                }

                if ($task['type'] == 2){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Обновление';
                    }
                }

                if ($task['type'] == 3){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-book" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Инструкция';
                    }
                }

                if ($task['type'] == 4){
                    $annColor = '21, 209, 33';
                    $annIco = '<i class="fa fa-bolt" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Акция';
                    }
                }

                if ($task['type'] == 5){
                    $annColor = '255, 51, 51';
                    $annIco = '<i class="fa fa-bullhorn" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Важно!';
                    }
                }

                if ($task['read_status'] == 1){
                    if (($task['type'] != 4) && ($task['type'] != 5)) {
                        //$readStateClass = 'display: none;';
                    }
                    $newTopic = false;

                }

                if($task['status'] == 8){
                    $annColor = '162, 162, 162';
                    $annColorAlpha = '0.8';
                }

                $temp_str .= '                
                <div style="border: 1px dotted #CCC; margin: 0 1px 3px; padding: 10px 15px 0px; font-size: 80%; background-color: rgba('.$annColor.', '.$annColorAlpha.'); position: relative;">
                    <h2 class="';
                if ($newTopic) {
                    $temp_str .= 'blink1';
                }
                $temp_str .= '" style="height: 13px; border: 1px dotted #CCC; background-color: rgba(250, 250, 250, 0.9); width: 100%; padding: 6px 13px; margin: -9px 0 5px -14px; position: relative;">
                        <div style="position: absolute; top: 3px; left: 10px; font-size: 14px; color: rgba('.$annColor.', 1);  text-shadow: 1px 1px 3px rgb(0, 0, 0), 0 0 2px rgba(52, 152, 219, 1);">
                            '.$annIco.'
                        </div>
                                                
                        <div style="position: absolute; top: 5px; left: 35px; font-size: 11px;">';

                if($task['status'] == 8){
                    $temp_str .= '  <span style="color: rgb(239,22,22) ;font-weight:bold;">ЗАКРЫТО / ЗАВЕРШЕНО</span> ';
                }

                $temp_str .= '
                            <b>'.$topicTheme.'</b>
                        </div>
                              
                        <div style="position: absolute; top: 2px; right: 50px; font-size: 10px; text-align: right;">
                            Дата: '.date('d.m.y H:i' ,strtotime($task['create_time'])).'<br>
                            <span style="font-size: 10px; color: #716f6f;">Автор: '.WriteSearchUser('spr_workers', $task['create_person'], 'user', false).'</span>
                        </div>';

//                if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {
//                    if ($task['status'] == 8){
//                        $temp_str .= '
//                        <div style="position: absolute; top: 6px; right: 0px; text-align: right;">
//                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $task['id'] . ', 0);"><i class="fa fa-reply" aria-hidden="true" style="color: grey; " title="Вернуть"></i></span>
//                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $task['id'] . ', 9);"><i class="fa fa-trash-o" aria-hidden="true" title="Удалить"></i></span>
//                        </div>
//                        ';
//                    }elseif($task['status'] == 9){
//
//                    }else {
//                        $temp_str .= '
//                        <div style="position: absolute; top: 6px; right: 0px; text-align: right;">
//                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $task['id'] . ', 8);"><i class="fa fa-times" aria-hidden="true" style="color: red; " title="Закрыть"></i></span>
//                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $task['id'] . ', 9);"><i class="fa fa-trash-o" aria-hidden="true" title="Удалить"></i></span>
//                        </div>
//                        ';
//                    }
//                }

                $temp_str .= '
                    <div style="position: absolute; bottom: 0; left: 34px; font-size: 80%;';
                if ($newTopic) {
                    //$temp_str .= 'display:none;';
                }
                $temp_str .= '">';
//                if (($task['type'] != 4) && ($task['type'] != 5)) {
//                    $temp_str .= '
//                        <a href="" class="ahref showMeTopic" announcingID="' . $task['id'] . '">Развернуть</a>';
//                }
                $temp_str .= '
                    </div>';

//                $temp_str .= '
//                    </h2>
//                    <p id="topic_'.$task['id'].'" style="margin-bottom: 5px; '.$readStateClass.'">
//                            '.nl2br($task['descr']).'
//                        <a class="ahref b2" href="univer_task.php?id='.$task['id'].'">Перейти</a>
//                    </p>';

                $temp_str .= '     
                    </h2>
                    <p id="topic_'.$task['id'].'" style="margin-bottom: 5px; '.$readStateClass.'">
                        <a class="ahref b2" href="univer_task.php?id='.$task['id'].'">Перейти</a>
                    </p>';

//                if ($newTopic) {
//                    $temp_str .= '
//                    <div style="position: absolute; bottom: 0; right: 10px;">
//                        <button class="b iUnderstand" announcingID="' . $task['id'] . '">Ясно</button>
//                    </div>';
//                }


                 $temp_str .= '
                 </div>';

                //var_dump($task['status']);
                //var_dump($task['read_status']);


                if (($task['type'] == 1) || ($task['type'] == 2) || ($task['type'] == 3)){
                    $news_str .= $temp_str;
                }
                if ($task['type'] == 4){
                    $stocks_str .= $temp_str;
                }
                if ($task['type'] == 5){
                    $warning_str .= $temp_str;
                }
            }

            //Дни рождений
//            $births_str = '';
//            $today_birth_str = '';
//            $last_birth_str = '';
//
//            $day = date('d');
//            $month = date('m');
//            $year = date('Y');
//
//            $today = date('Y-m-d', time());
//            $lastMonthDate = date('Y-m-d', strtotime(' -3 days', gmmktime(0, 0, 0, $month, $day, $year)));
//            $afterMonthDate = date('Y-m-d', strtotime(' +1 month', gmmktime(0, 0, 0, $month, $day, $year)));
////            var_dump($lastMonthDate);
////            var_dump($afterMonthDate);
//
//            $order_str = 'ORDER BY MONTH (s_w.birth), DAY (s_w.birth) ASC';
//
//            if ($month == 12){
//                $order_str = 'ORDER BY MONTH (s_w.birth) DESC, DAY (s_w.birth) ASC';
//            }

//            $query = "SELECT `id`, `full_name`, `birth` FROM `spr_workers`
//            WHERE
//            `status` <> '8'
//            AND
//            (
//                (
//                    (MONTH (`birth`) = '{$month}')
//                        AND
//                        (
//                            (DAY (`birth`) > '$day')
//                            OR
//                            (DAY (`birth`) = '$day')
//                        )
//                )
//                OR
//                (
//                    (MONTH (`birth`) = MONTH ('$afterMonthDate'))
///*                     AND
//                   (
//                        (DAY (`birth`) < DAY ('$afterMonthDate'))
//                        OR
//                        (DAY (`birth`) = DAY ('$afterMonthDate'))
//                    )*/
//                )
//            )
//            ".$order_str;

            // Сложна выборка к основному запросу плюсуем максимальное из другой таблицы
//            $query = "SELECT s_w.id, s_w.full_name, s_w.birth, congr.id AS congr_id, congr.status AS congr, congr.year FROM `spr_workers` s_w
//            LEFT OUTER JOIN `journal_bd_congr` congr ON congr.worker_id = s_w.id
//            AND congr.year = (SELECT MAX(year) FROM journal_bd_congr
//                WHERE worker_id = s_w.id
//            )
//            WHERE
//            s_w.status <> '8'
//            AND
//            (
//                (
//                    (MONTH (s_w.birth) = '".explode('-', $lastMonthDate)[1]."')
//                    AND
//                    (
//                        (DAY (s_w.birth) > '".explode('-', $lastMonthDate)[2]."')
//                        OR
//                        (DAY (s_w.birth) = '".explode('-', $lastMonthDate)[2]."')
//                    )
//                )
//                OR
//                (
//                    (MONTH (s_w.birth) = '".explode('-', $afterMonthDate)[1]."')
//                    AND
//                    (
//                        (DAY (s_w.birth) < '".explode('-', $afterMonthDate)[2]."')
//                        OR
//                        (DAY (s_w.birth) = '".explode('-', $afterMonthDate)[2]."')
//                    )
//                )
//                OR
//                (
//                    (MONTH (s_w.birth) < MONTH ('$afterMonthDate'))
//                    AND
//                    (MONTH (s_w.birth) > '".explode('-', $lastMonthDate)[1]."')
//                )
//            )
//            ".$order_str;
//
//            $args = [
//
//            ];
//
//            $births_arr = $db::getRows($query, $args);
////            var_dump($births_arr);

//            if (!empty($births_arr)){
//                foreach ($births_arr as $birth) {
//                    //var_dump(date('m-d', strtotime($birth['birth'])));
//
//                    $congrat = '';
//                    //Если есть права
//                    if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {
//
//                        // Если январь, а др в декабре
//                        if ((date('m') == '01') && (explode('-', $birth['birth'])[1]) == '12') {
//                            $congrYear = date('Y') - 1;
//                        // Если декабрь, а др в январе
//                        }elseif((date('m') == '12') && (explode('-', $birth['birth'])[1]) == '01') {
//                            $congrYear = date('Y') + 1;
//                        }else{
//                            $congrYear = date('Y');
//                        }
//
//                        //Если год отметки = текущему
//                        if ($birth['year'] == date('Y')){
//                            if ($birth['congr'] == 1) {
//                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
//                            }else{
//                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
//                            }
//                            //Если год отметки раньше
//                        }elseif ($birth['year'] < date('Y')){
//                                // Если декабрь, а др в январе
//                                if((date('m') == '12') && (explode('-', $birth['birth'])[1]) == '01') {
//                                    if ($birth['congr'] == 1) {
//                                        $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
//                                    }else{
//                                        $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
//                                    }
//                                }else{
//                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
//                                }
//                        //Если год отметки позже
//                        }else{
//                            // Если январь, а др в декабре
//                            if ((date('m') == '01') && (explode('-', $birth['birth'])[1]) == '12') {
//                                if ($birth['congr'] == 1) {
//                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
//                                }else{
//                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
//                                }
//                            }else{
//                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
//                            }
//                        }
//                    }
//
//                    //Прошедшие и если это не декабрь
//                    if(
//                        (
//                            (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] < date('m-d', time()))
//                            &&
//                            ($month != '12')
//                        )
//                        ||
//                        (
//                            (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] < date('m-d', time()))
//                            &&
//                            ($month == '12')
//                            &&
//                            (explode('-', $birth['birth'])[1] == 12)
//                        )
//                    /*&&
//                        (explode('-', $afterMonthDate)[1] != '01')*/
//                    ){
//                        $last_birth_str .= '
//                            <tr>
//                                <td style="text-align: right; width: 50%; padding-right: 10px; font-size: 90%; ">
//                                    <i>'.explode('-', $birth['birth'])[2].' '.$monthsName[explode('-', $birth['birth'])[1]].'</i>
//                                    '.$congrat.'
//                                </td>
//                                <td style="text-align: left; width: 50%; padding-left: 5px;">
//                                    '.$birth['full_name'].'
//                                </td>
//                            </tr>';
//
//                    //if (date('m-d', strtotime($birth['birth'])) == date('m-d', time())){
//                    }elseif (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] == date('m-d', time())){
//
//                        $today_birth_str .= '
//                            <tr>
//                                <td style="text-align: center; width: 50%; padding-left: 5px; color: #fbff00; text-shadow: 1px 1px 4px #ef0909, 0px 0px 10px #000b34;">
//                                    <i>' .$birth['full_name'].'</i>
//                                    '.$congrat.'
//                                </td>
//                            </tr>';
//                    }else{
//                        $births_str .= '
//                            <tr>
//                                <td style="text-align: right; width: 50%; padding-right: 10px; font-size: 90%; ">
//                                    <i>'.explode('-', $birth['birth'])[2].' '.$monthsName[explode('-', $birth['birth'])[1]].'</i>
//                                    '.$congrat.'
//                                </td>
//                                <td style="text-align: left; width: 50%; padding-left: 5px;">
//                                    '.$birth['full_name'].'
//                                </td>
//                            </tr>';
//                    }
//                }
//
//            }
//
//            if (mb_strlen($today_birth_str) > 0) {
//                $today_birth_str = '
//                    <table width="100%" border="0" style="text-align: center; background-color: #cbfcff; box-shadow: 0px 9px 0px #cbfcff;">
//                        <tr>
//                            <td style="text-align: center; position: relative;">
//                                <img src="img/HappyBirthday.png" style="width: 200px;">
//                                <!--<div style="width: 100%; position: absolute; bottom: 3px;">
//
//                                </div>-->
//                            </td>
//                        </tr>
//                        <tr>
//                            <td>
//                                <table width="100%" style="text-align: center; font-size: 160%;">
//                                    '.$today_birth_str.'
//                                </table>
//                            </td>
//                        </tr>
//                    </table>';
//            }
//
//            if ((mb_strlen($births_str) > 0) || (mb_strlen($last_birth_str) > 0)) {
//                if (mb_strlen($today_birth_str) > 0) {
//                    $absolute_pos = 'position: absolute; /*bottom: 0;*/';
//                }else{
//                    $absolute_pos = '';
//                }
//
//                if (mb_strlen($last_birth_str) > 0){
//                    $last_birth_str = '
//                        <tr>
//                            <td colspan="2" style="text-align: center; font-size: 80%; background-color: rgb(206 206 206); padding: 3px;">
//                                <i>Недавно было день рождения:</i>
//                            </td>
//                        </tr>
//                        '.$last_birth_str.'';
//                }
//
//                $births_str = '
//                    <table width="100%" border="0" style="text-align: center; border: 1px solid #BFBCB5; '.$absolute_pos.'">
//                        '.$last_birth_str.'
//                        <tr>
//                            <td colspan="2" style="text-align: center; font-size: 80%; background-color: rgb(206 206 206); padding: 3px;">
//                                <i>Скоро день рождения:</i>
//                            </td>
//                        </tr>
//                        '.$births_str.'
//                    </table>';
//            }

            //В таблицу выводим результат
            echo '
            <table width="100%" style="border:1px solid #BFBCB5;">';

//            if (mb_strlen($warning_str) > 0) {
//                echo '
//                <tr>
//                    <td colspan="2" style="text-align: center; width: 100%; border:1px solid #BFBCB5;">
//                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(255, 51, 51); color: rgb(255, 255, 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
//                            <i>Важные объявления</i>
//                        </div>
//                        <div  style="/*height: 70px; */max-height: 140px; overflow-y: scroll; text-align: center; position: relative;">
//                            ' . $warning_str . '
//                        </div>
//                    </td>
//                </tr>';
//            }
            echo '
                <tr style="">
                    <td style="width: 50%; border:1px solid #BFBCB5; vertical-align: top;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(133 0 15); color: white; margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Задания на выполнение</i>
                        </div>
                        <div style="height: 700px; max-height: 700px; overflow-y: scroll; text-align: center; border:1px solid #BFBCB5;">
                            '.$news_str.'
                        </div>
                    </td>
                    <td style="text-align: center; width: 50%; border:1px solid #BFBCB5; vertical-align: top;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(133 133 133); color: rgb(255 255 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Выполненные задания</i>
                        </div>
                        <div style="height: 700px; max-height: 700px; overflow-y: scroll; text-align: center; position: relative;">
                            тут пока ничего не будет отображаться
                        </div>
                    </td>
                </tr>
                <!--<tr>
                    <td colspan="2" style="text-align: center; width: 100%; border:1px solid #BFBCB5;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(233 255 0); color: rgb(39, 0, 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Объявления / обновления</i>
                        </div>
                        <div style="height: 350px; max-height: 350px; overflow-y: scroll; text-align: center;">
                            '.$news_str.'
                        </div>
                    </td>
                </tr>-->
            </table>';



        }

        /*echo '<a href="history2.php" class="b3">История изменений и обновлений</a><br>';*/
        echo '	
			
			    <div id="doc_title">Univer - Асмедика</div>
				</div>';
		
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>