<?php

//widget_calendar.php
//Календарик

function widget_calendar ($month, $year, $path, $dop){
	
	$result = '';
	
	//if ($month < 10) $month = '0'.$month;
	
	//Массив с месяцами
	$monthsName = array(
		'01' => 'Январь',
		'02' => 'Февраль',
		'03' => 'Март',
		'04' => 'Апрель',
		'05' => 'Май',
		'06' => 'Июнь',
		'07'=> 'Июль',
		'08' => 'Август',
		'09' => 'Сентябрь',
		'10' => 'Октябрь',
		'11' => 'Ноябрь',
		'12' => 'Декабрь'
	);
	
	//Предыдущий
	if ((int)$month - 1 < 1){
		$prev = '12';
		$pYear = $year - 1;
	}else{
		$pYear = $year;
		if ((int)$month - 1 < 10){
			$prev = '0'.strval((int)$month-1);
		}else{
			$prev = strval((int)$month-1);
		}
	}
	
	//Следующий
	if ((int)$month + 1 > 12){
		$next = '01';
		$nYear = $year + 1;
	}else{
		$nYear = $year;
		if ((int)$month + 1 < 10){
			$next = '0'.strval((int)$month+1);
		}else{
			$next = strval((int)$month+1);
		}
	}
	
	$prevLink = $path.'?'.$dop.'&m='.$prev.'&y='.$pYear;
	$nextLink = $path.'?'.$dop.'&m='.$next.'&y='.$nYear;
	
	$result .= '
		<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
			<span style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="'.$path.'?'.$dop.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>
		</li>
		<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
			<a href="'.$prevLink.'" class="cellTime ahref" style="text-align: center;">
				<span style="font-weight: normal; font-size: 80%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
			</a>
			<div class="cellTime" style="text-align: center;">
				<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
			</div>
			<a href="'.$nextLink.'" class="cellTime ahref" style="text-align: center;">
				<span style="font-weight: normal; font-size: 80%;">'.$monthsName[$next].' >><br>'.$nYear.'</span>
			</a>
			
			<div class="cellTime" style="text-align: center; width: auto;">
				<select id="iWantThisMonth">';
	foreach ($monthsName as $val => $name){
		
		if ($val == $month){
			 $selected = 'selected';
		}else{
			 $selected = '';
		}
		
		$result .= '
				<option value="'.$val.'" '.$selected.'>'.$name.'</option>';
	}
	$result .= '
			</select>
			<input id="iWantThisYear" type="number" value="'.$year.'" min="2000" max="2030" size="4" style="width: 60px;">
			<i class="fa fa-check-square" style="font-size: 130%; color: green; cursor: pointer" onclick="iWantThisDate(\''.$path.'?'.$dop.'\')"></i>
		</div>
	</li>';
	
	return $result;
	
}

?>