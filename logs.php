<?php

//logs.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if ($god_mode){
			include_once 'DBWork.php';
			//$offices = SelDataFromDB('spr_filials', '', '');

            //Деление на странички пагинатор paginator
            $paginator_str = '';
            $limit_pos[0] = 0;
            $limit_pos[1] = 30;
            $pages = 0;

            if (isset($_GET['page'])){
                $limit_pos[0] = ($_GET['page']-1) * $limit_pos[1];
            }
            $number = 0;

			echo '
			<style type="text/css">
				.loading_overlay{	
					position: fixed;
					width: 100%;
					height: 100%;
					opacity: 1;
					visibility: visible;
					top: 0;
					left: 0;
					z-index: 1000;
					background: rgba(196,196,96,.8);
					-webkit-transition: all 0.3s;
					-moz-transition: all 0.3s;
					transition: all 0.3s;
				}
			</style>
			<!--<div class="loading_overlay"></div>-->
			
				<header style="margin-bottom: 5px;">
					<h1>Логи</h1>
				</header>		
				<!--<p style="margin: 5px 0; padding: 1px; font-size:80%;">
					Быстрый поиск по врачу: 
					<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
				</p>-->
				<div id="data">
					<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
				
			//$logs = SelDataFromDB ('logs', '', '');
			$logs_j = array();

            $msql_cnnct = ConnectToDB ();


            $query = "SELECT * FROM `logs` ORDER BY `date` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]};";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($logs_j, $arr);
                }
            }

            //Хочу получить общее количество
            $query = "SELECT COUNT(*) AS total_logs_id FROM
            `logs`;";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                $arr = mysqli_fetch_assoc($res);
                $total_logs_ids = $arr['total_logs_id'];
            }else{
                $total_logs_ids = 0;
            }


            if (!empty($logs_j)){
				//var_dump ($logs_j);

                //Для пагинатора
                if ($number != 0) {

                    $pages = (int)ceil($total_logs_ids/$limit_pos[1]);
                    //var_dump($pages);

                    if ($pages > 10){
                        $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';

                        if (($_GET['page'] == 1) || ($_GET['page'] == $pages)){
                            $paginator_str .= '<a href="logs.php?page=1" class="paginator_btn" style="';

                            if ($_GET['page'] == 1){
                                $paginator_str .= $pg_btn_bgcolor;
                            }

                            $paginator_str .= '">1</a> ';

                            $paginator_str .= '<a href="logs.php?page=2" class="paginator_btn" style="';

                            if ($_GET['page'] == 2){
                                $paginator_str .= $pg_btn_bgcolor;
                            }

                            $paginator_str .= '">2</a> ... ';

                            $paginator_str .= '<a href="logs.php?page=' . ($pages-1) . '" class="paginator_btn" style="';

                            if ($_GET['page'] == $pages-1){
                                $paginator_str .= $pg_btn_bgcolor;
                            }

                            $paginator_str .= '">' . ($pages-1) . '</a>';
                            $paginator_str .= '</a> ';

                            $paginator_str .= '<a href="logs.php?page=' . ($pages) . '" class="paginator_btn" style="';

                            if ($_GET['page'] == $pages){
                                $paginator_str .= $pg_btn_bgcolor;
                            }

                            $paginator_str .= '">' . ($pages) . '</a>';
                            $paginator_str .= '</a> ';
                        }else{

                            $paginator_str .= '<a href="logs.php?page=1" class="paginator_btn" style="' . $pg_btn_bgcolor . '">1</a> ... ';
                            //$paginator_str .= '<a href="logs.php?page=' . ($_GET['page']-2) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($_GET['page']-1) . '</a> ';
                            $paginator_str .= '<a href="logs.php?page=' . ($_GET['page']-1) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($_GET['page']-1) . '</a> ';
                            $paginator_str .= '<a href="logs.php?page=' . ($_GET['page']) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($_GET['page']) . '</a> ';
                            $paginator_str .= '<a href="logs.php?page=' . ($_GET['page']+1) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($_GET['page']+1) . '</a> ... ';
                            //$paginator_str .= '<a href="logs.php?page=' . ($_GET['page']+2) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($_GET['page']+1) . '</a> ';
                            $paginator_str .= '<a href="logs.php?page=' . ($pages) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($pages) . '</a> ';

                        }



                    }else {
                        for ($i = 1; $i <= $pages; $i++) {
                            $pg_btn_bgcolor = '';
                            if (isset($_GET)) {
                                if (isset($_GET['page'])) {
                                    if ($_GET['page'] == $i) {
                                        $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                                    }
                                } else {
                                    if ($i == 1) {
                                        $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                                    }
                                }
                            }
                            $paginator_str .= '<a href="logs.php?page=' . ($i) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($i) . '</a> ';
                        }
                    }
                }

                if ($pages > 1) {
                    echo '
						    <div style="margin: 2px 6px 3px;">
						        <span style="font-size: 80%; color: rgb(0, 172, 237);">Перейти на страницу: </span>' . $paginator_str . '
						    </div>';
                }


				for ($i=0; $i<count($logs_j); $i++){
					echo '
						<li class="cellsBlock cellsBlockHover">
							<div class="cellCosmAct" style="background-color:'.$logs_j[$i]['id'].'"></div>
							<div class="cellTime">'.date('d.m.y H:i', $logs_j[$i]['date']).'</div>
							<div class="cellName 4filter"  id="4filter">'.$logs_j[$i]['creator'].'</div>
							<div class="cellOffice" style="text-align: center;">'.$logs_j[$i]['ip'].'</div>
							<div class="cellOffice" style="text-align: center;">'.$logs_j[$i]['mac'].'</div>
							<div class="cellText 4filter">'.$logs_j[$i]['description_new'].'<hr>
								<span style="background:#f0f0f0;">'.$logs_j[$i]['description_old'].'</span>
							</div>
						</li>';
					
				}
			}
			echo '	</ul>		
				</div>
				
				
<script type="text/javascript">

	$(document).ready(function() {
		$(".loading_overlay").hide;
		//alert("Ok");
	});
</script>
				
				';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>