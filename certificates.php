<?php

//certificates.php
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

            $msql_cnnct = ConnectToDB ();

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
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

		    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
				echo '
					<a href="cert_add.php" class="b">Добавить</a>';
		    }
			echo '
						<div id="data">';

            //Пагинатор
            echo paginationCreate ($limit_pos[1], $_GET['page'], 'journal_cert', 'certificates.php', $msql_cnnct, $dop);

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
                        if (strtotime($sd) > strtotime($cd)) {
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
                        $offices_j = SelDataFromDB('spr_filials', $cert_j[$i]['office_id'], 'offices');
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