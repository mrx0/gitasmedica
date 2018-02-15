<?php

//tickets.php
//Тикеты

	require_once 'header.php';
	//require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($ticket['see_all'] == 1) || ($ticket['see_own'] == 1) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //Деление на странички пагинатор paginator
            $paginator_str = '';
            $limit_pos[0] = 0;
            $limit_pos[1] = 30;
            $pages = 0;

            $show_option_str = " AND j_ticket.status <> '9' AND j_ticket.status <> '1'";
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
                        $show_option_str = " AND j_ticket.status <> '9'";
                        $show_option_str_for_paginator = 'show_option=all';
                        $show_option_str_for_header = 'Все тикеты <span style="color: green">(кроме удалённых)</span>';
                        $bgColor_all = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Все открытые
                    if ($_GET['show_option'] == 'allopen'){
                        $show_option_str = " AND j_ticket.status <> '9' AND j_ticket.status <> '1'";
                        $show_option_str_for_paginator = 'show_option=allopen';
                        $show_option_str_for_header = 'Все <span style="color: green">открытые</span>';
                        $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Подходит к концу
                    /*if ($_GET['show_option'] == 'excl2'){
                        $show_option_str = " AND j_ticket.status <> '9'";
                        $show_option_str_for_paginator = 'show_option=excl2';
                        $show_option_str_for_header = '<span style="color: red">Просроченные</span> тикеты';
                    }*/
                    //Истёк срок
                    if ($_GET['show_option'] == 'excl'){
                        $show_option_str = " AND j_ticket.status <> '9' AND j_ticket.status <> '1' AND j_ticket.plan_date < '{$today3daysplus}'";
                        $show_option_str_for_paginator = 'show_option=excl';
                        $show_option_str_for_header = '<span style="color: red">Просроченные и подходящие по сроку</span> тикеты';
                        $bgColor_excl = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Изменения
                    if ($_GET['show_option'] == 'newtopic'){
                        $show_option_str = " AND j_ticket.id NOT IN (SELECT `ticket_id` FROM `journal_tickets_readmark` jticket_rm2 WHERE j_ticket.id = jticket_rm2.ticket_id AND jticket_rm2.create_person = '{$_SESSION['id']}' AND jticket_rm2.status = '1')";
                        $show_option_str_for_paginator = 'show_option=newtopic';
                        $show_option_str_for_header = '<span style="color: forestgreen">Обновлённые</span> тикеты';
                        $bgColor_newtopic = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Сделанные
                    if ($_GET['show_option'] == 'done'){
                        $show_option_str = " AND j_ticket.status = '1'";
                        $show_option_str_for_paginator = 'show_option=done';
                        $show_option_str_for_header = '<span style="color: green">Завершенные</span> тикеты';
                        $bgColor_done = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Удалённые
                    if ($_GET['show_option'] == 'deleted'){
                        $show_option_str = " AND j_ticket.status = '9'";
                        $show_option_str_for_paginator = 'show_option=deleted';
                        $show_option_str_for_header = '<span style="color: darkslategrey">Удалённые</span> тикеты';
                        $bgColor_deleted = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                    //Персональные
                    if ($_GET['show_option'] == 'person'){
                        $show_option_str = " AND j_tickets_worker.worker_id = '{$_SESSION['id']}' AND j_ticket.status <> '9'";
                        $show_option_str_for_paginator = 'show_option=person';
                        $show_option_str_for_header = '<span style="color: rgba(124, 0, 255, 0.68);">Персональные</span> тикеты';
                        $bgColor_person = 'background-color: rgba(0, 201, 255, 0.5)';
                    }
                }else{
                    $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
                }
            }

            $filials_j = getAllFilials(false, true);

            $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

            $tickets_arr = array();

            $arr = array();
            $rez = array();

            $query_dop = '';

            //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `ticket_id` = j_ticket.id)";
            }

            //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                if (isset($_SESSION['filial'])) {
                    $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `ticket_id` = j_ticket.id)";
                }
            }

            //Надо выбрать те, которые относятся к конкретному сотруднику
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                $query_dop .= " OR j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `ticket_id` = j_ticket.id)";
            }

            //Выборка объявлений не удалённых (j_ticket.status <> '9')
            //и плюс статус прочитан он данным сотрудником или нет
            //и плюс если текущий пользователь указан как исполнитель
            $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
            GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
            FROM `journal_tickets` j_ticket
            LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
            LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
            LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = j_ticket.id
            WHERE (TRUE
            {$query_dop}
            OR j_ticket.create_person = '{$_SESSION['id']}')
            {$show_option_str} 
            
            GROUP BY `id` ORDER BY /*`plan_date` ASC,*/ `id` DESC";

            /*$query = "SELECT jticket.*, jticket_rm.status AS read_status
            FROM `journal_tickets_readmark` jticket_rm
            RIGHT JOIN (
              /*SELECT * FROM `journal_tickets` j_ticket  WHERE j_ticket.status <> '9'*/
            /*  SELECT * FROM `journal_tickets` j_ticket  WHERE TRUE
              {$query_dop}
              OR j_ticket.create_person = '{$_SESSION['id']}'
            ) jticket ON jticket.id = jticket_rm.ticket_id
            AND jticket_rm.create_person = '{$_SESSION['id']}'
            ORDER BY `plan_date`, `create_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";*/

            $res = mysqli_query($msql_cnnct2, $query." LIMIT {$limit_pos[0]}, {$limit_pos[1]};") or die(mysqli_error($msql_cnnct2).' -> '.$query." LIMIT {$limit_pos[0]}, {$limit_pos[1]};");

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($tickets_arr, $arr);
                }
            }
            //echo $query;
            //var_dump ($tickets_arr);

            //Хочу получить общее количество
            $query = "SELECT COUNT(*) AS total_ticket_id FROM
            (".$query.") total_ticket_id_count;";

            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                $arr = mysqli_fetch_assoc($res);
                $total_ticket_ids = $arr['total_ticket_id'];
            }else{
                $total_ticket_ids = 0;
            }

            $filials_j = getAllFilials(false, false);
            //var_dump ($filials_j);

            CloseDB ($msql_cnnct2);

            echo '
				<header>
					<h1>'.$show_option_str_for_header.'</h1>
					<span style="color: red">Тестовый режим</span>
					
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo '
					</div>';

            if (($ticket['add_new'] == 1) || ($ticket['add_own'] == 1) || $god_mode){
                echo '
					<a href="ticket_add.php" class="b4">Новый тикет</a>';
            }

            echo '
						<div id="data">
						    <div style="border: 1px dotted #CCC; margin: 10px; font-size: 95%;">
						        <div style="display: inline-block;">
                                    <a href="tickets.php?show_option=allopen" class="b4" style="padding: 0 2px;'.$bgColor_allopen.'">Все открытые</a>
                                    <a href="tickets.php?show_option=all" class="b4" style="padding: 0 2px;'.$bgColor_all.'">Все</a>
                                    <!--<a href="tickets.php?show_option=excl2" class="b4" style="padding: 0 2px;">Просроченные <i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"  title="Истёк срок"></i></a>-->
                                    <a href="tickets.php?show_option=excl" class="b4" style="padding: 0 2px;'.$bgColor_excl.'">Подходит срок + просроченные <i class="fa fa-exclamation" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"  title="Истёк срок"></i></a>
                                    <a href="tickets.php?show_option=newtopic" class="b4" style="padding: 0 2px;'.$bgColor_newtopic.'">Обновлённые <i class="fa fa-bell" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Обновлено"></i></a><br>
                                    <a href="tickets.php?show_option=done" class="b4" style="padding: 0 2px;'.$bgColor_done.'">Закрытые <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></a>
                                    <a href="tickets.php?show_option=person" class="b4" style="padding: 0 2px;'.$bgColor_person.'">Персональные <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Вы исполнитель"></i></a>
                                    <a href="tickets.php?show_option=deleted" class="b4" style="padding: 0 2px;'.$bgColor_deleted.'">Удалённые <i class="fa fa-trash" aria-hidden="true" style="color: rgba(244, 244, 244, 0.8); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Удалено"></i></a>
                                </div>                                    
						        <div style="display: inline-block; float: right; text-align: right">
						            <!--<button class="b2" style="padding: 0 2px; margin: 2px 2px; cursor: pointer;" onclick="">Фильтр <i class="fa fa-filter" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></button>-->
						            <br>
						            <button class="b3" style="padding: 0 2px; margin: 2px 2px; cursor: pointer;" onclick="iReadAllOfTickts('.$_SESSION['id'].');">Пометить все как прочитанное</button>
						        </div>
                            </div>';

            if (!empty($tickets_arr)){

                //Для пагинатора
                if ($number != 0) {

                    $pages = (int)ceil($total_ticket_ids/$limit_pos[1]);
                    //var_dump($pages);

                    for ($i=1; $i <= $pages; $i++) {
                        $pg_btn_bgcolor = '';
                        if (isset($_GET)) {
                            if (isset($_GET['page'])) {
                                if ($_GET['page'] == $i){
                                    $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                                }
                            }else{
                                if ($i == 1){
                                    $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                                }
                            }
                        }
                        $paginator_str .= '<a href="tickets.php?page='.($i).'&'.$show_option_str_for_paginator.'" class="paginator_btn" style="'.$pg_btn_bgcolor.'">'.($i).'</a> ';
                    }
                }

                if ($pages > 1) {
                    echo '
						    <div style="margin: 2px 6px 3px;">
						        <span style="font-size: 80%; color: rgb(0, 172, 237);">Перейти на страницу: </span>' . $paginator_str . '
						    </div>';
                }

                foreach ($tickets_arr as $j_tickets) {

                    $ticket_style = 'ticketBlock';
                    $expired_icon = '';

                    //Если просрочен
                    if ($j_tickets['plan_date'] != '0000-00-00') {
                        //время истечения срока
                        $pd = $j_tickets['plan_date'];
                        //текущее
                        $nd = $today;
                        //сравнение не прошли ли сроки исполнения
                        if (strtotime($pd) > strtotime($nd)+2*24*60*60) {
                            $expired = false;
                        } else {
                            if (strtotime($pd) < strtotime($nd)){
                                $expired = true;
                                $ticket_style = 'ticketBlockexpired';
                                $expired_icon = 'fa fa-exclamation-circle';
                            }else {
                                $expired = true;
                                $ticket_style = 'ticketBlockexpired2';
                                $expired_icon = 'fa fa-exclamation';
                            }
                        }
                        /*var_dump(strtotime($nd));
                        var_dump(strtotime($pd));
                        var_dump(strtotime($pd)-strtotime($nd));
                        var_dump(3*24*60*60);*/
                        //var_dump(date('Y-m-d', time()));
                        //var_dump(strtotime(date('Y-m-d', time())));
                    }else{
                        $expired = false;
                    }
                    //Если выполнен и закрыт
                    if ($j_tickets['status'] == 1) {
                        $ticket_done = true;
                        $ticket_style = 'ticketBlockdone';
                    }else{
                        $ticket_done = false;
                    }
                    //Если удалён
                    if ($j_tickets['status'] == 9) {
                        $ticket_deleted = true;
                        $ticket_style = 'ticketBlockdeleted';
                    }else{
                        $ticket_deleted = false;
                    }
                    //Если прочитано
                    if ($j_tickets['read_status'] == 1){
                        //$readStateClass = 'display: none;';
                        $newTopic = false;
                    }else{
                        $newTopic = true;
                    }


                    //Длина строки проверка, если больше, то сокращаем
                    if (strlen($j_tickets['descr']) > 100){
                        $descr = mb_strimwidth($j_tickets['descr'], 0, 50, "...", 'utf-8');
                    }else{
                        $descr = $j_tickets['descr'];
                    }

                    echo '
                        <div class="'.$ticket_style.'" style="font-size: 95%;">
                            <div class="ticketBlockheader">
                                <div style="margin-left: 5px; text-align: left; float: left;">
                                    <span style=" color: rgb(29, 29, 29); font-size: 80%; font-weight: bold; margin-right: 3px;">#'.$j_tickets['id'].'</span>';
                    if (!$ticket_deleted) {
                        if ($ticket_done) {
                            echo '                                    
                                    <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">' . date('d.m.Y', strtotime($j_tickets['fact_date'])) . '</span>';
                        } else {
                            if ($j_tickets['plan_date'] != '0000-00-00') {
                                echo '
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">до ' . date('d.m.Y', strtotime($j_tickets['plan_date'])) . '</span>';
                                   //<i class="fa fa-times" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                            }
                        }
                        if (!$ticket_done && $expired) {
                            echo '
                                    <i class="'.$expired_icon.'" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"  title=""></i>';
                        }
                    }else{
                        echo '
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">удалён</span>';
                    }
                    echo '
                                </div>
                                <div style="margin-right: 5px; text-align: right; float: right;">';
                    if ($ticket_deleted){
                        echo '
                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(244, 244, 244, 0.8); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Удалено"></i>
                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
                    }else {
                        if ($_SESSION['id'] == $j_tickets['worker_id']){
                            echo '                        
                                        <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Вы исполнитель"></i>';
                        }
                        if ($newTopic) {
                            echo '                        
                                        <i class="fa fa-bell" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Обновлено"></i>';
                        }
                    }
                    
                    echo '
                                </div>
                            </div>
                            <a href="ticket.php?id='.$j_tickets['id'].'&'.$show_option_str_for_paginator.'" class="ticketBlockmain ahref">
                                '.$descr.'<br>
                                <span style="font-size: 80%; color: rgb(115, 112, 112);">нажмите, чтобы открыть</span>
                            </a><br>

                            <div class="ticketBlockfooter">
                                <!--создан '.date('d.m.y H:i', strtotime($j_tickets['create_time'])).'<br>-->
                                автор: <span style="color: rgb(51, 51, 51);">'.WriteSearchUser('spr_workers', $j_tickets['create_person'], 'user', false).'</span><br>
                                <!--где создано: ', $j_tickets['filial_id']==0 ? 'не указано' : $filials_j[$j_tickets['filial_id']]['name'] ,'-->';
                    if ($j_tickets['filials'] != NULL){
                        echo 'филиалы: ';
                        $filials_arr_temp = explode(',', $j_tickets['filials']);

                        if (!empty($filials_arr_temp)) {
                            foreach ($filials_arr_temp as $f_id) {
                                $bgColor_filialHere = '';
                                if (isset($_SESSION['filial'])){
                                    if ($f_id == $_SESSION['filial']){
                                        $bgColor_filialHere = 'background-color: rgba(144,247,95, 1); border: 1px dotted rgba(65, 33, 222, 0.34);';
                                    }
                                }
                                echo '<div style="display: inline-block; font-size: 80%; margin-right: 5px; color: rgb(59, 9, 111); '.$bgColor_filialHere.'">' . $filials_j[$f_id]['name2'] . '</div>';
                            }
                        }

                    }
                    echo '                                
                            </div>
                        </div>';
                }
            }else{
                echo '<br><br>ничего не найдено<br><br><a href="tickets.php" class="b4">Перейти в начало</a>';
            }

			echo '
					</ul>
					
					<div id="doc_title">Тикеты</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>