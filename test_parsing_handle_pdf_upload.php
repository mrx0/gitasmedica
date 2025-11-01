<?php

//test_parsing_handle_pdf_upload.php
//Тестовая функция для парсинга

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['pdf_file'])) {
    die('Нет файла для загрузки.');
}

$file = $_FILES['pdf_file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    die('Ошибка загрузки файла.');
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    die('Можно загружать только PDF.');
}

$target = $uploadDir . time() . '_' . basename($file['name']);
if (!move_uploaded_file($file['tmp_name'], $target)) {
    die('Не удалось сохранить файл.');
}

echo "<p>Файл загружен: <strong>" . htmlspecialchars(basename($target)) . "</strong></p>";
echo "<p>Начинается распознавание…</p>";

// Запускаем OCR
include 'test_parsing_ocr_parse.php';
