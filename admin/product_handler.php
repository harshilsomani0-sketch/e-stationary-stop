<?php
// admin/product_handler.php
session_start();
include_once '../includes/db_connect.php';

// Check if Admin is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access Denied: You must be logged in.");
}

if (isset($_POST['save_product'])) {
    
    // --- 1. CAPTURE BASIC DATA ---
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Determine if we are Adding New or Editing
    $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;
    $is_edit = !empty($id);

    // --- 2. HANDLE MAIN IMAGE UPLOAD ---
    $image_url = ""; // Default empty
    $upload_dir = "../assets/images/";
    
    // Ensure folder exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check if a Main Image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        // Create a clean filename: "173928_Product-Name.jpg"
        $clean_name = preg_replace("/[^a-zA-Z0-9]/", "", $name);
        $new_filename = time() . "_" . $clean_name . "." . $file_ext;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $new_filename;
        } else {
            die("ERROR: Failed to save Main Image. Check folder permissions.");
        }
    }

    // --- 3. SAVE TO PRODUCTS TABLE ---
    if ($is_edit) {
        // UPDATE Existing
        if ($image_url != "") {
            // New image uploaded -> Update everything including image
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=?, is_featured=?, image_url=? WHERE id=?");
            $stmt->bind_param("ssddiisi", $name, $description, $price, $stock, $category_id, $is_featured, $image_url, $id);
        } else {
            // No new image -> Keep old one (Don't update image_url column)
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=?, is_featured=? WHERE id=?");
            $stmt->bind_param("ssddiii", $name, $description, $price, $stock, $category_id, $is_featured, $id);
        }
    } else {
        // INSERT New
        if ($image_url == "") { die("ERROR: Main Image is required for new products."); }
        
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, is_featured, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiis", $name, $description, $price, $stock, $category_id, $is_featured, $image_url);
    }

    // Execute the Main Query
    if ($stmt->execute()) {
        
        // Get the Product ID (If edit, use $id. If new, get insert_id)
        $product_id = $is_edit ? $id : $conn->insert_id;

        // --- 4. HANDLE GALLERY IMAGES (MULTIPLE) ---
        if (isset($_FILES['gallery_images'])) {
            $count = count($_FILES['gallery_images']['name']);
            
            // Prepare the query for the separate table
            $g_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");

            for ($i = 0; $i < $count; $i++) {
                // Check for errors in this specific file
                if ($_FILES['gallery_images']['error'][$i] === 0) {
                    
                    $g_ext = strtolower(pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION));
                    // Unique name for gallery item: "173928_gal_0.jpg"
                    $g_name = time() . "_gal_" . $i . "." . $g_ext;
                    $g_target = $upload_dir . $g_name;

                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $g_target)) {
                        // Insert into product_images table
                        $g_stmt->bind_param("is", $product_id, $g_name);
                        $g_stmt->execute();
                    }
                }
            }
        }

        // Success! Redirect.
        header("Location: index.php?msg=Product Saved Successfully");
        exit();

    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>