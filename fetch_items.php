<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "127.0.0.1";
$username = "root";
$password = "1234"; // Update this if needed
$database = "pos_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$category = isset($_GET['category']) ? $_GET['category'] : "";

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

// Initialize output
$output = "";

// Check if items exist
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "
            <div class='card item-card' data-id='{$row['ID']}' data-name='{$row['name']}' data-price='{$row['price']}'>
                <img src='{$row['image_path']}' alt='{$row['name']}'>
                <h4>{$row['name']}</h4>
                <p>\${$row['price']}</p>
            </div>";
    }
} else {
    $output = "<p>No items found.</p>";
}

// Return output
echo $output;

// Close connection
$conn->close();
?>