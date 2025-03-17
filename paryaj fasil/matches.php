<?php
// matches.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les matchs à venir
$stmt = $pdo->query("SELECT * FROM matches WHERE event_date >= NOW() ORDER BY event_date ASC");
$matches = $stmt->fetchAll();
$csrf_token = generate_csrf_token();

// Dans matches.php
$category = $_GET['category'] ?? 'all';
$query = "SELECT * FROM matches WHERE event_date >= NOW()";

if(in_array($category, ['football', 'basketball', 'tennis', 'autres'])) {
    $query .= " AND category = '$category'";
}

$matches = $pdo->query($query . " ORDER BY event_date ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matches Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1>Matches Disponibles</h1>
        <?php if (isset($_GET['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <?php if (count($matches) === 0): ?>
            <p>Aucun match disponible pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Date</th>
                        <th>Cote <?php echo htmlspecialchars('Équipe 1'); ?></th>
                        <th>Cote <?php echo htmlspecialchars('Équipe 2'); ?></th>
                        <th>Cote Match Nul</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($match['team1']) . " vs " . htmlspecialchars($match['team2']); ?></td>
                        <td><?php echo htmlspecialchars($match['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($match['odds_team1']); ?></td>
                        <td><?php echo htmlspecialchars($match['odds_team2']); ?></td>
                        <td><?php echo htmlspecialchars($match['odds_draw']); ?></td>
                        <td>
                            <form action="place_bet.php" method="post">
                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <label for="bet_amount_<?php echo $match['id']; ?>">Mise :</label>
                                <input type="number" name="bet_amount" id="bet_amount_<?php echo $match['id']; ?>" min="1" required>
                                <label for="chosen_team_<?php echo $match['id']; ?>">Choix :</label>
                               
<select name="chosen_team" class="form-select" required>
    <option value="team1"><?= htmlspecialchars($match['team1']) ?></option>
    <option value="team2"><?= htmlspecialchars($match['team2']) ?></option>
    <option value="draw">Match nul</option>

</select>
<div class="mb-4">
    <div class="btn-group">
        <a href="?category=football" class="btn btn-outline-primary <?= $category === 'football' ? 'active' : '' ?>">
            Football
        </a>
        <a href="?category=basketball" class="btn btn-outline-primary <?= $category === 'basketball' ? 'active' : '' ?>">
            Basketball
        </a>
        <a href="?category=tennis" class="btn btn-outline-primary <?= $category === 'tennis' ? 'active' : '' ?>">
            Tennis
        </a>
        <a href="?category=autres" class="btn btn-outline-primary <?= $category === 'autres' ? 'active' : '' ?>">
            Autres
        </a>
    </div>
</div>                        <button type="submit">Parier</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
