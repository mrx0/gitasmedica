<?php

//certificates.php
//

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {

            include_once 'functions.php';

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 20;
            $pages = 0;
            $dop = '';
            $dop_link_str = '';

            //$filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //Для выделения кнопки
            $option1_color = 'background-color: #fff261;';
            $option2_color = '';
            $option3_color = '';
            $option4_color = '';
            $option5_color = '';

            $current_page = '';

            $msql_cnnct = ConnectToDB ();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page'] - 1) * $limit_pos[1];
                $current_page = 'page=' . $_GET['page'];
            }else{
                $_GET['page'] = 1;
            }


            echo '
				<header>
					<h1>Сертификаты</h1>
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo $block_fast_search_certificate;

            echo '
					</div>';

            if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode) {
                echo '
					<a href="cert_add.php" class="b">Добавить</a>';
            }

            //var_dump($dop);

            if (isset($_GET['option'])) {
                //Проданные, не потраченные
                if ($_GET['option'] == 2) {
                    $dop = "WHERE `status`='7' AND `nominal` <> `debited` AND `expires_time` > '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=2';
                    $option1_color = '';
                    $option2_color = 'background-color: #fff261;';
                }
                //Проданные истёк срок
                if ($_GET['option'] == 3) {
                    $dop = "WHERE `status`='7' AND `nominal` <> `debited` AND `expires_time` < '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=3';
                    $option1_color = '';
                    $option3_color = 'background-color: #fff261;';
                }
                //Проданные, истёк срок, не потраченные
                if ($_GET['option'] == 5) {
                    $dop = "WHERE `status`='7' AND `nominal` <> `debited` AND `debited` <> '0' AND `expires_time` < '" . date('Y-m-d', time()) . "'";
                    $dop_link_str = 'option=5';
                    $option1_color = '';
                    $option5_color = 'background-color: #fff261;';
                }
                //Закрытые
                if ($_GET['option'] == 4) {
                    $dop = "WHERE `status`='5'";
                    $dop_link_str = 'option=4';
                    $option1_color = '';
                    $option4_color = 'background-color: #fff261;';
                }
            }

            echo '
						<div id="data">';
            echo '
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите опцию просмотра</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?" class="b" style="' . $option1_color . '">Все</a>
								<a href="?&option=2" class="b" style="' . $option2_color . '">Проданные, не потраченные</a>
								<a href="?&option=3" class="b" style="' . $option3_color . '">Проданные, истёк срок</a>
								<a href="?&option=5" class="b" style="' . $option5_color . '">Проданные, истёк срок, потраченные частично</a>
								<a href="?&option=4" class="b" style="' . $option4_color . '">Закрытые</a>
								<!--<a href="?&option=5" class="b" style="' . $option5_color . '"></a>-->
							</li>';

            //Пагинатор
            echo paginationCreate($limit_pos[1], $_GET['page'], 'journal_cert', 'certificates.php', $msql_cnnct, $dop, $dop_link_str);

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
            echo '
						<li class="cellsBlock3" style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellOffice" style="text-align: center; width: 180px; min-width: 180px;">Номер';
            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellOffice" style="text-align: center;">Номинал</div>
							<div class="cellOffice" style="text-align: center;">Остаток</div>
							<div class="cellText" style="text-align: center;">Статус</div>
						</li>';

            include_once 'DBWork.php';

            //var_dump($limit_pos);

            $cert_j = array();
            //$cert_j = SelDataFromDB('journal_cert', '', $limit_pos);
            //var_dump ($cert_j);
            $query = "SELECT * FROM `journal_cert` {$dop} ORDER BY `num` ASC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";

            if (isset($_GET['option'])) {
                if ($_GET['option'] == 2) {
                    $query = "SELECT * FROM `journal_cert` {$dop} ORDER BY `expires_time` ASC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 3) {
                    $query = "SELECT * FROM `journal_cert` {$dop} ORDER BY `expires_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 5) {
                    $query = "SELECT * FROM `journal_cert` {$dop} ORDER BY `expires_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
                if ($_GET['option'] == 4) {
                    $query = "SELECT * FROM `journal_cert` {$dop} ORDER BY `closed_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";
                }
            }
//            var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($cert_j, $arr);
                }

            }
            CloseDB ($msql_cnnct);
