<?php

//ticket.php
//Тикет

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($ticket['see_all'] == 1) || ($ticket['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

				$ticket_j = array();

                $show_option_str_for_paginator = '';

                //Че показываем
                if (isset($_GET['show_option'])){
                    //Все кроме удалённых
                    if ($_GET['show_option'] == 'all'){
                        $show_option_str_for_paginator = 'show_option=all';
                    }
                    //Все открытые
                    if ($_GET['show_option'] == 'allopen'){
                        $show_option_str_for_paginator = 'show_option=allopen';
                    }
                    //Подходит к концу
                    if ($_GET['show_option'] == 'excl2'){
                        $show_option_str_for_paginator = 'show_option=excl2';
                    }
                    //Истёк срок
                    if ($_GET['show_option'] == 'excl'){
                        $show_option_str_for_paginator = 'show_option=excl';
                    }
                    //Изменения
                    if ($_GET['show_option'] == 'newtopic'){
                        $show_option_str_for_paginator = 'show_option=newtopic';
                    }
                    //Сделанные
                    if ($_GET['show_option'] == 'done'){
                        $show_option_str_for_paginator = 'show_option=done';
                    }
                    //Удалённые
                    if ($_GET['show_option'] == 'deleted'){
                        $show_option_str_for_paginator = 'show_option=deleted';
                    }
                }


                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

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

                $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
                GROUP_CONCAT(DISTINCT j_tickets_worker2.worker_id ORDER BY j_tickets_worker2.worker_id ASC SEPARATOR \",\") AS worker_ids,
                GROUP_CONCAT(DISTINCT j_tickets_worker_t.worker_type ORDER BY j_tickets_worker_t.worker_type ASC SEPARATOR \",\") AS worker_types,
                GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
                FROM `journal_tickets` j_ticket
                LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_workers` j_tickets_worker2 ON j_ticket.id = j_tickets_worker2.ticket_id 
                LEFT JOIN `journal_tickets_worker_type` j_tickets_worker_t ON j_tickets_worker_t.ticket_id = '{$_GET['id']}' 
                LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = '{$_GET['id']}' 
                /*WHERE j_ticket.status <> '9'*/
                WHERE (TRUE 
                {$query_dop}
                OR j_ticket.create_person = '{$_SESSION['id']}')
                AND j_ticket.id = '{$_GET['id']}'";

                //echo $query;

                $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($ticket_j, $arr);
                    }
                }

                //var_dump($ticket_j);

				if (!empty($ticket_j)){
                    if ($ticket_j[0]['id'] == $_GET['id']){

                        //$offices = SelDataFromDB('spr_filials', '', '');
                        $filials_j = getAllFilials(false, true);
                        //var_dump($filials_j);
                        //Получили список прав
                        $permissions_j = getAllPermissions(false, true);
                        //var_dump($permissions_j);

                        //Отметим сначала как "прочитано"
                        if ($ticket_j[0]['read_status'] != 1) {
                            $time = date('Y-m-d H:i:s', time());

                            $query = "INSERT INTO `journal_tickets_readmark` (
                            `ticket_id`, `create_time`, `create_person`, `status`)
                            VALUES ('{$ticket_j[0]['id']}', '{$time}', '{$_SESSION['id']}', '1')";

                            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);
                        }

                        $ticket_style = 'ticketBlock_in';
                        $expired_text = '';


                        //Если просрочен
                        if ($ticket_j[0]['plan_date'] != '0000-00-00') {
                            //время истечения срока
                            $pd = $ticket_j[0]['plan_date'];
                            //текущее
                            $nd = date('Y-m-d', time());
                            //сравнение не прошли ли сроки исполнения
                            if (strtotime($pd) > strtotime($nd)+2*24*60*60) {
                                $expired = false;
                            } else {
                                if (strtotime($pd) < strtotime($nd)){
                                    $expired = true;
                                    $ticket_style = 'ticketBlockexpired_in';
                                    //$expired_icon = 'fa fa-exclamation-circle';
                                    $expired_text = 'срок выполнения истёк';
                                }else {
                                    $expired = true;
                                    $ticket_style = 'ticketBlockexpired2_in';
                                    //$expired_icon = 'fa fa-exclamation';
                                    $expired_text = 'срок выполнения скоро истечёт';
                                }
                            }
                        }else{
                            $expired = false;
                        }
                        //Если выполнен и закрыт
                        if ($ticket_j[0]['status'] == 1) {
                            $ticket_done = true;
                            $ticket_style = 'ticketBlock_in';
                        }else{
                            $ticket_done = false;
                        }
                        //Если удалён
                        if ($ticket_j[0]['status'] == 9) {
                            $ticket_deleted = true;
                            $ticket_style = 'ticketBlockdeleted_in';
                        }else{
                            $ticket_deleted = false;
                        }
                        //Если прочитано
                        if ($ticket_j[0]['read_status'] == 1){
                            //$readStateClass = 'display: none;';
                            $newTopic = false;
                        }else{
                            $newTopic = true;
                        }

                        echo '
                            <div id="status">
                                <header>
                                    <div class="nav">
                                        <a href="tickets.php?'.$show_option_str_for_paginator.'" class="b">Все тикеты</a>
                                    </div>
                                    <h2>
                                        Тикет #'.$ticket_j[0]['id'];
                        if (!$ticket_done || ($ticket['see_all'] == 1) || $god_mode)     {
                            if ((($ticket['edit'] == 1) && (($ticket_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
                                if ($ticket_j[0]['status'] != 9) {
                                    echo '
                                                <a href="ticket_edit.php?id=' . $_GET['id'] . '" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                }
                                if (($ticket_j[0]['status'] == 9) && (($ticket['close'] == 1) || $god_mode)) {
                                    echo '
                                        <a href="#" onclick="Ajax_reopen_ticket(' . $_GET['id'] . ')" title="Восстановить" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                                }
                            }
                            if ((($ticket['close'] == 1) && (($ticket_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
                                if ($ticket_j[0]['status'] != 9) {
                                    echo '
                                                <a href="#modal_ticket_delete" class="open_modal_ticket_delete info" style="font-size: 80%;" id="" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                                }
                            }
                        }

                        echo '
                                    </h2>';

                        if ($ticket_j[0]['status'] == 9){
                            echo '<i style="color:red;">Тикет удален (заблокирован).</i><br>';
                        }

                        echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:80%;  color: #555;">';

                        if (($ticket_j[0]['create_time'] != 0) || ($ticket_j[0]['create_person'] != 0)){
                            echo '
                                                        Добавлен: '.date('d.m.y H:i' ,strtotime($ticket_j[0]['create_time'])).'<br>
                                                        Автор: '.WriteSearchUser('spr_workers', $ticket_j[0]['create_person'], 'user', true).'<br>';
                        }else{
                            echo 'Добавлен: не указано<br>';
                        }
                        if (($ticket_j[0]['last_edit_time'] != 0) || ($ticket_j[0]['last_edit_person'] != 0)){
                            echo '
                                                        Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($ticket_j[0]['last_edit_time'])).'<br>
                                                        Кем: '.WriteSearchUser('spr_workers', $ticket_j[0]['last_edit_person'], 'user', true).'';
                        }
                        echo '
                                                </span>
                                            </div>';

                        echo '
                                    <input type="hidden" id="ticket_id" value="'.$ticket_j[0]['id'].'">
                                </header>';

                        echo '
                                <div id="data" style="">
                                    <div style="display: inline-block; vertical-align: top;">';

                         echo '
                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="padding: 5px 15px 0;">
                                                <div style="font-size: 100%;  color: #555; margin-bottom: 1px; margin-left: -10px;">';

                        if (!$ticket_deleted) {

                            $ticket_done_btn = FALSE;

                            if ($ticket_j[0]['filials'] != NULL) {

                                $filials_arr_temp = explode(',', $ticket_j[0]['filials']);

                                if (!empty($filials_arr_temp)) {

                                    if (count($filials_arr_temp) <= 1){
                                        $ticket_done_btn = TRUE;
                                    }else{
                                        if ((($ticket['close'] == 1) && (($ticket_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
                                            $ticket_done_btn = TRUE;
                                        }else{
                                            if ($ticket_j[0]['worker_ids'] != NULL) {
                                                $workers_arr_temp = explode(',', $ticket_j[0]['worker_ids']);
                                                //var_dump($workers_arr_temp);
                                                if (!empty($workers_arr_temp)) {
                                                    if (in_array($_SESSION['id'], $workers_arr_temp)) {
                                                        $ticket_done_btn = TRUE;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    if ($ticket_j[0]['worker_ids'] != NULL) {
                                        $workers_arr_temp = explode(',', $ticket_j[0]['worker_ids']);
                                        //var_dump($workers_arr_temp);
                                        if (!empty($workers_arr_temp)) {
                                            if (in_array($_SESSION['id'], $workers_arr_temp)) {
                                                $ticket_done_btn = TRUE;
                                            }
                                        }
                                    }
                                }
                            }else{
                                if ($ticket_j[0]['worker_ids'] != NULL) {
                                    $workers_arr_temp = explode(',', $ticket_j[0]['worker_ids']);
                                    //var_dump($workers_arr_temp);
                                    if (!empty($workers_arr_temp)) {
                                        if (in_array($_SESSION['id'], $workers_arr_temp)) {
                                            $ticket_done_btn = TRUE;
                                        }
                                    }
                                }
                            }

                            if ($ticket_done_btn) {
                                if ($ticket_done) {
                                    echo '<button class="b4" value="Вернуть в работу" onclick="Ajax_ticket_restore(' . $ticket_j[0]['id'] . ')"> Вернуть в работу <i class="fa fa-briefcase" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></button>';
                                } else {
                                    echo '<a href="#modal_ticket_done" class="open_modal_ticket_done b4" id="">Завершить <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></a>';
                                }
                                if (!$ticket_done && $expired) {

                                }
                            }
                        }else{

                        }

                        echo '
                                                </div>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="background-color: rgba(246, 255, 77, 0.57); padding: 10px 20px 30px;">
                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Описание</div>
                                                <div>'.$ticket_j[0]['descr'].'</div>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="padding: 5px 20px 5px;">
                                                <div style="float: left; /*border: 2px solid #FF0000;*/">
                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Срок выполнения по плану</div>
                                                    <div class="'.$ticket_style.'">';
                        echo date('d.m.Y', strtotime($ticket_j[0]['plan_date']));

                        echo '
                                                    </div>
                                                </div>
                                                <div style="float: right; /*border: 2px solid #FF0000;*/">
                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Дата выполнения по факту</div>
                                                    <div>';
                        if ($ticket_j[0]['fact_date'] != '0000-00-00') {
                            echo date('d.m.Y', strtotime($ticket_j[0]['plan_date']));
                        }else{
                            echo '<span style="color: red;">не закрыт</span>';
                        }
                        echo '
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
                                            <div class="cellLeft" style="padding: 7px 20px 5px;">
                                                <div style="font-size:80%;  color: #555; margin-bottom: 5px; margin-left: -10px;">Назначенные исполнители</div>
                                                <div>';

                        if ($ticket_j[0]['worker_ids'] != NULL) {
                            $workers_arr_temp = explode(',', $ticket_j[0]['worker_ids']);
                            //var_dump($workers_arr_temp);
                            if (!empty($workers_arr_temp)) {
                                foreach ($workers_arr_temp as $w_id) {
                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">' . WriteSearchUser('spr_workers', $w_id, 'user', true) . '</div><input type="hidden" id="workers_exist" value="true">';
                                }
                            }
                        }else{
                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px; color: red;">не указаны</div><input type="hidden" id="workers_exist" value="false">';
                        }
                        echo '
                                                </div>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
                                            <div class="cellLeft" style="padding: 10px 20px 10px;">
                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Для каких категорий сотрудников</div>
                                                <div>';

                        if ($ticket_j[0]['worker_types'] != NULL) {
                            $worker_types_arr_temp = explode(',', $ticket_j[0]['worker_types']);

                            if (!empty($worker_types_arr_temp)) {
                                foreach ($worker_types_arr_temp as $p_id) {
                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px; border-bottom: 1px dashed rgba(0, 0, 128, 0.5);">' . $permissions_j[$p_id]['name'] . '</div>';
                                }
                            }
                        }else{
                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">-</div>';
                        }

                        echo '
                                                </div>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
                                            <div class="cellLeft" style="padding: 10px 20px 10px;">
                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Для каких филиалов</div>
                                                <div>';

                        if ($ticket_j[0]['filials'] != NULL) {
                            $filials_arr_temp = explode(',', $ticket_j[0]['filials']);

                            if (!empty($filials_arr_temp)) {
                                foreach ($filials_arr_temp as $f_id) {
                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">' . $filials_j[$f_id]['name2'] . '</div>';
                                }
                            }
                        }else{
                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">-</div>';
                        }

                        echo '
                                                </div>
                                            </div>
                                        </div>';

                        /*echo '
                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="padding: 10px 20px 30px;">
                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Прикреплённые фото</div>
                                                <div>(coming soon... (maybe))</div>
                                            </div>
                                        </div>';*/

                        echo '
                                    </div>';

                        echo '
                                    <div style="display: inline-block; vertical-align: top;">';
                        echo '
                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="padding: 5px 15px 10px;">
                                                <div style="font-size:70%;  color: #555; margin-left: -10px; text-align: right;">';

                        if ($ticket_deleted){
                            echo '
                                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(27, 27, 27, 0.8); font-size: 170%; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i><br>
                                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
                        }else {
                            if ($_SESSION['id'] == $ticket_j[0]['worker_id']){
                                echo '                        
                                                    <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); font-size: 170%; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> Вы - один из исполнителей<br>';
                            }
                        }

                        if (!$ticket_deleted) {
                            if ($ticket_done) {
                                echo '                                    
                                                    <i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 170%;  text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> выполнен и закрыт<br>';
                            } else {
                            }
                            if (!$ticket_done && $expired) {
                                echo '
                                                    <i class="fa fa-exclamation" aria-hidden="true" style="color: red; font-size: 170%; padding-left: 3px; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> ';
                                /*if (strtotime($pd) < strtotime($nd)){
                                    echo 'срок выполнения скоро истечёт<br>';
                                }else{
                                    echo 'срок выполнения истек<br>';
                                }*/
                                echo $expired_text;
                            }
                        }

                        echo '
                                                </div>
                                            </div>
                                        </div>';
                        echo '

                                        <div class="cellsBlock2" style="width: 370px;">
                                            <div class="cellLeft" style="padding: 10px 20px 5px;">
                                                <div style="font-size:80%; float: right; color: #555; margin-bottom: 10px; margin-left: -10px;">Комментарии</div>
                                                <div id="chat" class="scroll-pane">
                                                     <div id="ticket_comments"></div>
                                                </div>';
                        if (!$ticket_deleted && !$ticket_done) {
                            echo '
                                                <div>
                                                    <!--<input type="text" id="msg_input" class="msg_input" autofocus contenteditable/>-->
                                                    <div id="msg_input" class="msg_input" contenteditable placeholder=""></div>
                                                </div>
                                                <div>
                                                    <input type="submit" id="msg_send" class="msg_send" value="Отправить" onclick="Add_newComment_inTicket(' . $ticket_j[0]['id'] . ');">
                                                </div>';
                        }
                        echo '
                                            </div>
                                        </div>
                                    </div>';


                        echo '
                        <div style="font-size:80%;  color: #555; margin: 10px 0 5px;">Лог изменений</div>
                        <ul id="ticket_change_log" style="font-size:80%;  color: #555;"></ul>
                        <div id="doc_title">Тикет #'.$ticket_j[0]['id'].'</div>';

                        //Модальные окна
                        echo $block_modal_ticket_done;
                        echo $block_modal_ticket_delete;

                        echo '			
                                </div>';

                        echo '	
                                <!-- Подложка только одна -->
                                <div id="overlay"></div>';

                        //Скрипты которые грузят комменты и лог
                        echo '
                                <script>
                                    $(document).ready(function() {
                                        getLogForTicket($("#ticket_id").val());
                                        getCommentsForTicket($("#ticket_id").val());
                                    })
                                </script>                        
                        ';

                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>