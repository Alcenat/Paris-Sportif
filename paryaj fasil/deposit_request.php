<?php
// deposit_request.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: deposit.php");
    exit();
}

// Correction ici : utiliser validate_csrf() au lieu de validate_csrf_token()
if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die("Token CSRF invalide.");
}

// Récupération et validation des données
$amount = floatval($_POST['amount']);
$method = $_POST['method'];
$payment_id = trim($_POST['payment_id']);
$comment = trim($_POST['comment']);

if ($amount <= 0) {
    header("Location: deposit.php?error=" . urlencode("Le montant doit être supérieur à zéro."));
    exit();
}
if (!in_array($method, ['moncash', 'paypal', 'wise'])) {
    header("Location: deposit.php?error=" . urlencode("Méthode de paiement invalide."));
    exit();
}
if (empty($payment_id)) {
    header("Location: deposit.php?error=" . urlencode("L'ID du paiement est requis."));
    exit();
}

// Gestion de l'upload de la preuve de paiement (obligatoire)
if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
    header("Location: deposit.php?error=" . urlencode("Erreur lors du téléchargement de la preuve de paiement."));
    exit();
}

$uploadDir = __DIR__ . '/uploads/deposits/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$fileName = time() . '_' . basename($_FILES['proof']['name']);
$targetFile = $uploadDir . $fileName;
if (!move_uploaded_file($_FILES['proof']['tmp_name'], $targetFile)) {
    header("Location: deposit.php?error=" . urlencode("Erreur lors de l'enregistrement de la preuve."));
    exit();
}
$proofPath = 'uploads/deposits/' . $fileName;

// Insertion de la demande dans la table deposits
$stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, payment_id, proof, comment, status, request_date) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
if ($stmt->execute([$_SESSION['user'], $amount, $method, $payment_id, $proofPath, $comment])) {
    header("Location: deposit.php?success=" . urlencode("Dépôt envoyé avec succès, en attente de validation."));
    exit();
} else {
    header("Location: deposit.php?error=" . urlencode("Erreur lors de l'enregistrement du dépôt."));
    exit();
}
?>