//            var_dump($cert_j);
			
			if (!empty($cert_j)){
				for ($i = 0; $i < count($cert_j); $i++) {

                    $status = '';

					if ($cert_j[$i]['status'] == 9) {
                        $back_color = 'background-color: rgba(161,161,161,1);';
                        $status = 'Удалён';
                    }elseif ($cert_j[$i]['status'] == 7){
                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                        $status = '<div style="font-size: 90%;">Продан '.date('d.m.y H:i', strtotime($cert_j[$i]['cell_time'])).'</div>';

                        $expires_time_color = '';

                        $expirestime1weekminus = date('Y-m-d', strtotime(date('Y-m-d', strtotime($cert_j[$i]['expires_time'])).' -2 weeks'));
                        //var_dump($expirestime1weekminus);

                        if (date('Y-m-d', time()) > $expirestime1weekminus) {
                            $expires_time_color = 'color: rgb(236 62 62);';
                        }

                        $status .= '
                                    <div style="font-size: 85%; '.$expires_time_color.'"><b>Срок истекает: '.date('d.m.Y', strtotime($cert_j[$i]['expires_time'])).'</b></div>';
                    }elseif ($cert_j[$i]['status'] == 5){
                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                        $status = 'Закрыт '.date('d.m.y H:i', strtotime($cert_j[$i]['closed_time']));
					}else{
                            $back_color = '';
					}

                    $expired_color = '';
                    $expired_txt = '';

                    if (($cert_j[$i]['expires_time'] != '0000-00-00') && ($cert_j[$i]['status'] != 5)) {
                        //время истечения срока годности
                        $sd = $cert_j[$i]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        /*var_dump(strtotime($sd));
                        var_dump(strtotime($cd));*/
                        //сравнение не прошла ли гарантия
                        if (strtotime($sd) > strtotime($cd)) {
                            $expired_txt .= '';
                        } else {
                            $expired_color = 'background-color: rgba(239,47,55, .7)';
                            $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
                            $status = 'Истёк срок '.date('d.m.Y', strtotime($cert_j[$i]['expires_time']));
                        }

                    }


					echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="certificate.php?id='.$cert_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; font-weight: bold; width: 180px; min-width: 180px;" id="4filter">'.$cert_j[$i]['num'].'</a>
								<div class="cellOffice" style="text-align: right">'.$cert_j[$i]['nominal'].' руб.</div>
								<div class="cellOffice" style="text-align: right">';
                    //Очень странное условие, не помню, что тут должно было быть
                    //if (($cert_j[$i]['status'] == 7) && ($cert_j[$i]['status'] != '0000-00-00 00:00:00')) {
                    //Поменял его на это
                    if ($cert_j[$i]['status'] == 7) {
                        echo ($cert_j[$i]['nominal'] - $cert_j[$i]['debited']).' руб.';
                    }
                    echo '
                                 </div>';
                    echo '
								<div class="cellText" style="text-align: center;">'.$status.'';
                    if ($cert_j[$i]['office_id'] != 0) {
                        $offices_j = SelDataFromDB('spr_filials', $cert_j[$i]['office_id'], 'offices');
                        if ($offices_j != 0) {
                            echo '<div style="font-size: 90%;"><i>'.$offices_j[0]['name'].'</i></div>';
                        }else {
                            echo '-';
                        }
                    }
                    echo '
                                </div>';
                    echo '
							</li>';
				}
			}

			echo '
					</ul>
					
					<div id="doc_title">Сертификаты</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>