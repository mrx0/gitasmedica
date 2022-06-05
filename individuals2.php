<?php

//individuals2.php
//Индивидуальные занятия для админов

	require_once 'header.php';
    require_once 'blocks_dom.php';

	//var_dump($_SESSION);
	//var_dump($_SESSION['calculate_data']);

	if ($enter_ok){
		require_once 'header_tags.php';

		//include_once 'DBWork.php';
        include_once('DBWorkPDO.php');
		include_once 'functions.php';

        include_once 'variables.php';

        //$announcing_arr = array();

		//$offices = SelDataFromDB('spr_filials', '', '');

        //!!!Массив тех, кому видно по умолчанию, потому надо будет вывести это в базу или в другой файл
        $permissionsWhoCanSee_arr = array(2, 3, 9);

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
//            if (isset($_GET['show_option'])){
//                //Все кроме удалённых
//                if ($_GET['show_option'] == 'all'){
//                    $show_option_str = " AND j_task.status <> '9'";
//                    $show_option_str_for_paginator = 'show_option=all';
//                    $show_option_str_for_header = 'Все заявки <span style="color: green">(кроме удалённых)</span>';
//                    $bgColor_all = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Все открытые
//                if ($_GET['show_option'] == 'allopen'){
//                    $show_option_str = " AND j_task.status <> '9' AND j_task.status <> '1'";
//                    $show_option_str_for_paginator = 'show_option=allopen';
//                    $show_option_str_for_header = 'Все <span style="color: green">открытые</span>';
//                    $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Подходит к концу
//                /*if ($_GET['show_option'] == 'excl2'){
//                    $show_option_str = " AND j_task.status <> '9'";
//                    $show_option_str_for_paginator = 'show_option=excl2';
//                    $show_option_str_for_header = '<span style="color: red">Просроченные</span> заявки';
//                }*/
//                //Истёк срок
//                if ($_GET['show_option'] == 'excl'){
//                    $show_option_str = " AND j_task.status <> '9' AND j_task.status <> '1' AND j_task.plan_date < '{$today3daysplus}'";
//                    $show_option_str_for_paginator = 'show_option=excl';
//                    $show_option_str_for_header = '<span style="color: red">Просроченные и подходящие по сроку</span> заявки';
//                    $bgColor_excl = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Изменения
//                if ($_GET['show_option'] == 'newtopic'){
//                    $show_option_str = " AND j_task.id NOT IN (SELECT `task_id` FROM `journal_tasks_readmark` jtask_rm2 WHERE j_task.id = jtask_rm2.task_id AND jtask_rm2.create_person = '{$_SESSION['id']}' AND jtask_rm2.status = '1')";
//                    $show_option_str_for_paginator = 'show_option=newtopic';
//                    $show_option_str_for_header = '<span style="color: forestgreen">Обновлённые</span> заявки';
//                    $bgColor_newtopic = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Сделанные
//                if ($_GET['show_option'] == 'done'){
//                    $show_option_str = " AND j_task.status = '1'";
//                    $show_option_str_for_paginator = 'show_option=done';
//                    $show_option_str_for_header = '<span style="color: green">Завершенные</span> заявки';
//                    $bgColor_done = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Удалённые
//                if ($_GET['show_option'] == 'deleted'){
//                    $show_option_str = " AND j_task.status = '9'";
//                    $show_option_str_for_paginator = 'show_option=deleted';
//                    $show_option_str_for_header = '<span style="color: darkslategrey">Удалённые</span> заявки';
//                    $bgColor_deleted = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//                //Персональные
//                if ($_GET['show_option'] == 'person'){
//                    $show_option_str = " AND j_tasks_worker.worker_id = '{$_SESSION['id']}' AND j_task.status <> '9'";
//                    $show_option_str_for_paginator = 'show_option=person';
//                    $show_option_str_for_header = '<span style="color: rgba(124, 0, 255, 0.68);">Персональные</span> заявки';
//                    $bgColor_person = 'background-color: rgba(0, 201, 255, 0.5)';
//                }
//            }else{
//                $bgColor_allopen = 'background-color: rgba(0, 201, 255, 0.5)';
//            }
        }

        $query_dop = '';

        if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {
            $query_dop = '';
        }else{
            //$query_dop = 'AND (j_ind.author_id	 = '.$_SESSION['id'].' OR j_ind.student_id	 = '.$_SESSION['id'].')';
            $query_dop = 'AND (j_ind.author_id	 = '.$_SESSION['id'].')';
        }

//        //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
//        if (($ticket['see_all'] != 1) && (!$god_mode)){
//            $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `task_id` = j_task.id)";
//        }
//
//        //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
//        if (($ticket['see_all'] != 1) && (!$god_mode)){
//            if (isset($_SESSION['filial'])) {
//                $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `task_id` = j_task.id)";
//            }
//        }
//
//        //Надо выбрать те, которые относятся к конкретному сотруднику
//        if (($ticket['see_all'] != 1) && (!$god_mode)){
//            $query_dop .= " OR j_task.id IN (SELECT `task_id` FROM `journal_tasks_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `task_id` = j_task.id)";
//        }

        //$msql_cnnct = ConnectToDB ();
        $db = new DB();

        //Выборка
        $query = "SELECT j_ind.*
            FROM `journal_individuals2` j_ind
            WHERE (TRUE
            {$query_dop})
            ORDER BY j_ind.date DESC";
        //var_dump($query);

        $tasks_j = $db::getRows($query, []);
        //var_dump($tasks_j);

        echo '
			<header style="margin-bottom: 5px;">
                <div class="nav">
                    <a href="univer.php" class="b">UNIVER главная</a>
                    <a href="individuals.php" class="b">Индивидуальные занятия</a>
                </div>
				<h1>Работа с админами</h1>';
			echo '
			</header>

            <div id="infoDiv" style="display: none; position: absolute; z-index: 2; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
            </div>

			<div id="data">';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
            echo '<a href="individuals2_add.php" class="b">Добавить</a><br>';
          }


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
                            <table width="100%" style="/*border: 1px solid #BEBEBE;*/ margin:5px;">
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
                            <tr>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.explode('-', $task['date'])[2].'.'.explode('-', $task['date'])[1].'.'.explode('-', $task['date'])[0].'</td>
                                <td style="border: 1px solid #BEBEBE; padding: 1px 5px;">'.WriteSearchUser('spr_workers', $task['worker_id'], 'user', false).'</td>
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