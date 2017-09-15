<?php
	
	$URL = 'https://www.asstom.ru/zapis_giveitotome.php?last=0&token=ec3d3704abf1bb0430cd82e66fefdce7';
	$query = $URL;
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $query);

	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
	
	$str = curl_exec($ch);
	
	var_dump(json_decode($str));
	
	curl_close($ch);

?>
	