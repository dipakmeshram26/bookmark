<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "website_db");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $site_name = $_POST['site_name'];
    $description = $_POST['description'];
    $site_link = $_POST['site_link'];
    $category = $_POST['category'];

    // Validate & sanitize inputs
    $site_name = mysqli_real_escape_string($conn, $site_name);
    $description = mysqli_real_escape_string($conn, $description);
    $site_link = mysqli_real_escape_string($conn, $site_link);
    $category = mysqli_real_escape_string($conn, $category);
     $visibility = $_POST['visibility'] == 'private' ? 'private' : 'public';

   
    $domain = parse_url($site_link, PHP_URL_HOST);
    $logo = "https://www.google.com/s2/favicons?sz=256&domain=" . $domain;

    // If user uploads an image, save it
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/logos/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES["image"]["name"]);
        $image_path = $target_dir . time() . "_" . $image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            $logo = $image_path;  // Use uploaded image if available
        }
    }

    //  insert data into the database
 // Insert Data
    $stmt = $conn->prepare("INSERT INTO websites (site_name, description, site_link, logo, category, visibility, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $site_name, $description, $site_link, $logo, $category, $visibility);


    // Execute query and check for errors
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1"); // Redirect after success
        
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
