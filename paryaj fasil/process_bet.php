<?php
session_start();
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if (!is_superadmin()) {
    die("Accès refusé");
}

// Vérification CSRF
if (!isset($_GET['csrf_token']) || !validate_csrf($_GET['csrf_token'])) {
    die("Token CSRF invalide !");
}

$bet_id = intval($_GET['id']);
$result = $_GET['result'] === 'win' ? 'won' : 'lost';

try {
    $pdo->beginTransaction();
    
    // Récupérer les infos du pari
    $stmt = $pdo->prepare("
        SELECT b.*, u.points 
        FROM bets b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bet_id]);
    $bet = $stmt->fetch();

    if ($bet) {
        // Mettre à jour le statut du pari
        $stmt = $pdo->prepare("UPDATE bets SET status = ? WHERE id = ?");
        $stmt->execute([$result, $bet_id]);

        // Si victoire, créditer le compte
        if ($result === 'won') {
            $new_balance = $bet['points'] + $bet['potential_gain'];
            $stmt = $pdo->prepare("UPDATE users SET points = ? WHERE id = ?");
            $stmt->execute([$new_balance, $bet['user_id']]);
        }

        $pdo->commit();
        header("Location: superadmin.php?action=bets&success=Paris mis à jour");
    }
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: superadmin.php?action=bets&error=" . urlencode($e->getMessage()));
}
// Après validation réussie dans process_bet.php
$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Régénération
exit();