<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Предварительный просмотр данных для сохранения в БД</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .preview-section { background: #f8f9fa; border: 2px solid #dee2e6; padding: 15px; margin: 15px 0; border-radius: 8px; }
    .preview-section h3 { margin-top: 0; color: #495057; }
    .data-table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    .data-table th { background: #007bff; color: white; padding: 10px; text-align: left; }
    .data-table td { border: 1px solid #dee2e6; padding: 8px; }
    .data-table tr:nth-child(even) { background: #f8f9fa; }
    .supplier-box { background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 8px; }
    .supplier-box strong { color: #155724; }
    .json-preview { background: #f4f4f4; border: 1px solid #ccc; padding: 15px; overflow-x: auto; white-space: pre-wrap; font-family: monospace; font-size: 12px; }
    .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 20px 0; }
    .back-btn:hover { background: #5a6268; }
    .stats { background: #cfe2ff; border-left: 4px solid #0d6efd; padding: 10px; margin: 10px 0; }
</style>";

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Ошибка: неверный метод запроса</p>");
}

// === 1) Получение общих данных ===
$documentNumber = isset($_POST['document_number']) ? trim($_POST['document_number']) : '';
$supplier = isset($_POST['supplier']) ? trim($_POST['supplier']) : '';
$deliveryDate = isset($_POST['delivery_date']) ? trim($_POST['delivery_date']) : '';

if (empty($documentNumber) || empty($supplier) || empty($deliveryDate)) {
    die("<p style='color:red;'>Ошибка: не заполнены обязательные поля (Номер документа, Поставщик или Дата поставки)</p>");
}

echo "<div class='supplier-box'>";
echo "<h3>📦 Информация о накладной</h3>";
echo "<p><strong>Номер документа:</strong> " . htmlspecialchars($documentNumber, ENT_QUOTES, 'UTF-8') . "</p>";
echo "<p><strong>Поставщик:</strong> " . htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8') . "</p>";
echo "<p><strong>Дата поставки:</strong> " . htmlspecialchars($deliveryDate, ENT_QUOTES, 'UTF-8') . "</p>";
echo "</div>";

// === 2) Обработка данных таблиц - собираем в ОДИН массив ===
$allItems = array(); // Единый массив всех товаров из всех таблиц
$totalTables = 0;

// Получаем список всех таблиц из POST
$tableIds = array();
foreach ($_POST as $key => $value) {
    if (strpos($key, 'rows_') === 0) {
        $tableId = str_replace('rows_', '', $key);
        if (!in_array($tableId, $tableIds)) {
            $tableIds[] = $tableId;
        }
    }
}

foreach ($tableIds as $tableId) {
    $totalTables++;

    // Получаем отмеченные строки для этой таблицы
    $selectedRows = isset($_POST['rows_' . $tableId]) ? $_POST['rows_' . $tableId] : array();

    if (empty($selectedRows)) {
        continue;
    }

    // Получаем типы колонок для этой таблицы
    $columnTypes = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'col_type_' . $tableId . '_') === 0) {
            $col = str_replace('col_type_' . $tableId . '_', '', $key);
            $columnTypes[$col] = $value;
        }
    }

    // Фильтруем только нужные колонки (не ignore)
    $activeColumns = array();
    foreach ($columnTypes as $col => $type) {
        if ($type !== 'ignore') {
            $activeColumns[$col] = $type;
        }
    }

    if (empty($activeColumns)) {
        continue;
    }

    // Собираем данные по строкам
    foreach ($selectedRows as $rowIndex) {
        $rowData = array();

        foreach ($activeColumns as $col => $type) {
            $dataKey = 'data_' . $tableId . '_' . $rowIndex . '_' . $col;
            if (isset($_POST[$dataKey])) {
                $rowData[$type] = $_POST[$dataKey];
            }
        }

        // Добавляем только если есть название
        if (!empty($rowData['name'])) {
            $allItems[] = $rowData; // Добавляем в общий массив
        }
    }
}

// Функция для конвертации в число (запятая = десятичный разделитель)
function toFloat($value) {
    if (empty($value) || $value === '-') return 0;
    // Убираем все пробелы
    $value = str_replace(array(' ', "\xC2\xA0"), '', $value);
    // Заменяем запятую на точку для PHP
    $value = str_replace(',', '.', $value);
    return floatval($value);
}

// Функция для форматирования числа (с запятой как десятичный разделитель)
function formatNumber($value, $decimals = 2) {
    if ($value == 0) return '0';

    // Форматируем с точкой
    $formatted = number_format($value, $decimals, '.', '');

    // Убираем незначащие нули
    $formatted = rtrim($formatted, '0');
    $formatted = rtrim($formatted, '.');

    // Заменяем точку на запятую
    $formatted = str_replace('.', ',', $formatted);

    return $formatted;
}

// Обрабатываем все товары: вычисляем цену и конвертируем в копейки
foreach ($allItems as &$item) {
    // Получаем количество и стоимость
    $qty = isset($item['qty']) ? toFloat($item['qty']) : 0;
    $total = isset($item['total']) ? toFloat($item['total']) : 0;

    // Вычисляем цену за единицу (если есть количество)
    $price = ($qty > 0) ? ($total / $qty) : 0;

    // Сохраняем для отображения с запятыми
    $item['price_display'] = formatNumber($price, 2);
    $item['qty_display'] = formatNumber($qty, 3);
    $item['total_display'] = formatNumber($total, 2);

    // Конвертируем в копейки для БД
    $item['price_kopeks'] = round($price * 100);
    $item['total_kopeks'] = round($total * 100);
    $item['qty_value'] = $qty;
}
unset($item);

// === 3) Итоговая статистика ===
$totalItems = count($allItems);

// echo "<div class='supplier-box'>";
// echo "<h3>📊 Итоговая статистика</h3>";
// echo "<p><strong>Номер документа:</strong> $documentNumber</p>";
// echo "<p><strong>Обработано таблиц:</strong> $totalTables</p>";
// echo "<p><strong>Всего товаров для сохранения:</strong> $totalItems</p>";
// echo "</div>";

// === 4) ЕДИНЫЙ массив всех товаров - ОДНА ТАБЛИЦА ===
echo "<div class='preview-section'>";
echo "<h3>📦 ОБЩАЯ НАКЛАДНАЯ - ВСЕ ТОВАРЫ</h3>";
echo "<div class='stats'>📊 Обработано таблиц из файла: $totalTables | ✅ Всего позиций: $totalItems</div>";

if (!empty($allItems)) {
    // Подсчитываем итоги
    $totalQty = 0;
    $totalSum = 0;
    foreach ($allItems as $item) {
        $totalQty += $item['qty_value'];
        $totalSum += toFloat($item['total_display']);
    }

    echo "<table class='data-table'>";
    echo "<thead><tr>";
    echo "<th>№</th><th>Наименование</th><th>Количество</th><th>Цена за ед. (₽)</th><th>Сумма (₽)</th>";
    echo "</tr></thead><tbody>";

    $num = 1;
    foreach ($allItems as $item) {
        echo "<tr>";
        echo "<td>" . $num++ . "</td>";
        echo "<td>" . htmlspecialchars(isset($item['name']) ? $item['name'] : '', ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($item['qty_display'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($item['price_display'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($item['total_display'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }

    // Итоговая строка
    echo "<tr style='background: #fff3cd; font-weight: bold; border-top: 3px solid #333;'>";
    echo "<td colspan='2' style='text-align: right;'>ИТОГО:</td>";
    echo "<td>" . formatNumber($totalQty, 3) . "</td>";
    echo "<td></td>";
    echo "<td>" . formatNumber($totalSum, 2) . "</td>";
    echo "</tr>";

    echo "</tbody></table>";

    echo "<div class='stats' style='background: #d4edda; border-left-color: #28a745;'>";
    echo "✅ <strong>Итоговые суммы для проверки:</strong><br>";
    echo "Общее количество: <strong>" . formatNumber($totalQty, 3) . "</strong><br>";
    echo "Общая сумма: <strong>" . formatNumber($totalSum, 2) . " ₽</strong> (в копейках: <strong>" . round($totalSum * 100) . "</strong>)";
    echo "</div>";
} else {
    echo "<p>Нет данных для отображения</p>";
}
echo "</div>";

// === 5) JSON для отладки ===
echo "<div class='preview-section'>";
echo "<h3>🔧 Структура данных для БД (JSON)</h3>";
echo "<p><strong>Примечание:</strong> Цены и стоимость в копейках для точного хранения в БД</p>";
echo "<div class='json-preview'>";

$jsonData = array(
    'document_number' => $documentNumber,
    'supplier' => $supplier,
    'delivery_date' => $deliveryDate,
    'total_items' => $totalItems,
    'items' => array()
);

foreach ($allItems as $item) {
    $jsonData['items'][] = array(
        'name' => isset($item['name']) ? $item['name'] : '',
        'qty' => formatNumber($item['qty_value'], 3),
        'price_kopeks' => $item['price_kopeks'],
        'total_kopeks' => $item['total_kopeks']
    );
}

echo json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo "</div>";
echo "</div>";

// // === 6) Пример SQL запросов ===
// echo "<div class='preview-section'>";
// echo "<h3>💾 Примеры SQL запросов для сохранения</h3>";
// echo "<div class='json-preview'>";
// echo "-- Создание таблицы накладных\n";
// echo "CREATE TABLE IF NOT EXISTS deliveries (\n";
// echo "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
// echo "    document_number VARCHAR(100) NOT NULL,\n";
// echo "    supplier VARCHAR(255) NOT NULL,\n";
// echo "    delivery_date DATE NOT NULL,\n";
// echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n";
// echo ");\n\n";
//
// echo "-- Создание таблицы товаров\n";
// echo "CREATE TABLE IF NOT EXISTS delivery_items (\n";
// echo "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
// echo "    delivery_id INT NOT NULL,\n";
// echo "    name TEXT,\n";
// echo "    qty DECIMAL(10,3),\n";
// echo "    price_kopeks INT COMMENT 'Цена в копейках',\n";
// echo "    total_kopeks INT COMMENT 'Сумма в копейках',\n";
// echo "    FOREIGN KEY (delivery_id) REFERENCES deliveries(id)\n";
// echo ");\n\n";
//
// echo "-- Пример INSERT запроса для накладной\n";
// echo "INSERT INTO deliveries (document_number, supplier, delivery_date) \n";
// echo "VALUES ('" . addslashes($documentNumber) . "', '" . addslashes($supplier) . "', '$deliveryDate');\n\n";
//
// echo "-- Примеры INSERT запросов для товаров (первые 5)\n";
// $count = 0;
// foreach ($allItems as $item) {
//     if ($count >= 5) break;
//
//     $name = isset($item['name']) ? addslashes($item['name']) : '';
//     $qty = $item['qty_value'];
//     $priceKop = $item['price_kopeks'];
//     $totalKop = $item['total_kopeks'];
//
//     echo "INSERT INTO delivery_items (delivery_id, name, qty, price_kopeks, total_kopeks)\n";
//     echo "VALUES (LAST_INSERT_ID(), '$name', $qty, $priceKop, $totalKop);\n";
//     echo "-- (Количество: " . formatNumber($qty, 3) . ", Цена: " . formatNumber($priceKop / 100, 2) . " ₽, Сумма: " . formatNumber($totalKop / 100, 2) . " ₽)\n\n";
//
//     $count++;
// }
// echo "\n-- Всего товаров для сохранения: $totalItems\n";
// echo "</div>";
// echo "</div>";

echo "<a href='javascript:history.back()' class='back-btn'>← Вернуться назад</a>";

// === Кнопка для реального сохранения в БД ===
echo "<form method='POST' action='test_parsing_save_to_mysql.php' style='margin: 20px 0;'>";
echo "<input type='hidden' name='document_number' value='" . htmlspecialchars($documentNumber, ENT_QUOTES, 'UTF-8') . "'>";
echo "<input type='hidden' name='supplier' value='" . htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8') . "'>";
echo "<input type='hidden' name='delivery_date' value='" . htmlspecialchars($deliveryDate, ENT_QUOTES, 'UTF-8') . "'>";

// Передаем все данные из исходной формы
foreach ($_POST as $key => $value) {
    if (is_array($value)) {
        foreach ($value as $v) {
            echo "<input type='hidden' name='" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "[]' value='" . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . "'>";
        }
    } else {
        if ($key !== 'document_number' && $key !== 'supplier' && $key !== 'delivery_date') {
            echo "<input type='hidden' name='" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "' value='" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "'>";
        }
    }
}

echo "<button type='submit' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; font-weight: bold;'>✅ СОХРАНИТЬ В БАЗУ ДАННЫХ</button>";
echo "</form>";

echo "<div class='info' style='background: #fff3cd; border-color: #ffc107;'>";
echo "⚠️ <strong>Внимание!</strong> После нажатия кнопки данные будут <strong>безвозвратно</strong> сохранены в базу данных MySQL.";
echo "</div>";

?>