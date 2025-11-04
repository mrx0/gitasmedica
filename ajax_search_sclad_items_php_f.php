<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'configPDO.php';

if (!isset($_POST['query'])) {
    echo json_encode([]);
    exit;
}

$query = trim($_POST['query']);

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $mysqli = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
    $mysqli->set_charset("utf8");

    $searchQuery = '%' . $query . '%';
    $stmt = $mysqli->prepare("SELECT id, name FROM spr_sclad_items WHERE name LIKE ? ORDER BY name LIMIT 10");
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = array();
    while ($row = $result->fetch_assoc()) {
        $items[] = array(
            'id' => $row['id'],
            'name' => $row['name']
        );
    }

    $stmt->close();
    $mysqli->close();

    echo json_encode($items);

} catch (Exception $e) {
    echo json_encode([]);
}
?>