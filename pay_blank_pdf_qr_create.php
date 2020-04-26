<?php

//pay_blank_pdf_qr_create.php
//Статистика по пациентам с открытыми рассрочками

if (
    isset($_GET['inn']) &&
    isset($_GET['kpp']) &&
    isset($_GET['org_full_name']) &&
    isset($_GET['bik']) &&
    isset($_GET['ks']) &&
    isset($_GET['bank_name']) &&
    isset($_GET['rs']) &&
    isset($_GET['fio']) &&
    isset($_GET['address']) &&
    isset($_GET['comment']) &&
    isset($_GET['rub']) &&
    isset($_GET['kop']) &&
    isset($_GET['payerinn'])
) {

    //var_dump($_GET);

    require_once("fpdf/mypdf.php");
    include_once 'createQRcodePng.php';

    $qrFile = createQRcodePng($_GET['org_full_name'], $_GET['rs'], $_GET['bank_name'], $_GET['bik'], $_GET['ks'], $_GET['kpp'], $_GET['inn'], $_GET['fio'], $_GET['address'], $_GET['comment'], $_GET['rub'].'00', $_GET['payerinn']);
    $qrXPos = 63;
    $qrYPos = 8;
    $qrWidth = 40;


    // create pdf
    $pdf=new MYPDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->SetMargins($pdf->left, $pdf->top, $pdf->right);

    /**
     * Создаем страницу
     **/

    $pdf->AddPage();

    $pdf->AddFont('ArialMT','','arial.php');
    $pdf->SetFont('ArialMT', '', 8);

    // create table
    //Общая ширина 190
    $columns = array();

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', "   Оплатите счет, отсканировав код"), 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', "   через платежный терминал,"), 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', "   мобильное приложение Сбербанк Онлайн на смартфоне"), 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '45', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', "   или передав документ сотруднику банка"), 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
    $columns[] = $col;

    //Под QR-кодом
    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '20', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '25', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'T');
    $col[] = array('text' => '', 'width' => '95', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $columns[] = $col;


    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Организация получатель платежа: '.$_GET['org_full_name']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'B');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251','ИНН: '.$_GET['inn']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'КПП: '.$_GET['kpp']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Р/С: '.$_GET['rs']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '9', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Наименование банка: '.$_GET['bank_name']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '9', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Отметки банка"), 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'БИК: '.$_GET['bik']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'К/С: '.$_GET['ks']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '128, 128, 128', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Плательщик: '.$_GET['fio']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Адрес плательщика: '.$_GET['address']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'ИНН плательщика: '.$_GET['payerinn']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Назначение: '.$_GET['comment']), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'TB');
    $columns[] = $col;

    //Пропуск
    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '5', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '5', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => 'T');
    $columns[] = $col;

    //Сумма
    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Сумма: '.$_GET['rub'].' руб. 00 коп.'), 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => '');
    $columns[] = $col;

    //Пропуск
    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => '', 'width' => '140', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => '');
    $columns[] = $col;

    $col = array();
    $col[] = array('text' => '', 'width' => '50', 'height' => '10', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Подпись:________________________'), 'width' => '70', 'height' => '10', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => '');
    $col[] = array('text' => iconv('UTF-8', 'cp1251', 'Дата: "___ " _________  20__ г. '), 'width' => '70', 'height' => '10', 'align' => 'R', 'font_name' => 'ArialMT', 'font_size' => '10', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.2', 'linearea' => '');
    $columns[] = $col;

//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => '', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => '');
//    $col[] = array('text' => '', 'width' => '140', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRBL');
//    $columns[] = $col;

//    //2
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '8', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "ИЗВЕЩЕНИЕ"), 'width' => '40', 'height' => '8', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TR');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LT');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//
//    //3 ИНН КПП
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "ИНН ".$_GET['inn']." КПП ".$_GET['kpp'].""), 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //4
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Р/С:  ".$_GET['rs'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //5
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', $_GET['bank_name']), 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //6
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "БИК: ".$_GET['bik']."  к/c ".$_GET['ks'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //7
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Плательщик: ".$_GET['fio'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //8
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Адрес плательщика: ".$_GET['address'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //9
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Назначение: ".$_GET['comment'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //10
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Кассир"), 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Дата: ____________ Сумма платежа  ".$_GET['rub']." руб. 00 коп."), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//
//    //Пробел
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'T');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRB');
//    $columns[] = $col;
//
//
//    //1
////    $col = array();
////    $col[] = array('text' => '', 'width' => '5', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRL');
////    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Идентификатор"), 'width' => '120', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTB');
////    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Форма N ПД-4"), 'width' => '65', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TR');
////    $columns[] = $col;
//
//    //2
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '8', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LT');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "КВИТАНЦИЯ"), 'width' => '40', 'height' => '8', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TR');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LT');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Форма N ПД-4"), 'width' => '60', 'height' => '8', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TR');
//    $columns[] = $col;
//
//
//    //3 ИНН КПП
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "ИНН ".$_GET['inn']." КПП ".$_GET['kpp'].""), 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //4
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Р/С:  ".$_GET['rs'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //5
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', $_GET['bank_name']), 'width' => '85', 'height' => '8', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //6
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "БИК: ".$_GET['bik']."  к/c ".$_GET['ks'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //7
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Плательщик: ".$_GET['fio'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //8
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Адрес плательщика: ".$_GET['address'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //9
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Назначение: ".$_GET['comment'].""), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//    //10
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Кассир"), 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $col[] = array('text' => iconv('UTF-8', 'cp1251', "Дата: ____________ Сумма платежа  ".$_GET['rub']." руб. 00 коп."), 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'R');
//    $columns[] = $col;
//
//
//
//
//
//
//
//
//
//
//    //Пробел
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'L');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'T');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRB');
//    $columns[] = $col;
//    //Пробел
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'T');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRB');
//    $columns[] = $col;
//    //Пробел
//    $col = array();
//    $col[] = array('text' => '', 'width' => '5', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TLB');
//    $col[] = array('text' => '', 'width' => '40', 'height' => '6', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'T');
//    $col[] = array('text' => '', 'width' => '85', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TB');
//    $col[] = array('text' => '', 'width' => '60', 'height' => '6', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TRB');
//    $columns[] = $col;
//

