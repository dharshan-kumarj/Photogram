<?php
// Database connection details
$host = 'localhost';
$dbname = 'Photogram';
$username = 'root';
$password = '12345';

// Attempt to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

    try {
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        // Execute the prepared statement
        $stmt->execute();
        
        echo "Registration successful!";
    } catch(PDOException $e) {
        die("ERROR: Could not execute $sql. " . $e->getMessage());
    }
}

// Close connection
unset($pdo);
?>