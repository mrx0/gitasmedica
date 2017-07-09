<?php

//certificates.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){
			
			echo '
				<header>
					<h1>Сертификаты</h1>
				</header>';
		if (($spravka['add_new'] == 1) || $god_mode){
				echo '
					<a href="cert_add.php" class="b">Добавить</a>';
			}
			echo '
						<p style="margin: 5px 0; padding: 2px;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						</p>
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock3" style="font-weight:bold; margin-bottom: -1px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellOffice" style="text-align: center;">Номер</div>
							<div class="cellOffice" style="text-align: center;">Номинал</div>
							<div class="cellOffice" style="text-align: center;">Остаток</div>
							<div class="cellText" style="text-align: center;">Статус</div>
						</li>';
			
			include_once 'DBWork.php';
			$cert_j = SelDataFromDB('journal_cert', '', '');
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
					}else{
                        $back_color = '';
					}
					echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="certificate.php?id='.$cert_j[$i]['id'].'" class="cellOffice ahref" style="text-align: left; font-weight: bold;" id="4filter">'.$cert_j[$i]['num'].'</a>
								<div class="cellOffice" style="text-align: right">'.$cert_j[$i]['nominal'].' руб.</div>
								<div class="cellOffice" style="text-align: right">';
                    if (($cert_j[$i]['status'] == 7) && ($cert_j[$i]['status'] != '0000-00-00 00:00:00')) {
                        echo ($cert_j[$i]['nominal'] - $cert_j[$i]['debited']).' руб.';
                    }
                    echo '
                                 </div>';
                    echo '
								<div class="cellText" style="text-align: center;">'.$status.'</div>';
                    echo '
							</li>';
				}
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