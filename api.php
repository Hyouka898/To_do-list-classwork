<?php
include('connection_to_DB.php');
// Handle CORS (if needed)
header('Content-Type: application/json');

// Display Products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
    $sql = "SELECT * FROM tbl_to_do WHERE title LIKE '%$query%'";
    $result = $conn->query($sql);
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products);
    exit;
}

// Insert or Update Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = $conn->real_escape_string($_POST['name']);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = "uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    if ($id > 0) {
        // Update
        $sql = "UPDATE tbl_to_do SET title='$title', status='$status'";
        if ($image) {
            $sql .= ", image='$image'";
        }
        $sql .= " WHERE id=$id";
    } else {
        // Insert (status is default 1, no need to insert it explicitly)
        $sql = "INSERT INTO tbl_to_do (title, image,status, created_at) VALUES ('$title', '$image','$status', NOW())";
    }

    if ($conn->query($sql)) {
        $newId = $id > 0 ? $id : $conn->insert_id;
        $result = $conn->query("SELECT * FROM tbl_to_do WHERE id=$newId");
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => $conn->error]);
    }
    exit;
}

// Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM tbl_to_do WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
    exit;
}

$conn->close();
?>