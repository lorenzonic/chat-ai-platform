<?php

/**
 * Script for production database migration and admin creation
 * This script should be run on Railway to fix the admin table and create admin accounts
 */

echo "=== PRODUCTION ADMIN FIX SCRIPT ===\n\n";

// Check if we're in production environment
if (!isset($_ENV['RAILWAY_ENVIRONMENT']) && !isset($_SERVER['RAILWAY_ENVIRONMENT'])) {
    echo "WARNING: This doesn't appear to be a Railway environment.\n";
    echo "Make sure to run this on the production server.\n\n";
}

try {
    // Check database connection
    $pdo = new PDO(
        "mysql:host=" . ($_ENV['MYSQLHOST'] ?? $_SERVER['MYSQLHOST'] ?? 'localhost') .
        ";port=" . ($_ENV['MYSQLPORT'] ?? $_SERVER['MYSQLPORT'] ?? '3306') .
        ";dbname=" . ($_ENV['MYSQLDATABASE'] ?? $_SERVER['MYSQLDATABASE'] ?? 'laravel'),
        $_ENV['MYSQLUSER'] ?? $_SERVER['MYSQLUSER'] ?? 'root',
        $_ENV['MYSQLPASSWORD'] ?? $_SERVER['MYSQLPASSWORD'] ?? ''
    );

    echo "✓ Database connection successful\n\n";

    // Check if role column exists
    $stmt = $pdo->query("DESCRIBE admins");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('role', $columns)) {
        echo "Adding 'role' column to admins table...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN role VARCHAR(255) DEFAULT 'admin' AFTER email");
        echo "✓ Role column added successfully\n\n";
    } else {
        echo "✓ Role column already exists\n\n";
    }

    // Update existing admins without role
    echo "Updating existing admins with default role...\n";
    $stmt = $pdo->prepare("UPDATE admins SET role = 'admin' WHERE role IS NULL OR role = ''");
    $updated = $stmt->execute();
    $affectedRows = $stmt->rowCount();
    echo "✓ Updated {$affectedRows} admin records with default role\n\n";

    // Create super admin if not exists
    echo "Creating super admin account...\n";
    $superAdminEmail = 'admin@chatai.platform';
    $superAdminPassword = password_hash('AdminChat2025!', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$superAdminEmail]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO admins (name, email, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            'Super Admin',
            $superAdminEmail,
            $superAdminPassword,
            'super_admin'
        ]);
        echo "✓ Super admin created successfully!\n";
        echo "  Email: {$superAdminEmail}\n";
        echo "  Password: AdminChat2025!\n";
        echo "  Role: super_admin\n\n";
    } else {
        echo "✓ Super admin already exists\n\n";
    }

    // Display current admin accounts
    echo "=== CURRENT ADMIN ACCOUNTS ===\n";
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM admins ORDER BY created_at DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($admins as $admin) {
        echo "ID: {$admin['id']} | {$admin['name']} | {$admin['email']} | {$admin['role']} | {$admin['created_at']}\n";
    }

    echo "\n=== FIX COMPLETED SUCCESSFULLY ===\n";
    echo "You can now login at: https://your-app-url.railway.app/admin/login\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
