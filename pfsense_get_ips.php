<?php

//pfsense_get_ips.php
//


//Три запроса в консоли, через которые с помощью curl мы получаем таблицу IP из OpenVPN
//
//curl -L -k --cookie-jar cookies.txt http://192.168.137.254:8081/ | grep "name='__csrf_magic'" | sed 's/.*value="\(.*\)".*/\1/' > csrf.txt
//
//curl -L -k --cookie cookies.txt --cookie-jar cookies.txt --data-urlencode "login=Login" --data-urlencode "usernamefld=admin" --data-urlencode "passwordfld=84286252" --data-urlencode "__csrf_magic=$(cat csrf.txt)" http://192.168.137.254:8081/ > /dev/null
//
//curl -L -k --cookie cookies.txt --cookie-jar cookies.txt http://192.168.137.254:8081/status_openvpn.php
//




    $url = "http://192.168.137.254:8081";
    $urlTo = "http://192.168.137.254:8081/status_openvpn.php";

    $username = "admin";
    $password = "84286252";

    $cookies = '';
    $cookies_file = 'tmp/cookies.txt';
    $csrf = '';
    $csrf_file = 'tmp/csrf.txt';
    //var_dump(file_exists($cookie));


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);

    //curl_setopt ($ch, CURLOPT_VERBOSE, 2); // Отображать детальную информацию о соединении
    curl_setopt ($ch, CURLOPT_ENCODING, 0); // Шифрование можно включить, если нужно
    curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'); //Прописываем User Agent, чтобы приняли за своего
    curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookies_file); // Сюда будем записывать cookies, файл в той же папке, что и сам скрипт
    curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookies_file);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt ($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);
//    curl_setopt ($ch, CURLOPT_COOKIE, "cookie1=1;cookie2=2"); //Устанавливаем нужные куки в необходимом формате


    curl_setopt($ch, CURLOPT_URL, $url);
    $html = curl_exec($ch);
//Получаем csrf
    $doc = new DOMDocument;
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXpath($doc);

    $nodes = $xpath->query("//input[@name='__csrf_magic']");
    $node = $nodes->item(0);

//Сохраним в переменную
    $csrf = $node->getAttribute('value');
//print_r($csrf);

    $post = [
        '__csrf_magic' => $csrf ,
        'login' => 'Login',
        'usernamefld' => $username,
        'passwordfld' => $password
    ];

//Авторизация

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

curl_setopt($ch, CURLOPT_URL,$urlTo);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $html = curl_exec($ch);
    curl_close($ch);


    echo $html;

?>
	