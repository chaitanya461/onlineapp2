<?php
// Database configuration
define('DB_HOST', 'your-rds-endpoint.rds.amazonaws.com');
define('DB_PORT', '5432');
define('DB_NAME', 'dynamic1');
define('DB_USER', 'postgre');
define('DB_PASS', 'Satyasai17');

// AWS S3 Configuration
define('AWS_REGION', 'your-region');
define('S3_BUCKET', 'your-bucket-name');

// Website settings
define('BASE_URL', 'http://your-ec2-public-ip-or-domain');
define('UPLOAD_DIR', '/var/www/html/images/');

// Start session
session_start();

// Include AWS SDK
require '/var/www/html/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Initialize S3 client
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => AWS_REGION,
]);
?>
