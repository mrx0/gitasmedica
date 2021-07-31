<?php

//orgs.php
//Список организаций

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		echo '
			<header>
				<h1>Организации</h1>
			</header>';

		echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
		echo '
                            <li class="cellsBlock" style="font-weight:bold;">	
                                <div class="cellPriority" style="text-align: center"></div>
                                <div class="cellOffice" style="text-align: center; width: 180px; min-width: 180px;">
						            Организация';
        echo $block_fast_filter;
        echo '
                                </div>
                                <div class="cellAddress" style="text-align: center">ИНН</div>
                                <div class="cellText" style="text-align: center">Юр. адрес</div>
                                <div class="cellCosmAct" style="text-align: center">-</div>
                            </li>';
		
		include_once 'DBWork.php';
		$orgs = SelDataFromDB('spr_org', '', '');
		//var_dump ($orgs);
		
		if ($orgs !=0){
			for ($i = 0; $i < count($orgs); $i++) {
                $bgColor = '';
			    if ($orgs[$i]['status'] == 9){
                    $bgColor = "background-color: #8C8C8C;";
                }
				echo '
                            <li class="cellsBlock" style="'.$bgColor.'">
                                <div class="cellPriority" style="background-color:"></div>
                                <a href="org.php?id='.$orgs[$i]['id'].'" class=" ahref cellOffice 4filter" style="text-align: center; width: 180px; min-width: 180px;" id="4filter">'.$orgs[$i]['name'].' ['.$orgs[$i]['full_name'].']</a>
                                <div class="cellAddress" style="text-align: left">'.$orgs[$i]['inn'].'</div>
                                <div class="cellText" style="text-align: left">'.$orgs[$i]['ur_address'].'</div>
                                <div class="cellCosmAct" style="text-align: center; "></div>
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