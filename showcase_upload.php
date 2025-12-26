<?php
include_once 'includes/header.php';

// User must be logged in to upload
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $caption = trim($_POST['caption']);

    if (isset($_FILES['showcase_image']) && $_FILES['showcase_image']['error'] == 0) {
        $target_dir = "assets/showcase/";
        $original_filename = basename($_FILES["showcase_image"]["name"]);
        $image_filename = uniqid() . '-' . $original_filename;
        $target_file = $target_dir . $image_filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validation (same as product image upload)
        $check = getimagesize($_FILES["showcase_image"]["tmp_name"]);
        if($check === false) {
            $errors[] = "File is not an image.";
            $uploadOk = 0;
        }
        if ($_FILES["showcase_image"]["size"] > 5000000) { // 5MB limit
            $errors[] = "Your file is too large.";
            $uploadOk = 0;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
            $errors[] = "Only JPG, JPEG, & PNG files are allowed.";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["showcase_image"]["tmp_name"], $target_file)) {
                // File uploaded successfully, now insert into DB
                $stmt = $conn->prepare("INSERT INTO showcase (user_id, image_url, caption) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $image_filename, $caption);
                if ($stmt->execute()) {
                    $success_message = "Thank you! Your submission has been received and is pending approval.";
                } else {
                    $errors[] = "Sorry, there was an error saving your submission.";
                }
                $stmt->close();
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $errors[] = "Please select an image to upload.";
    }
}

?>

<div class="form-container">
    <h2>Showcase Your Setup</h2>
    <p>Share a photo of your desk, journal, or art made with our products!</p>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="showcase_upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="showcase_image">Upload Photo (JPG, PNG)</label>
                <input type="file" name="showcase_image" id="showcase_image" required>
            </div>
            <div class="form-group">
                <label for="caption">Caption (Optional)</label>
                <textarea name="caption" id="caption" rows="3" placeholder="Tell us about your photo..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit for Approval</button>
        </form>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>