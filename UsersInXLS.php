<?php

//UsersInXLS.php
//

/*
    array (size=13)
      'id' => string '720' (length=3)
      'login' => string 'admin' (length=5)
      'name' => string 'admin' (length=5)
      'full_name' => string 'admin' (length=5)
      'password' => string '8428625200' (length=10)
      'birth' => string '0000-00-00' (length=10)
      'contacts' => string '' (length=0)
      'permissions' => string '0' (length=1)
      'filial_id' => string '0' (length=1)
      'org' => string '0' (length=1)
      'fired' => string '1' (length=1)
      'status' => string '0' (length=1)
      'category' => null
*/

    require_once 'header.php';

    include_once 'DBWork.php';
    include_once 'functions.php';

    $filials_j = getAllFilials(false, false, true);

    $arr_permissions = SelDataFromDB('spr_permissions', '', '');
//    var_dump ($arr_permissions);
    $arr_orgs = SelDataFromDB('spr_org', '', '');
    //var_dump ($arr_orgs);

    include_once('DBWorkPDO.php');
    $db = new DB();

    $query = "SELECT sw.*, sc.name AS category
                FROM `spr_workers` sw  
                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
                WHERE sw.status <> '8' AND sw.login <> 'admin' AND sw.login <> 'mrx'              
                ORDER BY sw.full_name ASC";

    $users_j = $db::getRows($query, []);
//    var_dump($users_j);

    echo '
            <section>
                <section>';

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    //Путь для сохранения файла и скачивания
    $path = 'excel\users.xls';

    // Подключаем класс для работы с excel
    require_once('PHPExcel/Classes/PHPExcel.php');
    // Подключаем класс для вывода данных в формате excel
    require_once('PHPExcel/Classes/PHPExcel/Writer/Excel5.php');

    // Создаем объект класса PHPExcel
    $xls = new PHPExcel();
    // Устанавливаем индекс активного листа
    $xls->setActiveSheetIndex(0);
    // Получаем активный лист
    $sheet = $xls->getActiveSheet();
    // Подписываем лист
    $sheet->setTitle('Лист');

    //стили текста массивы
    $style_arial_7 = array(
        'font' => array(
            'name' => 'Arial',
            'size' => '7',
        ),
    );
    $style_arial_8 = array(
        'font' => array(
            'name' => 'Arial',
            'size' => '8',
        ),
    );
    $style_arial_9 = array(
        'font' => array(
            'name' => 'Arial',
            'size' => '9',
        ),
    );
    $style_bold = array(
        'font' => array(
            'bold' => true,
        ),
    );
    $style_verical_center = array(
        'alignment' => array (
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
    );
    $style_verical_top = array(
        'alignment' => array (
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        )
    );
    $style_horizontal_center = array(
        'alignment' => array (
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );
    $style_horizontal_left = array(
        'alignment' => array (
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        )
    );
    $style_wrap_true = array(
        'alignment' => array(
            'wrap' => true,
        ),
    );
    $style_wrap_false = array(
        'alignment' => array(
            'wrap' => false,
        ),
    );
    $style_number_money = array(
        'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00,
    );

    $style_border_around_black = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            )
        )
    );
    $style_border_around_white = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        )
    );
    $style_border_around_grey = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => 'CCCCCC'
                )
            )
        )
    );
    $style_border_left_fight = array(
        'borders' => array(
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
        )
    );
    $style_border_left = array(
        'borders' => array(
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
        )
    );
    $style_border_right = array(
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
        )
    );
    $style_border_bottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
        )
    );
    $style_border_top = array(
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000001'
                )
            ),
        )
    );
    $style_error = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'FF4F6F'
            ),
        )
    );

    //Переменная для расчета общей суммы всех нарядов из отчета
    $all_pay_arr_temp = array();

    //Стили по умолчанию
    $sheet->getParent()->getDefaultStyle()->applyFromArray($style_arial_9);
    $sheet->getParent()->getDefaultStyle()->applyFromArray($style_wrap_true);
    $sheet->getParent()->getDefaultStyle()->applyFromArray($style_border_around_grey);

    //Ширина столбцов
