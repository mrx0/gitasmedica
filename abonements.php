<?php

//abonements.php
//

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){

            include_once 'functions.php';

            //Деление на странички пагинатор paginator
            $limit_pos[0] = 0;
            $limit_pos[1] = 20;
            $pages = 0;
            $dop = '';

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            $msql_cnnct = ConnectToDB ();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }else{
                $_GET['page'] = 1;
            }


			echo '
				<header>
					<h1>Абонементы</h1>
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo $block_fast_search_abonement;

            echo '
					</div>';

		    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
				echo '
					<a href="abonement_add.php" class="b">Добавить</a>';
		    }
			echo '
						<div id="data">';

            //Пагинатор
            echo paginationCreate ($limit_pos[1], $_GET['page'], 'journal_abonement_solar', 'abonements.php', $msql_cnnct, $dop);

            echo '		    
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3" style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellPriority" style="font-size: 80%; text-align: center"></div>
							<div class="cellOffice" style="font-size: 80%; text-align: center; width: 180px; min-width: 180px;">Номер';
            echo $block_fast_filter;
            echo '
                            </div>
							<div class="cellOffice" style="font-size: 80%; text-align: center;">Название</div>
							<div class="cellOffice" style="font-size: 80%; text-align: center;">Минут<br><span style="font-size: 80%;">(всего/потрачено/ост.)</span></div>
							<div class="cellText" style="font-size: 80%; text-align: center;">Статус</div>
						</li>';
			
			include_once 'DBWork.php';


			$abonements_j = SelDataFromDB('journal_abonement_solar', '', $limit_pos);
			//var_dump ($abonements_j);
			
			if ($abonements_j !=0){

                //Соберем данные по типам абонементов
                $abon_types_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT `id`, `name` FROM `spr_solar_abonements` WHERE `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($abon_types_j, $arr);

                        $abon_types_j[$arr['id']] = $arr;
                    }
                }
                //var_dump($abon_types_j);

				for ($i = 0; $i < count($abonements_j); $i++) {

                    $status = '';

					if ($abonements_j[$i]['status'] == 9) {
                        $back_color = 'background-color: rgba(161,161,161,1);';
                        $status = 'Удалён';
                    }elseif ($abonements_j[$i]['status'] == 7){
                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                        $status = 'Продан '.date('d.m.y H:i', strtotime($abonements_j[$i]['cell_time']));
                    }elseif ($abonements_j[$i]['status'] == 5){
                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                        $status = 'Закрыт '.date('d.m.y H:i', strtotime($abonements_j[$i]['closed_time']));
					}else{
                            $back_color = '';
					}

                    $expired_color = '';
                    $expired_txt = '';

                    if (($abonements_j[$i]['expires_time'] != '0000-00-00') && ($abonements_j[$i]['status'] != 5)) {
                        //время истечения срока годности
                        $sd = $abonements_j[$i]['expires_time'];
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
                            $status = 'Истёк срок '.date('d.m.y', strtotime($abonements_j[$i]['expires_time']));
                        }

                    }


					echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="abonement.php?id='.$abonements_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; font-weight: bold; width: 180px; min-width: 180px;" id="4filter">'.$abonements_j[$i]['num'].'</a>
								<div class="cellOffice" style="text-align: left; font-size: 80%;">'.$abon_types_j[$abonements_j[$i]['abon_type']]['name'].'</div>
								<div class="cellOffice" style="text-align: right">';
//                    if (($abonements_j[$i]['status'] == 7) && ($abonements_j[$i]['status'] != '0000-00-00 00:00:00')) {
//                        echo ($abonements_j[$i]['nominal'] - $abonements_j[$i]['debited']).' руб.';
//                    }

                    echo $abonements_j[$i]['min_count'].' / '.$abonements_j[$i]['debited_min'].' / '.($abonements_j[$i]['min_count'] - $abonements_j[$i]['debited_min']);

                    echo '
                                 </div>';
                    echo '
								<div class="cellText" style="text-align: center;">'.$status.'<br>';
                    if ($abonements_j[$i]['filial_id'] != 0) {

                        if (!empty($filials_j)) {
                            echo '<span style="font-size: 70%;">'.$filials_j[$abonements_j[$i]['filial_id']]['name'].'</span>';
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
					
					<div id="doc_title">Абонементы</div>
					
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>