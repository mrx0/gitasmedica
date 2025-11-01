<?php

//test_parsing_excel_upload_upload.php
//Тестовая функция для парсинга

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('PHPExcel/Classes/PHPExcel.php');

// === 1) Приём файла ===
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (!isset($_FILES['excel_file'])) die('Нет файла.');
$file = $_FILES['excel_file'];
if ($file['error'] !== UPLOAD_ERR_OK) die('Ошибка загрузки файла.');

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, array('xls','xlsx'))) die('Неверный формат файла.');

$targetPath = $uploadDir . time() . '_' . basename($file['name']);
if (!move_uploaded_file($file['tmp_name'], $targetPath)) die('Не удалось сохранить файл.');

echo "<h2>Результаты парсинга Excel:</h2>";
echo "<style>
    table.excel-table { border-collapse: collapse; margin: 20px 0; }
    table.excel-table th { background: #4CAF50; color: white; padding: 8px; font-weight: bold; }
    table.excel-table td { padding: 6px 8px; border: 1px solid #ddd; }
    table.excel-table tr:nth-child(even) { background: #f9f9f9; }
    .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196F3; }
</style>";

try {
    // === 2) Чтение Excel с подавлением Notice ===
    $oldErrorLevel = error_reporting();
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    $excel = PHPExcel_IOFactory::load($targetPath);

    error_reporting($oldErrorLevel);

    // === Вспомогательные функции ===
    function norm_s($s) {
        $s = trim((string)$s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return mb_strtolower($s, 'UTF-8');
    }

    function is_money($s) {
        $s = trim((string)$s);
        if ($s === '' || $s === '-') return false;
        $s = str_replace("\xC2\xA0", ' ', $s);
        return (bool)preg_match('/^\d{1,3}(?:[ ]\d{3})*(?:[.,]\d{2})?$|^\d+(?:[.,]\d+)?$/u', $s);
    }

    function is_number($s) {
        $s = trim((string)$s);
        if ($s === '' || $s === '-') return false;
        $s = str_replace(array(' ', "\xC2\xA0", ','), array('', '', '.'), $s);
        return is_numeric($s);
    }

    function has_text_content($s) {
        $s = trim((string)$s);
        if ($s === '' || $s === '-') return false;
        // Должны быть буквы
        return preg_match('/[а-яА-ЯёЁa-zA-Z]/u', $s);
    }

    function is_row_number($s) {
        $s = trim((string)$s);
        return preg_match('/^\d+[а-яА-Яa-zA-Z]?$/u', $s);
    }

    function col_to_num($col) {
        $col = strtoupper($col);
        $len = strlen($col);
        $num = 0;
        for ($i=0; $i<$len; $i++) {
            $num = $num * 26 + (ord($col[$i]) - 64);
        }
        return $num;
    }

    // === 3) Обработка каждого листа ===
    foreach ($excel->getAllSheets() as $sheet) {
        $sheetTitle = htmlspecialchars($sheet->getTitle(), ENT_QUOTES, 'UTF-8');
        echo "<h3>Лист: $sheetTitle</h3>";

        $data = $sheet->toArray(null, true, true, true);
        if (!$data || count($data) < 3) {
            echo "<div class='info'>Пустой лист или слишком мало данных.</div>";
            continue;
        }

        // === 4) Поиск всех таблиц на листе ===
        $tables = array();
        $inTable = false;
        $currentTable = null;

        for ($rowIdx = 1; $rowIdx <= count($data); $rowIdx++) {
            if (!isset($data[$rowIdx])) continue;
            $row = $data[$rowIdx];

            // Подсчёт непустых ячеек с содержимым
            $filledCells = 0;
            $textCells = 0;
            $numberCells = 0;

            foreach ($row as $col => $val) {
                $v = trim((string)$val);
                if ($v !== '' && $v !== '-') {
                    $filledCells++;
                    if (has_text_content($v)) $textCells++;
                    if (is_number($v)) $numberCells++;
                }
            }

            // Эвристика: строка таблицы должна иметь минимум 3 заполненных ячейки
            $isTableRow = ($filledCells >= 3);

            // Проверка на строки-разделители ("Итого", "Всего")
            $joined = norm_s(implode(' ', $row));
            $isStopRow = (strpos($joined, 'итого') !== false ||
                strpos($joined, 'всего') !== false ||
                strpos($joined, 'документ составлен') !== false);

            if ($isTableRow && !$inTable) {
                // Начало новой таблицы
                $inTable = true;
                $currentTable = array(
                    'start_row' => $rowIdx,
                    'header_row' => $rowIdx,
                    'rows' => array(),
                    'columns' => array()
                );

                // Запоминаем заголовки
                foreach ($row as $col => $val) {
                    $v = trim((string)$val);
                    if ($v !== '' && $v !== '-') {
                        $currentTable['columns'][$col] = $v;
                    }
                }
            }

            if ($inTable) {
                if ($isStopRow && $filledCells < 5) {
                    // Конец таблицы
                    $currentTable['end_row'] = $rowIdx - 1;
                    $tables[] = $currentTable;
                    $inTable = false;
                    $currentTable = null;
                } elseif (!$isTableRow && $filledCells < 2) {
                    // Пустая строка - возможно конец таблицы
                    if ($currentTable && count($currentTable['rows']) > 0) {
                        $currentTable['end_row'] = $rowIdx - 1;
                        $tables[] = $currentTable;
                        $inTable = false;
                        $currentTable = null;
                    }
                } else {
                    // Добавляем строку в текущую таблицу
                    $currentTable['rows'][] = array(
                        'row_idx' => $rowIdx,
                        'data' => $row
                    );
                }
            }
        }

        // Если таблица не закрылась - закрываем в конце
        if ($inTable && $currentTable && count($currentTable['rows']) > 0) {
            $currentTable['end_row'] = count($data);
            $tables[] = $currentTable;
        }

        // === 5) Вывод найденных таблиц ===
        if (empty($tables)) {
            echo "<div class='info'>Таблицы не обнаружены на этом листе.</div>";
            continue;
        }

        echo "<div class='info'>Найдено таблиц: " . count($tables) . "</div>";

        foreach ($tables as $tIdx => $table) {
            $tableNum = $tIdx + 1;
            echo "<h4>Таблица $tableNum (строки {$table['start_row']}-{$table['end_row']})</h4>";

            // Определяем непустые колонки
            $nonEmptyColumns = array();
            foreach ($table['columns'] as $col => $header) {
                $hasData = false;
                foreach ($table['rows'] as $r) {
                    $val = isset($r['data'][$col]) ? trim((string)$r['data'][$col]) : '';
                    if ($val !== '' && $val !== '-') {
                        $hasData = true;
                        break;
                    }
                }
                if ($hasData) {
                    $nonEmptyColumns[$col] = $header;
                }
            }

            if (empty($nonEmptyColumns)) {
                echo "<p>Нет данных в колонках.</p>";
                continue;
            }

            // Фильтруем строки с данными (убираем служебные и порядковые номера)
            $dataRows = array();
            foreach ($table['rows'] as $r) {
                $row = $r['data'];
                $rowIdx = $r['row_idx'];

                // Пропускаем строку заголовка
                if ($rowIdx == $table['header_row']) continue;

                // Проверяем, есть ли значимые данные
                $hasSignificantData = false;
                $firstColValue = '';

                foreach ($nonEmptyColumns as $col => $header) {
                    $val = isset($row[$col]) ? trim((string)$row[$col]) : '';
                    if ($val !== '' && $val !== '-') {
                        if ($firstColValue === '') {
                            $firstColValue = $val;
                        }
                        if (!is_row_number($val)) {
                            $hasSignificantData = true;
                        }
                    }
                }

                // Пропускаем строки где первая ячейка - просто номер
                if (is_row_number($firstColValue) && !$hasSignificantData) {
                    continue;
                }

                if ($hasSignificantData) {
                    $dataRows[] = $row;
                }
            }

            if (empty($dataRows)) {
                echo "<p>Нет строк с данными.</p>";
                continue;
            }

            echo "<p>Строк данных: " . count($dataRows) . "</p>";
            echo "<table class='excel-table'>";

            // Заголовки
            echo "<tr><th>№</th>";
            foreach ($nonEmptyColumns as $col => $header) {
                echo "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>";
            }
            echo "</tr>";

            // Данные
            $num = 1;
            foreach ($dataRows as $row) {
                echo "<tr>";
                echo "<td>" . $num++ . "</td>";
                foreach ($nonEmptyColumns as $col => $header) {
                    $value = isset($row[$col]) ? trim((string)$row[$col]) : '';
                    $displayValue = ($value === '-' || $value === '') ? '' : $value;
                    echo "<td>" . htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Ошибка при чтении Excel: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>