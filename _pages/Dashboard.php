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

// Fetch posts with usernames, like counts, and comments
$sql = "SELECT posts.*, users.username, 
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
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
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            z-index: 1000;
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-text {
            height: 4.5em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Welcome to the Dashboard, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        
        <!-- Display posts -->
        <div id="posts-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='col'>";
                    echo "<div class='card h-100'>";
                    echo "<div class='card-header'>";
                    echo "<strong>" . htmlspecialchars($row["username"]) . "</strong>";
                    echo "</div>";
                    if ($row["file_type"] == "image") {
                        echo "<img src='" . htmlspecialchars($row["file_path"]) . "' class='card-img-top' alt='Posted image'>";
                    } elseif ($row["file_type"] == "video") {
                        echo "<video controls class='card-img-top'><source src='" . htmlspecialchars($row["file_path"]) . "' type='video/mp4'></video>";
                    }
                    echo "<div class='card-body'>";
                    echo "<p class='card-text'>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<button class='btn btn-outline-primary btn-sm me-2 like-btn' data-post-id='" . $row["id"] . "'>";
                    echo "<i class='fas fa-thumbs-up'></i> Like (<span class='like-count'>" . $row["like_count"] . "</span>)";
                    echo "</button>";
                    echo "<button class='btn btn-outline-secondary btn-sm comment-btn' data-post-id='" . $row["id"] . "'>";
                    echo "<i class='fas fa-comment'></i> Comment (" . $row["comment_count"] . ")";
                    echo "</button>";
                    echo "<small class='text-muted d-block mt-2'>Posted on " . $row["created_at"] . "</small>";
                    echo "</div>";
                    echo "<div class='card-footer comment-section' id='comment-section-" . $row["id"] . "' style='display:none;'>";
                    echo "<form class='comment-form' data-post-id='" . $row["id"] . "'>";
                    echo "<div class='input-group mb-3'>";
                    echo "<input type='text' class='form-control' placeholder='Add a comment...' aria-label='Add a comment' aria-describedby='button-addon2'>";
                    echo "<button class='btn btn-outline-secondary' type='submit' id='button-addon2'>Post</button>";
                    echo "</div>";
                    echo "</form>";
                    echo "<div class='comments-container' id='comments-container-" . $row["id"] . "'></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
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
            console.log("Raw response:", text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Failed to parse response as JSON:", e);
                throw new Error("Server returned an invalid response");
            }
        })
        .then(data => {
            if (data.success) {
                console.log("New post HTML:", data.html);
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

    // Like functionality
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('like-btn')) {
            var postId = e.target.dataset.postId;
            fetch('Like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    e.target.querySelector('.like-count').textContent = data.likes;
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    });

    // Comment functionality
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('comment-btn')) {
            var postId = e.target.dataset.postId;
            var commentSection = document.getElementById('comment-section-' + postId);
            if(commentSection.style.display === 'none') {
                commentSection.style.display = 'block';
                loadComments(postId);
            } else {
                commentSection.style.display = 'none';
            }
        }
    });

    document.addEventListener('submit', function(e) {
        if(e.target && e.target.classList.contains('comment-form')) {
            e.preventDefault();
            var postId = e.target.dataset.postId;
            var commentInput = e.target.querySelector('input');
            var comment = commentInput.value;
            
            fetch('Add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId + '&comment=' + encodeURIComponent(comment)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    commentInput.value = '';
                    loadComments(postId);
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    });

    function loadComments(postId) {
        fetch('Get_comments.php?post_id=' + postId)
        .then(response => response.json())
        .then(data => {
            var commentsContainer = document.getElementById('comments-container-' + postId);
            commentsContainer.innerHTML = '';
            data.comments.forEach(comment => {
                var deleteButton = '';
                if (comment.user_id == <?php echo $_SESSION['id']; ?>) {
                    deleteButton = `<button class="btn btn-sm btn-danger delete-comment" data-comment-id="${comment.id}">Delete</button>`;
                }
                commentsContainer.innerHTML += `
                    <div class="comment">
                        <strong>${comment.username}</strong>: ${comment.comment}
                        <small class="text-muted">${comment.created_at}</small>
                        ${deleteButton}
                    </div>
                `;
            });
        });
    }

    // Add event listener for delete buttons
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('delete-comment')) {
            var commentId = e.target.dataset.commentId;
            if(confirm('Are you sure you want to delete this comment?')) {
                deleteComment(commentId, e.target);
            }
        }
    });

    function deleteComment(commentId, buttonElement) {
        fetch('Delete_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'comment_id=' + commentId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Remove the comment from the DOM
                buttonElement.closest('.comment').remove();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
    </script>
</body>
</html>