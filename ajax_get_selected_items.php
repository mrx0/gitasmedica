<?php

//ajax_get_selected_items.php
//

session_start();

header('Content-Type: application/json');

$response = array(
    'success' => false,
    'items' => array()
);

// ID хранятся как значения массива, а не как ключи
if (isset($_SESSION['sclad']['items_data']) && !empty($_SESSION['sclad']['items_data'])) {
    $response['success'] = true;
    // Берём значения массива, а не ключи
    $response['items'] = array_values($_SESSION['sclad']['items_data']);
}

echo json_encode($response);
?>