//// data col
//    $col = array();
//    $col[] = array('text' => '15.12.2010', 'width' => '20', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => 'Zahlung: 123456789', 'width' => '125', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => '', 'width' => '15', 'height' => '5', 'align' => 'R', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => '120.50', 'width' => '15', 'height' => '5', 'align' => 'R', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => '0.00H', 'width' => '15', 'height' => '5', 'align' => 'R', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => 'Ist der Text zu lang, ist das kein Problem', 'width' => '50', 'height' => '5', 'align' => 'R', 'font_name' => 'ArialMT', 'font_size' => '12', 'font_style' => '', 'fillcolor' => '0,0,255', 'textcolor' => '0,255,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => 'Auch mit mehreren Farben ist es kein Problem', 'width' => '50', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,0', 'textcolor' => '0,255,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => 'So ist das Bauen einer Tabelle einfach nur einfach. MuliCell macht es einfach. Okay das ist nun lang genug', 'width' => '50', 'height' => '5', 'align' => 'C', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,255,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => 'Erstellen von Rechnungen sind kein Problem mehr', 'width' => '40', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,0,255', 'textcolor' => '0,255,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => 'Einfach nur mal eine Zeile ohne Rahmen', 'width' => '190', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,255,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'TB');
//    $columns[] = $col;
//
//    $col = array();
//    $col[] = array('text' => 'Einfach nur mal eine Zeile in der Tabelle', 'width' => '80', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,0,0', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $col[] = array('text' => 'Gerne auch mit einer Spalte mehr', 'width' => '110', 'height' => '5', 'align' => 'L', 'font_name' => 'ArialMT', 'font_size' => '8', 'font_style' => '', 'fillcolor' => '255,255,255', 'textcolor' => '0,0,0', 'drawcolor' => '0,0,0', 'linewidth' => '0.4', 'linearea' => 'LTBR');
//    $columns[] = $col;

    // Draw Table
    $pdf->WriteTable($columns);



    // QRcode
    $pdf->Image( $qrFile, $qrXPos, $qrYPos, $qrWidth );


    // Show PDF
    $pdf->Output();


