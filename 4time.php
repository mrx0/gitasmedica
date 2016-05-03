<?php

	$ttime = time();			
	$month = date('n', $ttime);		
	$year = date('Y', $ttime);
	$datestart = strtotime('1.'.$month.'.'.$year);
	 //нулевой день следующего месяца - это последний день предыдущего
	$lastday = mktime(0, 0, 0, $month+1, 0, $year);
	$datefinish = strtotime(strftime("%d", $lastday).'.'.$month.'.'.$year.' 23:59:59');;
	echo $datestart.'<br />'.date('d.m.y H:i', $datestart).'<br />'.$datefinish.'<br />'.date('d.m.y H:i', $datefinish).'<br />'.($datefinish - $datestart).'<br />'.(($datefinish - $datestart)/(60*60*24)).'<br />'.'<br />'.'<br />'.'<br />';

?>