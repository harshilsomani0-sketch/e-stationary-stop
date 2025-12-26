<?php
// fix_images.php
include 'includes/db_connect.php';

// Handle Update
if (isset($_POST['update_image'])) {
    $id = intval($_POST['product_id']);
    $new_filename = trim($_POST['filename']);
    
    $stmt = $conn->prepare("UPDATE products SET image_url = ? WHERE id = ?");
    $stmt->bind_param("si", $new_filename, $id);
    
    if ($stmt->execute()) {
        $msg = "<div style='background:#d4edda; color:#155724; padding:10px; margin-bottom:10px;'>✅ Saved! Product ID $id is now linked to: <strong>$new_filename</strong></div>";
    } else {
        $msg = "<div style='color:red;'>Error updating database.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fix Image Links</title>
    <style>
        body { font-family: sans-serif; padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f4f4f4; }
        input[type="text"] { width: 300px; padding: 8px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .folder-list { background: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h1>🔧 Fix Product Images Manually</h1>
    <p>Use this tool to connect your products to the files currently sitting in your folder.</p>
    
    <?php if(isset($msg)) echo $msg; ?>

    <div class="folder-list">
        <h3>📂 Files currently in 'assets/images/':</h3>
        <p style="font-size: 0.9em; color: #555;">Copy one of these names and paste it into the box below.</p>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php
            $files = scandir("assets/images/");
            foreach ($files as $file) {
                if ($file == "." || $file == ".." || is_dir("assets/images/$file")) continue;
                echo "<span style='background: white; padding: 5px 10px; border: 1px solid #ddd; font-family: monospace;'>$file</span>";
            }
            ?>
        </div>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Current Database Link</th>
            <th>Update Link</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM products");
        while ($row = $result->fetch_assoc()):
            $img_path = "assets/images/" . $row['image_url'];
            $exists = file_exists($img_path) && !empty($row['image_url']);
            $color = $exists ? "green" : "red";
            $status = $exists ? "✅ Working" : "❌ Broken";
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td>
                <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
            </td>
            <td style="color: <?php echo $color; ?>;">
                <?php echo htmlspecialchars($row['image_url']); ?><br>
                <small><?php echo $status; ?></small>
            </td>
            <td>
                <form method="POST" style="display:flex; gap:10px;">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="filename" value="<?php echo htmlspecialchars($row['image_url']); ?>" placeholder="Paste filename here (e.g. notebook.jpg)">
                    <button type="submit" name="update_image">Save</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>