//    // Начало конфигурации
//
//    $textColour = array(0, 0, 0);
//    $headerColour = array(100, 100, 100);
//    $tableHeaderTopTextColour = array(255, 255, 255);
//    $tableHeaderTopFillColour = array(125, 152, 179);
//    $tableHeaderTopProductTextColour = array(0, 0, 0);
//    $tableHeaderTopProductFillColour = array(128, 128, 128);
//    $tableHeaderLeftTextColour = array(99, 42, 57);
//    $tableHeaderLeftFillColour = array(184, 207, 229);
//    $tableBorderColour = array(50, 50, 50);
//    $tableRowFillColour = array(213, 170, 170);
//    $reportName = "2009 Widget Sales Report";
//    $reportNameYPos = 160;
//    $logoFile = "widget-company-logo.png";
//    $logoXPos = 50;
//    $logoYPos = 108;
//    $logoWidth = 110;
//    $columnLabels = array("Q1", "Q2", "Q3", "Q4");
//    $rowLabels = array("SupaWidget", "WonderWidget", "MegaWidget", "HyperWidget");
//    $chartXPos = 20;
//    $chartYPos = 250;
//    $chartWidth = 160;
//    $chartHeight = 80;
//    $chartXLabel = "Product";
//    $chartYLabel = "2009 Sales";
//    $chartYStep = 20000;
//
//    $chartColours = array(
//        array(255, 100, 100),
//        array(100, 255, 100),
//        array(100, 100, 255),
//        array(255, 255, 100),
//    );
//
//    $data = array(
//        array(9940, 10100, 9490, 11730),
//        array(19310, 21140, 20560, 22590),
//        array(25110, 26260, 25210, 28370),
//        array(27650, 24550, 30040, 31980),
//    );
//
//    // Конец конфигурации
//
//
//    /**
//     * Создаем титульную страницу
//     **/
//
//    $pdf = new FPDF('P', 'mm', 'A4');
//    $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
//    //$pdf->AddPage();
//
//    // Логотип
//    //$pdf->Image( $logoFile, $logoXPos, $logoYPos, $logoWidth );
//
//    // Название отчета
//    //$pdf->SetFont( 'Arial', 'B', 24 );
//    //$pdf->Ln( $reportNameYPos );
//    //$pdf->Cell( 0, 15, $reportName, 0, 0, 'C' );
//
//
//    /**
//     * Создаем страницу
//     **/
//
//    $pdf->AddPage();
//
//    $pdf-> AddFont('ArialMT','','arial.php');
//    $pdf->SetFont('ArialMT', '', 8);
//
////    $pdf->SetTextColor($headerColour[0], $headerColour[0], $headerColour[0]);
////    $pdf->SetFont('Arial', '', 17);
////    $pdf->Cell(0, 15, $reportName, 0, 0, 'C');
////    $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
////    $pdf->SetFont('Arial', '', 20);
////    $pdf->Write(19, "2009 Was A Good Year");
//    $pdf->Ln(40);
//
//    $pdf->Write(6, iconv('UTF-8', 'cp1251', "Идентификатор"));
//
//    $pdf->Ln(12);
//    $pdf->Write(6, "2010 is expected to see increased sales growth as we expand into other countries.");
//
//
//    /**
//     * Создаем таблицу
//     **/
//
//    $pdf->SetDrawColor($tableBorderColour[0], $tableBorderColour[0], $tableBorderColour[0]);
//    $pdf->Ln(15);
//
//    // Создаем строку заголовков
//    //$pdf->SetFont('Arial', '', 8);
//
//    // Ячейка "PRODUCT"
//    $pdf->SetTextColor($tableHeaderTopProductTextColour[0], $tableHeaderTopProductTextColour[1], $tableHeaderTopProductTextColour[2]);
//    $pdf->SetFillColor($tableHeaderTopProductFillColour[0], $tableHeaderTopProductFillColour[1], $tableHeaderTopProductFillColour[2]);
//    $pdf->Cell(46, 12, " PRODUCT", 1, 0, 'L', false);
//
//    // Остальные ячейки заголовков
//    $pdf->SetTextColor($tableHeaderTopTextColour[0], $tableHeaderTopTextColour[1], $tableHeaderTopTextColour[2]);
//    $pdf->SetFillColor($tableHeaderTopFillColour[0], $tableHeaderTopFillColour[1], $tableHeaderTopFillColour[2]);
//
//    for ($i = 0; $i < count($columnLabels); $i++) {
//        $pdf->Cell(36, 12, $columnLabels[$i], 1, 0, 'C', true);
//    }
//
//    $pdf->Ln(12);
//
//    // Создаем строки с данными
//
//    $fill = false;
//    $row = 0;
//
//    foreach ($data as $dataRow) {
//
//        // Create the left header cell
//        //$pdf->SetFont('Arial', 'B', 15);
//        $pdf->SetTextColor($tableHeaderLeftTextColour[0], $tableHeaderLeftTextColour[1], $tableHeaderLeftTextColour[2]);
//        $pdf->SetFillColor($tableHeaderLeftFillColour[0], $tableHeaderLeftFillColour[1], $tableHeaderLeftFillColour[2]);
//        $pdf->Cell(46, 12, " " . $rowLabels[$row], 1, 0, 'L', $fill);
//
//        // Create the data cells
//        $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
//        $pdf->SetFillColor($tableRowFillColour[0], $tableRowFillColour[1], $tableRowFillColour[2]);
//        //$pdf->SetFont('Arial', '', 15);
//
//        for ($i = 0; $i < count($columnLabels); $i++) {
//            $pdf->Cell(36, 12, ('$' . number_format($dataRow[$i])), 1, 0, 'C', $fill);
//        }
//
//        $row++;
//        $fill = !$fill;
//        $pdf->Ln(12);
//    }
//
//
//    /***
//     * Создаем график
//     ***/
//
//    // Вычисляем масштаб по оси X
//    $xScale = count($rowLabels) / ($chartWidth - 40);
//
//    // Вычисляем масштаб по оси Y
//
//    $maxTotal = 0;
//
//    foreach ($data as $dataRow) {
//        $totalSales = 0;
//        foreach ($dataRow as $dataCell) $totalSales += $dataCell;
//        $maxTotal = ($totalSales > $maxTotal) ? $totalSales : $maxTotal;
//    }
//
//    $yScale = $maxTotal / $chartHeight;
//
//    // Вычисляем ширину столбца
//    $barWidth = (1 / $xScale) / 1.5;
//
//    // Добавляем оси:
//
//    //$pdf->SetFont('Arial', '', 10);
//
//    // Ось X
//    $pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos);
//
//    for ($i = 0; $i < count($rowLabels); $i++) {
//        $pdf->SetXY($chartXPos + 40 + $i / $xScale, $chartYPos);
//        $pdf->Cell($barWidth, 10, $rowLabels[$i], 0, 0, 'C');
//    }
//
//    // Ось Y
//    $pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8);
//
//    for ($i = 0; $i <= $maxTotal; $i += $chartYStep) {
//        $pdf->SetXY($chartXPos + 7, $chartYPos - 5 - $i / $yScale);
//        $pdf->Cell(20, 10, '$' . number_format($i), 0, 0, 'R');
//        $pdf->Line($chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale);
//    }
//
//    // Добавляем метки осей
//    //$pdf->SetFont('Arial', 'B', 12);
//    $pdf->SetXY($chartWidth / 2 + 20, $chartYPos + 8);
//    $pdf->Cell(30, 10, $chartXLabel, 0, 0, 'C');
//    $pdf->SetXY($chartXPos + 7, $chartYPos - $chartHeight - 12);
//    $pdf->Cell(20, 10, $chartYLabel, 0, 0, 'R');
//
//    // Создаем столбцы графика
//    $xPos = $chartXPos + 40;
//    $bar = 0;
//
//    foreach ($data as $dataRow) {
//
//        // Вычисляем суммарное значение для продукта
//        $totalSales = 0;
//        foreach ($dataRow as $dataCell) $totalSales += $dataCell;
//
//        // Выводим столбец
//        $colourIndex = $bar % count($chartColours);
//        $pdf->SetFillColor($chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2]);
//        $pdf->Rect($xPos, $chartYPos - ($totalSales / $yScale), $barWidth, $totalSales / $yScale, 'DF');
//        $xPos += (1 / $xScale);
//        $bar++;
//    }
//
//
//    /***
//     * Выводим PDF
//     ***/
//
//    $pdf->Output("blank.pdf", "I");

}else{
    echo '<span style="color: red;">Ошибка данных</span>';
}
?>

