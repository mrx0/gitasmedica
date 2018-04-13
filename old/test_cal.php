<?php
if (isset($_GET['date'])){
	echo "выбрана дата ".$_GET['date'];
}
my_calendar(array(date("Y-m-d"))); 


function my_calendar($fill=array()){
	$month_names=array(
		"январь",
		"февраль",
		"март",
		"апрель",
		"май",
		"июнь",
		"июль",
		"август",
		"сентябрь",
		"октябрь",
		"ноябрь",
		"декабрь"
	); 
	if (isset($_GET['y']))
		$y = $_GET['y'];
	if (isset($_GET['m']))
		$m = $_GET['m']; 
	if (isset($_GET['date']) && strstr($_GET['date'],"-"))
		list($y,$m) = explode("-",$_GET['date']);
	if (!isset($y) || $y < 1970 || $y > 2037)
		$y = date("Y");
	if (!isset($m) || $m < 1 || $m > 12)
		$m = date("m");
	$month_stamp = mktime(0, 0, 0, $m, 1, $y);
	$day_count = date("t",$month_stamp);
	$weekday = date("w", $month_stamp);
	if ($weekday == 0)
		$weekday = 7;
	$start = -($weekday-2);
	$last = ($day_count + $weekday - 1) % 7;
	if ($last == 0) 
		$end = $day_count; 
	else 
		$end = $day_count + 7 - $last;
	$today = date("Y-m-d");
	$prev = date('?\m=m&\y=Y', mktime (0, 0, 0, $m-1, 1, $y));  
	$next = date('?\m=m&\y=Y', mktime (0, 0, 0, $m+1, 1, $y));
	$i = 0;

echo '
<table border=1 cellspacing=0 cellpadding=2> 
	<tr>
		<td colspan=7> 
			<table width="100%" border=0 cellspacing=0 cellpadding=0> 
				<tr> 
					<td align="left"><a href="'.$prev.'">&lt;&lt;&lt;</a></td> 
					<td align="center">',$month_names[$m-1],' ',$y,'</td> 
					<td align="right"><a href="'.$next.'">&gt;&gt;&gt;</a></td> 
				</tr> 
			</table> 
		</td> 
	</tr> 
	<tr>
		<td>Пн</td>
		<td>Вт</td>
		<td>Ср</td>
		<td>Чт</td>
		<td>Пт</td>
		<td>Сб</td>
		<td>Вс</td>
	<tr>';

for($d = $start; $d <= $end; $d++){ 
	if (!($i++ % 7)){
		echo '
			<tr>';
	}
	echo '  <td align="center">';
	if ($d < 1 || $d > $day_count){
		echo "&nbsp";
	}else{
		$now="$y-$m-".sprintf("%02d",$d);
		if (is_array($fill) && in_array($now, $fill)){
			echo '<b><a href="'.$_SERVER['PHP_SELF'].'?date='.$now.'">'.$d.'</a></b>'; 
		}else{
			echo $d;
		}
	}
	echo "</td>\n";
	if (!($i % 7)){
		echo "</tr>\n";
	}
} 

echo '
</table>';

}