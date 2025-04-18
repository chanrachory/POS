<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "127.0.0.1"; // Corrected IP
$username = "root";
$password = "1234";
$database = "pos_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch category from query parameter (optional)
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL query
$sql = "SELECT * FROM stock";
if (!empty($category)) {
    $sql .= " WHERE category = ?";
}

// Prepare and execute the query
if (!empty($category)) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        die(json_encode(["error" => "SQL preparation failed: " . $conn->error]));
    }
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
    if (!$result) {
        http_response_code(500); // Internal Server Error
        die(json_encode(["error" => "SQL query failed: " . $conn->error]));
    }
}

// Fetch data and encode as JSON
if ($result->num_rows > 0) {
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $items]);
} else {
    echo json_encode(["status" => "success", "data" => []]); // No items found
}

// Close statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>