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

// Fetch distinct categories
$sql = "SELECT DISTINCT category FROM stock";
$result = $conn->query($sql);

// Initialize output
$output = "";

// Check if categories exist
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = htmlspecialchars($row['category']); // Prevent XSS
        $output .= "<li class='category-item'>" . $category . "</li>";
    }
} else {
    $output = "<li>No categories found</li>";
}

// Return output
echo $output;

// Close connection
$conn->close();
?>