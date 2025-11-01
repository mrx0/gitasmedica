<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// const DB_HOST = 'localhost'; // 127.0.0.1
// const DB_USER = 'root';
// const DB_PASSWORD = 'gfhjkm84286252';
// const DB_NAME = 'asmed1';
// const CHARSET = 'utf8';
// const DB_PREFIX = '';

// Настройки подключения к БД
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'gfhjkm84286252';
$DB_NAME = 'asmed1';

echo "<h2>💾 Сохранение данных в базу MySQL</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 8px; color: #155724; }
    .error { background: #f8d7da; border: 2px solid #dc3545; padding: 15px; margin: 15px 0; border-radius: 8px; color: #721c24; }
    .info { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 8px; color: #0c5460; }
    .step { background: #e2e3e5; padding: 10px; margin: 10px 0; border-left: 4px solid #6c757d; }
    .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 20px 0; }
    .back-btn:hover { background: #5a6268; }
</style>";

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<div class='error'>Ошибка: неверный метод запроса</div>");
}

// === 1) Получение данных из формы ===
$documentNumber = isset($_POST['document_number']) ? trim($_POST['document_number']) : '';
$supplier = isset($_POST['supplier']) ? trim($_POST['supplier']) : '';
$deliveryDate = isset($_POST['delivery_date']) ? trim($_POST['delivery_date']) : '';

if (empty($documentNumber) || empty($supplier) || empty($deliveryDate)) {
    die("<div class='error'>Ошибка: не заполнены обязательные поля</div>");
}

// Функция для конвертации в число
function toFloat($value) {
    if (empty($value) || $value === '-') return 0;
    $value = str_replace(array(' ', "\xC2\xA0"), '', $value);
    $value = str_replace(',', '.', $value);
    return floatval($value);
}

// Собираем все товары из POST
$allItems = array();
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
    $selectedRows = isset($_POST['rows_' . $tableId]) ? $_POST['rows_' . $tableId] : array();
    if (empty($selectedRows)) continue;

    $columnTypes = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'col_type_' . $tableId . '_') === 0) {
            $col = str_replace('col_type_' . $tableId . '_', '', $key);
            $columnTypes[$col] = $value;
        }
    }

    $activeColumns = array();
    foreach ($columnTypes as $col => $type) {
        if ($type !== 'ignore') {
            $activeColumns[$col] = $type;
        }
    }

    if (empty($activeColumns)) continue;

    foreach ($selectedRows as $rowIndex) {
        $rowData = array();
        foreach ($activeColumns as $col => $type) {
            $dataKey = 'data_' . $tableId . '_' . $rowIndex . '_' . $col;
            if (isset($_POST[$dataKey])) {
                $rowData[$type] = $_POST[$dataKey];
            }
        }
        if (!empty($rowData['name'])) {
            $allItems[] = $rowData;
        }
    }
}

if (empty($allItems)) {
    die("<div class='error'>Нет данных для сохранения</div>");
}

// Обрабатываем товары
$totalSumKopeks = 0;
foreach ($allItems as &$item) {
    $qty = isset($item['qty']) ? toFloat($item['qty']) : 0;
    $total = isset($item['total']) ? toFloat($item['total']) : 0;
    $price = ($qty > 0) ? ($total / $qty) : 0;

    $item['qty_value'] = $qty;
    $item['price_kopeks'] = round($price * 100);
    $item['total_kopeks'] = round($total * 100);

    $totalSumKopeks += $item['total_kopeks'];
}
unset($item);

echo "<div class='info'>";
echo "<strong>📦 Данные для сохранения:</strong><br>";
echo "Номер документа: <strong>$documentNumber</strong><br>";
echo "Поставщик: <strong>$supplier</strong><br>";
echo "Дата поставки: <strong>$deliveryDate</strong><br>";
echo "Позиций: <strong>" . count($allItems) . "</strong><br>";
echo "Общая сумма: <strong>" . number_format($totalSumKopeks / 100, 2, ',', ' ') . " ₽</strong>";
echo "</div>";

