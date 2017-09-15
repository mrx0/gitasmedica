<?php

if(trim($_REQUEST['name']) == '' || trim($_REQUEST['email']) == '' || trim($_REQUEST['comments']) == '' || trim($_REQUEST['phone']) == '' || trim($_REQUEST['place']) == '' || trim($_REQUEST['time']) == '')
{
	echo "empty";
	exit;
}
if(!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL))
{
	echo "bad_email";
	exit;
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/*
if(isset($_REQUEST['captcha_sid']))
{
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
	if(isset($_REQUEST['captcha_word']) && !$APPLICATION->CaptchaCheckCode($_REQUEST['captcha_word'], $_REQUEST['captcha_sid']))
	{
		echo "bad_captcha";
		exit;
	}
}

if(trim($_REQUEST['captcha_response']) == '')
{
	echo "bad_recaptcha";
	exit;
}
else
{
	$receivedRecaptcha = $_POST['captcha_response'];
    $verifiedRecaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=6LdMrRoUAAAAAMvarWtIBY9w3nQM8AQgwK8O7KXc&response='.$receivedRecaptcha);

    $verResponseData = json_decode($verifiedRecaptcha);

    if(!$verResponseData->success)
    {
        echo "bad_recaptcha";
		exit;
    }

	}
*/

$place_arr = array(
	15 => "пр. Просвещения, д.21",
	16 => "Гражданский пр., д.114/1",
	18 => "ул. Фонтанная, д.3",
	13 => "пр. Просвещения 54",
	12 => "пр. Авиаконструкторов, д.10",
	14 => "пр. Комендантский, д.17/1",
	19 => "пр. Просвещения, д.72",
);


$to = "asstom@mail.ru";
$sendermail = "site@asstom.ru";
$message = "Запись на прием на сайте cosmet.assom.ru!\n\n";
$Cc = "zapis.asstom@mail.ru";

$message .= "Имя: ".$_REQUEST['name'] . "\n";
$message .= "E-Mail: ".$_REQUEST['email'] . "\n";
$message .= "Контактный телефон: ".$_REQUEST['phone'] . "\n";
$message .= "Желаемое время и дата: ".$_REQUEST['time'] . "\n";
$message .= "Место обращения: ".$place_arr[$_REQUEST['place']] . "\n";
$message .= "Описание проблемы:\n ".$_REQUEST['comments'] . "\n";


    $from = "cosmet.asstom.ru <".$sendermail.">"; 
    $subject = "Запись на прием на сайте cosmet.asstom.ru"; 
    $headers = "From: $from\r\nCc: $Cc\r\n";
	$headers .= "MIME-Version: 1.0\r\n"."Content-type: text/plain; charset=utf-8\r\n"; 

$returnpath = "-f" . $sendermail;

mail($to,$subject,$message,$headers);


//---------

	require 'zapis_config.php';
	
	$msql_connect = mysqli_connect($hostname, $username, $db_pass, $dbName);
	
	if ($msql_connect){
		mysqli_query($msql_connect, "SET NAMES 'utf8'");
		
		$datetime = date('Y-m-d H:i:s', time());
		
		$name = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['name']))));
		$email = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['email']))));
		$phone = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['phone']))));
		$time = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['time']))));
		$place = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['place']))));
		$comments = trim(strip_tags(stripcslashes(htmlspecialchars($_REQUEST['comments']))));
		
		$query = "INSERT INTO `zapis` (
			`datetime`, `name`, `email`, `phone`, `time`, `place`, `comments`) 
			VALUES (
			'$datetime', '$name', '$email', '$phone', '$time', '$place', '$comments') ";

		$result = mysqli_query($msql_connect, $query);
		
		mysqli_close($msql_connect);
	}
	
	
	
	

?>
