<?php
// dashboard.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Récupération des informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user']]);
$user = $stmt->fetch();

// Récupération des paris de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM bets WHERE user_id = ? ORDER BY bet_date DESC");
$stmt->execute([$user['id']]);
$bets = $stmt->fetchAll();

// Récupération des messages de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY sent_at DESC");
$stmt->execute([$user['id']]);
$messages = $stmt->fetchAll();

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <!-- Inclusion de Bootstrap pour un design moderne -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-3">Bienvenue, <?php echo htmlspecialchars($user['email']); ?></h1>
        <div class="alert alert-info">
            <strong>Solde actuel :</strong> <?php echo number_format($user['points'], 2); ?> €
        </div>
        
        <!-- Boutons d'accès aux pages Dépôt et Retrait -->
        <div class="mb-4">
            <a href="deposit.php" class="btn btn-success me-2">Dépôt</a>
            <a href="withdraw.php" class="btn btn-warning">Retrait</a>
        </div>
        
        <!-- Catégories de Paris -->
        <h2>Catégories de Paris</h2>
        <div class="mb-4">
            <a href="matches.php?category=football" class="btn btn-primary me-2">Football</a>
            <a href="matches.php?category=basketball" class="btn btn-primary me-2">Basketball</a>
            <a href="matches.php?category=tennis" class="btn btn-primary me-2">Tennis</a>
            <a href="matches.php?category=autres" class="btn btn-primary">Autres</a>
        </div>
        
        <!-- Section Mes Paris -->
        <h2>Mes Paris</h2>
        <?php if(count($bets) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Match</th>
                        <th>Mise</th>
                        <th>Choix</th>
                        <th>Gain Potentiel</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bets as $bet): ?>
                    <tr>
                        <td><?php echo $bet['id']; ?></td>
                        <td>
                            <?php 
                            // Vous pouvez ici effectuer un JOIN pour récupérer les détails du match
                            echo "Match #" . $bet['match_id'];
                            ?>
                        </td>
                        <td><?php echo number_format($bet['bet_amount'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($bet['chosen_team']); ?></td>
                        <td><?php echo number_format($bet['potential_gain'], 2); ?> €</td>
                        <td><?php echo $bet['bet_date']; ?></td>
                        <td><?php echo htmlspecialchars($bet['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun pari enregistré pour le moment.</p>
        <?php endif; ?>
        
        <!-- Section Messagerie -->
        <h2>Messagerie</h2>
        <div class="mb-4">
            <?php if(count($messages) > 0): ?>
                <ul class="list-group">
                    <?php foreach($messages as $msg): ?>
                    <li class="list-group-item">
                        <strong><?php echo ucfirst($msg['sender']); ?>:</strong> 
                        <?php echo htmlspecialchars($msg['message']); ?>
                        <br>
                        <small class="text-muted"><?php echo $msg['sent_at']; ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun message pour le moment.</p>
            <?php endif; ?>
        </div>
        <!-- Formulaire pour envoyer un message -->
        <h3>Envoyer un message</h3>
        <form action="send_message.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="mb-3">
                <textarea name="message" class="form-control" rows="3" placeholder="Votre message..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>
    
    <!-- Inclusion de Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
