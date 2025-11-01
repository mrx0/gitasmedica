<?php

//test_parsing_handle_excel.php
//Тестовая функция для парсинга

// Пути к Poppler и Tesseract
$poppler = 'C:\\wamp\\www\\Fortest\\asm_journal\\poppler-25.07.0\\Library\\bin\\pdftoppm.exe';
$tesseract = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';

// PDF для обработки
if (!isset($target) || !file_exists($target)) {
    die('Файл PDF не найден.');
}

$base = $uploadDir . 'page_' . time();
$cmd1 = "\"$poppler\" -png \"$target\" \"$base\"";
exec($cmd1, $out1, $ret1);

if ($ret1 !== 0) {
    die('Ошибка при разложении PDF на изображения.');
}

$images = glob($base . '-*.png');
if (!$images) {
    die('Не удалось создать изображения из PDF.');
}

$textAll = '';
foreach ($images as $img) {
    $outputTxt = $img . '.txt';
    $cmd2 = "\"$tesseract\" \"$img\" \"$img\" -l rus+eng";
    exec($cmd2, $out2, $ret2);

    if ($ret2 === 0 && file_exists($outputTxt)) {
        $textAll .= file_get_contents($outputTxt) . "\n\n";
        unlink($outputTxt); // удалить промежуточный txt
    }
    unlink($img); // удалить изображение после обработки
}

// Вывод результата
if (trim($textAll) === '') {
    echo "<p><strong>Распознанный текст пуст.</strong></p>";
} else {
    echo "<h3>Распознанный текст:</h3>";
    echo "<pre style='white-space: pre-wrap; background:#f5f5f5; padding:10px; border:1px solid #ccc;'>"
        . htmlspecialchars($textAll, ENT_QUOTES, 'UTF-8')
        . "</pre>";
}

// === Попробуем выделить таблицу товаров ===

// === Извлечение товаров из раздела "Счет-фактура" ===

// 1. Ищем блок после "Счет-фактура"
if (preg_match('/С.?ч.?е.?т.?[-\s]*ф.?а.?к.?т.?у.?р.?а(.+)/uis', $textAll, $m)) {
    $invoiceText = $m[1];
} else {
    $invoiceText = $textAll;
}

// 2. Разбиваем на строки
$lines = preg_split('/\r?\n/', $invoiceText);
$items = [];

// 3. Новый шаблон под строки типа:
// "Артикаин 4% ... fur 4000] 12.4905 49 963,64 ..."
foreach ($lines as $line) {
    $src = trim(preg_replace('/\s+/', ' ', $line));
    if ($src === '' || mb_strlen($src) < 10) continue;

    // фильтруем только строки с наименованием товара
    if (!preg_match('/(Артикаин|Артикан)/iu', $src)) continue;

    // извлекаем количество (например 4,000 или 7,000)
    preg_match('/\b(\d{1,3}(?:[.,]\d{3})?)\b/u', $src, $mq);
    $qty = isset($mq[1]) ? str_replace([',', ' '], ['.', ''], $mq[1]) : '';

    // извлекаем цену — число, начинающееся с 1 или 2, с 4–6 цифрами после точки
    preg_match('/(\d{2,3}[.,]?\d{3}[.,]\d{2})/u', $src, $mp);
    $price = isset($mp[1]) ? str_replace([' ', ','], ['', '.'], $mp[1]) : '';

    // чистим наименование — до части с fur / числом
    $name = preg_replace('/[\=\d].*$/u', '', $src);
    $name = preg_replace('/^[ТЭ\|\s]+/u', '', $name);PhpOffice

    if ($price && (float)$price > 100) {
        $items[] = [
            'name' => trim($name),
            'qty' => $qty,
            'price' => $price
        ];
    }
}

// 4. Вывод
if ($items) {
    echo "<h3>Товары из счёта-фактуры:</h3>";
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Наименование</th><th>Кол-во</th><th>Цена за 1 шт</th></tr>";
    foreach ($items as $it) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($it['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td align='right'>" . htmlspecialchars($it['qty']) . "</td>";
        echo "<td align='right'>" . htmlspecialchars($it['price']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>Не удалось выделить товары из счёта-фактуры.</strong></p>";
}



?>
