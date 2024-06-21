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

$post_id = $_GET['post_id'];

$sql = "SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'id' => $row['id'],
        'user_id' => $row['user_id'],
        'username' => $row['username'],
        'comment' => $row['comment'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'comments' => $comments]);

$conn->close();
?>