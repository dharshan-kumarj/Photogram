<?php
session_start();

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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a select statement
    $sql = "SELECT id, username, email, password FROM users WHERE email = :email";

    try {
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':email', $email);
        
        // Execute the prepared statement
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // Fetch the row
            $row = $stmt->fetch();
            
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Password is correct, start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row['id'];
                $_SESSION["username"] = $row['username'];
                
                // Redirect to dashboard
                header("location: ../../_pages/Dashboard.php");
            } else {
                // Password is not valid
                echo "Invalid password.";
            }
        } else {
            // Email doesn't exist
            echo "No account found with that email.";
        }
    } catch(PDOException $e) {
        die("ERROR: Could not execute $sql. " . $e->getMessage());
    }
}

// Close connection
unset($pdo);
?>