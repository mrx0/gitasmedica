<?php

//filials.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		echo '
			<header>
				<h1>Координаты филиалов</h1>
			</header>';

		echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
		echo '
                            <li class="cellsBlock" style="font-weight:bold;">	
                                <div class="cellPriority" style="text-align: center"></div>
                                <div class="cellOffice" style="text-align: center; width: 180px; min-width: 180px;">
						            Филиал';
        echo $block_fast_filter;
        echo '
                                </div>
                                <div class="cellAddress" style="text-align: center">Адрес</div>
                                <div class="cellText" style="text-align: center">Контакты</div>
                                <div class="cellCosmAct" style="text-align: center">-</div>
                            </li>';
		
		include_once 'DBWork.php';
		$offices = SelDataFromDB('spr_filials', '', '');
		//var_dump ($offices);
		
		if ($offices !=0){
			for ($i = 0; $i < count($offices); $i++) {
                $bgColor = '';
			    if ($offices[$i]['status'] == 9){
                    $bgColor = "background-color: #8C8C8C;";
                }
				echo '
                            <li class="cellsBlock" style="'.$bgColor.'">
                                <div class="cellPriority" style="background-color:"></div>
                                <a href="filial.php?id='.$offices[$i]['id'].'" class=" ahref cellOffice 4filter" style="text-align: center; width: 180px; min-width: 180px;" id="4filter">'.$offices[$i]['name'].'</a>
                                <div class="cellAddress" style="text-align: left">'.$offices[$i]['address'].'</div>
                                <div class="cellText" style="text-align: left">'.$offices[$i]['contacts'].'</div>
                                <div class="cellCosmAct" style="text-align: center; background-color: rgba('.$offices[$i]['color'].')"></div>
                            </li>';
			}
		}

		echo '
				        </ul>
			        </div>';

        echo '
                    <div id="doc_title">Филиалы</div>';

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>