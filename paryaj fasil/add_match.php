<?php
// add_match.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!is_superadmin()) {
    die("Accès refusé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'])) {
    $data = [
        'team1' => clean_input($_POST['team1']),
        'team2' => clean_input($_POST['team2']),
        'event_date' => $_POST['event_date'],
        'odds_team1' => floatval($_POST['odds_team1']),
        'odds_team2' => floatval($_POST['odds_team2']),
        'odds_draw' => floatval($_POST['odds_draw'])
    ];

    $stmt = $pdo->prepare("INSERT INTO matches 
        (team1, team2, event_date, odds_team1, odds_team2, odds_draw)
        VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        header("Location: superadmin.php?action=matches&success=Match ajouté");
    } else {
        header("Location: superadmin.php?action=matches&error=Erreur d'ajout");
    }
    exit();
}