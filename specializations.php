<?php

//specializations.php
//

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){

        echo '
				<header>
					<h1>Специализации</h1>
				</header>';
        if (($spravka['add_new'] == 1) || $god_mode){
            echo '
					<a href="specialization_add.php" class="b">Добавить</a>';
        }
        echo '
						<p style="margin: 5px 0; padding: 2px;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						</p>
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
        echo '
						<li class="cellsBlock3" style="font-weight:bold;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellOffice" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Название</div>
						</li>';

        include_once 'DBWork.php';
        $specialization_j = SelDataFromDB('spr_specialization', '', '');
        //var_dump ($insure_j);

        if ($specialization_j !=0){
            for ($i = 0; $i < count($specialization_j); $i++) {
                if ($specialization_j[$i]['status'] == 9){
                    $bgcolor = 'background-color: rgba(161,161,161,1);';
                }else{
                    $bgcolor = '';
                }
                echo '
							<li class="cellsBlock3" style="'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="specialization.php?id='.$specialization_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px; font-weight: bold;" id="4filter">'.$specialization_j[$i]['name'].'</a>
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