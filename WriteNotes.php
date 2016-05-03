<?php

	function WriteNotes($notes){
		include_once 'DBWork.php';	
		
		$rez = '<div class="cellsBlock">
			';
		//$notes = SelDataFromDB ($datatable, $sw, $type);
		if ($notes != 0){
			//var_dump ($notes);	

			$for_notes = array (
			1 => 'Каласепт, Метапекс, Септомиксин (Эндосольф)',
			2 => 'Временная пломба',
			3 => 'Открытый зуб',
			4 => 'Депульпин',
			5 => 'Распломбирован под вкладку (вкладка)',
			6 => 'Имплантация (ФДМ ,  абатмент, временная коронка на импланте)',
			7 => 'Временная коронка',
			8 => 'Санированные пациенты ( поддерживающее лечение через 6 мес)',
			9 => 'Прочее',	
			10 => 'Установлены брекеты',	
			);

			$rez .= '<br /><br />Особые отметки';
			$rez .= '
			<ul class="live_filter" style="margin-left:6px;">
			<li class="cellsBlock" style="font-weight:bold;">	
				<div class="cellPriority" style="text-align: center"></div>
				<div class="cellTime" style="text-align: center">Срок</div>
				<div class="cellName" style="text-align: center">Пациент</div>
				<div class="cellName" style="text-align: center">Посещение</div>
				<div class="cellText" style="text-align: center">Описание</div>
				<div class="cellTime" style="text-align: center">Создано</div>
				<div class="cellName" style="text-align: center">Автор</div>
				<div class="cellTime" style="text-align: center">Закрыто</div>
			</li>';
			for ($i = 0; $i < count($notes); $i++) {
				$dead_line_time = $notes[$i]['dead_line'] - time() ;
				if ($dead_line_time <= 0){
					$priority_color = '#FF1F0F';
				}elseif (($dead_line_time > 0) && ($dead_line_time <= 2*24*60*60)){
					$priority_color = '#FF9900';
				}elseif (($dead_line_time > 2*24*60*60) && ($dead_line_time <= 3*24*60*60)){
					$priority_color = '#EFDF3F';
				}else{
					$priority_color = '#FFF';
				}

				
				if ($notes[$i]['closed'] == 0){
					$ended = 'Нет';
					$background_style = '';
					$background_style2 = '
						background: rgba(231,55,71, 0.9);
						color:#fff;
						';
					if ($dead_line_time <= 0){
						$background_style = '
							background: rgba(239,23,63, 0.5);
							background: -moz-linear-gradient(45deg, rgba(239,23,63, 1) 0%, rgba(231,55,39, 0.7) 33%, rgba(239,23,63, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
							background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(239,23,63, 0.4)), color-stop(33%,rgba(231,55,39, 0.7)), color-stop(71%,rgba(239,23,63, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
							background: -webkit-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: -o-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: -ms-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: linear-gradient(-135deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							';
					}
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
						<div class="cellPriority" style="background-color:'.$priority_color.'"></div>
						<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['dead_line']).'</div>
						<a href="client.php?id='.$notes[$i]['client'].'" class="ahref cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$notes[$i]['client'], 'user').'</a>
						<a href="task_stomat_inspection.php?id='.$notes[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$notes[$i]['task'].'</a>
						<div class="cellText" style="'.$background_style.'">'.$for_notes[$notes[$i]['description']].'</div>
						<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['create_time']).'</div>
						<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$notes[$i]['create_person'], 'user').'</div>
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