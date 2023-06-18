<?php

//index.php
//Главная

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
        $permissionsWhoCanSee_arr = array(2, 3, 8, 9);


        echo '
			<header style="margin-bottom: 5px;">
				<h1>Главная</h1>';
			echo '
			</header>
            
            <div id="infoDiv" style="display: none; position: absolute; z-index: 2; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
            </div>
			
			<div id="data">';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
            echo '<a href="announcing_add.php" class="b">Добавить объявление</a><br>';
        }


        echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 1;">';

        echo $block_fast_search_client;

        echo '
					</div>';


        //$msql_cnnct = ConnectToDB ();
        $db = new DB();

        $arr = array();
        $rez = array();

        //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
        if ($_SESSION['permissions'] != 777) {
            $query_dop = "AND j_ann.id IN (SELECT `annoncing_id` FROM `journal_announcing_worker` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `annoncing_id` = j_ann.id)";
        }else{
            $query_dop = '';
        }

        //Выборка объявлений не удалённых (j_ann.status <> '9')
        //и плюс статус прочитан он данным сотрудником или нет
        $query = "SELECT jann.*, jannrm.status AS read_status
        FROM `journal_announcing_readmark` jannrm
        RIGHT JOIN (
          SELECT * FROM `journal_announcing` j_ann  WHERE j_ann.status <> '9' AND (j_ann.type = '1' OR j_ann.type = '2' OR j_ann.type = '3' OR j_ann.type = '4' OR j_ann.type = '5')
          {$query_dop}
        ) jann ON jann.id = jannrm.announcing_id
        AND jannrm.create_person = '{$_SESSION['id']}'
        ORDER BY `create_time` DESC";

