<?php
// check_images.php
include 'includes/db_connect.php';

echo "<h2>🕵️ Image Diagnostic Tool</h2>";
echo "<p>This tool compares your Database list vs. your Actual Files.</p>";
echo "<hr>";

// 1. Get all filenames from Database
$db_files = [];
$result = $conn->query("SELECT id, name, image_url FROM products");
while ($row = $result->fetch_assoc()) {
    $db_files[$row['id']] = [
        'name' => $row['name'],
        'img' => $row['image_url']
    ];
}

// 2. Get all actual files from the Folder
$folder = "assets/images/";
$actual_files = scandir($folder);

echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#eee;'>
        <th>Product Name</th>
        <th>Database Says Filename Is...</th>
        <th>Status</th>
        <th>Action</th>
      </tr>";

foreach ($db_files as $id => $data) {
    $db_name = $data['img'];
    $status = "";
    $action = "";
    $color = "black";

    // Check if the database entry is empty
    if (empty($db_name)) {
        $status = "❌ <strong style='color:red'>EMPTY</strong>";
        $action = "Go to Admin > Edit Product and upload an image.";
    } 
    // Check if file exists in folder (Exact Match)
    elseif (in_array($db_name, $actual_files)) {
        $status = "✅ <strong style='color:green'>FOUND</strong>";
        $action = "Filename is correct.<br>If image is broken, the file itself is corrupt.";
    } 
    // Check for "Close Match" (Case sensitivity or spaces)
    else {
        $status = "❌ <strong style='color:red'>MISSING</strong>";
        $action = "<strong>Your folder does not have this file!</strong><br>";
        
        // Try to find a similar file
        $found_similar = false;
        foreach ($actual_files as $file) {
            if ($file == "." || $file == "..") continue;
            // Check if names match but case is different
            if (strtolower($file) == strtolower($db_name)) {
                $action .= "Did you mean: <b style='color:blue'>$file</b>? (Case mismatch)";
                $found_similar = true;
            }
            // Check if user forgot the timestamp prefix
            if (strpos($file, $db_name) !== false) {
                $action .= "Did you mean: <b style='color:blue'>$file</b>?";
                $found_similar = true;
            }
        }
        if (!$found_similar) {
            $action .= "Please verify the filename in 'assets/images/'.";
        }
    }

    echo "<tr>";
    echo "<td>" . htmlspecialchars($data['name']) . "</td>";
    echo "<td>" . htmlspecialchars($db_name) . "</td>";
    echo "<td>$status</td>";
    echo "<td>$action</td>";
    echo "</tr>";
}
echo "</table>";
?>