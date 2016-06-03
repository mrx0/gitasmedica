<?php

	function WriteRemoves($removes){
		include_once 'DBWork.php';	
		
		$rez = '<div class="cellsBlock">
			';
		//$notes = SelDataFromDB ($datatable, $sw, $type);
		if ($removes != 0){
			//var_dump ($notes);	

			$rez .= '<br /><br />Направления';
			$rez .= '
							<ul class="live_filter" style="margin-left:6px;">
								<li class="cellsBlock" style="font-weight:bold;">	
									<div class="cellName" style="text-align: center">К кому</div>
									<div class="cellName" style="text-align: center">Пациент</div>
									<div class="cellName" style="text-align: center">Посещение</div>
									<div class="cellText" style="text-align: center">Описание</div>
									<div class="cellTime" style="text-align: center">Создано</div>
									<div class="cellName" style="text-align: center">Автор</div>
									<div class="cellTime" style="text-align: center">Закрыто</div>
								</li>';
								
			for ($i = 0; $i < count($removes); $i++) {
				if ($removes[$i]['closed'] == 0){
					$ended = 'Нет';
					$background_style = '';
					$background_style2 = '
						background: rgba(231,55,71, 0.9);
						color:#fff;
						';

					$background_style = '
						background: rgba(55,127,223, 0.5);
						background: -moz-linear-gradient(45deg, rgba(55,127,223, 1) 0%, rgba(151,223,255, 0.7) 33%, rgba(55,127,223, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
						background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(55,127,223, 0.4)), color-stop(33%,rgba(151,223,255, 0.7)), color-stop(71%,rgba(55,127,223, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
						background: -webkit-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
						background: -o-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
						background: -ms-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
						background: linear-gradient(-135deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
						';
				}else{
					$ended = 'Да';
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
						
				$rez .= '
					<li class="cellsBlock cellsBlockHover">
						<a href="user.php?id='.$removes[$i]['whom'].'" class="ahref cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removes[$i]['whom'], 'user').'</a>
						<a href="client.php?id='.$removes[$i]['client'].'" class="ahref cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removes[$i]['client'], 'user').'</a>
						<a href="task_stomat_inspection.php?id='.$removes[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removes[$i]['task'].'</a>
						<div class="cellText" style="'.$background_style.'">'.$removes[$i]['description'].'</div>';
				$rez .= '

						<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removes[$i]['create_time']).'</div>
						<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removes[$i]['create_person'], 'user').'</div>
						<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
					</li>';
				}
			}else{
				//echo '<h1>Нечего показывать.</h1>';
			}
		$rez .= '</div>';
		
		return $rez;
	}
?>