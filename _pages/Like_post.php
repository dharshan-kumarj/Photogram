<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "Photogram";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$user_id = $_SESSION['id'];
$post_id = $_POST['post_id'];

// Check if the user has already liked the post
$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already liked the post, so remove the like
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
} else {
    // User hasn't liked the post, so add a like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
}

if ($stmt->execute()) {
    // Get the updated like count
    $stmt = $conn->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $like_count = $row['like_count'];

    echo json_encode(['success' => true, 'likes' => $like_count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update like']);
}

$conn->close();
?>