// === 2) Подключение к БД ===
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    if ($mysqli->connect_error) {
        throw new Exception("Ошибка подключения: " . $mysqli->connect_error);
    }

    $mysqli->set_charset("utf8");
    echo "<div class='success'>✅ Подключение к базе данных установлено</div>";

    // Начинаем транзакцию (совместимость с PHP 5.3+)
    $mysqli->autocommit(FALSE);

    // === ШАГ 1: Добавление позиций в spr_sclad_items ===
    echo "<div class='step'><strong>ШАГ 1:</strong> Добавление/проверка позиций в справочнике spr_sclad_items</div>";

    $itemIds = array();
    $stmtCheck = $mysqli->prepare("SELECT id FROM spr_sclad_items WHERE name = ?");
    $stmtInsert = $mysqli->prepare("INSERT INTO spr_sclad_items (name, unit, parent_id, status) VALUES (?, 'pc', 0, 1)");

    foreach ($allItems as $index => &$item) {
        $name = $item['name'];

        // Проверяем, есть ли уже такое название
        $stmtCheck->bind_param("s", $name);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($row = $result->fetch_assoc()) {
            // Позиция уже существует
            $itemId = $row['id'];
            echo "<div style='padding: 5px; margin: 5px 0; background: #fff3cd;'>➡️ Найдена: \"" . htmlspecialchars(mb_substr($name, 0, 60), ENT_QUOTES, 'UTF-8') . "...\" (ID: $itemId)</div>";
        } else {
            // Добавляем новую позицию
            $stmtInsert->bind_param("s", $name);
            $stmtInsert->execute();
            $itemId = $mysqli->insert_id;
            echo "<div style='padding: 5px; margin: 5px 0; background: #d4edda;'>✅ Добавлена: \"" . htmlspecialchars(mb_substr($name, 0, 60), ENT_QUOTES, 'UTF-8') . "...\" (ID: $itemId)</div>";
        }

        $item['sclad_item_id'] = $itemId;
        $itemIds[$index] = $itemId;
    }
    unset($item);

    $stmtCheck->close();
    $stmtInsert->close();

    echo "<div class='success'>✅ Шаг 1 завершен: обработано " . count($itemIds) . " позиций</div>";

    // === ШАГ 2: Добавление накладной в sclad_prihod ===
    echo "<div class='step'><strong>ШАГ 2:</strong> Добавление накладной в sclad_prihod</div>";

    // Форматируем дату из YYYY-MM-DD в DD.MM.YY
    $dateObj = DateTime::createFromFormat('Y-m-d', $deliveryDate);
    $formattedDate = $dateObj->format('d.m.y');
    $provDoc = $documentNumber . " /" . $formattedDate;

    $stmtPrihod = $mysqli->prepare("INSERT INTO sclad_prihod (filial_id, provider_id, provider_name, prov_doc, summ, prihod_time, status) VALUES (16, 0, ?, ?, ?, ?, 7)");
    $stmtPrihod->bind_param("ssis", $supplier, $provDoc, $totalSumKopeks, $deliveryDate);
    $stmtPrihod->execute();
    $prihodId = $mysqli->insert_id;
    $stmtPrihod->close();

    echo "<div class='success'>✅ Накладная добавлена (ID: $prihodId)<br>";
    echo "Документ: $provDoc<br>";
    echo "Поставщик: $supplier<br>";
    echo "Сумма: " . number_format($totalSumKopeks / 100, 2, ',', ' ') . " ₽</div>";

    // === ШАГ 3: Добавление позиций в sclad_prihod_ex ===
    echo "<div class='step'><strong>ШАГ 3:</strong> Добавление позиций накладной в sclad_prihod_ex</div>";

    $stmtPrihodEx = $mysqli->prepare("INSERT INTO sclad_prihod_ex (prihod_id, ind, sclad_item_id, quantity, price) VALUES (?, 0, ?, ?, ?)");

    foreach ($allItems as $item) {
        $scladItemId = $item['sclad_item_id'];
        $quantity = $item['qty_value'];
        $price = $item['price_kopeks'];

        $stmtPrihodEx->bind_param("iiii", $prihodId, $scladItemId, $quantity, $price);
        $stmtPrihodEx->execute();

        echo "<div style='padding: 5px; margin: 5px 0; background: #e7f3ff;'>➕ Позиция ID $scladItemId: кол-во $quantity, цена " . number_format($price / 100, 2, ',', ' ') . " ₽</div>";
    }

    $stmtPrihodEx->close();
    echo "<div class='success'>✅ Шаг 3 завершен: добавлено " . count($allItems) . " позиций</div>";

    // === ШАГ 4: Обновление остатков в sclad_availability ===
    echo "<div class='step'><strong>ШАГ 4:</strong> Обновление остатков в sclad_availability</div>";

    $stmtCheckAvail = $mysqli->prepare("SELECT quantity FROM sclad_availability WHERE filial_id = 16 AND sclad_item_id = ?");
    $stmtUpdateAvail = $mysqli->prepare("UPDATE sclad_availability SET quantity = quantity + ? WHERE filial_id = 16 AND sclad_item_id = ?");
    $stmtInsertAvail = $mysqli->prepare("INSERT INTO sclad_availability (filial_id, sclad_item_id, quantity) VALUES (16, ?, ?)");

    foreach ($allItems as $item) {
        $scladItemId = $item['sclad_item_id'];
        $quantity = $item['qty_value'];

        // Проверяем, есть ли уже запись
        $stmtCheckAvail->bind_param("i", $scladItemId);
        $stmtCheckAvail->execute();
        $result = $stmtCheckAvail->get_result();

        if ($row = $result->fetch_assoc()) {
            // Обновляем существующий остаток
            $oldQty = $row['quantity'];
            $stmtUpdateAvail->bind_param("ii", $quantity, $scladItemId);
            $stmtUpdateAvail->execute();
            $newQty = $oldQty + $quantity;
            echo "<div style='padding: 5px; margin: 5px 0; background: #fff3cd;'>📊 ID $scladItemId: было $oldQty → стало $newQty</div>";
        } else {
            // Добавляем новую запись
            $stmtInsertAvail->bind_param("ii", $scladItemId, $quantity);
            $stmtInsertAvail->execute();
            echo "<div style='padding: 5px; margin: 5px 0; background: #d4edda;'>✅ ID $scladItemId: добавлен остаток $quantity</div>";
        }
    }

    $stmtCheckAvail->close();
    $stmtUpdateAvail->close();
    $stmtInsertAvail->close();

    echo "<div class='success'>✅ Шаг 4 завершен: обновлены остатки для " . count($allItems) . " позиций</div>";

    // Подтверждаем транзакцию
    $mysqli->commit();

    echo "<div class='success' style='font-size: 18px; text-align: center;'>";
    echo "🎉 <strong>ВСЕ ДАННЫЕ УСПЕШНО СОХРАНЕНЫ!</strong><br><br>";
    echo "Накладная №<strong>$documentNumber</strong><br>";
    echo "ID в базе: <strong>$prihodId</strong><br>";
    echo "Позиций: <strong>" . count($allItems) . "</strong><br>";
    echo "Общая сумма: <strong>" . number_format($totalSumKopeks / 100, 2, ',', ' ') . " ₽</strong>";
    echo "</div>";

    $mysqli->close();

} catch (Exception $e) {
    // Откатываем транзакцию при ошибке
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->close();
    }

    echo "<div class='error'>";
    echo "❌ <strong>ОШИБКА ПРИ СОХРАНЕНИИ:</strong><br>";
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<br><br>Все изменения отменены (rollback)";
    echo "</div>";
}

echo "<a href='javascript:history.back()' class='back-btn'>← Вернуться назад</a>";
?>