//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//        $number = mysqli_num_rows($res);
//        if ($number != 0){
//            while ($arr = mysqli_fetch_assoc($res)){
//                array_push($announcing_arr, $arr);
//            }
//        }

        $args = [

        ];

        $announcing_arr = $db::getRows($query, $args);

        //var_dump($announcing_arr);
        //var_dump($query);

        $stocks_str = '';
        $news_str = '';
        $warning_str = '';

        if (!empty($announcing_arr)){

            foreach ($announcing_arr as $announcing) {
                //var_dump($announcing);

                $temp_str = '';

                $annColor = '245, 245, 245';
                $annIco = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                $annColorAlpha = '0.9';
                $readStateClass = '';
                $newTopic = true;
                $topicTheme = nl2br($announcing['theme']);

                if ($announcing['type'] == 1){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-bullhorn" aria-hidden="true"></i>';
                    $annColorAlpha = '0.53';
                    if ($topicTheme == ''){
                        $topicTheme = 'Объявление';
                    }
                }

                if ($announcing['type'] == 2){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Обновление';
                    }
                }

                if ($announcing['type'] == 3){
                    $annColor = '252, 255, 51';
                    $annIco = '<i class="fa fa-book" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Инструкция';
                    }
                }

                if ($announcing['type'] == 4){
                    $annColor = '21, 209, 33';
                    $annIco = '<i class="fa fa-bolt" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Акция';
                    }
                }

                if ($announcing['type'] == 5){
                    $annColor = '255, 51, 51';
                    $annIco = '<i class="fa fa-bullhorn" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Важно!';
                    }
                }

                if ($announcing['read_status'] == 1){
                    if (($announcing['type'] != 4) && ($announcing['type'] != 5)) {
                        $readStateClass = 'display: none;';
                    }
                    $newTopic = false;

                }

                if($announcing['status'] == 8){
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

                if($announcing['status'] == 8){
                    $temp_str .= '  <span style="color: rgb(239,22,22) ;font-weight:bold;">ЗАКРЫТО / ЗАВЕРШЕНО</span> ';
                }

                $temp_str .= '
                            <b>'.$topicTheme.'</b>
                        </div>
                              
                        <div style="position: absolute; top: 2px; right: 50px; font-size: 10px; text-align: right;">
                            Дата: '.date('d.m.y H:i' ,strtotime($announcing['create_time'])).'<br>
                            <span style="font-size: 10px; color: #716f6f;">Автор: '.WriteSearchUser('spr_workers', $announcing['create_person'], 'user', false).'</span>
                        </div>';

                if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {
                    if ($announcing['status'] == 8){
                        $temp_str .= '
                        <div style="position: absolute; top: 6px; right: 0px; text-align: right;">
                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $announcing['id'] . ', 0);"><i class="fa fa-reply" aria-hidden="true" style="color: grey; " title="Вернуть"></i></span>
                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $announcing['id'] . ', 9);"><i class="fa fa-trash-o" aria-hidden="true" title="Удалить"></i></span>
                        </div>
                        ';
                    }elseif($announcing['status'] == 9){

                    }else {
                        $temp_str .= '
                        <div style="position: absolute; top: 6px; right: 0px; text-align: right;">
                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $announcing['id'] . ', 8);"><i class="fa fa-times" aria-hidden="true" style="color: red; " title="Закрыть"></i></span>
                            <span style="background-color: #e8e8e8; padding: 1px 3px; border: 1px solid #868686; font-size: 15px; cursor: pointer;" onclick="announcingDelete(' . $announcing['id'] . ', 9);"><i class="fa fa-trash-o" aria-hidden="true" title="Удалить"></i></span>
                        </div>
                        ';
                    }
                }

                $temp_str .= '
                    <div style="position: absolute; bottom: 0; left: 34px; font-size: 80%;';
                if ($newTopic) {
                    $temp_str .= 'display:none;';
                }
                $temp_str .= '">';
                if (($announcing['type'] != 4) && ($announcing['type'] != 5)) {
                    $temp_str .= '
                        <a href="" class="ahref showMeTopic" announcingID="' . $announcing['id'] . '">Развернуть</a>';
                }
                $temp_str .= '
                    </div>';

                $temp_str .= '     
                    </h2>
                    <p id="topic_'.$announcing['id'].'" style="margin-bottom: 5px; '.$readStateClass.'">
                        '.nl2br($announcing['text']).'
                    </p>';

                if ($newTopic) {
                    $temp_str .= '
                    <div style="position: absolute; bottom: 0; right: 10px;">
                        <button class="b iUnderstand" announcingID="' . $announcing['id'] . '">Ясно</button>
                    </div>';
                }


                 $temp_str .= '
                 </div>';

                //var_dump($announcing['status']);
                //var_dump($announcing['read_status']);


                if (($announcing['type'] == 1) || ($announcing['type'] == 2) || ($announcing['type'] == 3)){
                    $news_str .= $temp_str;
                }
                if ($announcing['type'] == 4){
                    $stocks_str .= $temp_str;
                }
                if ($announcing['type'] == 5){
                    $warning_str .= $temp_str;
                }
            }

            //Дни рождений
            $births_str = '';
            $today_birth_str = '';
            $last_birth_str = '';

            $day = date('d');
            $month = date('m');
            $year = date('Y');

            $today = date('Y-m-d', time());
            $lastMonthDate = date('Y-m-d', strtotime(' -3 days', gmmktime(0, 0, 0, $month, $day, $year)));
            $afterMonthDate = date('Y-m-d', strtotime(' +1 month', gmmktime(0, 0, 0, $month, $day, $year)));
//            var_dump($lastMonthDate);
//            var_dump($afterMonthDate);

            $order_str = 'ORDER BY MONTH (s_w.birth), DAY (s_w.birth) ASC';

            if ($month == 12){
                $order_str = 'ORDER BY MONTH (s_w.birth) DESC, DAY (s_w.birth) ASC';
            }

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
            $query = "SELECT s_w.id, s_w.full_name, s_w.birth, congr.id AS congr_id, congr.status AS congr, congr.year FROM `spr_workers` s_w
            LEFT OUTER JOIN `journal_bd_congr` congr ON congr.worker_id = s_w.id 
            AND congr.year = (SELECT MAX(year) FROM journal_bd_congr 
                WHERE worker_id = s_w.id
            )
            WHERE
            s_w.status <> '8' AND s_w.id <> 1
            AND
            (
                (
                    (MONTH (s_w.birth) = '".explode('-', $lastMonthDate)[1]."')
                    AND
                    (
                        (DAY (s_w.birth) > '".explode('-', $lastMonthDate)[2]."')
                        OR
                        (DAY (s_w.birth) = '".explode('-', $lastMonthDate)[2]."')
                    )
                )
                OR
                (
                    (MONTH (s_w.birth) = '".explode('-', $afterMonthDate)[1]."')
                    AND
                    (
                        (DAY (s_w.birth) < '".explode('-', $afterMonthDate)[2]."')
                        OR
                        (DAY (s_w.birth) = '".explode('-', $afterMonthDate)[2]."')
                    )
                )
                OR 
                (
                    (MONTH (s_w.birth) < MONTH ('$afterMonthDate'))
                    AND
                    (MONTH (s_w.birth) > '".explode('-', $lastMonthDate)[1]."')
                )
            )
            ".$order_str;

            $args = [

            ];

            $births_arr = $db::getRows($query, $args);
