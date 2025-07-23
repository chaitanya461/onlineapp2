cat > /var/www/html/create_admin.php << 'EOL'
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$username = 'admin';
$password = 'your_secure_password'; // Change this!

$pdo = get_db_connection();
$stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);

echo "Admin user created successfully!";
?>
EOL

# Run it once (access via browser or command line)
php /var/www/html/create_admin.php

# Then delete it for security
rm /var/www/html/create_admin.php
