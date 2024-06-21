<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "Photogram";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome to the Dashboard, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        
        <!-- Display posts -->
        <div id="posts-container" class="mt-4">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    echo "<p class='card-text'>" . htmlspecialchars($row["description"]) . "</p>";
                    if ($row["file_type"] == "image") {
                        echo "<img src='" . htmlspecialchars($row["file_path"]) . "' class='img-fluid' alt='Posted image'>";
                    } elseif ($row["file_type"] == "video") {
                        echo "<video controls class='w-100'><source src='" . htmlspecialchars($row["file_path"]) . "' type='video/mp4'></video>";
                    }
                    echo "<p class='text-muted mt-2'>Posted on " . $row["created_at"] . "</p>";
                    echo "</div></div>";
                }
            } else {
                echo "<p>No posts yet.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button type="button" class="btn btn-primary fab" data-bs-toggle="modal" data-bs-target="#popupModal">
        +
    </button>

    <!-- Modal Popup -->
    <div class="modal fade" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="popupModalLabel">Create a New Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="postForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">Upload Image or Video</label>
                            <input type="file" class="form-control" id="fileUpload" name="file" accept="image/*,video/*">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Write a description..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitPost()">Post</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
    function submitPost() {
        var formData = new FormData(document.getElementById('postForm'));
        
        fetch('submit_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log("Raw response:", text);  // This will log the raw response
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Failed to parse response as JSON:", e);
                throw new Error("Server returned an invalid response");
            }
        })
        .then(data => {
            if (data.success) {
                // Close the modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('popupModal'));
                modal.hide();
                
                // Add the new post to the top of the posts container
                var postsContainer = document.getElementById('posts-container');
                postsContainer.insertAdjacentHTML('afterbegin', data.html);
                
                // Clear the form
                document.getElementById('postForm').reset();
            } else {
                console.error('Error:', data.message);
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the post: ' + error.message);
        });
    }
    </script>
</body>
</html>