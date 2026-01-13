<?php
// import_proizvcalendar_kuzyak.php

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("location: enter.php");
    exit;
}

include_once 'DBWork.php';
include_once 'functions.php';

$year = 2026; // <- можно сделать параметром ?year=2026

$msql_cnnct = ConnectToDB();

/**
 * GET JSON через cURL
 */
function http_get_json($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false || $code < 200 || $code >= 300) {
        return [null, "HTTP $code, cURL: $err, url=$url"];
    }

    $data = json_decode($body, true);
    if (!is_array($data)) {
        return [null, "Не смог распарсить JSON: $url"];
    }

    return [$data, null];
}

/**
 * Построим множество нерабочих дат YYYY-MM-DD:
 * 1) все субботы/воскресенья
 * 2) + праздники/переносы из API (в зависимости от структуры ответа)
 */
$nonWorking = [];

// (1) все выходные (Сб/Вс)
$dt = new DateTime("$year-01-01");
$end = new DateTime(($year + 1) . "-01-01");
while ($dt < $end) {
    $dow = (int)$dt->format('N'); // 6=Сб, 7=Вс
    if ($dow >= 6) {
        $nonWorking[$dt->format('Y-m-d')] = true;
    }
    $dt->modify('+1 day');
}

// (2) пробуем забрать данные из API kuzyak
// По документации есть /api/calendar/{year} и /api/calendar/{year}/holidays :contentReference[oaicite:1]{index=1}
$base = "https://calendar.kuzyak.in/api/calendar/$year";

// Сначала пробуем /holidays (обычно там есть праздники и сокращенные)
list($holData, $holErr) = http_get_json($base . "/holidays");

// Если /holidays недоступен или вернул ошибку — пробуем годовой эндпоинт
if ($holData === null) {
    list($yearData, $yearErr) = http_get_json($base);
    if ($yearData === null) {
        echo "Не удалось получить данные API.<br>\n";
        echo "holidays: " . htmlspecialchars($holErr) . "<br>\n";
        echo "year: " . htmlspecialchars($yearErr) . "<br>\n";
        exit;
    }
} else {
    $yearData = null;
}

/**
 * Универсальный разбор:
 * - Если есть массив строк дат:
 *     holidays: ["2026-01-01", ...]
 *     days:     ["2026-01-01", ...]  (на главной странице пример: data.days :contentReference[oaicite:2]{index=2})
 * - Если есть массив объектов дней:
 *     days: [{date:"2026-01-01", type:"holiday", ...}, ...]
 *   Тогда берём те, что явно нерабочие (type/working/isWorking)
 *
 * Сокращённые (preholidays) НЕ добавляем.
 */
function add_date_list(&$set, $list) {
    if (!is_array($list)) return;
    foreach ($list as $v) {
        if (is_string($v) && preg_match('~^\d{4}-\d{2}-\d{2}$~', $v)) {
            $set[$v] = true;
        }
    }
}

if ($holData !== null) {
    // Частый вариант: {"holidays":[...], "preholidays":[...]} или {"days":[...], ...}
    if (isset($holData['holidays'])) {
        add_date_list($nonWorking, $holData['holidays']);
    }
    if (isset($holData['days'])) {
        add_date_list($nonWorking, $holData['days']);
    }
    // preholidays игнорируем специально
} else {
    // Разбор годового ответа
    if (isset($yearData['holidays'])) {
        add_date_list($nonWorking, $yearData['holidays']);
    }
    if (isset($yearData['days']) && is_array($yearData['days'])) {

        foreach ($yearData['days'] as $dayObj) {

            // дата
            if (isset($dayObj['date'])) {
                $date = $dayObj['date'];
            } elseif (isset($dayObj['day'])) {
                $date = $dayObj['day'];
            } else {
                continue;
            }

            if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $date)) {
                continue;
            }

            // тип дня
            if (isset($dayObj['type'])) {
                $type = strtolower($dayObj['type']);
            } elseif (isset($dayObj['kind'])) {
                $type = strtolower($dayObj['kind']);
            } else {
                $type = '';
            }

            // рабочий / нерабочий
            if (isset($dayObj['isWorking'])) {
                $isWorking = $dayObj['isWorking'];
            } elseif (isset($dayObj['working'])) {
                $isWorking = $dayObj['working'];
            } else {
                $isWorking = null;
            }

            // считаем нерабочим
            if (
                in_array($type, array('holiday','weekend','dayoff','nonworking','non-working'), true)
                || ($isWorking === false)
            ) {
                $nonWorking[$date] = true;
            }
        }
    }
}

// --- пишем в БД ---

// удаляем всё начиная с нужного года (как у вас было)
$yearEsc = mysqli_real_escape_string($msql_cnnct, (string)$year);
$query = "DELETE FROM `spr_proizvcalendar_holidays` WHERE `year` >= '$yearEsc'";
mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

// вставка
$count = 0;
foreach (array_keys($nonWorking) as $dateStr) {
    // YYYY-MM-DD
    $y = substr($dateStr, 0, 4);
    $m = substr($dateStr, 5, 2);
    $d = substr($dateStr, 8, 2);

    // status оставляем 0 как у вас
    $qy = mysqli_real_escape_string($msql_cnnct, $y);
    $qm = mysqli_real_escape_string($msql_cnnct, $m);
    $qd = mysqli_real_escape_string($msql_cnnct, $d);

    $query = "INSERT INTO `spr_proizvcalendar_holidays` (`year`, `month`, `day`, `status`)
              VALUES ('$qy', '$qm', '$qd', 0)";
    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    $count++;
}

echo "Готово: импортировал нерабочих дней за $year: <b>$count</b><br>\n";

?>