<?php
// withdraw_request.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw.php");
    exit();
}

// CORRECTION : Utiliser validate_csrf() au lieu de validate_csrf_token()
if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die("Token CSRF invalide.");
}
// Récupération des données
$method = $_POST['method'] ?? '';
$message = trim($_POST['message'] ?? '');

// Validation du mode de paiement
if (!in_array($method, ['moncash', 'paypal', 'wise'])) {
    header("Location: withdraw.php?error=" . urlencode("Méthode de paiement invalide."));
    exit();
}

// Gestion de la preuve de paiement (upload facultatif)
$proofFilePath = null;
if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = basename($_FILES['proof']['name']);
    $targetFile = $uploadDir . time() . "_" . $fileName;
    if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFile)) {
        // Enregistrer le chemin relatif pour la base
        $proofFilePath = 'uploads/' . time() . "_" . $fileName;
    } else {
        header("Location: withdraw.php?error=" . urlencode("Erreur lors du téléchargement du fichier."));
        exit();
    }
}

// Insertion de la demande dans la table withdrawals
$stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, method, payment_proof, message, request_date) VALUES (?, ?, ?, ?, NOW())");
if ($stmt->execute([$_SESSION['user'], $method, $proofFilePath, $message])) {
    header("Location: withdraw.php?success=" . urlencode("Demande de retrait envoyée avec succès."));
    exit();
} else {
    header("Location: withdraw.php?error=" . urlencode("Erreur lors de l'enregistrement de la demande."));
    exit();
}
?>
