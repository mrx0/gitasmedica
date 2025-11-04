<?php

//test_parsing_save_to_mysql.php
//Функция сохранения данных из Excel в БД

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'header.php';

if ($enter_ok) {
    require_once 'header_tags.php';


    if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
        // include_once 'DBWork.php';
        // include_once 'functions.php';

        require_once('configPDO.php');

        echo '
				<header>
                    <div class="nav">
                        <a href="sclad.php" class="b">Склад</a>
                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                        <a href="test_parsing.php" class="b">Сканировать накладную</a>
                    </div>
				</header>';

        echo "<h2>💾 Сохранение данных в базу MySQL</h2>";
        echo "<style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 8px; color: #155724; }
            .error { background: #f8d7da; border: 2px solid #dc3545; padding: 15px; margin: 15px 0; border-radius: 8px; color: #721c24; }
            .info { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 8px; color: #0c5460; }
            .step { background: #e2e3e5; padding: 10px; margin: 10px 0; border-left: 4px solid #6c757d; }
            .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 20px 0; }
            .back-btn:hover { background: #5a6268; }
            
            .step-collapsible {
                background: #e2e3e5;
                margin: 10px 0;
                border-radius: 4px;
                overflow: hidden;
            }
            .step-header {
                padding: 10px;
                cursor: pointer;
                border-left: 4px solid #6c757d;
                display: flex;
                justify-content: space-between;
                align-items: center;
                user-select: none;
            }
            .step-header:hover { background: #d6d8db; }
            .step-content {
                display: none;
                padding: 10px;
                border-top: 1px solid #ccc;
            }
            .step-content.show { display: block; }
            .step-arrow {
                font-weight: bold;
                transition: transform 0.3s;
            }
            .step-arrow.rotated { transform: rotate(90deg); }
            
            .save-btn { background: #4CAF50; color: white; padding: 12px 24px; border: none; 
                border-radius: 4px; cursor: pointer; font-size: 16px; margin: 20px 0; }
            .save-btn:hover { background: #45a049; }
            
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

        // // Собираем все товары из POST
        // $allItems = array();
        // $tableIds = array();
        //
        // foreach ($_POST as $key => $value) {
        //     if (strpos($key, 'rows_') === 0) {
        //         $tableId = str_replace('rows_', '', $key);
        //         if (!in_array($tableId, $tableIds)) {
        //             $tableIds[] = $tableId;
        //         }
        //     }
        // }
        //
        // foreach ($tableIds as $tableId) {
        //     $selectedRows = isset($_POST['rows_' . $tableId]) ? $_POST['rows_' . $tableId] : array();
        //     if (empty($selectedRows)) continue;
        //
        //     $columnTypes = array();
        //     foreach ($_POST as $key => $value) {
        //         if (strpos($key, 'col_type_' . $tableId . '_') === 0) {
        //             $col = str_replace('col_type_' . $tableId . '_', '', $key);
        //             $columnTypes[$col] = $value;
        //         }
        //     }
        //
        //     $activeColumns = array();
        //     foreach ($columnTypes as $col => $type) {
        //         if ($type !== 'ignore') {
        //             $activeColumns[$col] = $type;
        //         }
        //     }
        //
        //     if (empty($activeColumns)) continue;
        //
        //     foreach ($selectedRows as $rowIndex) {
        //         $rowData = array();
        //         foreach ($activeColumns as $col => $type) {
        //             $dataKey = 'data_' . $tableId . '_' . $rowIndex . '_' . $col;
        //             if (isset($_POST[$dataKey])) {
        //                 $rowData[$type] = $_POST[$dataKey];
        //             }
        //         }
        //         if (!empty($rowData['name'])) {
        //             $allItems[] = $rowData;
        //         }
        //     }
        // }
        //
        // if (empty($allItems)) {
        //     die("<div class='error'>Нет данных для сохранения</div>");
        // }
        //
        // // Обрабатываем товары
        // $totalSumKopeks = 0;
        // foreach ($allItems as &$item) {
        //     $qty = isset($item['qty']) ? toFloat($item['qty']) : 0;
        //     $total = isset($item['total']) ? toFloat($item['total']) : 0;
        //     $price = ($qty > 0) ? ($total / $qty) : 0;
        //
        //     $item['qty_value'] = $qty;
        //     $item['price_kopeks'] = round($price * 100);
        //     $item['total_kopeks'] = round($total * 100);
        //
        //     $totalSumKopeks += $item['total_kopeks'];
        // }
        // unset($item);

        // Собираем все товары из POST
        $allItems = array();

// Проверяем новый формат данных (с редактированием)
        if (isset($_POST['item_name']) && is_array($_POST['item_name'])) {
            foreach ($_POST['item_name'] as $idx => $name) {
                $name = trim($name);
                if (empty($name)) continue;

                $qty = isset($_POST['item_qty'][$idx]) ? floatval($_POST['item_qty'][$idx]) : 0;
                $priceKopeks = isset($_POST['item_price_kopeks'][$idx]) ? intval($_POST['item_price_kopeks'][$idx]) : 0;
                $totalKopeks = isset($_POST['item_total_kopeks'][$idx]) ? intval($_POST['item_total_kopeks'][$idx]) : 0;

                $allItems[] = array(
                    'name' => $name,
                    'qty_value' => $qty,
                    'price_kopeks' => $priceKopeks,
                    'total_kopeks' => $totalKopeks
                );
            }
        } else {
            // Старый формат (без редактирования)
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
                        $qty = isset($rowData['qty']) ? toFloat($rowData['qty']) : 0;
                        $total = isset($rowData['total']) ? toFloat($rowData['total']) : 0;
                        $price = ($qty > 0) ? ($total / $qty) : 0;

                        $allItems[] = array(
                            'name' => $rowData['name'],
                            'qty_value' => $qty,
                            'price_kopeks' => round($price * 100),
                            'total_kopeks' => round($total * 100)
                        );
                    }
                }
            }
        }

        if (empty($allItems)) {
            die("<div class='error'>Нет данных для сохранения</div>");
        }

// Пересчитываем общую сумму
        $totalSumKopeks = 0;
        foreach ($allItems as $item) {
            $totalSumKopeks += $item['total_kopeks'];
        }

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
            $mysqli = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);

            if ($mysqli->connect_error) {
                throw new Exception("Ошибка подключения: " . $mysqli->connect_error);
            }

            $mysqli->set_charset("utf8");
            echo "<div class='success'>✅ Подключение к базе данных установлено</div>";

            // Начинаем транзакцию (совместимость с PHP 5.3+)
            $mysqli->autocommit(FALSE);

            // === ШАГ 1: Добавление позиций в spr_sclad_items ===
            // echo "<div class='step'><strong>ШАГ 1:</strong> Добавление/проверка позиций в справочнике spr_sclad_items</div>";
            echo "<div class='step-collapsible'>";
            echo "<div class='step-header' onclick='toggleStep(this)'>";
            echo "<strong>ШАГ 1:</strong> Добавление/проверка позиций в справочнике";
            echo "<span class='step-arrow'>▶</span>";
            echo "</div>";
            echo "<div class='step-content'>";

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

            echo "</div>"; // step-content
            echo "</div>"; // step-collapsible

            echo "<div class='success'>✅ Шаг 1 завершен: обработано " . count($itemIds) . " позиций</div>";

            // === ШАГ 2: Добавление накладной в sclad_prihod ===
            // echo "<div class='step'><strong>ШАГ 2:</strong> Добавление накладной в sclad_prihod</div>";
            echo "<div class='step-collapsible'>";
            echo "<div class='step-header' onclick='toggleStep(this)'>";
            echo "<strong>ШАГ 2:</strong> Добавление накладной";
            echo "<span class='step-arrow'>▶</span>";
            echo "</div>";
            echo "<div class='step-content'>";

            // Форматируем дату из YYYY-MM-DD в DD.MM.YY
            $dateObj = DateTime::createFromFormat('Y-m-d', $deliveryDate);
            $formattedDate = $dateObj->format('d.m.y');
            $provDoc = $documentNumber . " /" . $formattedDate;

            $time = date('Y-m-d H:i:s', time());

            $stmtPrihod = $mysqli->prepare("INSERT INTO sclad_prihod (filial_id, provider_id, provider_name, prov_doc, summ, prihod_time, status, create_time, create_person) VALUES (16, 0, ?, ?, ?, ?, 7, '{$time}', '{$_SESSION['id']}')");
            $stmtPrihod->bind_param("ssis", $supplier, $provDoc, $totalSumKopeks, $deliveryDate);
            $stmtPrihod->execute();
            $prihodId = $mysqli->insert_id;
            $stmtPrihod->close();

            echo "</div>"; // step-content
            echo "</div>"; // step-collapsible

            echo "<div class='success'>✅ Накладная добавлена (ID: $prihodId)<br>";
            echo "Документ: $provDoc<br>";
            echo "Поставщик: $supplier<br>";
            echo "Сумма: " . number_format($totalSumKopeks / 100, 2, ',', ' ') . " ₽</div>";


            // === ШАГ 3: Добавление позиций в sclad_prihod_ex ===
            // echo "<div class='step'><strong>ШАГ 3:</strong> Добавление позиций накладной в sclad_prihod_ex</div>";
            echo "<div class='step-collapsible'>";
            echo "<div class='step-header' onclick='toggleStep(this)'>";
            echo "<strong>ШАГ 3:</strong> Добавление позиций накладной";
            echo "<span class='step-arrow'>▶</span>";
            echo "</div>";
            echo "<div class='step-content'>";

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

            echo "</div>"; // step-content
            echo "</div>"; // step-collapsible

            echo "<div class='success'>✅ Шаг 3 завершен: добавлено " . count($allItems) . " позиций</div>";

            // === ШАГ 4: Обновление остатков в sclad_availability ===
            // echo "<div class='step'><strong>ШАГ 4:</strong> Обновление остатков в sclad_availability</div>";
            echo "<div class='step-collapsible'>";
            echo "<div class='step-header' onclick='toggleStep(this)'>";
            echo "<strong>ШАГ 4:</strong> Обновление остатков";
            echo "<span class='step-arrow'>▶</span>";
            echo "</div>";
            echo "<div class='step-content'>";

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

            echo "</div>"; // step-content
            echo "</div>"; // step-collapsible

            echo "<div class='success'>✅ Шаг 4 завершен: обновлены остатки для " . count($allItems) . " позиций</div>";

            // Подтверждаем транзакцию
            $mysqli->commit();

            echo "<div class='success' style='font-size: 18px; text-align: center;'>";
            echo "<strong>ВСЕ ДАННЫЕ УСПЕШНО СОХРАНЕНЫ! НАКЛАДНАЯ ПРОВЕДЕНА!</strong><br><br>";
            echo "Накладная №<strong>$documentNumber</strong><br>";
            echo "ID в базе: <strong>$prihodId</strong><br>";
            echo "Позиций: <strong>" . count($allItems) . "</strong><br>";
            echo "Общая сумма: <strong>" . number_format($totalSumKopeks / 100, 2, ',', ' ') . " ₽</strong>";
            echo "</div>";

            echo "<a href='sclad_prihod.php?id=$prihodId' class='save-btn' style='text-decoration: none; display: inline-block;'>📋 Перейти к накладной</a>";

            // Формируем список ID позиций для анализа
            $itemIdsForAnalysis = array();
            foreach ($allItems as $item) {
                $itemIdsForAnalysis[] = $item['sclad_item_id'];
            }
            $itemIdsString = implode(',', $itemIdsForAnalysis);

            echo "<a href='test_price_analysis.php?prihod_id=$prihodId&item_ids=$itemIdsString' class='save-btn' style='text-decoration: none; display: inline-block; background: #17a2b8; margin-left: 10px;'>📊 Провести анализ цен</a>";

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

        // echo "<a href='javascript:history.back()' class='back-btn'>← Вернуться назад</a>";

        echo "
        <script>
            function toggleStep(header) {
                var content = header.nextElementSibling;
                var arrow = header.querySelector('.step-arrow');
                
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