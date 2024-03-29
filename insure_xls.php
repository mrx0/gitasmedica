<?php

//insure_xls.php
//Выгрузки по страховым

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($spravka['see_all'] == 1) || $god_mode   || ($_SESSION['id'] == 719)){

            //include_once 'DBWork.php';
            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 30;
            $pages = 0;
            $dop = '';
            $dop_link_str = '';

            //Для выделения кнопки
            $option1_color = 'background-color: #fff261;';
            $option2_color = '';
            $option3_color = '';
            $option4_color = '';
            $option5_color = '';

            $current_page = '';

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
                $current_page = 'page='.$_GET['page'];
            }else{
                $_GET['page'] = 1;
            }

			echo '
				<header>
					<h1>Выгрузки по страховым</h1>
				</header>';

            $msql_cnnct = ConnectToDB ();

            //Пагинатор
            echo paginationCreate ($limit_pos[1], $_GET['page'], 'journal_insure_download', 'insure_xls.php', $msql_cnnct, $dop, $dop_link_str);

			echo '
						<!--<p style="margin: 5px 0; padding: 2px;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						</p>-->
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px; font-size: 11px;">';
			echo '
						<li class="cellsBlock3" style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellOffice" style="text-align: center;">Дата</div>
							<div class="cellOffice" style="text-align: center;">Филиал</div>
							<div class="cellOffice" style="text-align: center;">Страховая</div>
							<div class="cellOffice" style="text-align: center;">Автор</div>
							<div class="cellText" style="text-align: center;">Комментарий</div>
							<div class="cellOffice" style="text-align: center;">Ссылка</div>
						</li>';

//            $insure_xls_j = SelDataFromDB('journal_insure_download', '', '');
            //var_dump ($insure_xls_j);

            $db = new DB();

            $args = [

            ];

            $query = "SELECT * FROM `journal_insure_download` ORDER BY `id` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]}";

            $insure_xls_j = $db::getRows($query, $args);

            $deleted_str = '';
            $non_exist_str = '';

			if (!empty($insure_xls_j)){
				for ($i = 0; $i < count($insure_xls_j); $i++) {

				    $path = 'download\insure_xls'.'\\'.$insure_xls_j[$i]['id'].'.xls';
                    $insure_j = SelDataFromDB('spr_insure', $insure_xls_j[$i]['insure_id'], 'id');
                    $office_j = SelDataFromDB('spr_filials', $insure_xls_j[$i]['office_id'], 'id');

					if ($insure_xls_j[$i]['status'] == 9) {
                        $deleted_str .= '
                            <li class="cellsBlock3" style="margin-bottom: -1px; background-color: rgba(161,161,161,1);">	
                                <div class="cellPriority" style="text-align: center"></div>
                                <div class="cellOffice" style="text-align: center;">'.date('d.m.Y H:i',  strtotime($insure_xls_j[$i]['create_time'])).'</div>
                                <div class="cellOffice" style="text-align: center;">'.$office_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.$insure_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.WriteSearchUser('spr_workers', $insure_xls_j[$i]['create_person'], 'user', true).'</div>
                                <div class="cellText" style="text-align: center;">#'.$insure_xls_j[$i]['id'].' '.$insure_xls_j[$i]['comment'].'</div>
                                <div class="cellOffice" style="text-align: center;"><a href="'.$path.'" class="ahref">Скачать <i class="fa fa-file-excel-o" aria-hidden="true"></i></a></div>
                            </li>';
					}else{
                        if (file_exists ($path)){
                            echo '
                            <li class="cellsBlock3" style="margin-bottom: -1px;">	
                                <div class="cellPriority" style="text-align: center"></div>
                                <div class="cellOffice" style="text-align: center;">'.date('d.m.Y H:i',  strtotime($insure_xls_j[$i]['create_time'])).'</div>
                                <div class="cellOffice" style="text-align: center;">'.$office_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.$insure_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.WriteSearchUser('spr_workers', $insure_xls_j[$i]['create_person'], 'user', true).'</div>
                                <div class="cellText" style="text-align: center;">#'.$insure_xls_j[$i]['id'].' '.$insure_xls_j[$i]['comment'].'</div>
                                <div class="cellOffice" style="text-align: center;"><a href="'.$path.'" class="ahref">Скачать <i class="fa fa-file-excel-o" aria-hidden="true"></i></a></div>
                            </li>';
                        }else{
                            $non_exist_str .= '
                            <li class="cellsBlock3" style="margin-bottom: -1px;">	
                                <div class="cellPriority" style="text-align: center"></div>
                                <div class="cellOffice" style="text-align: center;">'.date('d.m.Y H:i',  strtotime($insure_xls_j[$i]['create_time'])).'</div>
                                <div class="cellOffice" style="text-align: center;">'.$office_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.$insure_j[0]['name'].'</div>
                                <div class="cellOffice" style="text-align: center;">'.WriteSearchUser('spr_workers', $insure_xls_j[$i]['create_person'], 'user', true).'</div>
                                <div class="cellText" style="text-align: center;">#'.$insure_xls_j[$i]['id'].' '.$insure_xls_j[$i]['comment'].'</div>
                                <div class="cellOffice" style="text-align: center;"><span class="query_neok">Не найден файл</span></div>
                            </li>';
                        }
                        //

					}
				}

				if ($god_mode){
				    echo $deleted_str;
                }
                echo $non_exist_str;

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