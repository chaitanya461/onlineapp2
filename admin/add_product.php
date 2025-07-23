<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $image = $_FILES['image'] ?? null;
    
    // Validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($price)) $errors[] = "Price is required";
    if (!is_numeric($price)) $errors[] = "Price must be a number";
    if (!$image || $image['error'] !== UPLOAD_ERR_OK) $errors[] = "Image is required";
    
    if (empty($errors)) {
        // Handle file upload
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $image_name = uniqid() . '.' . $image_ext;
        $image_path = UPLOAD_DIR . $image_name;
        
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            // Upload to S3
            $s3_url = upload_to_s3($image_path, 'products/' . $image_name);
            
            if ($s3_url) {
                // Save to database
                $pdo = get_db_connection();
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $s3_url]);
                
                // Delete local file
                unlink($image_path);
                
                $success = true;
            } else {
                $errors[] = "Failed to upload image to S3";
                unlink($image_path);
            }
        } else {
            $errors[] = "Failed to upload image";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Add New Product</h1>
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>
    </header>
    
    <main>
        <?php if ($success): ?>
            <div class="success">
                Product added successfully! <a href="dashboard.php">View all products</a>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            
            <button type="submit">Add Product</button>
        </form>
    </main>
</body>
</html>
