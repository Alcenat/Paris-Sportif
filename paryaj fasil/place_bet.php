<?php
// place_bet.php
session_start();
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: matches.php");
    exit();
}

// Correction du nom de fonction ici ▼
if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die("Token CSRF invalide.");
}

$match_id = intval($_POST['match_id']);
$bet_amount = floatval($_POST['bet_amount']);
$chosen_team = $_POST['chosen_team'];

// Vérifier que le match existe
$stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
$stmt->execute([$match_id]);
$match = $stmt->fetch();
if (!$match) {
    die("Match non trouvé.");
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user']]);
$user = $stmt->fetch();
if (!$user) {
    die("Utilisateur non trouvé.");
}

// Vérifier que l'utilisateur dispose d'un solde suffisant (ici, les points)
if ($user['points'] < $bet_amount) {
    die("Solde insuffisant pour parier.");
}

// Calculer le gain potentiel en fonction de la cote sélectionnée
switch ($chosen_team) {
    case 'team1':
        $odds = floatval($match['odds_team1']);
        break;
    case 'team2':
        $odds = floatval($match['odds_team2']);
        break;
    case 'draw':
        $odds = floatval($match['odds_draw']);
        break;
    default:
        die("Choix invalide.");
}
$potential_gain = $bet_amount * $odds;

// Insérer le pari dans la base de données
$stmt = $pdo->prepare("INSERT INTO bets (user_id, match_id, bet_amount, chosen_team, potential_gain, bet_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'pending')");
if (!$stmt->execute([$user['id'], $match_id, $bet_amount, $chosen_team, $potential_gain])) {
    die("Erreur lors du placement du pari.");
}

// Déduire la mise du solde de l'utilisateur
$stmt = $pdo->prepare("UPDATE users SET points = points - ? WHERE id = ?");
$stmt->execute([$bet_amount, $user['id']]);

header("Location: matches.php?message=Pari placé avec succès.");
exit();
?>
