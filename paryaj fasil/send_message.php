<?php
// send_message.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    die("Token CSRF invalide.");
}

$message = trim($_POST['message']);
if (empty($message)) {
    header("Location: dashboard.php?error=" . urlencode("Le message ne peut être vide."));
    exit();
}

// Insertion du message dans la table "messages" (l'expéditeur est ici 'user')
$stmt = $pdo->prepare("INSERT INTO messages (user_id, sender, message, sent_at) VALUES (?, 'user', ?, NOW())");
if ($stmt->execute([$_SESSION['user'], $message])) {
    header("Location: dashboard.php?success=" . urlencode("Message envoyé."));
    exit();
} else {
    header("Location: dashboard.php?error=" . urlencode("Erreur lors de l'envoi du message."));
    exit();
}
?>
