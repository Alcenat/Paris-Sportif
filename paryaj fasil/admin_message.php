<?php
// send_admin_message.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!is_superadmin()) {
    die("Accès refusé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'])) {
    $data = [
        'user_id' => intval($_POST['user_id']),
        'message' => clean_input($_POST['message']),
        'sender' => 'admin'
    ];

    $stmt = $pdo->prepare("INSERT INTO messages 
        (user_id, message, sender, sent_at)
        VALUES (?, ?, ?, NOW())");
    
    if ($stmt->execute([$data['user_id'], $data['message'], $data['sender']])) {
        header("Location: superadmin.php?action=messages&success=Message envoyé");
    } else {
        header("Location: superadmin.php?action=messages&error=Erreur d'envoi");
    }
    exit();
}