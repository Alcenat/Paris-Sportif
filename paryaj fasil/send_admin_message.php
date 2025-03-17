<?php
session_start();
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if (!is_superadmin()) die("Accès refusé");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, sender, sent_at) VALUES (?, ?, 'admin', NOW())");
        $stmt->execute([
            intval($_POST['user_id']),
            htmlspecialchars($_POST['message'])
        ]);
        header("Location: superadmin.php?action=messages&success=Message envoyé");
    } catch (PDOException $e) {
        header("Location: superadmin.php?action=messages&error=" . urlencode($e->getMessage()));
    }
    exit();
}