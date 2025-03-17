<?php
// update_withdrawal.php
session_start();
require 'includes/config.php';

// Vérification de la connexion et des droits d'accès
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user']]);
$currentUser = $stmt->fetch();
if ($currentUser['email'] !== 'superadmin@votresite.com') {
    die("Accès refusé");
}

// Vérification des paramètres
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("Paramètres invalides.");
}
$withdrawalId = intval($_GET['id']);
$action = $_GET['action'];
if (!in_array($action, ['approve', 'reject'])) {
    die("Action invalide.");
}
$newStatus = $action === 'approve' ? 'approved' : 'rejected';

// Mise à jour du statut
$stmt = $pdo->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
if ($stmt->execute([$newStatus, $withdrawalId])) {
    header("Location: superadmin.php?message=" . urlencode("Statut de la demande mis à jour"));
    exit();
} else {
    die("Erreur lors de la mise à jour du statut.");
}
?>
