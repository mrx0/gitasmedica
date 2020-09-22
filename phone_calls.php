<?php

//phone_calls.php
//Статистика обзвона

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($zapis['see_all'] == 1) || ($zapis['see_own'] == 1) || $god_mode){

            include_once 'functions.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 40;
            $pages = 0;
            $dop = '';

            //$filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();
            $db = new DB();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }else{
                $_GET['page'] = 1;
            }

            $individual = '';
            $dopQuery = '';

            if (isset($_GET['client_id'])) {
                $args = [
                    'client_id' => $_GET['client_id']
                ];

                $dopQuery = 'WHERE j_pc.client_id = :client_id';
                $individual = 'пациенту: '.WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full', false);
            }else{
                $args = [];
            }

			echo '
				<header>
					<h1>Статистика звонков ';

            echo $individual;

            echo '
                    </h1>
				</header>';


            echo '
					<a href="phone_calls.php" class="b">Все звонки</a>';
			echo '
                    <div id="data">';

            //Пагинатор
            echo paginationCreate2 ($limit_pos[1], $_GET['page'], 'journal_phone_calling', 'phone_calls.php', $db, $dop);

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3 " style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellCosmAct" style="font-size: 80%; text-align: center"></div>
							<div class="cellOffice" style="font-size: 80%; text-align: center; width: 100px; min-width: 100px;">Дата звонка';
//            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellFullName" style="font-size: 80%; text-align: center;">Пациент</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Контакты</div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Комментарий</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Добавил</div>
							<div class="cellTime" style="font-size: 80%; text-align: center;">Время доб.</div>
						</li>';

            $query = "
            SELECT j_pc.*, s_c.full_name, s_c.telephone, s_c.htelephone, s_c.telephoneo, s_c.htelephoneo, s_w.name AS worker_name
            FROM `journal_phone_calling` j_pc 
            LEFT JOIN `spr_clients` s_c ON s_c.id = j_pc.client_id
            LEFT JOIN `spr_workers` s_w ON s_w.id = j_pc.create_person
            $dopQuery
            ORDER BY j_pc.call_time DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";

            //Выбрать все
            $calls_j = $db::getRows($query, $args);
			
			if (!empty($calls_j)){
			    //var_dump($calls_j);

                //Соберем данные по типам абонементов
//                $abon_types_j = array();
//
//                $msql_cnnct = ConnectToDB ();
//
//                $query = "SELECT `id`, `name` FROM `spr_solar_abonements` WHERE `status` <> '9'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        //array_push($abon_types_j, $arr);
//
//                        $abon_types_j[$arr['id']] = $arr;
//                    }
//                }
                //var_dump($abon_types_j);

				for ($i = 0; $i < count($calls_j); $i++) {

                    $call_mark = '';

					if ($calls_j[$i]['status'] == 8) {
                        $call_mark = '<i class="fa fa-phone-square" style="color: red; font-size: 140%;" title="Не звонить"></i>';
                    }elseif ($calls_j[$i]['status'] == 6){
                        $call_mark = '<i class="fa fa-phone-square" style="color: orange; font-size: 140%;" title="Не дозвонились"></i>';
                    }elseif ($calls_j[$i]['status'] == 7){
                        $call_mark = '<i class="fa fa-phone-square" style="color: blue; font-size: 140%;" title="Записались"></i>';
					}else{
                        $call_mark = '<i class="fa fa-phone-square" style="color: #dcdcdc; font-size: 140%;" title="Нет отметки"></i>';
					}

//                    $expired_color = '';
//                    $expired_txt = '';
//
//                    if (($calls_j[$i]['expires_time'] != '0000-00-00') && ($calls_j[$i]['status'] != 5)) {
//                        //время истечения срока годности
//                        $sd = $calls_j[$i]['expires_time'];
//                        //текущее
//                        $cd = date('Y-m-d', time());
//                        /*var_dump(strtotime($sd));
//                        var_dump(strtotime($cd));*/
//                        //сравнение не прошла ли гарантия
//                        if (strtotime($sd) > strtotime($cd)) {
//                            $expired_txt .= '';
//                        } else {
//                            $expired_color = 'background-color: rgba(239,47,55, .7)';
//                            $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
//                            $status = 'Истёк срок '.date('d.m.y', strtotime($calls_j[$i]['expires_time']));
//                        }
//
//                    }


					echo '
							<li class="cellsBlock3 cellsBlockHover" style="">
								<div class="cellCosmAct" style="text-align: center;">'.$call_mark.'</div>
								<div class="cellOffice" style="text-align: center; width: 100px; min-width: 100px; font-size: 80%;">'.date('d.m.Y', strtotime($calls_j[$i]['call_time'])).'</div>
								<a href="client.php?id='.$calls_j[$i]['client_id'].'" class="cellFullName ahref" style="text-align: left; font-size: 80%;" target="_blank" rel="nofollow noopener">'.$calls_j[$i]['full_name'].'</a>
								<div class="cellTime" style="text-align: left; font-size: 80%;">';

                    echo $calls_j[$i]['telephone'].''.$calls_j[$i]['htelephone'].''.$calls_j[$i]['telephoneo'].''.$calls_j[$i]['htelephoneo'];

					echo '
                                </div>
								<div class="cellText" style="text-align: left; font-size: 80%;">'.$calls_j[$i]['comment'].'</div>
                                <div class="cellTime" style="font-size: 80%; text-align: center;">'.$calls_j[$i]['worker_name'].'</div>
                                <div class="cellTime" style="font-size: 70%; text-align: center;">'.date('d.m.Y H:i', strtotime($calls_j[$i]['create_time'])).'</div>
							</li>';
				}
			}

			echo '
					</ul>
					
					<div id="doc_title">Статистика звонков</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>