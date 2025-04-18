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

// Ensure data exists
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (!isset($_POST['category'], $_POST['name'], $_POST['price'])) {
        http_response_code(400); // Bad Request
        die(json_encode(["error" => "Missing required fields."]));
    }

    $category = $conn->real_escape_string($_POST['category']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']); // Ensure price is a number

    // Image upload handling
    if (isset($_FILES["image"]["name"]) && $_FILES["image"]["size"] > 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the uploads directory if it doesn't exist
        }

        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow only certain formats
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            http_response_code(400); // Bad Request
            die(json_encode(["error" => "Invalid file type! Allowed types: JPG, JPEG, PNG, GIF."]));
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO stock (category, name, price, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $category, $name, $price, $target_file);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Stock added successfully!"]);
            } else {
                http_response_code(500); // Internal Server Error
                die(json_encode(["error" => "Database error: " . $stmt->error]));
            }
            $stmt->close();
        } else {
            http_response_code(500); // Internal Server Error
            die(json_encode(["error" => "Error uploading file!"]));
        }
    } else {
        http_response_code(400); // Bad Request
        die(json_encode(["error" => "Image file is required."]));
    }
}

$conn->close();
?>