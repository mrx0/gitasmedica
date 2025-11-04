<?php

//test_parsing_save_to_db.php
//Функция предварительного просмотра данных для сохранения

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'header.php';

if ($enter_ok) {
    require_once 'header_tags.php';


    if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
        require_once('configPDO.php');

        echo '
				<header>
                    <div class="nav">
                        <a href="sclad.php" class="b">Склад</a>
                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                        <a href="test_parsing.php" class="b">Сканировать накладную</a>
                    </div>
				</header>';

        echo "<h2>🔍 Предварительный просмотр данных для сохранения в БД</h2>";
        echo "<style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .preview-section { background: #f8f9fa; border: 2px solid #dee2e6; padding: 15px; margin: 15px 0; border-radius: 8px; }
            .preview-section h3 { margin-top: 0; color: #495057; }
            .data-table { border-collapse: collapse; width: 100%; margin: 10px 0; }
            .data-table th { background: #007bff; color: white; padding: 10px; text-align: left; }
            .data-table td { border: 1px solid #dee2e6; padding: 8px; position: relative; }
            .data-table tr:nth-child(even) { background: #f8f9fa; }
            .data-table tr.new-item { background: #f8d7da !important; }
            .data-table tr.new-item td { background: #f8d7da; }
            .supplier-box { background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 8px; }
            .supplier-box strong { color: #155724; }
            .json-preview { background: #f4f4f4; border: 1px solid #ccc; padding: 15px; overflow-x: auto; white-space: pre-wrap; font-family: monospace; font-size: 12px; }
            .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 20px 0; }
            .back-btn:hover { background: #5a6268; }
            .stats { background: #cfe2ff; border-left: 4px solid #0d6efd; padding: 10px; margin: 10px 0; }
            
            .collapsible-section { margin: 15px 0; }
            .collapsible-header { 
                background: #6c757d; 
                color: white; 
                padding: 10px 15px; 
                cursor: pointer; 
                border-radius: 4px;
                user-select: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .collapsible-header:hover { background: #5a6268; }
            .collapsible-content { 
                margin-top: 10px; 
                display: none;
            }
            .collapsible-content.show { display: block; }
            .toggle-arrow { font-weight: bold; transition: transform 0.3s; }
            .toggle-arrow.rotated { transform: rotate(90deg); }
            
            .editable-name {
                width: 100% !important;
                padding: 5px 8px !important;
                border: none !important;
                background: transparent !important;
                font-size: 14px !important;
                box-sizing: border-box !important;
                cursor: text !important;
                font-family: inherit !important;
                outline: none !important;
                box-shadow: none !important;
            }
            .editable-name:hover {
                background: rgba(0, 123, 255, 0.05) !important;
            }
            .editable-name:focus {
                background: white !important;
                outline: 2px solid #007bff !important;
                border-radius: 3px !important;
                box-shadow: none !important;
            }
            .new-item .editable-name {
                background: rgba(220, 53, 69, 0.1) !important;
                font-weight: 500 !important;
            }
            .new-item .editable-name:hover {
                background: rgba(220, 53, 69, 0.15) !important;
            }
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

        // === Подключение к БД для проверки позиций ===
        try {
            $mysqli = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
            if ($mysqli->connect_error) {
                throw new Exception("Ошибка подключения к БД");
            }
            $mysqli->set_charset("utf8");
        } catch (Exception $e) {
            die("<p style='color:red;'>Ошибка подключения к базе данных</p>");
        }

        // === 2) Обработка данных таблиц - собираем в ОДИН массив ===
        $allItems = array();
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

        // Функции
        function toFloat($value) {
            if (empty($value) || $value === '-') return 0;
            $value = str_replace(array(' ', "\xC2\xA0"), '', $value);
            $value = str_replace(',', '.', $value);
            return floatval($value);
        }

        function formatNumber($value, $decimals = 2) {
            if ($value == 0) return '0';
            $formatted = number_format($value, $decimals, '.', '');
            $formatted = rtrim($formatted, '0');
            $formatted = rtrim($formatted, '.');
            $formatted = str_replace('.', ',', $formatted);
            return $formatted;
        }

        // Обрабатываем все товары
        foreach ($allItems as $index => &$item) {
            $qty = isset($item['qty']) ? toFloat($item['qty']) : 0;
            $total = isset($item['total']) ? toFloat($item['total']) : 0;
            $price = ($qty > 0) ? ($total / $qty) : 0;

            $item['price_display'] = formatNumber($price, 2);
            $item['qty_display'] = formatNumber($qty, 3);
            $item['total_display'] = formatNumber($total, 2);
            $item['price_kopeks'] = round($price * 100);
            $item['total_kopeks'] = round($total * 100);
            $item['qty_value'] = $qty;
            $item['index'] = $index;

            // Проверяем существование в БД
            $stmt = $mysqli->prepare("SELECT id FROM spr_sclad_items WHERE name = ? LIMIT 1");
            $name = $item['name'];
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            $item['exists'] = ($result->num_rows > 0);
            $stmt->close();
        }
        unset($item);

        $totalItems = count($allItems);

        // === 4) ЕДИНЫЙ массив всех товаров - ОДНА ТАБЛИЦА ===
        echo "<div class='preview-section'>";
        echo "<h3>📦 ОБЩАЯ НАКЛАДНАЯ - ВСЕ ТОВАРЫ <span class='new-item-badge'>Розовые строки - новые позиции</span></h3>";
        echo "<div class='stats'>📊 Обработано таблиц из файла: $totalTables | ✅ Всего позиций: $totalItems</div>";

        if (!empty($allItems)) {
            $totalQty = 0;
            $totalSum = 0;
            foreach ($allItems as $item) {
                $totalQty += $item['qty_value'];
                $totalSum += toFloat($item['total_display']);
            }

            // echo "<form method='POST' action='test_parsing_save_to_mysql.php' id='finalForm'>";
            // echo "<input type='hidden' name='document_number' value='" . htmlspecialchars($documentNumber, ENT_QUOTES, 'UTF-8') . "'>";
            // echo "<input type='hidden' name='supplier' value='" . htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8') . "'>";
            // echo "<input type='hidden' name='delivery_date' value='" . htmlspecialchars($deliveryDate, ENT_QUOTES, 'UTF-8') . "'>";
            //
            // // Передаем все данные из исходной формы
            // foreach ($_POST as $key => $value) {
            //     if (is_array($value)) {
            //         foreach ($value as $v) {
            //             echo "<input type='hidden' name='" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "[]' value='" . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . "'>";
            //         }
            //     } else {
            //         if ($key !== 'document_number' && $key !== 'supplier' && $key !== 'delivery_date') {
            //             echo "<input type='hidden' name='" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "' value='" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "'>";
            //         }
            //     }
            // }
            //
            // echo "<table class='data-table' id='itemsTable'>";
            // echo "<thead><tr>";
            // echo "<th>№</th><th>Наименование</th><th>Количество</th><th>Цена за ед. (₽)</th><th>Сумма (₽)</th>";
            // echo "</tr></thead><tbody>";
            //
            // $num = 1;
            // foreach ($allItems as $item) {
            //     $rowClass = $item['exists'] ? '' : 'new-item';
            //     $inputClass = $item['exists'] ? 'exists' : '';
            //     $idx = $item['index'];
            //
            //     echo "<tr class='$rowClass' data-index='$idx'>";
            //     echo "<td>" . $num++ . "</td>";
            //     echo "<td>";
            //     echo "<input type='text' class='editable-name $inputClass' ";
            //     echo "data-index='$idx' ";
            //     echo "data-original='" . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . "' ";
            //     echo "name='item_name_$idx' ";
            //     echo "value='" . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . "' ";
            //     echo "autocomplete='off' ";
            //     echo "onkeyup='checkItem(this)' ";
            //     echo "onfocus='this.select()'>";
            //     echo "<div class='autocomplete-suggestions' id='suggestions_$idx'></div>";
            //     echo "</td>";
            //     echo "<td>" . htmlspecialchars($item['qty_display'], ENT_QUOTES, 'UTF-8') . "</td>";
            //     echo "<td>" . htmlspecialchars($item['price_display'], ENT_QUOTES, 'UTF-8') . "</td>";
            //     echo "<td>" . htmlspecialchars($item['total_display'], ENT_QUOTES, 'UTF-8') . "</td>";
            //     echo "</tr>";
            // }
            //
            // // Итоговая строка
            // echo "<tr style='background: #fff3cd; font-weight: bold; border-top: 3px solid #333;'>";
            // echo "<td colspan='2' style='text-align: right;'>ИТОГО:</td>";
            // echo "<td>" . formatNumber($totalQty, 3) . "</td>";
            // echo "<td></td>";
            // echo "<td>" . formatNumber($totalSum, 2) . "</td>";
            // echo "</tr>";
            //
            // echo "</tbody></table>";

            echo "<form method='POST' action='test_parsing_save_to_mysql.php' id='finalForm'>";
            echo "<input type='hidden' name='document_number' value='" . htmlspecialchars($documentNumber, ENT_QUOTES, 'UTF-8') . "'>";
            echo "<input type='hidden' name='supplier' value='" . htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8') . "'>";
            echo "<input type='hidden' name='delivery_date' value='" . htmlspecialchars($deliveryDate, ENT_QUOTES, 'UTF-8') . "'>";

            // Передаем количество позиций
            echo "<input type='hidden' name='items_count' value='" . count($allItems) . "'>";

            echo "<table class='data-table' id='itemsTable'>";
            echo "<thead><tr>";
            echo "<th>№</th><th>Наименование</th><th>Количество</th><th>Цена за ед. (₽)</th><th>Сумма (₽)</th>";
            echo "</tr></thead><tbody>";

            $num = 1;
            foreach ($allItems as $item) {
                $rowClass = $item['exists'] ? '' : 'new-item';
                $idx = $item['index'];

                echo "<tr class='$rowClass' data-index='$idx'>";
                echo "<td>" . $num++ . "</td>";
                echo "<td>";
                echo "<input type='text' class='editable-name' ";
                echo "data-index='$idx' ";
                echo "name='item_name[$idx]' ";
                echo "value='" . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . "' ";
                echo "autocomplete='off' ";
                echo "onkeyup='checkItem(this)' ";
                echo "onclick='this.select()'>";

                // Скрытые поля с данными позиции
                echo "<input type='hidden' name='item_qty[$idx]' value='" . $item['qty_value'] . "'>";
                echo "<input type='hidden' name='item_price_kopeks[$idx]' value='" . $item['price_kopeks'] . "'>";
                echo "<input type='hidden' name='item_total_kopeks[$idx]' value='" . $item['total_kopeks'] . "'>";

                echo "<div class='autocomplete-suggestions' id='suggestions_$idx'></div>";
                echo "</td>";
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

            echo "<button type='submit' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; font-weight: bold;'>✅ СОХРАНИТЬ В БАЗУ ДАННЫХ</button>";
            echo "</form>";
        } else {
            echo "<p>Нет данных для отображения</p>";
        }
        echo "</div>";

        $mysqli->close();

        // JavaScript для автодополнения
        echo "<script>
        let typingTimer;
        const doneTypingInterval = 300;

        function checkItem(input) {
            clearTimeout(typingTimer);
            const value = input.value.trim();
            const index = input.getAttribute('data-index');
            const suggestionsDiv = document.getElementById('suggestions_' + index);
            
            if (value.length < 2) {
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'none';
                return;
            }
            
            typingTimer = setTimeout(function() {
                searchItems(value, index, input);
            }, doneTypingInterval);
        }

        function searchItems(query, index, inputElement) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_search_sclad_items_php_f.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const suggestionsDiv = document.getElementById('suggestions_' + index);
                    
                    if (response.length > 0) {
                        let html = '';
                        response.forEach(function(item) {
                            html += '<div class=\"autocomplete-suggestion\" onclick=\"selectItem(' + index + ', \\'' + escapeHtml(item.name) + '\\')\"><strong>' + item.name + '</strong></div>';
                        });
                        suggestionsDiv.innerHTML = html;
                        suggestionsDiv.style.display = 'block';
                        
                        // Проверяем точное совпадение
                        const exactMatch = response.some(item => item.name.toLowerCase() === query.toLowerCase());
                        if (exactMatch) {
                            inputElement.classList.remove('exists');
                            inputElement.classList.add('exists');
                            inputElement.parentElement.parentElement.classList.remove('new-item');
                        } else {
                            inputElement.classList.remove('exists');
                            inputElement.parentElement.parentElement.classList.add('new-item');
                        }
                    } else {
                        suggestionsDiv.innerHTML = '';
                        suggestionsDiv.style.display = 'none';
                        inputElement.classList.remove('exists');
                        inputElement.parentElement.parentElement.classList.add('new-item');
                    }
                }
            };
            
            xhr.send('query=' + encodeURIComponent(query));
        }

        function selectItem(index, name) {
            const input = document.querySelector('input[data-index=\"' + index + '\"]');
            input.value = name;
            input.classList.add('exists');
            input.parentElement.parentElement.classList.remove('new-item');
            
            const suggestionsDiv = document.getElementById('suggestions_' + index);
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.style.display = 'none';
        }

        function escapeHtml(text) {
            return text.replace(/'/g, \"\\\\'\");
        }

        // Закрытие автодополнения при клике вне
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('editable-name')) {
                document.querySelectorAll('.autocomplete-suggestions').forEach(function(div) {
                    div.style.display = 'none';
                });
            }
        });
        </script>";

        // === 5) JSON для отладки ===
        echo "<div class='collapsible-section'>";
        echo "<div class='collapsible-header' onclick='toggleCollapsible(this)'>";
        echo "<span>🔧 Структура данных для БД (JSON)</span>";
        echo "<span class='toggle-arrow'>▶</span>";
        echo "</div>";
        echo "<div class='collapsible-content'>";
        echo "<div class='preview-section'>";
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
                'name' => $item['name'],
                'qty' => formatNumber($item['qty_value'], 3),
                'price_kopeks' => $item['price_kopeks'],
                'total_kopeks' => $item['total_kopeks'],
                'exists_in_db' => $item['exists']
            );
        }

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo "<script>
        function toggleCollapsible(header) {
            var content = header.nextElementSibling;
            var arrow = header.querySelector('.toggle-arrow');
            
            if (content.classList.contains('show')) {
                content.classList.remove('show');
                arrow.classList.remove('rotated');
            } else {
                content.classList.add('show');
                arrow.classList.add('rotated');
            }
        }
        </script>";

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>