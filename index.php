<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/functions.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Cell Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Phone Cell Store</h1>
        <?php if (is_admin_logged_in()): ?>
            <a href="admin/dashboard.php">Admin Dashboard</a> | 
            <a href="admin/logout.php">Logout</a>
        <?php endif; ?>
    </header>
    
    <main>
        <div class="products-grid">
            <?php foreach (get_all_products() as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
