<?php

//fl_percent_cats.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || $god_mode){
			
			echo '
				<header>
        			<div class="nav">
					    <a href="fl_percent_cats_personal.php" class="b">Персональные</a>
					</div>
					<h1>Категории процентов</h1>
				</header>';
		    if (($finances['add_new'] == 1) || $god_mode){
				echo '
					<a href="fl_percent_cat_add.php" class="b">Добавить</a>';
			}
			echo '
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock2" style="font-weight: bold; font-size: 11px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellName" style="text-align: center; width: 180px; min-width: 180px;">
                                Название';
            echo $block_fast_filter;
            echo '
							</div>
							<div class="cellTime" style="text-align: center">Процент за работу (общий)</div>
							<div class="cellTime" style="text-align: center;">Процент за материал (общий)</div>
							<div class="cellText" style="text-align: center;">Персонал</div>
						</li>';
			
			include_once 'DBWork.php';
			$percents_j = SelDataFromDB('fl_spr_percents', '', '');
			//var_dump ($percents_j);

            //!!! @@@
            include_once 'ffun.php';

            //Ну вроде все норм, поехали всё обновлять/сохранять
            $msql_cnnct = ConnectToDB2 ();

            $permissions_j = array();

            $msql_cnnct = ConnectToDB2 ();

            $query = "SELECT `id`, `name` FROM `spr_permissions`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $permissions_j[$arr['id']] = $arr['name'];
                }
            }


			if ($percents_j !=0){
				for ($i = 0; $i < count($percents_j); $i++) {
					if ($percents_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = 'background-color: rgba('.$percents_j[$i]['color'].',1);';
					}
					echo '
							<li class="cellsBlock2 cellsBlockHover" style="font-weight: bold; font-size: 11px;'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="fl_percent_cat.php?id='.$percents_j[$i]['id'].'" class="cellName ahref" style="text-align: left; width: 180px; min-width: 180px;" id="4filter">'.$percents_j[$i]['name'].'</a>
                                <div class="cellTime" style="text-align: center">'.$percents_j[$i]['work_percent'].'</div>
                                <div class="cellTime" style="text-align: center;">'.$percents_j[$i]['material_percent'].'</div>
                                <div class="cellText" style="text-align: center;">'.$permissions_j[$percents_j[$i]['type']].'</div>
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