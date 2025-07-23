<?php
function get_db_connection() {
    $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";user=".DB_USER.";password=".DB_PASS;
    
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function upload_to_s3($file_path, $s3_key) {
    global $s3;
    
    try {
        $result = $s3->putObject([
            'Bucket' => S3_BUCKET,
            'Key'    => $s3_key,
            'SourceFile' => $file_path,
        ]);
        
        return $result->get('ObjectURL');
    } catch (AwsException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function get_all_products() {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product_by_id($id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