//            var_dump($births_arr);

            if (!empty($births_arr)){
                foreach ($births_arr as $birth) {
                    //var_dump(date('m-d', strtotime($birth['birth'])));

                    $congrat = '';
                    //Если есть права
                    if (in_array($_SESSION['permissions'], $permissionsWhoCanSee_arr) || $god_mode) {

                        // Если январь, а др в декабре
                        if ((date('m') == '01') && (explode('-', $birth['birth'])[1]) == '12') {
                            $congrYear = date('Y') - 1;
                        // Если декабрь, а др в январе
                        }elseif((date('m') == '12') && (explode('-', $birth['birth'])[1]) == '01') {
                            $congrYear = date('Y') + 1;
                        }else{
                            $congrYear = date('Y');
                        }

                        //Если год отметки = текущему
                        if ($birth['year'] == date('Y')){
                            if ($birth['congr'] == 1) {
                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
                            }else{
                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
                            }
                            //Если год отметки раньше
                        }elseif ($birth['year'] < date('Y')){
                                // Если декабрь, а др в январе
                                if((date('m') == '12') && (explode('-', $birth['birth'])[1]) == '01') {
                                    if ($birth['congr'] == 1) {
                                        $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
                                    }else{
                                        $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
                                    }
                                }else{
                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
                                }
                        //Если год отметки позже
                        }else{
                            // Если январь, а др в декабре
                            if ((date('m') == '01') && (explode('-', $birth['birth'])[1]) == '12') {
                                if ($birth['congr'] == 1) {
                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, '.$birth['id'].', '.$birth['congr_id'].', 0, '.$congrYear.')"title="Уже поздравили"></i></span>';
                                }else{
                                    $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
                                }
                            }else{
                                $congrat = '<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, '.$birth['id'].', 0, 1, '.$congrYear.')" title="Еще не поздравили"></i></span>';
                            }
                        }
                    }

                    //Прошедшие и если это не декабрь
                    if(
                        (
                            (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] < date('m-d', time()))
                            &&
                            ($month != '12')
                        )
                        ||
                        (
                            (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] < date('m-d', time()))
                            &&
                            ($month == '12')
                            &&
                            (explode('-', $birth['birth'])[1] == 12)
                        )
                    /*&&
                        (explode('-', $afterMonthDate)[1] != '01')*/
                    ){
                        $last_birth_str .= '
                            <tr>
                                <td style="text-align: right; width: 50%; padding-right: 10px; font-size: 90%; ">
                                    <i>'.explode('-', $birth['birth'])[2].' '.$monthsName[explode('-', $birth['birth'])[1]].'</i>
                                    '.$congrat.'
                                </td>
                                <td style="text-align: left; width: 50%; padding-left: 5px;">
                                    '.$birth['full_name'].'
                                </td>
                            </tr>';

                    //if (date('m-d', strtotime($birth['birth'])) == date('m-d', time())){
                    }elseif (explode('-', $birth['birth'])[1].'-'.explode('-', $birth['birth'])[2] == date('m-d', time())){

                        $today_birth_str .= '
                            <tr>
                                <td style="text-align: center; width: 50%; padding-left: 5px; color: #fbff00; text-shadow: 1px 1px 4px #ef0909, 0px 0px 10px #000b34;">
                                    <i>' .$birth['full_name'].'</i>
                                    '.$congrat.'
                                </td>
                            </tr>';
                    }else{
                        $births_str .= '
                            <tr>
                                <td style="text-align: right; width: 50%; padding-right: 10px; font-size: 90%; ">
                                    <i>'.explode('-', $birth['birth'])[2].' '.$monthsName[explode('-', $birth['birth'])[1]].'</i>
                                    '.$congrat.'
                                </td>
                                <td style="text-align: left; width: 50%; padding-left: 5px;">
                                    '.$birth['full_name'].'
                                </td>
                            </tr>';
                    }
                }

            }

            if (mb_strlen($today_birth_str) > 0) {
                $today_birth_str = '
                    <table width="100%" border="0" style="text-align: center; background-color: #cbfcff; box-shadow: 0px 9px 0px #cbfcff;">
                        <tr>
                            <td style="text-align: center; position: relative;">
                                <img src="img/HappyBirthday.png" style="width: 200px;">
                                <!--<div style="width: 100%; position: absolute; bottom: 3px;">
                               
                                </div>-->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" style="text-align: center; font-size: 160%;">
                                    '.$today_birth_str.'
                                </table> 
                            </td>
                        </tr>
                    </table>';
            }

            if ((mb_strlen($births_str) > 0) || (mb_strlen($last_birth_str) > 0)) {
                if (mb_strlen($today_birth_str) > 0) {
                    $absolute_pos = 'position: absolute; /*bottom: 0;*/';
                }else{
                    $absolute_pos = '';
                }

                if (mb_strlen($last_birth_str) > 0){
                    $last_birth_str = '
                        <tr>
                            <td colspan="2" style="text-align: center; font-size: 80%; background-color: rgb(206 206 206); padding: 3px;">
                                <i>Недавно было день рождения:</i>
                            </td>
                        </tr>
                        '.$last_birth_str.'';
                }

                $births_str = '
                    <table width="100%" border="0" style="text-align: center; border: 1px solid #BFBCB5; '.$absolute_pos.'">
                        '.$last_birth_str.'
                        <tr>
                            <td colspan="2" style="text-align: center; font-size: 80%; background-color: rgb(206 206 206); padding: 3px;">
                                <i>Скоро день рождения:</i>
                            </td>
                        </tr>
                        '.$births_str.'
                    </table>';
            }

            //В таблицу выводим результат
            echo '
            <table width="100%" style="border:1px solid #BFBCB5;">';

            if (mb_strlen($warning_str) > 0) {
                echo '
                <tr>
                    <td colspan="2" style="text-align: center; width: 100%; border:1px solid #BFBCB5;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(255, 51, 51); color: rgb(255, 255, 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Важные объявления</i>
                        </div>
                        <div  style="/*height: 70px; */max-height: 140px; overflow-y: scroll; text-align: center; position: relative;">
                            ' . $warning_str . '
                        </div>
                    </td>
                </tr>';
            }
            echo '
                <tr style="">
                    <td style="width: 50%; border:1px solid #BFBCB5; vertical-align: top;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(0 150 15); color: white; margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Текущие акции</i>
                        </div>
                        <div style="height: 350px; max-height: 350px; overflow-y: scroll; text-align: center; border:1px solid #BFBCB5;">
                            '.$stocks_str.'
                        </div>
                    </td>
                    <td style="text-align: center; width: 50%; border:1px solid #BFBCB5; vertical-align: top;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(255 0 252); color: rgb(255 255 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i><a href="all_congrats.php" class="ahref" style="color: white;">Дни рождения</a></i>
                        </div>
                        <div style="height: 350px; max-height: 350px; overflow-y: scroll; text-align: center; position: relative;">
                            '.$today_birth_str.'
                            '.$births_str.'
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; width: 100%; border:1px solid #BFBCB5;">
                        <div style="height: 20px; max-height: 20px; text-align: center; background-color: rgb(233 255 0); color: rgb(39, 0, 255); margin-bottom: 5px; border-bottom: 1px solid #BFBCB5;">
                            <i>Объявления / обновления</i>
                        </div>
                        <div style="height: 350px; max-height: 350px; overflow-y: scroll; text-align: center;">
                            '.$news_str.'
                        </div>
                    </td>
                </tr>
            </table>';



        }

        /*echo '<a href="history2.php" class="b3">История изменений и обновлений</a><br>';*/
        echo '	
			
			    <div id="doc_title">Главная - Асмедика</div>
				</div>';
		
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>