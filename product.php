<?php 
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product = get_product_by_id($_GET['id']);
if (!$product) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Phone Cell Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Phone Cell Store</h1>
        <a href="index.php">Back to Products</a>
    </header>
    
    <main class="product-detail">
        <div class="product-image">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
        </div>
    </main>
</body>
</html>
