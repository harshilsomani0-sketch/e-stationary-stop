<?php
include_once 'includes/db_connect.php';

$results = [];

if (isset($_GET['query'])) {
    $search_term = trim($_GET['query']);

    if (!empty($search_term)) {
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, image_url FROM products WHERE name LIKE ? LIMIT 5");
        
        // Add wildcards for the LIKE query
        $like_term = "%" . $search_term . "%";
        $stmt->bind_param("s", $like_term);
        
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}

// Set header to output JSON
header('Content-Type: application/json');
echo json_encode($results);
?>