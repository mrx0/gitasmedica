<?php

//index.php
//Главная

	require_once 'header.php';

	//var_dump($_SESSION);
	//var_dump($_SESSION['calculate_data']);

	if ($enter_ok){
		require_once 'header_tags.php';

		include_once 'DBWork.php';
		include_once 'functions.php';

        $announcing_arr = array();

		$offices = SelDataFromDB('spr_filials', '', '');

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

        $msql_cnnct = ConnectToDB ();

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
          SELECT * FROM `journal_announcing` j_ann  WHERE j_ann.status <> '9'
          {$query_dop}
        ) jann ON jann.id = jannrm.announcing_id
        AND jannrm.create_person = '{$_SESSION['id']}'
        ORDER BY `create_time` DESC";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($announcing_arr, $arr);
            }
        }
        //var_dump($announcing_arr);
        //var_dump($query);

        if (!empty($announcing_arr)){
            foreach ($announcing_arr as $announcing) {

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
                    $annColor = '73, 208, 183';
                    $annIco = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Обновление';
                    }
                }
                if ($announcing['type'] == 3){
                    $annColor = '21, 209, 33';
                    $annIco = '<i class="fa fa-book" aria-hidden="true"></i>';
                    $annColorAlpha = '0.35';
                    if ($topicTheme == ''){
                        $topicTheme = 'Инструкция';
                    }
                }

                if ($announcing['read_status'] == 1){
                    $readStateClass = 'display: none;';
                    $newTopic = false;
                }

                echo '
                
                <div style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 0px; font-size: 80%; background-color: rgba('.$annColor.', '.$annColorAlpha.'); position: relative;">
                    <h2 class="', $newTopic ? "blink1":"" ,'" style="height: 15px; border: 1px dotted #CCC; background-color: rgba(250, 250, 250, 0.9); width: 100%; padding: 6px 13px; margin: -9px 0 15px -14px; position: relative;">
                        
                        <div style="position: absolute; top: 3px; left: 10px; font-size: 17px; color: rgba('.$annColor.', 1);  text-shadow: 1px 1px 3px rgb(0, 0, 0), 0 0 2px rgba(52, 152, 219, 1);">
                            '.$annIco.'
                        </div>
                                                
                        <div style="position: absolute; top: 5px; left: 35px; font-size: 13px;">
                            <b>'.$topicTheme.'</b>
                        </div>
                              
                        <div style="position: absolute; top: 2px; right: 10px; font-size: 11px; text-align: right;">
                            Дата: '.date('d.m.y H:i' ,strtotime($announcing['create_time'])).'<br>
                            <span style="font-size: 10px; color: #716f6f;">Автор: '.WriteSearchUser('spr_workers', $announcing['create_person'], 'user', false).'</span>
                        </div>';

                echo '
                    <div style="position: absolute; bottom: 0; left: 34px; font-size: 80%;', $newTopic ? "display:none;":"",'">
                        <a href="" class="ahref showMeTopic" announcingID="' . $announcing['id'] . '">Развернуть</a>
                    </div>';

                echo '        
                    </h2>
                    <p id="topic_'.$announcing['id'].'" style="margin-bottom: 30px; '.$readStateClass.'">
                        '.nl2br($announcing['text']).'
                    </p>';

                if ($newTopic) {
                    echo '
                    <div style="position: absolute; bottom: 0; right: 10px;">
                        <button class="b iUnderstand" announcingID="' . $announcing['id'] . '">Ясно</button>
                    </div>';
                }


                echo '
                </div>';
                //var_dump($announcing['status']);
                //var_dump($announcing['read_status']);
            }
        }

        echo '<a href="history.php" class="b3">История изменений и обновлений до <b style="color: red;">15.09.2017</b></a><br>';
        echo '	
			
			    <div id="doc_title">Главная - Асмедика</div>
				</div>';
		
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>