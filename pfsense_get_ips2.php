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

    $cookies_file = 'tmp/cookies.txt';

$curl = curl_init();
curl_setopt($curl, CURLOPT_COOKIESESSION, true);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookies_file);
curl_setopt($curl, CURLOPT_COOKIEJAR, $cookies_file);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
curl_setopt($curl, CURLOPT_URL, $url);
$html = curl_exec($curl);
//print_r($html);

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
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));


//Переходим на нужный url
curl_setopt($curl, CURLOPT_URL, $urlTo);

$result = curl_exec($curl);

if (curl_errno($curl)) print curl_error($ch);
curl_close($curl);

print_r($result);

?>
	