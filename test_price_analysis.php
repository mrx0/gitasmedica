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
            .analysis-table th { background: #007bff; color: white; padding: 10px; text-align: left; position: sticky; top: 0; }
            .analysis-table td { border: 1px solid #dee2e6; padding: 8px; }
            .analysis-table tr:nth-child(even) { background: #f8f9fa; }
            .price-up { color: #dc3545; font-weight: bold; }
            .price-down { color: #28a745; font-weight: bold; }
            .price-same { color: #6c757d; }
            .info-box { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 8px; }
            
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
            echo "<h3>Анализ по позициям (" . count($itemIds) . ")</h3>";

            // Общая статистика
            $totalIncreases = 0;
            $totalDecreases = 0;
            $totalSame = 0;
            $totalNew = 0;

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
                    SELECT ex.price, ex.quantity, p.provider_name, p.prihod_time, p.id as prihod_id
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
                    $totalNew++;
                    continue;
                }

                // Анализируем изменения
                $firstPrice = $priceHistory[0]['price'];
                $lastPrice = $priceHistory[count($priceHistory) - 1]['price'];
                // $minPrice = min(array_column($priceHistory, 'price'));
                // $maxPrice = max(array_column($priceHistory, 'price'));
                // $avgPrice = array_sum(array_column($priceHistory, 'price')) / count($priceHistory);
                $prices = array();
                foreach ($priceHistory as $h) {
                    $prices[] = $h['price'];
                }
                $minPrice = min($prices);
                $maxPrice = max($prices);
                $avgPrice = array_sum($prices) / count($prices);

                $priceChange = $lastPrice - $firstPrice;
                $priceChangePercent = $firstPrice > 0 ? (($priceChange / $firstPrice) * 100) : 0;

                if ($priceChange > 0) $totalIncreases++;
                elseif ($priceChange < 0) $totalDecreases++;
                else $totalSame++;

                $analysisData[] = array(
                    'item_id' => $itemId,
                    'name' => $itemInfo['name'],
                    'history' => $priceHistory,
                    'first_price' => $firstPrice,
                    'last_price' => $lastPrice,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'avg_price' => $avgPrice,
                    'change' => $priceChange,
                    'change_percent' => $priceChangePercent,
                    'entries_count' => count($priceHistory)
                );
            }

            // Выводим статистику
            echo "<div style='margin: 20px 0;'>";
            echo "<div class='stats-box' style='border-color: #dc3545;'>";
            echo "<div class='stats-label'>Подорожало</div>";
            echo "<div class='stats-value' style='color: #dc3545;'>$totalIncreases</div>";
            echo "</div>";

            echo "<div class='stats-box' style='border-color: #28a745;'>";
            echo "<div class='stats-label'>Подешевело</div>";
            echo "<div class='stats-value' style='color: #28a745;'>$totalDecreases</div>";
            echo "</div>";

            echo "<div class='stats-box' style='border-color: #ffc107;'>";
            echo "<div class='stats-label'>Без изменений</div>";
            echo "<div class='stats-value' style='color: #ffc107;'>$totalSame</div>";
            echo "</div>";

            echo "<div class='stats-box' style='border-color: #17a2b8;'>";
            echo "<div class='stats-label'>Новые</div>";
            echo "<div class='stats-value' style='color: #17a2b8;'>$totalNew</div>";
            echo "</div>";
            echo "</div>";

            // Таблица с анализом
            echo "<table class='analysis-table'>";
            echo "<thead><tr>";
            echo "<th style='width: 40%;'>Наименование</th>";
            echo "<th>График</th>";
            echo "<th>Первая цена</th>";
            echo "<th>Последняя цена</th>";
            echo "<th>Изменение</th>";
            echo "<th>Мин/Макс</th>";
            echo "<th>История</th>";
            echo "</tr></thead><tbody>";

            foreach ($analysisData as $idx => $data) {
                $changeClass = '';
                $changeIcon = '';
                if ($data['change'] > 0) {
                    $changeClass = 'price-up';
                    $changeIcon = '▲';
                } elseif ($data['change'] < 0) {
                    $changeClass = 'price-down';
                    $changeIcon = '▼';
                } else {
                    $changeClass = 'price-same';
                    $changeIcon = '=';
                }

                echo "<tr>";
                echo "<td><a href='sclad_item.php?id={$data['item_id']}' target='_blank'>" . htmlspecialchars($data['name']) . "</a></td>";

                // Мини-график
                echo "<td>";
                if (count($data['history']) > 1) {
                    $chartId = "chart_" . $idx;
                    echo "<svg class='mini-chart' id='$chartId'></svg>";

                    // Подготовка данных для графика
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
                        
                        // Линия
                        var pathD = 'M' + points.map(p => p.x + ',' + p.y).join(' L');
                        var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', pathD);
                        path.setAttribute('class', 'chart-line');
                        svg.appendChild(path);
                        
                        // Точки
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

                echo "<td>" . number_format($data['first_price'] / 100, 2, ',', ' ') . " ₽</td>";
                echo "<td>" . number_format($data['last_price'] / 100, 2, ',', ' ') . " ₽</td>";

                echo "<td class='$changeClass'>";
                echo $changeIcon . " " . number_format(abs($data['change']) / 100, 2, ',', ' ') . " ₽";
                if ($data['change'] != 0) {
                    echo " (" . number_format(abs($data['change_percent']), 1, ',', '') . "%)";
                }
                echo "</td>";

                echo "<td style='font-size: 11px;'>";
                echo "↓ " . number_format($data['min_price'] / 100, 2, ',', ' ') . " ₽<br>";
                echo "↑ " . number_format($data['max_price'] / 100, 2, ',', ' ') . " ₽";
                echo "</td>";

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