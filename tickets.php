<?php

//tickets.php
//Задачи

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
            $limit_pos[1] = 20;
            $pages = 0;

            if (isset($_GET)){
                if (isset($_GET['page'])){
                    $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
                }
            }

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
            $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id FROM `journal_tickets` j_ticket
            LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
            LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
            /*WHERE j_ticket.status <> '9'*/
            WHERE TRUE
            {$query_dop}
            
            OR j_ticket.create_person = '{$_SESSION['id']}'
            
            ORDER BY `plan_date` ASC, `create_time` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";


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

            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($tickets_arr, $arr);
                }
            }
            //echo $query;
            //var_dump ($tickets_arr);

            $filials_j = getAllFilials(false);
            //var_dump ($filials_j);

            CloseDB ($msql_cnnct2);

            echo '
				<header>
					<h1>Все задачи</h1>
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo '
					</div>';

            if (($ticket['add_new'] == 1) || ($ticket['add_own'] == 1) || $god_mode){
                echo '
					<a href="ticket_add.php" class="b4">Новая задача</a>';
            }

            if (!empty($tickets_arr)){

                //Для пагинатора
                if ($number != 0) {

                    $pages = intval($number/$limit_pos[1]);

                    for ($i=0; $i <= $pages; $i++) {
                        $paginator_str .= '<a class="paginator_btn" href="tickets.php?page='.($i+1).'">'.($i+1).'</a> ';
                    }
                }

                echo '
						<div id="data">
						    <div style="margin: 2px 6px 3px;">
						        '.$paginator_str.'
						    </div>';

                foreach ($tickets_arr as $j_tickets) {

                    $ticket_style = 'ticketBlock';

                    //Если просрочен
                    if ($j_tickets['plan_date'] != '0000-00-00') {
                        //время истечения срока
                        $pd = $j_tickets['plan_date'];
                        //текущее
                        $nd = date('Y-m-d', time());
                        //сравнение не прошли ли сроки исполнения
                        if (strtotime($pd) > strtotime($nd)) {
                            $expired = false;
                        } else {
                            if (strtotime($pd) === strtotime($nd)){
                                $expired = true;
                                $ticket_style = 'ticketBlockexpired2';
                            }else {
                                $expired = true;
                                $ticket_style = 'ticketBlockexpired';
                            }
                        }
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
                    if (strlen($j_tickets['descr']) > 200){
                        $descr = mb_strimwidth($j_tickets['descr'], 0, 100, "...", 'utf-8');
                    }else{
                        $descr = $j_tickets['descr'];
                    }

                    echo '
                        <div class="'.$ticket_style.'" style="font-size: 95%;">
                            <div class="ticketBlockheader">
                                <div style="margin-left: 5px; text-align: left; float: left;">
                                    <span style=" font-color: rgb(115, 112, 112); font-size: 80%; font-weight: bold; margin-right: 3px;">#'.$j_tickets['id'].'</span>';
                    if (!$ticket_deleted) {
                        if ($ticket_done) {
                            echo '                                    
                                    <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                                    <span style=" font-color: rgb(115, 112, 112); font-size: 80%;">' . date('d.m.Y', strtotime($j_tickets['fact_date'])) . '</span>';
                        } else {
                            if ($j_tickets['plan_date'] != '0000-00-00') {
                                echo '
                                    <span style=" font-color: rgb(115, 112, 112); font-size: 80%;">до ' . date('d.m.Y', strtotime($j_tickets['plan_date'])) . '</span>';
                                   //<i class="fa fa-times" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                            }
                        }
                        if (!$ticket_done && $expired) {
                            echo '
                                    <i class="fa fa-exclamation" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>';
                        }
                    }else{
                        echo '
                                    <span style=" font-color: rgb(115, 112, 112); font-size: 80%;">удалён</span>';
                    }
                    echo '
                                </div>
                                <div style="margin-right: 5px; text-align: right; float: right;">';
                    if ($ticket_deleted){
                        echo '
                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(244, 244, 244, 0.8); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
                    }else {
                        if ($_SESSION['id'] == $j_tickets['worker_id']){
                            echo '                        
                                        <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>';
                        }
                        if (!$ticket_done && $newTopic) {
                            echo '                        
                                        <i class="fa fa-bell" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>';
                        }
                    }
                    
                    echo '
                                </div>
                            </div>
                            <a href="ticket.php?id='.$j_tickets['id'].'" class="ticketBlockmain">
                                '.$descr.'<br>
                            </a>
                            <div class="ticketBlockfooter">
                                <!--создан '.date('d.m.y H:i', strtotime($j_tickets['create_time'])).'<br>-->
                                автор: '.WriteSearchUser('spr_workers', $j_tickets['create_person'], 'user', false).'<br>
                                где создано: ', $j_tickets['filial_id']==0 ? 'не указано' : $filials_j[$j_tickets['filial_id']]['name'] ,'
                            </div>
                        </div>';
                }
            }



			/*include_once 'DBWork.php';


			$cert_j = SelDataFromDB('journal_cert', '', $limit_pos);
			//var_dump ($cert_j);
			
			if ($cert_j !=0){
				for ($i = 0; $i < count($cert_j); $i++) {

                    $status = '';

					if ($cert_j[$i]['status'] == 9) {
                        $back_color = 'background-color: rgba(161,161,161,1);';
                        $status = 'Удалён';
                    }elseif ($cert_j[$i]['status'] == 7){
                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                        $status = 'Продан '.date('d.m.y H:i', strtotime($cert_j[$i]['cell_time']));
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
             /*           if (strtotime($sd) > strtotime($cd)) {
                            $expired_txt .= '';
                        } else {
                            $expired_color = 'background-color: rgba(239,47,55, .7)';
                            $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
                            $status = 'Истёк срок '.date('d.m.y', strtotime($cert_j[$i]['expires_time']));
                        }

                    }


					echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="certificate.php?id='.$cert_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; font-weight: bold; width: 180px; min-width: 180px;" id="4filter">'.$cert_j[$i]['num'].'</a>
								<div class="cellOffice" style="text-align: right">'.$cert_j[$i]['nominal'].' руб.</div>
								<div class="cellOffice" style="text-align: right">';
                    if (($cert_j[$i]['status'] == 7) && ($cert_j[$i]['status'] != '0000-00-00 00:00:00')) {
                        echo ($cert_j[$i]['nominal'] - $cert_j[$i]['debited']).' руб.';
                    }
                    echo '
                                 </div>';
                    echo '
								<div class="cellText" style="text-align: center;">'.$status.'<br>';
                    if ($cert_j[$i]['office_id'] != 0) {
                        $offices_j = SelDataFromDB('spr_office', $cert_j[$i]['office_id'], 'offices');
                        if ($offices_j != 0) {
                            echo '<span style="font-size: 70%;">'.$offices_j[0]['name'].'</span>';
                        }else {
                            echo '-';
                        }
                    }
                    echo '
                                </div>';
                    echo '
							</li>';
				}
			}*/

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