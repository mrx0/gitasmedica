<?php

//createQRcodePng.php
//Формирование QR кода

function createQRcodePng($Name, $PersonalAcc, $BankName, $BIC, $CorrespAcc, $KPP, $PayeeINN, $lastName, $payerAddress, $Purpose, $Sum, $PayerINN){
    //$Name = 'ООО "Смарт Хоум"';
    //$PersonalAcc = '40204810500000003145';
    //$BankName = 'Ф-Л СЕВЕРО-ЗАПАДНЫЙ ПАО БАНК "ФК ОТКРЫТИЕ"';
    //$BIC = '044030795';
    //$CorrespAcc = '30101810540300000795';
    //$KPP = '780401001';
    //$PayeeINN = '7841412929';
    //$lastName = 'Иванов Алекус Выапвпа';
    //$payerAddress = 'адрес адрес адрес';
    //$Purpose = 'НАвазазаза';
    //$Sum = '100033300';
    //$PayerINN = '6441412929';

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qr' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
    //var_dump($PNG_TEMP_DIR);

    //html PNG location prefix
    $PNG_WEB_DIR = 'qr/temp/';

    include "qr/qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);


    //$filename = $PNG_TEMP_DIR.'test.png';

    $d = '|';
    $QR_str = 'ST00012'.$d;
    $QR_str .= 'Name=' . $Name.$d;
    $QR_str .= 'PersonalAcc=' . $PersonalAcc.$d;
    $QR_str .= 'BankName=' . $BankName.$d;
    $QR_str .= 'BIC=' . $BIC.$d;
    $QR_str .= 'CorrespAcc=' . $CorrespAcc.$d;
    $QR_str .= 'KPP=' . $KPP.$d;
    $QR_str .= 'PayeeINN=' . $PayeeINN.$d;
    $QR_str .= 'lastName=' . $lastName.$d;
    $QR_str .= 'payerAddress=' . $payerAddress.$d;
    if ($PayerINN != '') {
        $QR_str .= 'PayerINN=' . $PayerINN.$d;
    }
    $QR_str .= 'Purpose=' . $Purpose.$d;
    $QR_str .= 'Sum=' . $Sum;

    $errorCorrectionLevel = 'L';
    $matrixPointSize = 3;

    //$QR_str = $start . $d . $Name . $d . $PersonalAcc . $d . $BankName . $d . $BIC . $d . $CorrespAcc . $d . $KPP . $d . $PayeeINN . $d . $lastName . $d . $payerAddress . $d . $payerINN . $d . $Purpose . $d . $Sum;
    //var_dump($QR_str);


    // user data
    $filename = $PNG_TEMP_DIR . '' . md5($QR_str . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
    //var_dump($filename);

    // create a QR Code with this text and display it
    QRcode::png($QR_str, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

    //display generated file
    //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'"><hr/>';

    return ($filename);

}

?>




