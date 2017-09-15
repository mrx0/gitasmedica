<?php
	
	//get_zapis.php
	//
	
	//session_start();
	
	function GetData ($type, $last, $token){
		
		$ACCESS_TOKEN = '&token='.$token;
		$LAST = '&last='.$last;

		switch($type){
			//asstom
			case 'asstom':
				$URL = 'https://www.asstom.ru/zapis_giveitotome.php?';
				$query = $URL.$ACCESS_TOKEN.$LAST;
			break;
			//
			case 'search_accounts_chat':
				$URL = 'http://api.worldoftanks.ru/wot/account/list/?application_id=';
				$query = $URL.$APPLICATION_ID.'&search='.$data.'&limit='.$LIMIT;
			break;
			//
			case 'search_clans':
				//$data = urlencode($data);
				$URL = 'http://api.worldoftanks.ru/wgn/clans/list/?application_id=';
				$query = $URL.$APPLICATION_ID.'&search='.$data.'&limit='.$LIMIT;
			break;
		}
		
		//echo $query;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $query);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		
		$str = curl_exec($ch);
		var_dump($str);
		
		curl_close($ch);

		return $str;
	
	}

?>
	