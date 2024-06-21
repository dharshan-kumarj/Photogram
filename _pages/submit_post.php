<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "Photogram";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// File upload handling
$uploadDir = '/var/www/html/Photogram/_pages/uploads/';
$webPath = '/Photogram/_pages/uploads/';

if (!file_exists($uploadDir)) {
    echo json_encode(['success' => false, 'message' => 'Upload directory does not exist. Please create it manually.']);
    exit;
}

if (!is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'message' => 'Upload directory is not writable']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'No file was uploaded']);
    exit;
}

$file = $_FILES['file'];
$description = $_POST['description'];

$fileType = explode('/', $file['type'])[0]; // 'image' or 'video'
$fileName = uniqid() . '_' . $file['name'];
$filePath = $uploadDir . $fileName;
$webFilePath = $webPath . $fileName;

$phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    $errorCode = $file['error'];
    $errorMessage = isset($phpFileUploadErrors[$errorCode]) ? $phpFileUploadErrors[$errorCode] : 'Unknown upload error';
    echo json_encode(['success' => false, 'message' => 'Failed to upload file: ' . $errorMessage]);
    exit;
}

// Insert into database
$sql = "INSERT INTO posts (user_id, description, file_path, file_type) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $_SESSION['id'], $description, $webFilePath, $fileType);

if ($stmt->execute()) {
    $post_id = $conn->insert_id;
    $created_at = date("Y-m-d H:i:s");
    
    // Prepare HTML for the new post
    $html = "<div class='card mb-3'>";
    $html .= "<div class='card-body'>";
    $html .= "<p class='card-text'>" . htmlspecialchars($description) . "</p>";
    if ($fileType == "image") {
        $html .= "<img src='" . htmlspecialchars($webFilePath) . "' class='img-fluid' alt='Posted image'>";
    } elseif ($fileType == "video") {
        $html .= "<video controls class='w-100'><source src='" . htmlspecialchars($webFilePath) . "' type='video/mp4'></video>";
    }
    $html .= "<p class='text-muted mt-2'>Posted on " . $created_at . "</p>";
    $html .= "</div></div>";
    
    echo json_encode(['success' => true, 'html' => $html]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert into database: ' . $stmt->error]);
}

$conn->close();
?>