//    $sheet->getColumnDimension('A')->setWidth(23/5);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('C')->setWidth(12);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(30);
//    $sheet->getColumnDimension('G')->setWidth(66/5);
//    $sheet->getColumnDimension('H')->setWidth(76/5);
//    $sheet->getColumnDimension('I')->setWidth(51/5);
//    $sheet->getColumnDimension('J')->setWidth(32/5);
//    $sheet->getColumnDimension('K')->setWidth(57/5);
//    $sheet->getColumnDimension('L')->setWidth(2/5);

//Временная переменная для отображения в акте
//$str_time_temp = 'с '.date('d.m.Y', strtotime($_POST['datastart'].' 00:00:00')).' по '.date('d.m.Y', strtotime($_POST['dataend'].' 23:59:59'));

// Вставляем текст в ячейку A1
/*$sheet->setCellValue("A1", 'Расшифровка к Акту выполненных работ '.$str_time_temp);
//применяем стиль текста
$sheet->getStyle('A1')->applyFromArray($style_wrap_false);
//устанавливает строке высоту
$sheet->getRowDimension(1)->setRowHeight(14);*/

/*$sheet->setCellValue("A2", 'Перечень медицинских услуг, оказанных застрахованным лицам');
$sheet->getStyle('A2')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(2)->setRowHeight(14);
$sheet->setCellValue("A3", $insure_j[0]['name']);
$sheet->getStyle('A3')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(3)->setRowHeight(14);
$sheet->setCellValue("A4", '_______________');
$sheet->getStyle('A4')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(4)->setRowHeight(14);
$sheet->setCellValue("A5", 'в рамках Договора  на предоставление лечебно-профилактической помощи');
$sheet->getStyle('A5')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(5)->setRowHeight(14);
$sheet->setCellValue("A6", '(медицинских услуг) по добровольному медицинскому страхованию');
$sheet->getStyle('A6')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(6)->setRowHeight(14);
$sheet->setCellValue("A7", ' № '.$insure_j[0]['contract2']);
$sheet->getStyle('A7')->applyFromArray($style_wrap_false);
$sheet->getRowDimension(7)->setRowHeight(14);*/

// Выравнивание текста
/*
$sheet->getStyle('A1')->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
*/

//устанавливает строке высоту
$sheet->getRowDimension(1)->setRowHeight(20);

//$sheet->getRowDimension(2)->setRowHeight(31);
//$sheet->getRowDimension(1)->setRowHeight(20);

