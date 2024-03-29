<?php

//univer_task.php
//Задание UNIVER

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        //if (($ticket['see_all'] == 1) || ($ticket['see_own'] == 1) || $god_mode){
			if ($_GET){
//				include_once 'DBWork.php';
				include_once 'functions.php';

                include_once 'DBWorkPDO_2.php';

                //Подключаемся к другой базе специально созданной для UNIVER
                $db = new DB2();

                $tasks_j = array();

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

//                $msql_cnnct2 = ConnectToDB_2 ('config_task');

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

                $query = "SELECT j_task.*, jtask_rm.status as read_status, j_tasks_worker.worker_id,
                GROUP_CONCAT(DISTINCT j_tasks_worker2.worker_id ORDER BY j_tasks_worker2.worker_id ASC SEPARATOR \",\") AS worker_ids,
                GROUP_CONCAT(DISTINCT j_tasks_worker_t.worker_type ORDER BY j_tasks_worker_t.worker_type ASC SEPARATOR \",\") AS worker_types,
                GROUP_CONCAT(DISTINCT j_tasks_filial.filial_id ORDER BY j_tasks_filial.filial_id ASC SEPARATOR \",\") AS filials
                FROM `journal_tasks` j_task
                LEFT JOIN `journal_tasks_readmark` jtask_rm ON j_task.id = jtask_rm.task_id AND jtask_rm.create_person = '{$_SESSION['id']}'
                LEFT JOIN `journal_tasks_workers` j_tasks_worker ON j_task.id = j_tasks_worker.task_id AND j_tasks_worker.worker_id = '{$_SESSION['id']}'
                LEFT JOIN `journal_tasks_workers` j_tasks_worker2 ON j_task.id = j_tasks_worker2.task_id 
                LEFT JOIN `journal_tasks_worker_type` j_tasks_worker_t ON j_tasks_worker_t.task_id = '{$_GET['id']}' 
                LEFT JOIN `journal_tasks_filial` j_tasks_filial ON j_tasks_filial.task_id = '{$_GET['id']}' 
                /*WHERE j_task.status <> '9'*/
                WHERE (TRUE 
                {$query_dop}
                OR j_task.create_person = '{$_SESSION['id']}')
                AND j_task.id = '{$_GET['id']}'";

//                echo $query;

                //$res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

//                $number = mysqli_num_rows($res);
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        array_push($tasks_j, $arr);
//                    }
//                }

                $tasks_j = $db::getRows($query, []);
//                var_dump($tasks_j);

				if (!empty($tasks_j)){
                    if ($tasks_j[0]['id'] == $_GET['id']){

                        //$offices = SelDataFromDB('spr_filials', '', '');
                        $filials_j = getAllFilials(false, true, true);
                        //var_dump($filials_j);
                        //Получили список прав
                        $permissions_j = getAllPermissions(false, true);
                        //var_dump($permissions_j);

                        //Отметим сначала как "прочитано"
                        if ($tasks_j[0]['read_status'] != 1) {
                            $time = date('Y-m-d H:i:s', time());

                            $query = "INSERT INTO `journal_tasks_readmark` (
                            `task_id`, `create_time`, `create_person`, `status`)
                            VALUES ('{$tasks_j[0]['id']}', '{$time}', '{$_SESSION['id']}', '1')";

//                            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);
                        }

                        $task_style = 'taskBlock_in';
                        $expired_text = '';


                        //Если просрочен
//                        if ($tasks_j[0]['plan_date'] != '0000-00-00') {
//                            //время истечения срока
//                            $pd = $tasks_j[0]['plan_date'];
//                            //текущее
//                            $nd = date('Y-m-d', time());
//                            //сравнение не прошли ли сроки исполнения
//                            if (strtotime($pd) > strtotime($nd)+2*24*60*60) {
//                                $expired = false;
//                            } else {
//                                if (strtotime($pd) < strtotime($nd)){
//                                    $expired = true;
//                                    $task_style = 'taskBlockexpired_in';
//                                    //$expired_icon = 'fa fa-exclamation-circle';
//                                    $expired_text = 'срок выполнения истёк';
//                                }else {
//                                    $expired = true;
//                                    $task_style = 'taskBlockexpired2_in';
//                                    //$expired_icon = 'fa fa-exclamation';
//                                    $expired_text = 'срок выполнения скоро истечёт';
//                                }
//                            }
//                        }else{
//                            $expired = false;
//                        }

                        //Если активирован
                        if ($tasks_j[0]['status'] == 1) {
                            $task_done = true;
                            $task_style = 'taskBlock_in';
                        }else{
                            $task_done = false;
                        }
                        //Если удалён
                        if ($tasks_j[0]['status'] == 9) {
                            $task_deleted = true;
                            $task_style = 'taskBlockdeleted_in';
                        }else{
                            $task_deleted = false;
                        }
                        //Если прочитано
                        if ($tasks_j[0]['read_status'] == 1){
                            //$readStateClass = 'display: none;';
                            $newTopic = false;
                        }else{
                            $newTopic = true;
                        }

                        echo '
                            <div id="status">
                                <header>
                                    <!--<div class="nav">
                                        <a href="tasks.php?'.$show_option_str_for_paginator.'" class="b">Все заявки</a>
                                    </div>-->
                                    <h2>
                                        Задание #'.$tasks_j[0]['id']. '(тестовый режим)';
//                        if (!$task_done || ($ticket['see_all'] == 1) || $god_mode)     {
//                            if ((($ticket['edit'] == 1) && (($tasks_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
//                                if ($tasks_j[0]['status'] != 9) {
//                                    echo '
//                                                <a href="task_edit.php?id=' . $_GET['id'] . '" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
//                                }
//                                if (($tasks_j[0]['status'] == 9) && (($ticket['close'] == 1) || $god_mode)) {
//                                    echo '
//                                        <a href="#" onclick="Ajax_reopen_task(' . $_GET['id'] . ')" title="Восстановить" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
//                                }
//                            }
//                            if ((($ticket['close'] == 1) && (($tasks_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
//                                if ($tasks_j[0]['status'] != 9) {
//                                    echo '
//                                                <a href="#modal_task_delete" class="open_modal_task_delete info" style="font-size: 80%;" id="" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
//                                }
//                            }
//                        }

                        echo '
                                    </h2>';

                        if ($tasks_j[0]['status'] == 9){
                            echo '<i style="color:red;">Задание удалено (заблокировано).</i><br>';
                        }

                        echo '
                                            <div class="cellsBlock2" style="margin-bottom: 10px;">
                                                <span style="font-size:80%;  color: #555;">';

                        if (($tasks_j[0]['create_time'] != 0) || ($tasks_j[0]['create_person'] != 0)){
                            echo '
                                                        Добавлен: '.date('d.m.y H:i' ,strtotime($tasks_j[0]['create_time'])).'<br>
                                                        Автор: '.WriteSearchUser('spr_workers', $tasks_j[0]['create_person'], 'user', true).'<br>';
                        }else{
                            echo 'Добавлен: не указано<br>';
                        }
                        if (($tasks_j[0]['last_edit_time'] != 0) || ($tasks_j[0]['last_edit_person'] != 0)){
                            echo '
                                                        Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($tasks_j[0]['last_edit_time'])).'<br>
                                                        Кем: '.WriteSearchUser('spr_workers', $tasks_j[0]['last_edit_person'], 'user', true).'';
                        }
                        echo '
                                                </span>
                                            </div>';

                        echo '
                                    <input type="hidden" id="task_id" value="'.$tasks_j[0]['id'].'">
                                </header>';


                        echo '
                        <div id="data">';

                        //Тема
                        if (!empty($tasks_j[0]['theme'])) {
                            echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    <b>' . $tasks_j[0]['theme'] . '</b>
                                </div>
                            </div>';
                        }

                        //Файл Видео или pdf
                        if ($tasks_j[0]['file_id'] != 0) {
                            //Добудем этот файл
                            $query = "SELECT * FROM `journal_upl_files` WHERE `id`=:file_id LIMIT 1";
                            //var_dump($query);

                            $args = [
                                'file_id' => $tasks_j[0]['file_id']
                            ];

                            $file_data = $db::getRow($query, $args);
//                            var_dump($file_data);

                            echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">';
                            if (!empty($file_data)) {
                                echo '
                                        <div style="margin-bottom: 20px;">Вам предложено ознакомиться:</div>';

                                if ($file_data['ext'] == 'mp4') {
                                    echo '
                                        <div id="video_block"  class="" style="">
                                            <video tabindex="-1" class="" controls controlslist="nodownload" id="my-video" width="640" height="480" poster="" preload="metadata">
                                               <!--<source src="video/nubex.ogv" type=\'video/ogg; codecs="theora, vorbis"\'>-->
                                               <source src="univerFiles/' . $file_data['path_name'] . '" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>
                                               <!--<source src="video/Сергей Мельников Кураторы лечения в стоматологии-master.m3u8" type="application/x-mpegURL">-->
                                               <!--<source src="video/nubex.webm" type=\'video/webm; codecs="vp8, vorbis"\'>-->
                                               Ваш браузер не поддерживает тег video.
                                            </video>
                                        </div>';
                                } else {
                                    echo '
                                        <div style="margin-top: 20px;">
                                            <embed src="univerFiles/' . $file_data['path_name'] . '" width="1200" height="900" alt="pdf">
                                        </div>
                                        ';
                                }
                            } else {
                                echo '<b>Без файла</b>';
                            }
                            echo '
                                    </div>
                                </div>';
                        }


                        echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Вопрос/задание: <i>'.$tasks_j[0]['descr'].'</i>
                                </div>
                            </div>';

//                var_dump(!empty($task_workers));
//                var_dump($task_workers);

                        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode) {
                            //Если есть конретные сотрудники, они в приоритете над остальным (20210912 отключено)
//                if (!empty($task_workers)) {
                            echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Для кого <br><span style="font-size: 80%; color: #555;">конкретные сотрудники</span><br>
                                </div>
                                <div class="cellRight">';
//                        if (!empty($task_workers)) {
//                            foreach ($task_workers as $w_id) {
//                                echo '<span style="display: block; font-style: italic; font-size: 90%;">' . WriteSearchUser('spr_workers', $w_id, 'user_full', true) . '</span>';
//                            }
//                        }else{
//                            echo '<b>Никто не указан</b>';
//                        }

                            if ($tasks_j[0]['worker_ids'] != NULL) {
                                $workers_arr_temp = explode(',', $tasks_j[0]['worker_ids']);
                                //var_dump($workers_arr_temp);
                                if (!empty($workers_arr_temp)) {
                                    foreach ($workers_arr_temp as $w_id) {
                                        echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">' . WriteSearchUser('spr_workers', $w_id, 'user', true) . '</div><input type="hidden" id="workers_exist" value="true">';
                                    }
                                }
                            } else {
                                echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px; color: red;">не указаны</div><input type="hidden" id="workers_exist" value="false">';
                            }

                            echo '
                                </div>
                            </div>';


                            //Если нет конретных сотрудников (20210912 отключено)
                            //}else{
//                    var_dump($permissions);
//                    var_dump($task_workers_type);

                            //Сотрудники по должностям
                            echo '
                                    
                                <div id="selPermisDiv" class="cellsBlock3">
                                    <div class="cellLeft">
                                        Для кого из сотрудников (по должностям)
                                    </div>
                                    <div class="cellRight">';
//                            if (!empty($task_workers_type)) {
//                                foreach ($task_workers_type as $p_id) {
//                                    echo '<span style="display: block; font-style: italic; font-size: 90%;">' . $permissions[$p_id]['name'] . '</span>';
//                                }
//                            } else {
//                                echo '<b>Никто не указан</b>';
//                            }

                            if ($tasks_j[0]['worker_types'] != NULL) {
                                $worker_types_arr_temp = explode(',', $tasks_j[0]['worker_types']);

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
                                </div>';


//                    var_dump($filials_j);
//                    var_dump($task_filial);

                            //По филиалам
                            echo '		
                                <div id="selFilialDiv" class="cellsBlock3">
                                    <div class="cellLeft">
                                        Для какого филиала
                                    </div>
                                    <div class="cellRight">';
//                            if (!empty($task_filial)) {
//                                foreach ($task_filial as $f_id) {
//                                    echo '<span style="display: block; font-style: italic; font-size: 90%;">' . $filials_j[$f_id]['name'] . '</span>';
//                                }
//                            } else {
//                                echo '<b>Ничего не указано</b>';
//                            }

                            if ($tasks_j[0]['filials'] != NULL) {
                                $filials_arr_temp = explode(',', $tasks_j[0]['filials']);

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
                                </div>';
                            //}
                        }
//                        echo '
//                            <div id="errror"></div>
//                            <!--<input type="button" class="b" value="Добавить" onclick=Ajax_add_test(\'add\')>-->
//                            <input type="button" class="b" style="background: #a4f90acf;" value="Добавить и активировать" onclick="Ajax_univer_task_add(\'add\', 1)">
//                            <!--<input type="button" class="b" value="Только добавить" onclick="Ajax_univer_task_add(\'add\', 0)">--><br><br>
//                            <a href="univer_add.php" class="b">Вернуться к заполнению</a>';


                        echo '
                        </div>
                    </div>';

                        echo '	
                <!-- Подложка только одна / Вариант подложки для затемнения области вокруг элемента блока со своим стилем в CSS -->
                <div id="layer"></div>';




//                        echo '
//                                <div id="data" style="">
//                                    <div style="display: inline-block; vertical-align: top;">';
//
//                         echo '
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="padding: 5px 15px 0;">
//                                                <div style="font-size: 100%;  color: #555; margin-bottom: 1px; margin-left: -10px;">';
//
//                        if (!$task_deleted) {
//
//                            $task_done_btn = FALSE;
//
//                            if ($tasks_j[0]['filials'] != NULL) {
//
//                                $filials_arr_temp = explode(',', $tasks_j[0]['filials']);
//
//                                if (!empty($filials_arr_temp)) {
//
//                                    if (count($filials_arr_temp) <= 1){
//                                        $task_done_btn = TRUE;
//                                    }else{
//                                        if ((($ticket['close'] == 1) && (($tasks_j[0]['create_person'] == $_SESSION['id']) || ($ticket['see_all'] == 1))) || $god_mode) {
//                                            $task_done_btn = TRUE;
//                                        }else{
//                                            if ($tasks_j[0]['worker_ids'] != NULL) {
//                                                $workers_arr_temp = explode(',', $tasks_j[0]['worker_ids']);
//                                                //var_dump($workers_arr_temp);
//                                                if (!empty($workers_arr_temp)) {
//                                                    if (in_array($_SESSION['id'], $workers_arr_temp)) {
//                                                        $task_done_btn = TRUE;
//                                                    }
//                                                }
//                                            }
//                                        }
//                                    }
//                                }else{
//                                    if ($tasks_j[0]['worker_ids'] != NULL) {
//                                        $workers_arr_temp = explode(',', $tasks_j[0]['worker_ids']);
//                                        //var_dump($workers_arr_temp);
//                                        if (!empty($workers_arr_temp)) {
//                                            if (in_array($_SESSION['id'], $workers_arr_temp)) {
//                                                $task_done_btn = TRUE;
//                                            }
//                                        }
//                                    }
//                                }
//                            }else{
//                                if ($tasks_j[0]['worker_ids'] != NULL) {
//                                    $workers_arr_temp = explode(',', $tasks_j[0]['worker_ids']);
//                                    //var_dump($workers_arr_temp);
//                                    if (!empty($workers_arr_temp)) {
//                                        if (in_array($_SESSION['id'], $workers_arr_temp)) {
//                                            $task_done_btn = TRUE;
//                                        }
//                                    }
//                                }
//                            }
//
//                            if ($task_done_btn) {
//                                if ($task_done) {
//                                    echo '<button class="b4" value="Вернуть в работу" onclick="Ajax_task_restore(' . $tasks_j[0]['id'] . ')"> Вернуть в работу <i class="fa fa-briefcase" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></button>';
//                                } else {
//                                    echo '<a href="#modal_task_done" class="open_modal_task_done b4" id="">Завершить <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i></a>';
//                                }
//                                if (!$task_done && $expired) {
//
//                                }
//                            }
//                        }else{
//
//                        }
//
//                        echo '
//                                                </div>
//                                            </div>
//                                        </div>';
//
//                        echo '
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="background-color: rgba(246, 255, 77, 0.57); padding: 10px 20px 30px;">
//                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Описание</div>
//                                                <div>'.$tasks_j[0]['descr'].'</div>
//                                            </div>
//                                        </div>';
//
//                        echo '
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="padding: 5px 20px 5px;">
//                                                <div style="float: left; /*border: 2px solid #FF0000;*/">
//                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Срок выполнения по плану</div>
//                                                    <div class="'.$task_style.'">';
//                        //echo date('d.m.Y', strtotime($tasks_j[0]['plan_date']));
//
//                        echo '
//                                                    </div>
//                                                </div>
//                                                <div style="float: right; /*border: 2px solid #FF0000;*/">
//                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Дата выполнения по факту</div>
//                                                    <div>';
////                        if ($tasks_j[0]['fact_date'] != '0000-00-00') {
////                            //echo date('d.m.Y', strtotime($tasks_j[0]['plan_date']));
////                        }else{
////                            echo '<span style="color: red;">не закрыт</span>';
////                        }
//                        echo '
//                                                    </div>
//                                                </div>
//                                            </div>
//                                        </div>';
//
////                        echo '
////                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
////                                            <div class="cellLeft" style="padding: 7px 20px 5px;">
////                                                <div style="font-size:80%;  color: #555; margin-bottom: 5px; margin-left: -10px;">Назначенные исполнители</div>
////                                                <div>';
////
////                        if ($tasks_j[0]['worker_ids'] != NULL) {
////                            $workers_arr_temp = explode(',', $tasks_j[0]['worker_ids']);
////                            //var_dump($workers_arr_temp);
////                            if (!empty($workers_arr_temp)) {
////                                foreach ($workers_arr_temp as $w_id) {
////                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">' . WriteSearchUser('spr_workers', $w_id, 'user', true) . '</div><input type="hidden" id="workers_exist" value="true">';
////                                }
////                            }
////                        }else{
////                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px; color: red;">не указаны</div><input type="hidden" id="workers_exist" value="false">';
////                        }
////                        echo '
////                                                </div>
////                                            </div>
////                                        </div>';
//
////                        echo '
////                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
////                                            <div class="cellLeft" style="padding: 10px 20px 10px;">
////                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Для каких категорий сотрудников</div>
////                                                <div>';
////
////                        if ($tasks_j[0]['worker_types'] != NULL) {
////                            $worker_types_arr_temp = explode(',', $tasks_j[0]['worker_types']);
////
////                            if (!empty($worker_types_arr_temp)) {
////                                foreach ($worker_types_arr_temp as $p_id) {
////                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px; border-bottom: 1px dashed rgba(0, 0, 128, 0.5);">' . $permissions_j[$p_id]['name'] . '</div>';
////                                }
////                            }
////                        }else{
////                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">-</div>';
////                        }
////
////                        echo '
////                                                </div>
////                                            </div>
////                                        </div>';
//
////                        echo '
////                                        <div class="cellsBlock2" style="width: 370px; text-align: right;">
////                                            <div class="cellLeft" style="padding: 10px 20px 10px;">
////                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Для каких филиалов</div>
////                                                <div>';
////
////                        if ($tasks_j[0]['filials'] != NULL) {
////                            $filials_arr_temp = explode(',', $tasks_j[0]['filials']);
////
////                            if (!empty($filials_arr_temp)) {
////                                foreach ($filials_arr_temp as $f_id) {
////                                    echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">' . $filials_j[$f_id]['name2'] . '</div>';
////                                }
////                            }
////                        }else{
////                            echo '<div style="display: inline-block; font-size: 80%; margin-right: 10px;">-</div>';
////                        }
////
////                        echo '
////                                                </div>
////                                            </div>
////                                        </div>';
//
//                        /*echo '
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="padding: 10px 20px 30px;">
//                                                <div style="font-size:80%;  color: #555; margin-bottom: 10px; margin-left: -10px;">Прикреплённые фото</div>
//                                                <div>(coming soon... (maybe))</div>
//                                            </div>
//                                        </div>';*/
//
//                        echo '
//                                    </div>';
//
//                        echo '
//                                    <div style="display: inline-block; vertical-align: top;">';
//                        echo '
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="padding: 5px 15px 10px;">
//                                                <div style="font-size:70%;  color: #555; margin-left: -10px; text-align: right;">';
//
//                        if ($task_deleted){
//                            echo '
//                                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(27, 27, 27, 0.8); font-size: 170%; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i><br>
//                                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
//                        }else {
//                            if ($_SESSION['id'] == $tasks_j[0]['worker_id']){
//                                echo '
//                                                    <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); font-size: 170%; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> Вы - один из исполнителей<br>';
//                            }
//                        }
//
//                        if (!$task_deleted) {
//                            if ($task_done) {
//                                echo '
//                                                    <i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 170%;  text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> выполнен и закрыт<br>';
//                            } else {
//                            }
//                            if (!$task_done && $expired) {
//                                echo '
//                                                    <i class="fa fa-exclamation" aria-hidden="true" style="color: red; font-size: 170%; padding-left: 3px; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i> ';
//                                /*if (strtotime($pd) < strtotime($nd)){
//                                    echo 'срок выполнения скоро истечёт<br>';
//                                }else{
//                                    echo 'срок выполнения истек<br>';
//                                }*/
//                                echo $expired_text;
//                            }
//                        }
//
//                        echo '
//                                                </div>
//                                            </div>
//                                        </div>';
//                        echo '
//
//                                        <div class="cellsBlock2" style="width: 370px;">
//                                            <div class="cellLeft" style="padding: 10px 20px 5px;">
//                                                <div style="font-size:80%; float: right; color: #555; margin-bottom: 10px; margin-left: -10px;">Комментарии</div>
//                                                <div id="chat" class="scroll-pane">
//                                                     <div id="task_comments"></div>
//                                                </div>';
//                        if (!$task_deleted && !$task_done) {
//                            echo '
//                                                <div>
//                                                    <!--<input type="text" id="msg_input" class="msg_input" autofocus contenteditable/>-->
//                                                    <div id="msg_input" class="msg_input" contenteditable placeholder=""></div>
//                                                </div>
//                                                <div>
//                                                    <input type="submit" id="msg_send" class="msg_send" value="Отправить" onclick="Add_newComment_intask(' . $tasks_j[0]['id'] . ');">
//                                                </div>';
//                        }
//                        echo '
//                                            </div>
//                                        </div>
//                                    </div>';
//
//
//                        echo '
//                        <div style="font-size:80%;  color: #555; margin: 10px 0 5px;">Лог изменений</div>
//                        <ul id="task_change_log" style="font-size:80%;  color: #555;"></ul>
//                        <div id="doc_title">Заявка #'.$tasks_j[0]['id'].'</div>';
//
////                        //Модальные окна
////                        echo $block_modal_task_done;
////                        echo $block_modal_task_delete;
//
//                        echo '
//                                </div>';

                        echo '
                                <!-- Подложка только одна -->
                                <div id="overlay"></div>';

                        //Скрипты которые грузят комменты и лог
//                        echo '
//                                <script>
//                                    $(document).ready(function() {
//                                        getLogFortask($("#task_id").val());
//                                        getCommentsFortask($("#task_id").val());
//                                    })
//                                </script>
//                        ';

                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		// }else{
			// echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		// }
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>