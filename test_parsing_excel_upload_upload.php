<?php

//test_parsing_excel_upload_upload.php
//Функция для парсинга

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'header.php';


if ($enter_ok) {
    require_once 'header_tags.php';

    require_once('PHPExcel/Classes/PHPExcel.php');

    if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
        // include_once 'DBWork.php';
        // include_once 'functions.php';
        //
        // $filials_j = getAllFilials(false, false, false);
        // $permissions = SelDataFromDB('spr_permissions', '', '');

        // === 1) Приём файла ===
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if (!isset($_FILES['excel_file'])) die('Нет файла.');
        $file = $_FILES['excel_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) die('Ошибка загрузки файла.');

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, array('xls', 'xlsx'))) die('Неверный формат файла.');

        $targetPath = $uploadDir . time() . '_' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) die('Не удалось сохранить файл.');

        echo '
				<header>
                    <div class="nav">
                        <a href="sclad.php" class="b">Склад</a>
                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                        <a href="test_parsing.php" class="b">Сканировать накладную</a>
                    </div>
				</header>';

        echo "<h2>Результаты разбора накладной Excel:</h2>";

        echo "<style>
            body { font-family: Arial, sans-serif; }
            table.excel-table { border-collapse: collapse; margin: 20px 0; width: 100%; }
            table.excel-table th { background: #4CAF50; color: white; padding: 8px; font-weight: bold; position: relative; }
            table.excel-table td { padding: 6px 8px; border: 1px solid #ddd; }
            table.excel-table tr:nth-child(even) { background: #f9f9f9; }
            .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196F3; }
            .column-marker { margin: 5px 0; }
            .column-type-select { 
                width: 100%; 
                padding: 5px; 
                border: 2px solid #ddd; 
                border-radius: 4px; 
                font-size: 12px;
                cursor: pointer;
                background: white;
            }
            .column-type-select:hover { border-color: #4CAF50; }
            .marked-ignore { background: #fff !important; }
            .marked-name { background: #fff3cd !important; }
            .marked-qty { background: #d4edda !important; }
            .marked-total { background: #d6d8db !important; }
            .save-btn { background: #4CAF50; color: white; padding: 12px 24px; border: none; 
                        border-radius: 4px; cursor: pointer; font-size: 16px; margin: 20px 0; }
            .save-btn:hover { background: #45a049; }
            th.marked-ignore { background: #6c757d !important; }
            th.marked-name { background: #ffc107 !important; }
            th.marked-qty { background: #28a745 !important; }
            th.marked-total { background: #343a40 !important; }
            
            .table-container { border: 2px solid #ddd; margin: 20px 0; padding: 10px; border-radius: 8px; 
                               transition: background-color 0.3s, border-color 0.3s; }
            .table-container.collapsed { background-color: #e9ecef; border-color: #adb5bd; }
            .table-header { background: #f8f9fa; padding: 10px; cursor: pointer; user-select: none; 
                             border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
            .table-header:hover { background: #e9ecef; }
            .table-content { margin-top: 10px; }
            .table-content.hidden { display: none; }
            .toggle-icon { font-weight: bold; font-size: 18px; }
            
            .supplier-info { background: #fff; border: 2px solid #4CAF50; padding: 15px; margin: 20px 0; border-radius: 8px; }
            .supplier-info label { display: block; margin: 10px 0 5px 0; font-weight: bold; }
            .supplier-info input { width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; 
                                   border-radius: 4px; font-size: 14px; }
            .row-checkbox { width: 18px; height: 18px; cursor: pointer; }
            .select-all-row { cursor: pointer; width: 18px; height: 18px; }
            
            .table-header.collapsed { background: #adb5bd !important; }
        </style>";

        echo "<div class='info'>
            <strong>Инструкция:</strong> 
            1. Заполните данные о накладной: номер, поставщик и дату<br>
            2. Отметьте чекбоксами строки товаров, которые нужно сохранить<br>
            3. Выберите типы для колонок в выпадающих списках<br>
            4. Нажмите кнопку сохранения<br><br>
            <span style='background: #6c757d; padding: 2px 8px; color: white;'>■</span> Игнорировать &nbsp;
            <span style='background: #ffc107; padding: 2px 8px;'>■</span> Наименование &nbsp;
            <span style='background: #28a745; padding: 2px 8px;'>■</span> Количество &nbsp;
            <span style='background: #343a40; padding: 2px 8px; color: white;'>■</span> Стоимость
        </div>";

        echo "<form method='POST' action='test_parsing_save_to_db.php' id='excelForm'>";

        echo "<div class='supplier-info'>
            <h3>📋 Информация о накладной</h3>
            <label for='document_number'>Номер документа:</label>
            <input type='text' id='document_number' name='document_number' placeholder='Введите номер накладной' required>
            
            <label for='supplier'>Поставщик:</label>
            <input type='text' id='supplier' name='supplier' placeholder='Введите название поставщика' required>
            
            <label for='delivery_date'>Дата поставки:</label>
            <input type='date' id='delivery_date' name='delivery_date' value='" . date('Y-m-d') . "' required>
        </div>";

        try {
            // === 2) Чтение Excel с подавлением Notice ===
            $oldErrorLevel = error_reporting();
            error_reporting(E_ERROR | E_WARNING | E_PARSE);

            $excel = PHPExcel_IOFactory::load($targetPath);

            error_reporting($oldErrorLevel);

            // === Вспомогательные функции ===
            function norm_s($s)
            {
                $s = trim((string)$s);
                $s = preg_replace('/\s+/u', ' ', $s);
                return mb_strtolower($s, 'UTF-8');
            }

            function is_money($s)
            {
                $s = trim((string)$s);
                if ($s === '' || $s === '-') return false;
                $s = str_replace("\xC2\xA0", ' ', $s);
                return (bool)preg_match('/^\d{1,3}(?:[ ]\d{3})*(?:[.,]\d{2})?$|^\d+(?:[.,]\d+)?$/u', $s);
            }

            function is_number($s)
            {
                $s = trim((string)$s);
                if ($s === '' || $s === '-') return false;
                $s = str_replace(array(' ', "\xC2\xA0", ','), array('', '', '.'), $s);
                return is_numeric($s);
            }

            function has_text_content($s)
            {
                $s = trim((string)$s);
                if ($s === '' || $s === '-') return false;
                return preg_match('/[а-яА-ЯёЁa-zA-Z]/u', $s);
            }

            function is_row_number($s)
            {
                $s = trim((string)$s);
                return preg_match('/^\d+[а-яА-Яa-zA-Z]?$/u', $s);
            }

            function col_to_num($col)
            {
                $col = strtoupper($col);
                $len = strlen($col);
                $num = 0;
                for ($i = 0; $i < $len; $i++) {
                    $num = $num * 26 + (ord($col[$i]) - 64);
                }
                return $num;
            }

            // === 3) Обработка каждого листа ===
            $globalTableIndex = 0;

            foreach ($excel->getAllSheets() as $sheet) {
                $sheetTitle = htmlspecialchars($sheet->getTitle(), ENT_QUOTES, 'UTF-8');

                $data = $sheet->toArray(null, true, true, true);
                if (!$data || count($data) < 3) {
                    continue;
                }

                // === 4) Поиск всех таблиц на листе ===
                $tables = array();
                $inTable = false;
                $currentTable = null;

                for ($rowIdx = 1; $rowIdx <= count($data); $rowIdx++) {
                    if (!isset($data[$rowIdx])) continue;
                    $row = $data[$rowIdx];

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

                    $isTableRow = ($filledCells >= 3);

                    $joined = norm_s(implode(' ', $row));
                    $isStopRow = (strpos($joined, 'итого') !== false ||
                        strpos($joined, 'всего по накладной') !== false ||
                        strpos($joined, 'всего к оплате') !== false ||
                        strpos($joined, 'всего отпущено') !== false ||
                        strpos($joined, 'документ составлен') !== false ||
                        strpos($joined, 'главный бухгалтер') !== false ||
                        strpos($joined, 'руководитель') !== false);

                    if ($isTableRow && !$inTable) {
                        $inTable = true;
                        $currentTable = array(
                            'start_row' => $rowIdx,
                            'header_row' => $rowIdx,
                            'rows' => array(),
                            'columns' => array()
                        );

                        foreach ($row as $col => $val) {
                            $v = trim((string)$val);
                            if ($v !== '' && $v !== '-') {
                                $currentTable['columns'][$col] = $v;
                            }
                        }
                    }

                    if ($inTable) {
                        if ($isStopRow) {
                            $currentTable['end_row'] = $rowIdx - 1;
                            if (count($currentTable['rows']) > 0) {
                                $tables[] = $currentTable;
                            }
                            $inTable = false;
                            $currentTable = null;
                        } elseif (!$isTableRow && $filledCells < 2) {
                            if ($currentTable && count($currentTable['rows']) > 0) {
                                $currentTable['end_row'] = $rowIdx - 1;
                                $tables[] = $currentTable;
                                $inTable = false;
                                $currentTable = null;
                            }
                        } else {
                            $currentTable['rows'][] = array(
                                'row_idx' => $rowIdx,
                                'data' => $row
                            );
                        }
                    }
                }

                if ($inTable && $currentTable && count($currentTable['rows']) > 0) {
                    $currentTable['end_row'] = count($data);
                    $tables[] = $currentTable;
                }

                // === 5) Вывод найденных таблиц ===
                if (empty($tables)) {
                    continue;
                }

                foreach ($tables as $tIdx => $table) {
                    $globalTableIndex++;
                    $tableId = "table_" . $globalTableIndex;
                    $tableNum = $globalTableIndex;

                    echo "<div class='table-container' id='container_$tableId'>";
                    echo "<div class='table-header' onclick=\"toggleTable('$tableId')\">";
                    // echo "<span class='toggle-icon' id='icon_$tableId'>▼</span>";
                    echo "<span><span class='toggle-icon' id='icon_$tableId'>▼</span>  <strong>Таблица $tableNum</strong> (Лист: $sheetTitle, строки {$table['start_row']}-{$table['end_row']})</span>";
                    echo "</div>";

                    echo "<div class='table-content' id='content_$tableId'>";

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
                        echo "</div></div>";
                        continue;
                    }

                    // Фильтруем строки с данными
                    $dataRows = array();
                    foreach ($table['rows'] as $r) {
                        $row = $r['data'];
                        $rowIdx = $r['row_idx'];

                        if ($rowIdx == $table['header_row']) continue;

                        $joined = norm_s(implode(' ', $row));
                        if (strpos($joined, 'итого') !== false ||
                            strpos($joined, 'всего') !== false) {
                            break;
                        }

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

                        if (is_row_number($firstColValue) && !$hasSignificantData) {
                            continue;
                        }

                        if ($hasSignificantData) {
                            $dataRows[] = $row;
                        }
                    }

                    if (empty($dataRows)) {
                        echo "<p>Нет строк с данными.</p>";
                        echo "</div></div>";
                        continue;
                    }

                    echo "<p>Строк данных: " . count($dataRows) . "</p>";

                    echo "<table class='excel-table' data-table-id='$tableId'>";

                    // Заголовки
                    echo "<tr>";
                    echo "<th><input type='checkbox' class='select-all-row' onchange='toggleAllRows(\"$tableId\")' checked></th>";
                    echo "<th>№</th>";
                    foreach ($nonEmptyColumns as $col => $header) {
                        $colId = $tableId . "_" . $col;
                        echo "<th class='marked-ignore' id='th_$colId'>";
                        echo htmlspecialchars($header, ENT_QUOTES, 'UTF-8');
                        echo "<div class='column-marker'>";
                        echo "<select name='col_type_$colId' class='column-type-select' data-table='$tableId' data-col='$colId' onchange=\"markColumn('$colId', this.value, '$tableId')\">";
                        echo "<option value='ignore' selected>— Игнорировать —</option>";
                        echo "<option value='name'>📦 Наименование</option>";
                        echo "<option value='qty'>🔢 Количество</option>";
                        echo "<option value='total'>💵 Стоимость</option>";
                        echo "</select>";
                        echo "</div>";
                        echo "</th>";
                    }
                    echo "</tr>";

                    // Данные
                    $num = 1;
                    foreach ($dataRows as $rowIndex => $row) {
                        $rowId = $tableId . "_row_" . $rowIndex;
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='row-checkbox' name='rows_" . $tableId . "[]' value='$rowIndex' data-table='$tableId' checked></td>";
                        echo "<td>" . $num++ . "</td>";
                        foreach ($nonEmptyColumns as $col => $header) {
                            $colId = $tableId . "_" . $col;
                            $value = isset($row[$col]) ? trim((string)$row[$col]) : '';

                            // Нормализуем числа: убираем пробелы и неразрывные пробелы, заменяем запятую/точку
                            $normalizedValue = $value;
                            if ($value !== '' && $value !== '-') {
                                // Убираем все пробелы (обычные и неразрывные)
                                $cleanValue = str_replace(array(' ', "\xC2\xA0"), '', $value);

                                // Проверяем, является ли это числом
                                // Если есть точка или запятая - это число
                                if (preg_match('/^[\d\s\.,]+$/', $cleanValue)) {
                                    // Определяем десятичный разделитель
                                    $hasComma = strpos($cleanValue, ',') !== false;
                                    $hasDot = strpos($cleanValue, '.') !== false;

                                    if ($hasComma && $hasDot) {
                                        // Оба разделителя: определяем какой последний (это десятичный)
                                        $lastComma = strrpos($cleanValue, ',');
                                        $lastDot = strrpos($cleanValue, '.');
                                        if ($lastDot > $lastComma) {
                                            // Точка - десятичный, запятая - тысячный
                                            $cleanValue = str_replace(',', '', $cleanValue);
                                            $cleanValue = str_replace('.', ',', $cleanValue);
                                        } else {
                                            // Запятая - десятичный, точка - тысячный
                                            $cleanValue = str_replace('.', '', $cleanValue);
                                        }
                                    } elseif ($hasDot && !$hasComma) {
                                        // Только точка - заменяем на запятую
                                        $cleanValue = str_replace('.', ',', $cleanValue);
                                    }

                                    // Убираем незначащие нули после запятой
                                    if (strpos($cleanValue, ',') !== false) {
                                        $cleanValue = rtrim($cleanValue, '0');
                                        $cleanValue = rtrim($cleanValue, ',');
                                    }

                                    $normalizedValue = $cleanValue;
                                }
                            }

                            $displayValue = ($normalizedValue === '-' || $normalizedValue === '') ? '' : $normalizedValue;
                            echo "<td class='marked-ignore col-$colId'>";
                            echo "<input type='hidden' name='data_{$tableId}_{$rowIndex}_{$col}' value='" . htmlspecialchars($normalizedValue, ENT_QUOTES, 'UTF-8') . "'>";
                            echo htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8');
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";

                    echo "</div>"; // table-content
                    echo "</div>"; // table-container
                }
            }

            echo "<button type='submit' class='save-btn'>💾 Проверить и сохранить</button>";
            echo "</form>";

            echo "<script>
            var usedTypes = {};
            
            function toggleTable(tableId) {
                var container = document.getElementById('container_' + tableId);
                var content = document.getElementById('content_' + tableId);
                var icon = document.getElementById('icon_' + tableId);
                var header = container.querySelector('.table-header');
                
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    container.classList.remove('collapsed');
                    header.classList.remove('collapsed');
                    icon.textContent = '▼';
                } else {
                    content.classList.add('hidden');
                    container.classList.add('collapsed');
                    header.classList.add('collapsed');
                    icon.textContent = '▶';
                }
            }
            
            function toggleAllRows(tableId) {
                var checkboxes = document.querySelectorAll('input[name=\"rows_' + tableId + '[]\"]');
                var mainCheckbox = event.target;
                var selectAll = mainCheckbox.checked;
                checkboxes.forEach(function(cb) {
                    cb.checked = selectAll;
                });
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                var selects = document.querySelectorAll('.column-type-select');
                selects.forEach(function(select) {
                    var tableId = select.getAttribute('data-table');
                    if (!usedTypes[tableId]) {
                        usedTypes[tableId] = {};
                    }
                    var value = select.value;
                    var colId = select.getAttribute('data-col');
                    if (value !== 'ignore') {
                        usedTypes[tableId][value] = colId;
                    }
                });
                updateAllSelects();
            });
            
            function markColumn(colId, type, tableId) {
                if (usedTypes[tableId]) {
                    for (var key in usedTypes[tableId]) {
                        if (usedTypes[tableId][key] === colId) {
                            delete usedTypes[tableId][key];
                        }
                    }
                }
                
                if (type !== 'ignore') {
                    if (!usedTypes[tableId]) {
                        usedTypes[tableId] = {};
                    }
                    usedTypes[tableId][type] = colId;
                }
                
                var th = document.getElementById('th_' + colId);
                th.className = th.className.replace(/marked-\\w+/g, '');
                th.classList.add('marked-' + type);
                
                var cells = document.querySelectorAll('.col-' + colId);
                cells.forEach(function(cell) {
                    cell.className = cell.className.replace(/marked-\\w+/g, '');
                    cell.classList.add('marked-' + type);
                    cell.classList.add('col-' + colId);
                });
                
                updateAllSelects();
            }
            
            function updateAllSelects() {
                var selects = document.querySelectorAll('.column-type-select');
                selects.forEach(function(select) {
                    var tableId = select.getAttribute('data-table');
                    var currentColId = select.getAttribute('data-col');
                    var currentValue = select.value;
                    
                    var options = select.querySelectorAll('option');
                    options.forEach(function(option) {
                        var optionValue = option.value;
                        
                        if (optionValue === 'ignore') {
                            option.disabled = false;
                            return;
                        }
                        
                        if (usedTypes[tableId] && usedTypes[tableId][optionValue]) {
                            if (usedTypes[tableId][optionValue] !== currentColId) {
                                option.disabled = true;
                                option.textContent = option.textContent.replace(' ✓', '').replace(' (занято)', '') + ' (занято)';
                            } else {
                                option.disabled = false;
                                option.textContent = option.textContent.replace(' (занято)', '').replace(' ✓', '') + ' ✓';
                            }
                        } else {
                            option.disabled = false;
                            option.textContent = option.textContent.replace(' ✓', '').replace(' (занято)', '');
                        }
                    });
                });
            }
            </script>";

        } catch (Exception $e) {
            echo "<p style='color:red;'>Ошибка при чтении Excel: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        }
    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';



?>