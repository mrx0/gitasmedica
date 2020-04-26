<?php

//test_qrcode.php
//Формирование QR кода

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qr'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    //var_dump($PNG_TEMP_DIR);

    //html PNG location prefix
    $PNG_WEB_DIR = 'qr/temp/';

    include "qr/qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);


    //$filename = $PNG_TEMP_DIR.'test.png';


    $d = '|';
    $start = 'ST00012';
    $Name = 'Name='.'ООО "Смарт Хоум"';
    $PersonalAcc = 'PersonalAcc='.'40204810500000003145';
    $BankName = 'BankName='.'Ф-Л СЕВЕРО-ЗАПАДНЫЙ ПАО БАНК "ФК ОТКРЫТИЕ"';
    $BIC = 'BIC='.'044030795';
    $CorrespAcc = 'CorrespAcc='.'30101810540300000795';
    $KPP = 'KPP='.'780401001';
    $PayeeINN = 'PayeeINN='.'7841412929';
    $lastName = 'lastName='.'Иванов ИВ';
    $payerAddress = 'payerAddress='.'адрес';
    $Purpose = 'Purpose='.'назначение';
    $Sum = 'Sum='.'100100';

    $errorCorrectionLevel = 'L';
    $matrixPointSize = 3;

    $QR_str =  $start.$d.$Name.$d.$PersonalAcc.$d.$BankName.$d.$BIC.$d.$CorrespAcc.$d.$KPP.$d.$PayeeINN.$d.$lastName.$d.$payerAddress.$d.$Purpose.$d.$Sum;
    //var_dump($QR_str);


    // user data
    $filename = $PNG_TEMP_DIR.''.md5($QR_str.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
    //var_dump($filename);

    // create a QR Code with this text and display it
    QRcode::png($QR_str, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

    //display generated file
    //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'"><hr/>';

    return($filename);

?>




