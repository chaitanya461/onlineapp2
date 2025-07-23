<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product = get_product_by_id($_GET['id']);
if (!$product) {
    header("Location: dashboard.php");
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
    
    if (empty($errors)) {
        $update_data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'id' => $product['id']
        ];
        
        $sql = "UPDATE products SET name = :name, description = :description, price = :price";
        
        // Handle image upload if provided
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $image_ext;
            $image_path = UPLOAD_DIR . $image_name;
            
            if (move_uploaded_file($image['tmp_name'], $image_path)) {
                $s3_url = upload_to_s3($image_path, 'products/' . $image_name);
                
                if ($s3_url) {
                    $sql .= ", image_url = :image_url";
                    $update_data['image_url'] = $s3_url;
                    unlink($image_path);
                } else {
                    $errors[] = "Failed to upload image to S3";
                    unlink($image_path);
                }
            } else {
                $errors[] = "Failed to upload image";
            }
        }
        
        if (empty($errors)) {
            $sql .= " WHERE id = :id";
            $pdo = get_db_connection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($update_data);
            
            $success = true;
            $product = get_product_by_id($product['id']); // Refresh product data
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Edit Product</h1>
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>
    </header>
    
    <main>
        <?php if ($success): ?>
            <div class="success">
                Product updated successfully! <a href="dashboard.php">View all products</a>
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*">
                <p>Current image: <img src="<?php echo $product['image_url']; ?>" alt="Current product image" width="100"></p>
            </div>
            
            <button type="submit">Update Product</button>
        </form>
    </main>
</body>
</html>
