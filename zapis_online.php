<?php

//zapis_online.php
//Запись онлайн

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($zapis['add_new'] == 1) || ($_SESSION['permissions'] == 8) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            $offices_j = array();
            $permissions_j = array();
            $zapis_online_j = array();
            $deleted_zapis = '';

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 15;
            $pages = 0;

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }else{
                $_GET['page'] = 1;
            }

            $msql_cnnct = ConnectToDB();

            $query = "SELECT * FROM `spr_filials`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $offices_j[$arr['id']] = $arr;
                }
            }
            //var_dump($offices_j);

            $query = "SELECT `id`, `name` FROM `spr_permissions`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $permissions_j[$arr['id']] = $arr;
                }
            }

            //$offices = SelDataFromDB('spr_filials', '', '');

			echo '
				<header style="margin-bottom: 5px;">
					<h1>Запись онлайн</h1>
					<span style="color: red; font-size: 80%;">Все записи со статусом "Не доступен" старше <b>двух недель</b> необходимо <b>вручную</b> закрывать статусом "Обработано".</span>';

    		echo '
				</header>';


            //когда была последняя запись
            $query = "SELECT `datetime` FROM `zapis_online` WHERE `id` IN (SELECT MAX(`id`) FROM `zapis_online`) LIMIT 1";
//            var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                $arr = mysqli_fetch_assoc($res);

                $datetime = $arr['datetime'];
            }

            $today3daysplus = date('Y-m-d', strtotime(date('Y-m-d', strtotime($datetime)).' +3 days'));

            if (date('Y-m-d', time()) >= $today3daysplus){
                echo '<div class="query_neok"><i class="fa fa-warning" aria-hidden="true" style="color: red;" title=""></i> Новых записей не было уже 3 дня или более. Возможно возникли проблемы. Обратитесь к руководителю!</div>';
            }

            $check_last = false;


    		$dop = '';

    		if (isset($_SESSION['filial'])){
                $dop = "WHERE `place`='".$_SESSION['filial']."'";

                //Если энгельса, то плюсуем пустые (без филиала)
                if ($_SESSION['filial'] == 15){
                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='')";
                }
                //Если 72, то плюсуем 54
                if ($_SESSION['filial'] == 19){
                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='13')";
                }
                //Если ком, то плюсуем авиа
                if ($_SESSION['filial'] == 14){
                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='12')";
                }
            }

            //$query = "SELECT * FROM `zapis_online` ".$dop." ORDER BY `id` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]};";
            $query = "SELECT * FROM `zapis_online` ".$dop." ORDER BY `status`, `id` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]};";
