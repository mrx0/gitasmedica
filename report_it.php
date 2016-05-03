<?php

//it.php
//IT

	require_once 'header.php';

	if ($enter_ok){
		if (($it['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Отчёт по IT</h1>
				</header>';
			
			
			
			echo '<div id="data">';
			
			if ($_GET){
				
				$filter_rez = array();
				$sw ='';
				
				if (!empty($_GET['report_data'])){
					$ttime = strtotime($_GET['report_data']);			
					$day = date('j', $ttime);		
					$month = date('n', $ttime);		
					$year = date('Y', $ttime);
					$datestart = strtotime($day.'.'.$month.'.'.$year.' 00:00:00');
					$datefinish = strtotime($day.'.'.$month.'.'.$year.' 23:59:59');
					$sw = "`end_time` = '0' OR `create_time` BETWEEN '{$datestart}' AND '{$datefinish}' ";
					//echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';
					$journal = SelDataFromDB('journal_it', $sw, 'filter');
					
					//var_dump($journal);
					if ($journal != 0){
						echo '
							<ul class="live_filter" style="margin-left:6px;">
								<li class="cellsBlock" style="font-weight:bold;">	
									<div class="cellPriority" style="text-align: center"></div>
									<a href="it.php?sort_added=1" class="cellTime ahref" style="text-align: center">Добавлено</a>
									<a href="it.php?sort_filial=1" class="cellOffice ahref" style="text-align: center">Филиал</a>
									<div class="cellText" style="text-align: center">Описание проблемы</div>
									<div class="cellName" style="text-align: center">Автор</div>
									<div class="cellName" style="text-align: center">Исполнитель</div>
									<div class="cellTime" style="text-align: center">Закрыто</div>
								</li>';
						for ($i = 0; $i < count($journal); $i++) { 
							if ($journal[$i]['priority'] == 1){
								$priority_color = '#FFFF3D';
							}elseif ($journal[$i]['priority'] == 2){
								$priority_color = '#FF9900';
							}elseif ($journal[$i]['priority'] == 3){
								$priority_color = '#E70F2F';
							}
							if ($journal[$i]['office'] == 99){
								$office = 'Во всех';
							}else{
								$offices = SelDataFromDB('spr_office', $journal[$i]['office'], 'offices');
								//var_dump ($offices);
								$office = $offices[0]['name'];
							}
							$worker = SelDataFromDB('spr_workers', $journal[$i]['worker'], 'worker_id');
							//var_dump ($worker);
							$author = SelDataFromDB('spr_workers', $journal[$i]['create_person'], 'worker_id');
							//
							if ($journal[$i]['end_time'] == 0){
								$ended = 'Нет';
								$background_style = '';
								$background_style2 = '
									background: rgba(231,55,71, 0.9);
									color:#fff;
									';
							}else{
								$ended = date('d.m.y H:i', $journal[$i]['end_time']);
								$background_style = '
									background: rgba(144,247,95, 0.5);
									background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
									background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
									background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
									background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
									background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
									background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
									';
								$background_style2 = '
									background: rgba(144,247,95, 0.5);
									';
							}
							echo '
									<li class="cellsBlock cellsBlockHover">
										<div class="cellPriority" style="background-color:'.$priority_color.'"></div>
										<div class="cellTime">'.date('d.m.y H:i', $journal[$i]['create_time']).'</div>
										<div class="cellOffice" style="text-align: center;"><a href="" class="ahref">'.$office.'</a></div>
										<a href="task.php?id='.$journal[$i]['id'].'" class="ahref cellText" style="'.$background_style.'"><b>#'.$journal[$i]['id'].'</b> '.$journal[$i]['description'].'</a>
										<div class="cellName" style="text-align: center;">'.$author[0]['name'].'</div>
										<div class="cellName" style="text-align: center;">'.$worker[0]['name'].'</div>
										<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
									</li>';
									
							
							if (($it['see_all'] == 1) || $god_mode){
								$comments = SelDataFromDB('comments', 'it'.':'.$journal[$i]['id'], 'parrent');
								//var_dump ($comments);	
								if ($comments != 0){
									foreach ($comments as $value){
										echo '
											<div class="cellsBlock3">
												<div class="cellLeft" style="font-size:80%;">
													'.WriteSearchUser('spr_workers',$value['create_person'], 'user').'<br />
													<span style="font-size:75%;">'.date('d.m.y H:i', $value['create_time']).'</span>
												</div>
												<div class="cellRight" style="font-size:75%">'.nl2br($value['description']).'</div>
											</div>';
									}
								}
							}
									
									
						}
					}else{
						echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
				
			}else{
				echo '
				
					<form action="report_it.php" method="GET" id="form">	
						<div class="cellsBlock3">
							<div class="cellLeft">
								Выберите дату для отчета
							</div>
							<div class="cellRight">
								<input name="report_data" class="date" value="'.date("d.m.Y").'">
							</div>
						</div>
					</form>
					
					<button  type="submit" form="form" formaction="report_it.php" formmethod="GET" class="b">Применить</button>';		
				
			}
			echo '</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	require_once 'footer.php';

?>