<?php

//test_price_analysis.php

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

        echo "<h2>📊 Полный анализ изменения цен</h2>";
        echo "<style>
            .analysis-section { background: #f8f9fa; border: 2px solid #dee2e6; padding: 15px; margin: 15px 0; border-radius: 8px; }
            .analysis-table { border-collapse: collapse; width: 100%; margin: 10px 0; }
            .analysis-table th { background: #007bff; color: white; padding: 10px; text-align: left; position: sticky; top: 0; z-index: 10; }
            .analysis-table td { border: 1px solid #dee2e6; padding: 8px; }
            .analysis-table tr:nth-child(even) { background: #f8f9fa; }
            .price-up { color: #dc3545; font-weight: bold; }
            .price-down { color: #28a745; font-weight: bold; }
            .price-same { color: #6c757d; }
            .info-box { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 8px; }
            
            .comparison-toggle {
                display: inline-block;
                padding: 8px 15px;
                margin: 5px;
                background: #007bff;
                color: white;
                border-radius: 4px;
                cursor: pointer;
                user-select: none;
            }
            .comparison-toggle:hover {
                background: #0056b3;
            }
            .comparison-toggle.active {
                background: #28a745;
            }
            
            .mini-chart {
                width: 200px;
                height: 50px;
                position: relative;
            }
            .chart-line {
                fill: none;
                stroke: #007bff;
                stroke-width: 2;
            }
            .chart-point {
                fill: #007bff;
            }
            .chart-point:hover {
                fill: #dc3545;
                r: 5;
            }
            
            .history-toggle {
                cursor: pointer;
                color: #007bff;
                text-decoration: underline;
            }
            .history-details {
                display: none;
                margin-top: 10px;
                padding: 10px;
                background: #fff;
                border-left: 3px solid #007bff;
            }
            .history-item {
                padding: 5px;
                border-bottom: 1px solid #eee;
            }
            .history-item:last-child {
                border-bottom: none;
            }
            
            .stats-box {
                display: inline-block;
                padding: 10px 15px;
                margin: 5px;
                border-radius: 5px;
                background: white;
                border: 1px solid #dee2e6;
            }
            .stats-label {
                font-size: 11px;
                color: #6c757d;
                text-transform: uppercase;
            }
            .stats-value {
                font-size: 18px;
                font-weight: bold;
                color: #495057;
            }
            
            .comparison-row {
                background: #fff3cd !important;
            }
            .comparison-cell {
                font-weight: bold;
            }
        </style>";

        $prihodId = isset($_GET['prihod_id']) ? intval($_GET['prihod_id']) : 0;
        $itemIdsString = isset($_GET['item_ids']) ? $_GET['item_ids'] : '';

        if (empty($itemIdsString)) {
            die("<p style='color:red;'>Ошибка: не указаны позиции для анализа</p>");
        }

        $itemIds = array_map('intval', explode(',', $itemIdsString));

        try {
            $mysqli = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
            $mysqli->set_charset("utf8");

            // Получаем информацию о текущей накладной (если указана)
            if ($prihodId > 0) {
                $stmt = $mysqli->prepare("SELECT provider_name, prov_doc, prihod_time, summ FROM sclad_prihod WHERE id = ?");
                $stmt->bind_param("i", $prihodId);
                $stmt->execute();
                $result = $stmt->get_result();
                $currentPrihod = $result->fetch_assoc();
                $stmt->close();

                echo "<div class='info-box'>";
                echo "<h3>Текущая накладная</h3>";
                echo "<p><strong>Поставщик:</strong> " . htmlspecialchars($currentPrihod['provider_name']) . "</p>";
                echo "<p><strong>Документ:</strong> " . htmlspecialchars($currentPrihod['prov_doc']) . "</p>";
                echo "<p><strong>Дата:</strong> " . date('d.m.Y', strtotime($currentPrihod['prihod_time'])) . "</p>";
                echo "<p><strong>Сумма:</strong> " . number_format($currentPrihod['summ'] / 100, 2, ',', ' ') . " ₽</p>";
                echo "</div>";
            }

            echo "<div class='analysis-section'>";
            echo "<h3>Режим сравнения</h3>";
            echo "<div style='margin: 15px 0;'>";
            echo "<span class='comparison-toggle' onclick='switchComparison(\"all\")' id='toggle_all'>📊 Вся история (первая → последняя)</span>";
            echo "<span class='comparison-toggle active' onclick='switchComparison(\"last2\")' id='toggle_last2'>🔄 Два последних прихода</span>";
            echo "</div>";
            echo "</div>";

            echo "<div class='analysis-section'>";
            echo "<h3>Анализ по позициям (" . count($itemIds) . ")</h3>";

            // Общая статистика
            $stats = array(
                'all' => array('increases' => 0, 'decreases' => 0, 'same' => 0, 'new' => 0),
                'last2' => array('increases' => 0, 'decreases' => 0, 'same' => 0, 'new' => 0)
            );

            // Собираем данные по всем позициям
            $analysisData = array();

            foreach ($itemIds as $itemId) {
                // Получаем название позиции
                $stmt = $mysqli->prepare("SELECT name FROM spr_sclad_items WHERE id = ?");
                $stmt->bind_param("i", $itemId);
                $stmt->execute();
                $result = $stmt->get_result();
                $itemInfo = $result->fetch_assoc();
                $stmt->close();

                if (!$itemInfo) continue;

                // Получаем всю историю цен для этой позиции
                $stmt = $mysqli->prepare("
                    SELECT ex.price, ex.quantity, p.provider_name, p.prihod_time, p.id as prihod_id, p.prov_doc
                    FROM sclad_prihod_ex ex
                    JOIN sclad_prihod p ON ex.prihod_id = p.id
                    WHERE ex.sclad_item_id = ? AND p.status = 7
                    ORDER BY p.prihod_time ASC, p.id ASC
                ");
                $stmt->bind_param("i", $itemId);
                $stmt->execute();
                $result = $stmt->get_result();

                $priceHistory = array();
                while ($row = $result->fetch_assoc()) {
                    $priceHistory[] = $row;
                }
                $stmt->close();

                if (empty($priceHistory)) {
                    $stats['all']['new']++;
                    $stats['last2']['new']++;
                    continue;
                }

                // Анализ по всей истории
                $firstPrice = $priceHistory[0]['price'];
                $lastPrice = $priceHistory[count($priceHistory) - 1]['price'];

                $prices = array();
                foreach ($priceHistory as $h) {
                    $prices[] = $h['price'];
                }
                $minPrice = min($prices);
                $maxPrice = max($prices);
                $avgPrice = array_sum($prices) / count($prices);

                $priceChangeAll = $lastPrice - $firstPrice;
                $priceChangePercentAll = $firstPrice > 0 ? (($priceChangeAll / $firstPrice) * 100) : 0;

                if ($priceChangeAll > 0) $stats['all']['increases']++;
                elseif ($priceChangeAll < 0) $stats['all']['decreases']++;
                else $stats['all']['same']++;

                // Анализ двух последних приходов
                $last2Data = null;
                if (count($priceHistory) >= 2) {
                    $prevPrice = $priceHistory[count($priceHistory) - 2]['price'];
                    $prevDate = $priceHistory[count($priceHistory) - 2]['prihod_time'];
                    $prevSupplier = $priceHistory[count($priceHistory) - 2]['provider_name'];
                    $prevPrihodId = $priceHistory[count($priceHistory) - 2]['prihod_id'];
                    $prevDoc = $priceHistory[count($priceHistory) - 2]['prov_doc'];

                    $lastDate = $priceHistory[count($priceHistory) - 1]['prihod_time'];
                    $lastSupplier = $priceHistory[count($priceHistory) - 1]['provider_name'];
                    $lastPrihodId = $priceHistory[count($priceHistory) - 1]['prihod_id'];
                    $lastDoc = $priceHistory[count($priceHistory) - 1]['prov_doc'];

                    $priceChangeLast2 = $lastPrice - $prevPrice;
                    $priceChangePercentLast2 = $prevPrice > 0 ? (($priceChangeLast2 / $prevPrice) * 100) : 0;

                    if ($priceChangeLast2 > 0) $stats['last2']['increases']++;
                    elseif ($priceChangeLast2 < 0) $stats['last2']['decreases']++;
                    else $stats['last2']['same']++;

                    $last2Data = array(
                        'prev_price' => $prevPrice,
                        'prev_date' => $prevDate,
                        'prev_supplier' => $prevSupplier,
                        'prev_prihod_id' => $prevPrihodId,
                        'prev_doc' => $prevDoc,
                        'last_date' => $lastDate,
                        'last_supplier' => $lastSupplier,
                        'last_prihod_id' => $lastPrihodId,
                        'last_doc' => $lastDoc,
                        'change' => $priceChangeLast2,
                        'change_percent' => $priceChangePercentLast2
                    );
                } elseif (count($priceHistory) == 1) {
                    $stats['last2']['new']++;
                }

                $analysisData[] = array(
                    'item_id' => $itemId,
                    'name' => $itemInfo['name'],
                    'history' => $priceHistory,
                    'first_price' => $firstPrice,
                    'last_price' => $lastPrice,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'avg_price' => $avgPrice,
                    'change_all' => $priceChangeAll,
                    'change_percent_all' => $priceChangePercentAll,
                    'last2' => $last2Data,
                    'entries_count' => count($priceHistory)
                );
            }

            // Выводим статистику (по умолчанию для последних 2)
            echo "<div style='margin: 20px 0;' id='stats_container'>";

            // Статистика для всей истории
            echo "<div id='stats_all' style='display: none;'>";
            echo "<h4>Статистика: вся история</h4>";
            echo "<div class='stats-box' style='border-color: #dc3545;'>";
            echo "<div class='stats-label'>Подорожало</div>";
            echo "<div class='stats-value' style='color: #dc3545;'>" . $stats['all']['increases'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #28a745;'>";
            echo "<div class='stats-label'>Подешевело</div>";
            echo "<div class='stats-value' style='color: #28a745;'>" . $stats['all']['decreases'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #ffc107;'>";
            echo "<div class='stats-label'>Без изменений</div>";
            echo "<div class='stats-value' style='color: #ffc107;'>" . $stats['all']['same'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #17a2b8;'>";
            echo "<div class='stats-label'>Новые</div>";
            echo "<div class='stats-value' style='color: #17a2b8;'>" . $stats['all']['new'] . "</div>";
            echo "</div>";
            echo "</div>";

            // Статистика для двух последних (по умолчанию показана)
            echo "<div id='stats_last2'>";
            echo "<h4>Статистика: два последних прихода</h4>";
            echo "<div class='stats-box' style='border-color: #dc3545;'>";
            echo "<div class='stats-label'>Подорожало</div>";
            echo "<div class='stats-value' style='color: #dc3545;'>" . $stats['last2']['increases'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #28a745;'>";
            echo "<div class='stats-label'>Подешевело</div>";
            echo "<div class='stats-value' style='color: #28a745;'>" . $stats['last2']['decreases'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #ffc107;'>";
            echo "<div class='stats-label'>Без изменений</div>";
            echo "<div class='stats-value' style='color: #ffc107;'>" . $stats['last2']['same'] . "</div>";
            echo "</div>";
            echo "<div class='stats-box' style='border-color: #17a2b8;'>";
            echo "<div class='stats-label'>Недостаточно данных</div>";
            echo "<div class='stats-value' style='color: #17a2b8;'>" . $stats['last2']['new'] . "</div>";
            echo "</div>";
            echo "</div>";

            echo "</div>";

            // Таблица с анализом
            echo "<div style='overflow-x: auto;'>";
            echo "<table class='analysis-table'>";
            echo "<thead><tr>";
            echo "<th style='width: 30%;'>Наименование</th>";
            echo "<th style='width: 15%;'>График</th>";

            // Заголовки для всей истории
            echo "<th class='col-all' style='display: none;'>Первая цена</th>";
            echo "<th class='col-all' style='display: none;'>Последняя цена</th>";
            echo "<th class='col-all' style='display: none;'>Изменение</th>";
            echo "<th class='col-all' style='display: none;'>Мин/Макс</th>";

            // Заголовки для двух последних
            echo "<th class='col-last2'>Предпоследний приход</th>";
            echo "<th class='col-last2'>Последний приход</th>";
            echo "<th class='col-last2'>Изменение</th>";

            echo "<th>История</th>";
            echo "</tr></thead><tbody>";

            foreach ($analysisData as $idx => $data) {
                // Определяем класс для всей истории
                $changeClassAll = '';
                $changeIconAll = '';
                if ($data['change_all'] > 0) {
                    $changeClassAll = 'price-up';
                    $changeIconAll = '▲';
                } elseif ($data['change_all'] < 0) {
                    $changeClassAll = 'price-down';
                    $changeIconAll = '▼';
                } else {
                    $changeClassAll = 'price-same';
                    $changeIconAll = '=';
                }

                // Определяем класс для двух последних
                $changeClassLast2 = '';
                $changeIconLast2 = '';
                if ($data['last2']) {
                    if ($data['last2']['change'] > 0) {
                        $changeClassLast2 = 'price-up';
                        $changeIconLast2 = '▲';
                    } elseif ($data['last2']['change'] < 0) {
                        $changeClassLast2 = 'price-down';
                        $changeIconLast2 = '▼';
                    } else {
                        $changeClassLast2 = 'price-same';
                        $changeIconLast2 = '=';
                    }
                }

                echo "<tr>";
                echo "<td><a href='sclad_item.php?id={$data['item_id']}' target='_blank'>" . htmlspecialchars($data['name']) . "</a></td>";

                // Мини-график (всегда показываем всю историю)
                echo "<td>";
                if (count($data['history']) > 1) {
                    $chartId = "chart_" . $idx;
                    echo "<svg class='mini-chart' id='$chartId'></svg>";

                    $chartData = array();
                    foreach ($data['history'] as $h) {
                        $chartData[] = array(
                            'date' => date('d.m.y', strtotime($h['prihod_time'])),
                            'price' => $h['price'] / 100,
                            'supplier' => $h['provider_name']
                        );
                    }

                    echo "<script>
                    (function() {
                        var data = " . json_encode($chartData) . ";
                        var svg = document.getElementById('$chartId');
                        var width = 200;
                        var height = 50;
                        var padding = 5;
                        
                        var prices = data.map(d => d.price);
                        var minPrice = Math.min(...prices);
                        var maxPrice = Math.max(...prices);
                        var priceRange = maxPrice - minPrice || 1;
                        
                        var points = [];
                        data.forEach((d, i) => {
                            var x = padding + (i / (data.length - 1)) * (width - 2 * padding);
                            var y = height - padding - ((d.price - minPrice) / priceRange) * (height - 2 * padding);
                            points.push({x: x, y: y, data: d});
                        });
                        
                        var pathD = 'M' + points.map(p => p.x + ',' + p.y).join(' L');
                        var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', pathD);
                        path.setAttribute('class', 'chart-line');
                        svg.appendChild(path);
                        
                        points.forEach(p => {
                            var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                            circle.setAttribute('cx', p.x);
                            circle.setAttribute('cy', p.y);
                            circle.setAttribute('r', 3);
                            circle.setAttribute('class', 'chart-point');
                            circle.innerHTML = '<title>' + p.data.date + ': ' + p.data.price + ' ₽\\n' + p.data.supplier + '</title>';
                            svg.appendChild(circle);
                        });
                    })();
                    </script>";
                } else {
                    echo "<span style='color: #6c757d;'>—</span>";
                }
                echo "</td>";

                // Колонки для всей истории (скрыты по умолчанию)
                echo "<td class='col-all' style='display: none;'>" . number_format($data['first_price'] / 100, 2, ',', ' ') . " ₽</td>";
                echo "<td class='col-all' style='display: none;'>" . number_format($data['last_price'] / 100, 2, ',', ' ') . " ₽</td>";
                echo "<td class='col-all $changeClassAll' style='display: none;'>";
                echo $changeIconAll . " " . number_format(abs($data['change_all']) / 100, 2, ',', ' ') . " ₽";
                if ($data['change_all'] != 0) {
                    echo " (" . number_format(abs($data['change_percent_all']), 1, ',', '') . "%)";
                }
                echo "</td>";
                echo "<td class='col-all' style='font-size: 11px; display: none;'>";
                echo "↓ " . number_format($data['min_price'] / 100, 2, ',', ' ') . " ₽<br>";
                echo "↑ " . number_format($data['max_price'] / 100, 2, ',', ' ') . " ₽";
                echo "</td>";

                // Колонки для двух последних (показаны по умолчанию)
                if ($data['last2']) {
                    echo "<td class='col-last2'>";
                    echo number_format($data['last2']['prev_price'] / 100, 2, ',', ' ') . " ₽<br>";
                    echo "<small style='color: #6c757d;'>" . date('d.m.Y', strtotime($data['last2']['prev_date'])) . "</small><br>";
                    echo "<small>" . htmlspecialchars($data['last2']['prev_supplier']) . "</small>";
                    echo " <a href='sclad_prihod.php?id={$data['last2']['prev_prihod_id']}' target='_blank'>→</a>";
                    echo "</td>";

                    echo "<td class='col-last2'>";
                    echo number_format($data['last_price'] / 100, 2, ',', ' ') . " ₽<br>";
                    echo "<small style='color: #6c757d;'>" . date('d.m.Y', strtotime($data['last2']['last_date'])) . "</small><br>";
                    echo "<small>" . htmlspecialchars($data['last2']['last_supplier']) . "</small>";
                    echo " <a href='sclad_prihod.php?id={$data['last2']['last_prihod_id']}' target='_blank'>→</a>";
                    echo "</td>";

                    echo "<td class='col-last2 $changeClassLast2 comparison-cell'>";
                    echo $changeIconLast2 . " " . number_format(abs($data['last2']['change']) / 100, 2, ',', ' ') . " ₽";
                    if ($data['last2']['change'] != 0) {
                        echo "<br>(" . number_format(abs($data['last2']['change_percent']), 1, ',', '') . "%)";
                    }
                    echo "</td>";
                } else {
                    echo "<td class='col-last2' colspan='3' style='text-align: center; color: #6c757d;'>";
                    echo "Недостаточно данных (менее 2 приходов)";
                    echo "</td>";
                }

                // История
                echo "<td>";
                echo "<span class='history-toggle' onclick='toggleHistory($idx)'>📋 " . $data['entries_count'] . " записей</span>";
                echo "<div class='history-details' id='history_$idx'>";

                foreach (array_reverse($data['history']) as $h) {
                    echo "<div class='history-item'>";
                    echo "<strong>" . date('d.m.Y', strtotime($h['prihod_time'])) . "</strong> ";
                    echo "— " . number_format($h['price'] / 100, 2, ',', ' ') . " ₽ ";
                    echo "(" . $h['quantity'] . " шт) ";
                    echo "<br><small style='color: #6c757d;'>" . htmlspecialchars($h['provider_name']) . "</small>";
                    echo " <a href='sclad_prihod.php?id={$h['prihod_id']}' target='_blank' style='font-size: 11px;'>→</a>";
                    echo "</div>";
                }

                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
            echo "</div>";
            echo "</div>";

            if ($prihodId > 0) {
                echo "<a href='sclad_prihod.php?id=$prihodId' class='b' style='background: #6c757d; text-decoration: none; display: inline-block; padding: 10px 20px; margin: 10px 5px;'>← Вернуться к накладной</a>";
            }
            echo "<a href='sclad.php' class='b' style='background: #28a745; text-decoration: none; display: inline-block; padding: 10px 20px; margin: 10px 5px;'>← На склад</a>";

            $mysqli->close();

        } catch (Exception $e) {
            echo "<p style='color:red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        echo "
        <script>
        var currentMode = 'last2';
        
        function switchComparison(mode) {
            currentMode = mode;
            
            // Обновляем кнопки
            document.getElementById('toggle_all').classList.remove('active');
            document.getElementById('toggle_last2').classList.remove('active');
            document.getElementById('toggle_' + mode).classList.add('active');
            
            // Переключаем статистику
            document.getElementById('stats_all').style.display = (mode === 'all') ? 'block' : 'none';
            document.getElementById('stats_last2').style.display = (mode === 'last2') ? 'block' : 'none';
            
            // Переключаем колонки в таблице
            var colsAll = document.querySelectorAll('.col-all');
            var colsLast2 = document.querySelectorAll('.col-last2');
            
            colsAll.forEach(function(el) {
                el.style.display = (mode === 'all') ? 'table-cell' : 'none';
            });
            
            colsLast2.forEach(function(el) {
                el.style.display = (mode === 'last2') ? 'table-cell' : 'none';
            });
        }
        
        function toggleHistory(index) {
            var element = document.getElementById('history_' + index);
            if (element.style.display === 'none' || element.style.display === '') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }
        </script>";

    } else {
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
} else {
    header("location: enter.php");
}

require_once 'footer.php';
?>