//            var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
//                    var_dump($arr);




                    $diff_days = 0;


                    //Если не обработано
                    if ($arr['status'] == 6){
                        //Разница между датами количествол дней
                        $diff_days = date_diff(new DateTime(), new DateTime($arr['datetime']))->days;
                        //var_dump($diff_days);

                        //Если дней больше 28 сами изменим статус, чтоб не болталось
                        if ($diff_days > 28) {

                            $time = date('Y-m-d H:i:s', time());

                            $dop1 = ", `closed_time`='".$time."', `last_edit_person`='0'";

                            //Обновляем
                            $query = "UPDATE `zapis_online` SET `status`='7'".$dop1." WHERE `id`='{$arr['id']}'";
//                            var_dump($query);

                            mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $arr['time'] = $time;
                            $arr['status'] = 7;
                            $arr['last_edit_person'] = 0;
                        }

                    }
                    array_push($zapis_online_j, $arr);
                }
            }

			if (!empty($zapis_online_j)){
				echo '
				
					<div id="data">
                        <div style="margin: 2px 6px 3px;">';

                //Пагинатор
                echo paginationCreate ($limit_pos[1], $_GET['page'], 'zapis_online', 'zapis_online.php', $msql_cnnct, $dop);

                echo '
                        </div>
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold; font-size: 10px;">	
							    <div class="cellTime" style="text-align: center;">Время обращения</div>
								<div class="cellName" style="text-align: center; width: 200px; max-width: 200px; min-width: 200px;">
                                    ФИО';
                echo $block_fast_filter;
                echo '
								</div>';

				echo '
                                <div class="cellTime" style="text-align: center; width: 130px; max-width: 130px; min-width: 130px;">Контакты</div>
                                <div class="cellText" style="text-align: center">Комментарий</div>
                                <!--<div class="cellTime" style="text-align: center">Время желаемое</div>-->
                                <div class="cellOffice" style="text-align: center;">Филиал / Специалист</div>
								<div class="cellTime" style="text-align: center">Статус</div>
							</li>';

				for ($i = 0; $i < count($zapis_online_j); $i++) {

                    if ($zapis_online_j[$i]['status'] == 7) {
                        $bgcolor = 'background-color: rgba(158, 249, 142,1);';
                        $status = 'Обработано';
                    } else {
                        if ($zapis_online_j[$i]['status'] == 6){
                            $bgcolor = 'background-color: rgba(148, 189, 216, 0.63);';
                            $status = 'Не доступен';
                        }else {
                            $bgcolor = 'background-color: rgba(247, 162, 162, 0.5);';
                            $status = 'Не обработано';
                        }
                    }

                    if ($zapis_online_j[$i]['status'] != 7) {
                        if (isset($offices_j[$zapis_online_j[$i]['place']])){
                            $filial_name = $offices_j[$zapis_online_j[$i]['place']]['name'];
                        }else{
                            $filial_name = 'филиал не указан';
                        }
                        //($offices_j[$zapis_online_j[$i]['place']] > 0) ? $offices_j[$zapis_online_j[$i]['place']]['name'] : 'не указан'
                        echo '
							<li class="cellsBlock cellsBlockHover" style="' . $bgcolor . '">
								<div class="cellTime" style="text-align: center">
								    ' . $zapis_online_j[$i]['datetime'] . '
								</div>
								<div id="4filter" class="cellFullName 4filter" style="text-align: left; width: 200px; max-width: 200px; min-width: 200px;">
								    ' . $zapis_online_j[$i]['name'] . '
								</div>
								<div class="cellTime" style="text-align: center; width: 130px; max-width: 130px; min-width: 130px;">
								    <b>тел.:</b><br>' . $zapis_online_j[$i]['phone'] . '<br>
								    <!--<b>e-mail:</b><br>' . $zapis_online_j[$i]['email'] . '-->   
								</div>
								<div class="cellText" style="text-align: center">
								    ' . $zapis_online_j[$i]['comments'] . '
								</div>
								<!--<div class="cellTime" style="text-align: center">
								    ' . $zapis_online_j[$i]['time'] . '
								</div>-->
								<div class="cellOffice" style="text-align: center;">
								    ' .$filial_name. '<br>
								    <b>' . $permissions_j[$zapis_online_j[$i]['type']]['name'] . '</b>
								</div>
								<div class="cellTime ahref" style="text-align: center" onclick="contextMenuShow('.$zapis_online_j[$i]['id'].', '.$zapis_online_j[$i]['status'].', event, \'zapisOnlineStatusChange\');">
								    '.$status.'<br>';
                        if ($zapis_online_j[$i]['closed_time'] != '0000-00-00 00:00:00'){
                            if ($zapis_online_j[$i]['last_edit_person'] != 0) {
                                echo '<b>' . WriteSearchUser('spr_workers', $zapis_online_j[$i]['last_edit_person'], 'user', false) . '</b><br>' . date('d.m.y H:i', strtotime($zapis_online_j[$i]['closed_time']));
                            }else{
                                echo '<b>автоматически</b><br>' . date('d.m.y H:i', strtotime($zapis_online_j[$i]['closed_time']));
                            }
                        }
                        echo '
								</div>
							</li>';


                    } else {
                        if (isset($offices_j[$zapis_online_j[$i]['place']])){
                            $filial_name = $offices_j[$zapis_online_j[$i]['place']]['name'];
                        }else{
                            $filial_name = 'филиал не указан';
                        }
                        $deleted_zapis .= '
							<li class="cellsBlock cellsBlockHover" style="' . $bgcolor . '">
								<div class="cellTime" style="text-align: center">
								    ' . $zapis_online_j[$i]['datetime'] . '
								</div>
								<div id="4filter" class="cellFullName 4filter" style="text-align: left; width: 200px; max-width: 200px; min-width: 200px;">
								    ' . $zapis_online_j[$i]['name'] . '
								</div>
								<div class="cellTime" style="text-align: center; width: 130px; max-width: 130px; min-width: 130px;">
								    <b>тел.:</b>' . $zapis_online_j[$i]['phone'] . '<br>
								    <!--<b>e-mail: </b>' . $zapis_online_j[$i]['email'] . '-->
								</div>
								<div class="cellText" style="text-align: center">
								    ' . $zapis_online_j[$i]['comments'] . '
								</div>
								<!--<div class="cellTime" style="text-align: center">
								    ' . $zapis_online_j[$i]['time'] . '
								</div>-->
								<div class="cellOffice" style="text-align: center;">
								    ' . $filial_name . '<br>
								    <b>' . $permissions_j[$zapis_online_j[$i]['type']]['name'] . '</b>
								</div>
								<div class="cellTime ahref" style="text-align: center" onclick="contextMenuShow('.$zapis_online_j[$i]['id'].', '.$zapis_online_j[$i]['status'].', event, \'zapisOnlineStatusChange\');">
								    '.$status.'<br>';
                        if ($zapis_online_j[$i]['closed_time'] != '0000-00-00 00:00:00'){
                            if ($zapis_online_j[$i]['last_edit_person'] != 0) {
                                $deleted_zapis .= '<b>'.WriteSearchUser('spr_workers', $zapis_online_j[$i]['last_edit_person'], 'user', false).'</b><br>'.date('d.m.y H:i',strtotime($zapis_online_j[$i]['closed_time']));
                            }else{
                                $deleted_zapis .= '<b>автоматически</b><br>'.date('d.m.y H:i',strtotime($zapis_online_j[$i]['closed_time']));
                            }
                        }
                        $deleted_zapis .= '
								</div>
							</li>';
                    }
                }

				echo $deleted_zapis;
				
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			echo '
					</ul>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>