//Заголовок таблицы
$sheet->setCellValue("A2", '№ п/п');
$sheet->getStyle('A2')->applyFromArray($style_arial_9);
$sheet->getStyle('A2')->applyFromArray($style_bold);
$sheet->getStyle('A2')->applyFromArray($style_verical_center);
$sheet->getStyle('A2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('A2')->applyFromArray($style_border_around_black);
$sheet->setCellValue("B2", 'ФИО');
$sheet->getStyle('B2')->applyFromArray($style_arial_9);
$sheet->getStyle('B2')->applyFromArray($style_bold);
$sheet->getStyle('B2')->applyFromArray($style_verical_center);
$sheet->getStyle('B2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('B2')->applyFromArray($style_border_around_black);
$sheet->setCellValue("C2", 'ДР');
$sheet->getStyle('C2')->applyFromArray($style_arial_9);
$sheet->getStyle('C2')->applyFromArray($style_bold);
$sheet->getStyle('C2')->applyFromArray($style_verical_center);
$sheet->getStyle('C2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('C2')->applyFromArray($style_border_around_black);
$sheet->setCellValue("D2", 'Тел.');
$sheet->getStyle('D2')->applyFromArray($style_arial_9);
$sheet->getStyle('D2')->applyFromArray($style_bold);
$sheet->getStyle('D2')->applyFromArray($style_verical_center);
$sheet->getStyle('D2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('D2')->applyFromArray($style_border_around_black);
$sheet->setCellValue("E2", 'Должность');
$sheet->getStyle('E2')->applyFromArray($style_arial_9);
$sheet->getStyle('E2')->applyFromArray($style_bold);
$sheet->getStyle('E2')->applyFromArray($style_verical_center);
$sheet->getStyle('E2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('E2')->applyFromArray($style_border_around_black);
$sheet->setCellValue("F2", 'филиал');
$sheet->getStyle('F2')->applyFromArray($style_arial_9);
$sheet->getStyle('F2')->applyFromArray($style_bold);
$sheet->getStyle('F2')->applyFromArray($style_verical_center);
$sheet->getStyle('F2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('F2')->applyFromArray($style_border_around_black);
//$sheet->mergeCells('F2:H9');
$sheet->setCellValue("G2", 'права доступа');
$sheet->getStyle('G2')->applyFromArray($style_arial_9);
$sheet->getStyle('G2')->applyFromArray($style_bold);
$sheet->getStyle('G2')->applyFromArray($style_verical_center);
$sheet->getStyle('G2')->applyFromArray($style_horizontal_center);
$sheet->getStyle('G2')->applyFromArray($style_border_around_black);

//$sheet->getStyle('G9')->applyFromArray($style_border_around_black);
//$sheet->getStyle('H9')->applyFromArray($style_border_around_black);
//
//$sheet->setCellValue("I9", 'Цена руб');
//$sheet->getStyle('I9')->applyFromArray($style_arial_8);
//$sheet->getStyle('I9')->applyFromArray($style_bold);
//$sheet->getStyle('I9')->applyFromArray($style_verical_center);
//$sheet->getStyle('I9')->applyFromArray($style_horizontal_center);
//$sheet->getStyle('I9')->applyFromArray($style_border_around_black);
//$sheet->setCellValue("J9", 'Кол-во');
//$sheet->getStyle('J9')->applyFromArray($style_arial_8);
//$sheet->getStyle('J9')->applyFromArray($style_bold);
//$sheet->getStyle('J9')->applyFromArray($style_verical_center);
//$sheet->getStyle('J9')->applyFromArray($style_horizontal_center);
//$sheet->getStyle('J9')->applyFromArray($style_border_around_black);
//$sheet->setCellValue("K9", 'Сумма руб');
//$sheet->getStyle('K9')->applyFromArray($style_arial_8);
//$sheet->getStyle('K9')->applyFromArray($style_bold);
//$sheet->getStyle('K9')->applyFromArray($style_verical_center);
//$sheet->getStyle('K9')->applyFromArray($style_horizontal_center);
//$sheet->getStyle('K9')->applyFromArray($style_border_around_black);

//Порядковый номер записи
$count = 1;
//Номер строки
$countRow = 3;

$i = 1;

//var_dump($rezult_arr);

//Пробуем сформировать xls из данных
foreach ($users_j as $data) {
//
//    $fio_pay_arr_temp = array();
//
//    //Страховая пациента
//    $fio_insure = $rezult_arr_fio['data']['insure'];
//
//    //Если страховая пациента попавшегося в массиве совпадает с той, которую мы ищем
//    //if ($fio_insure == $_POST['insure']) {
//
    //Высота строки
    $sheet->getRowDimension(1)->setRowHeight(20);

//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_left);

    //Вставляем №
    $sheet->setCellValue('A' . $countRow, $i);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('A' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('A' . $countRow)->applyFromArray($style_horizontal_center);
    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_around_black);

    //Вставляем ФИО
    $sheet->setCellValue('B' . $countRow, WriteSearchUser('spr_workers', $data['id'], 'user_full', false));
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('B' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('B' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('B' . $countRow)->applyFromArray($style_border_around_black);

    //Вставляем ДР
    $sheet->setCellValue('C' . $countRow, explode('-', $data['birth'])[2].'.'.explode('-', $data['birth'])[1].'.'.explode('-', $data['birth'])[0]);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('C' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('C' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_around_black);

    //Вставляем контакты
    $sheet->setCellValue('D' . $countRow, $data['contacts']);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('D' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('D' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_around_black);

    $permissions = SearchInArray($arr_permissions, $data['permissions'], 'name');
//    var_dump($permissions);

    if ($permissions === 0) {
        $permissions = '';
    }
//    }else{
//        $permissions = $permissions_j;
//    }

    //Вставляем должность
    $sheet->setCellValue('E' . $countRow, $permissions);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('E' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('E' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('E' . $countRow)->applyFromArray($style_border_around_black);

    if ($data['filial_id'] != 0){
        $filial = $filials_j[$data['filial_id']]['name'];
    }else{
        $filial = '';
    }

    //Вставляем филиал
    $sheet->setCellValue('F' . $countRow, $filial);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('F' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('F' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_around_black);

    //Вставляем права доступа
    $sheet->setCellValue('G' . $countRow, '');
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
    $sheet->getStyle('G' . $countRow)->applyFromArray($style_verical_center);
    $sheet->getStyle('G' . $countRow)->applyFromArray($style_horizontal_left);
    $sheet->getStyle('G' . $countRow)->applyFromArray($style_border_around_black);




    //Объединяем ячейки
//    $sheet->mergeCells('C' . $countRow . ':F' . $countRow);
    //Применяем стили
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_left);

    //Устанавливаем авто подбор высоты
    //$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(-1);

//    //Вставляем полис
//    $sheet->setCellValue('G' . $countRow, 'Полис №');
//    $sheet->getStyle('G' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('G' . $countRow)->applyFromArray($style_bold);
//
//    $sheet->setCellValue('H' . $countRow, $rezult_arr_fio['data']['polis']);
//    $sheet->mergeCells('H' . $countRow . ':J' . $countRow);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_bold);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_horizontal_left);
//
//    //Общая сумма по пациенту
//    $patient_summ = 0;
//    //Результат для вывода по пациенту
//    $rez_str_fio = '';
//
//    $insure_j = SelDataFromDB('spr_insure', $rezult_arr_fio['data']['insure'], 'id');
//
//    //бегаем по датам
//    foreach ($rezult_arr_fio as $fio_time => $rezult_arr_time) {
//
//        if ($fio_time != 'data') {
//            //Увеличиваем номер строки
//            $countRow++;
//
//            $rez_str_invoice_zub = '';
//            //Сумма каждого наряда
//            $invoice_summ = 0;
//
//            //Вставляем порядоковый номер
//            $sheet->setCellValue('A' . $countRow, $count);
//            $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
//            $sheet->getStyle('A' . $countRow)->applyFromArray($style_horizontal_center);
//            $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_left);
//
//            $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_bottom);
//            $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_left);
//
//            //Вставляем дату
//            $sheet->setCellValue('D' . $countRow, date('d.m.Y', strtotime($fio_time)));
//            $sheet->getStyle('D' . $countRow)->applyFromArray($style_arial_8);
//            $sheet->getStyle('D' . $countRow)->applyFromArray($style_bold);
//            $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_bottom);
//
//            //ФИО врача
//            $sheet->setCellValue('E' . $countRow, WriteSearchUser('spr_workers', $rezult_arr_time['worker_id'], 'user', false));
//            $sheet->mergeCells('E' . $countRow . ':F' . $countRow);
//            $sheet->getStyle('E' . $countRow)->applyFromArray($style_arial_8);
//            $sheet->getStyle('E' . $countRow)->applyFromArray($style_bold);
//            $sheet->getStyle('E' . $countRow)->applyFromArray($style_horizontal_left);
//            $sheet->getStyle('E' . $countRow)->applyFromArray($style_border_bottom);
//
//            $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_bottom);
//
//            //Прописываем страховую и договор
//            //!!! переделать с учетом нескольких договоров
//            if ($insure_j != 0) {
//                $sheet->setCellValue('G' . $countRow, $insure_j[0]['name'] . '. Дог. №' . $insure_j[0]['contract2']);
//                $sheet->mergeCells('G' . $countRow . ':J' . $countRow);
//                $sheet->getStyle('G' . $countRow)->applyFromArray($style_arial_8);
//                $sheet->getStyle('G' . $countRow)->applyFromArray($style_horizontal_left);
//                $sheet->getStyle('G' . $countRow)->applyFromArray($style_border_bottom);
//            } else {
//                if ($withErrors) {
//                    $sheet->setCellValue('G' . $countRow, 'ошибка страховой');
//                    $sheet->mergeCells('G' . $countRow . ':J' . $countRow);
//                    $sheet->getStyle('G' . $countRow)->applyFromArray($style_arial_8);
//                    $sheet->getStyle('G' . $countRow)->applyFromArray($style_horizontal_left);
//                    $sheet->getStyle('G' . $countRow)->applyFromArray($style_error);
//                    $sheet->getStyle('G' . $countRow)->applyFromArray($style_border_bottom);
//                }
//            }
//
//            $sheet->getStyle('H' . $countRow)->applyFromArray($style_border_bottom);
//
//            $sheet->getStyle('I' . $countRow)->applyFromArray($style_border_bottom);
//
//            $sheet->getStyle('J' . $countRow)->applyFromArray($style_border_bottom);
//
//            //Номер строки куда будем вставлять стоимость наряда
//            $invoiceSummCell = $countRow;
//
//            //Костыль, чтоб не появлялась лишняя строка пустая при переходе с зуба на зуб
//            $prev_zub = 0;
//
//            //Бегаем по наряду пациента
//            foreach ($rezult_arr_time['invoice_ex'] as $zub => $invoice_ex_data) {
//
//                //Костыль, чтоб не появлялась лишняя строка пустая при переходе с зуба на зуб
//                if ($prev_zub == 0) {
//                    $prev_zub = $zub;
//
//                    //Временный массив для всей суммы по пациенту
//                    //$fio_pay_arr_temp = array();
//
//                    //Временный массив для стоимости наряда
//                    $invoice_pay_arr_temp = array();
//                }
//
//                //Сумма каждого наряда
//                $invoice_summ_zub = 0;
//                //Временная переменная для ???!!!
//                $rez_str_invoice_ex = '';
//
//                //Бегаем по каждой позиции для зуба
//                foreach ($invoice_ex_data as $invoice_ex_zub_data) {
//                    //var_dump($invoice_ex_zub_data);
//
//                    //Увеличиваем номер строки
//                    $countRow++;
//
//                    //Костыль, чтоб не появлялась лишняя строка пустая при переходе с зуба на зуб
//                    if ($zub != $prev_zub) {
//                        $countRow--;
//                        $prev_zub = $zub;
//                    }
//
//                    $zub_count = '';
//                    if ($zub == 99) {
//                    } else {
//                        $zub_count .= $zub;
//                    }
//
//                    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_left);
//
//                    //Проверка $countRow
//                    //$sheet->setCellValue('B' . $countRow, $countRow);
//
//                    //Номер зуба
//                    $sheet->setCellValue('C' . $countRow, $zub_count);
//                    $sheet->getStyle('C' . $countRow)->applyFromArray($style_arial_8);
//                    $sheet->getStyle('C' . $countRow)->applyFromArray($style_horizontal_center);
//                    $sheet->getStyle('C' . $countRow)->applyFromArray($style_verical_top);
//                    $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_left_fight);
//
//                    //Диагноз
//                    if (isset($rezult_arr_time['invoice_ex_mkb'][$zub])) {
//                        //Временная переменная для диагнозов
//                        $mkb_str_temp = '';
//
//                        if (!empty($rezult_arr_time['invoice_ex_mkb'][$zub])) {
//                            foreach ($rezult_arr_time['invoice_ex_mkb'][$zub] as $mkb) {
//                                $rez = array();
//                                //$rezult2 = array();
//
//                                $query = "SELECT `name`, `code` FROM `spr_mkb` WHERE `id` = '{$mkb['mkb_id']}'";
//
//                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//                                $number = mysqli_num_rows($res);
//                                if ($number != 0) {
//                                    while ($arr = mysqli_fetch_assoc($res)) {
//                                        $rez[$mkb['mkb_id']] = $arr;
//                                    }
//                                } else {
//                                    $rez = 0;
//                                }
//                                if ($rez != 0) {
//                                    foreach ($rez as $mkb_name_val) {
//                                        $mkb_str_temp .= $mkb_name_val['code'] . ' ' . $mkb_name_val['name'];
//                                    }
//                                    $sheet->setCellValue('D' . $countRow, $mkb_str_temp);
//                                    $sheet->getStyle('D' . $countRow)->applyFromArray($style_arial_8);
//                                    $sheet->getStyle('D' . $countRow)->applyFromArray($style_horizontal_left);
//                                    $sheet->getStyle('D' . $countRow)->applyFromArray($style_verical_top);
//                                    $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_left_fight);
//                                } else {
//                                    if ($withErrors) {
//                                        $sheet->setCellValue('D' . $countRow, 'ошибка диагноза');
//                                        $sheet->getStyle('D' . $countRow)->applyFromArray($style_arial_8);
//                                        $sheet->getStyle('D' . $countRow)->applyFromArray($style_horizontal_left);
//                                        $sheet->getStyle('D' . $countRow)->applyFromArray($style_verical_center);
//                                        $sheet->getStyle('D' . $countRow)->applyFromArray($style_error);
//                                        $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_left_fight);
//                                    }
//                                }
//                            }
//                        }
//                    } else {
//                        if ($withErrors) {
//                            $sheet->setCellValue('D' . $countRow, 'ошибка диагноза');
//                            $sheet->getStyle('D' . $countRow)->applyFromArray($style_arial_8);
//                            $sheet->getStyle('D' . $countRow)->applyFromArray($style_horizontal_left);
//                            $sheet->getStyle('D' . $countRow)->applyFromArray($style_verical_center);
//                            $sheet->getStyle('D' . $countRow)->applyFromArray($style_error);
//                            $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_left_fight);
//                        }
//                    }
//
//                    //Переходим к названиям позиций из наряда
//                    $arr = array();
//                    $rez = array();
//
//                    $query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$invoice_ex_zub_data['price_id']}'";
//
//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//                    $number = mysqli_num_rows($res);
//                    if ($number != 0) {
//                        while ($arr = mysqli_fetch_assoc($res)) {
//                            array_push($rez, $arr);
//                        }
//                        $rezult2 = $rez;
//                    } else {
//                        $rezult2 = 0;
//                    }
//
//                    //var_dump($rezult2);
//
//                    //Код
//                    $sheet->setCellValue('E' . $countRow, $rezult2[0]['code']);
//                    $sheet->getStyle('E' . $countRow)->applyFromArray($style_arial_8);
//                    $sheet->getStyle('E' . $countRow)->applyFromArray($style_horizontal_left);
//                    $sheet->getStyle('E' . $countRow)->applyFromArray($style_verical_top);
//                    $sheet->getStyle('E' . $countRow)->applyFromArray($style_border_left_fight);
//
//                    //Название
//                    if ($rezult2 != 0) {
//
//                        $sheet->setCellValue('F' . $countRow, htmlspecialchars_decode($rezult2[0]['name']));
//                        $sheet->getStyle('F' . $countRow)->applyFromArray($style_arial_8);
//                        $sheet->getStyle('F' . $countRow)->applyFromArray($style_horizontal_left);
//                        $sheet->getStyle('F' . $countRow)->applyFromArray($style_verical_top);
//                        $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_left_fight);
//
//                    } else {
//                        if ($withErrors) {
//                            $sheet->setCellValue('F' . $countRow, 'ошибка названия позиции');
//                            $sheet->getStyle('F' . $countRow)->applyFromArray($style_arial_8);
//                            $sheet->getStyle('F' . $countRow)->applyFromArray($style_horizontal_left);
//                            $sheet->getStyle('F' . $countRow)->applyFromArray($style_verical_top);
//                            $sheet->getStyle('F' . $countRow)->applyFromArray($style_error);
//                            $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_left_fight);
//
//                        }
//                    }
//
//                    $sheet->mergeCells('F' . $countRow . ':H' . $countRow);
//
//                    //Цена позиции
//                    if ($invoice_ex_zub_data['price'] > 0) {
//                        $sheet->setCellValue('I' . $countRow, number_format($invoice_ex_zub_data['price'], 2, ',', ''));
//                        $sheet->getStyle('I' . $countRow)->applyFromArray($style_arial_8);
//                        $sheet->getStyle('I' . $countRow)->applyFromArray($style_verical_top);
//                        $sheet->getStyle('I' . $countRow)->getNumberFormat()->applyFromArray($style_number_money);
//                        $sheet->getStyle('I' . $countRow)->applyFromArray($style_border_left_fight);
//
//                    } else {
//                        if ($withErrors) {
//                            $sheet->setCellValue('I' . $countRow, number_format($invoice_ex_zub_data['price'], 2, ',', ''));
//                            $sheet->getStyle('I' . $countRow)->applyFromArray($style_arial_8);
//                            $sheet->getStyle('I' . $countRow)->applyFromArray($style_verical_top);
//                            $sheet->getStyle('I' . $countRow)->getNumberFormat()->applyFromArray($style_number_money);
//                            $sheet->getStyle('I' . $countRow)->applyFromArray($style_error);
//                            $sheet->getStyle('I' . $countRow)->applyFromArray($style_border_left_fight);
//
//                        }
//                    }
//
//                    //Количество позиции
//                    $sheet->setCellValue('J' . $countRow, $invoice_ex_zub_data['quantity']);
//                    $sheet->getStyle('J' . $countRow)->applyFromArray($style_arial_8);
//                    $sheet->getStyle('J' . $countRow)->applyFromArray($style_verical_top);
//                    $sheet->getStyle('J' . $countRow)->applyFromArray($style_border_left_fight);
//
//
//                    //Стоимость позиции
//                    $sheet->setCellValue('K'.$countRow, '='.'I'.$countRow.'*'.'J'.$countRow);
//                    $sheet->getStyle('K'.$countRow)->applyFromArray($style_arial_8);
//                    $sheet->getStyle('K'.$countRow)->applyFromArray($style_verical_top);
//                    $sheet->getStyle('K'.$countRow)->getNumberFormat()->applyFromArray($style_number_money);
//                    $sheet->getStyle('K' . $countRow)->applyFromArray($style_border_left_fight);
//
//                    $invoice_pay_arr_call_temp = 'K'.$countRow;
//                    //var_dump($invoice_pay_arr_call_temp);
//                    array_push($invoice_pay_arr_temp, $invoice_pay_arr_call_temp);
//
//                }
//                //var_dump($invoice_pay_arr_temp);
//
//                //Временная переменная для строки с формулой для расчета стоимости наряда
//                $invoice_summ_str_temp = '';
//
//                //Получим строку для расчета стоимости наряда
//                foreach ($invoice_pay_arr_temp as $cell){
//                    $invoice_summ_str_temp .= $cell.'+';
//                }
//
//                //Стоимость наряда
//                $sheet->setCellValue('K'.$invoiceSummCell, '='.$invoice_summ_str_temp.'0');
//                $sheet->getStyle('K' . $invoiceSummCell)->applyFromArray($style_arial_9);
//                $sheet->getStyle('K' . $invoiceSummCell)->applyFromArray($style_bold);
//                $sheet->getStyle('K' . $invoiceSummCell)->applyFromArray($style_verical_top);
//                $sheet->getStyle('K' . $invoiceSummCell)->getNumberFormat()->applyFromArray($style_number_money);
//                $sheet->getStyle('K' . $invoiceSummCell)->applyFromArray($style_border_bottom);
//
//                //Ячейки с суммами нарядов пациента
//                $fio_pay_arr_call_temp = 'K' . $invoiceSummCell;
//
//                if (!in_array($fio_pay_arr_call_temp, $fio_pay_arr_temp)) {
//                    array_push($fio_pay_arr_temp, $fio_pay_arr_call_temp);
//                }
//
//                //var_dump($fio_pay_arr_call_temp);
//                //var_dump($fio_pay_arr_temp);
//
//                //Увеличиваем номер строки
//                $countRow++;
//
//            }
//
//            //Увеличиваем порядковый номер
//            $count++;
//        }
//    }
//
//    //Увеличиваем номер строки
//    //$countRow++;
//
//    //Итого по всем нарядам пациента
//    $sheet->setCellValue('A' . $countRow, 'Итого по пациенту:');
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_horizontal_left);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_verical_top);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_bottom);
//    $sheet->getStyle('A' . $countRow)->applyFromArray($style_border_left);
//
//    $sheet->mergeCells('A' . $countRow . ':D' . $countRow);
//
//    $sheet->getStyle('B' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('B' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('C' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('D' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('E' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('E' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('F' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('G' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('G' . $countRow)->applyFromArray($style_border_bottom);
//
//    //Временная переменная для строки с формулой для расчета всей суммы нарядов по пациенту
//    $fio_summ_str_temp = '';
//
//    //var_dump($fio_pay_arr_temp);
//
//    //Получим строку для расчета всей суммы нарядов по пациенту
//    foreach ($fio_pay_arr_temp as $cell){
//        $fio_summ_str_temp .= $cell.'+';
//    }
//
//    $sheet->setCellValue('H' . $countRow, '='.$fio_summ_str_temp.'0');
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_arial_9);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_bold);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_verical_top);
//    $sheet->getStyle('H' . $countRow)->getNumberFormat()->applyFromArray($style_number_money);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->mergeCells('H' . $countRow . ':K' . $countRow);
//
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('H' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('I' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('I' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('K' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('K' . $countRow)->applyFromArray($style_border_right);
//    $sheet->getStyle('K' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getStyle('J' . $countRow)->applyFromArray($style_border_top);
//    $sheet->getStyle('J' . $countRow)->applyFromArray($style_border_bottom);
//
//    $sheet->getRowDimension($countRow)->setRowHeight(20);
//
//    //Ячейки с суммами всех нарядов всех пациента
//    $all_pay_arr_call_temp = 'H' . $countRow;
//    array_push($all_pay_arr_temp, $all_pay_arr_call_temp);
//
    //Увеличиваем номер строки
    $countRow++;
//    //}

    $i++;

}

////Вывод общей суммы
//$sheet->setCellValue('A' . $countRow, 'ВСЕГО:');
//$sheet->getStyle('A' . $countRow)->applyFromArray($style_arial_9);
//$sheet->getStyle('A' . $countRow)->applyFromArray($style_bold);
//$sheet->getStyle('A' . $countRow)->applyFromArray($style_horizontal_left);
//$sheet->getStyle('A' . $countRow)->applyFromArray($style_verical_top);
//
//$sheet->mergeCells('A' . $countRow . ':D' . $countRow);

////Временная переменная для строки с формулой для расчета всех сумм нарядов всех пациентов
//$all_pay_arr_call_temp = '';
//
////Получим строку для расчета всей суммы нарядов по пациенту
//foreach ($all_pay_arr_temp as $cell){
//    $all_pay_arr_call_temp .= $cell.'+';
//}
//
//$sheet->setCellValue('H' . $countRow, '='.$all_pay_arr_call_temp.'0');
//$sheet->getStyle('H' . $countRow)->applyFromArray($style_arial_9);
//$sheet->getStyle('H' . $countRow)->applyFromArray($style_bold);
//$sheet->getStyle('H' . $countRow)->applyFromArray($style_verical_top);
//$sheet->getStyle('H' . $countRow)->getNumberFormat()->applyFromArray($style_number_money);
//
//$sheet->mergeCells('H' . $countRow . ':K' . $countRow);
//
//$sheet->getRowDimension($countRow)->setRowHeight(20);

// Выводим содержимое в файл
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save($path);




?>