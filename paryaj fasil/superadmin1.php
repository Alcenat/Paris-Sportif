<?php
// superadmin.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

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

// Gestion des actions
$action = $_GET['action'] ?? 'dashboard';

// Récupération des données
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$withdrawals = $pdo->query("SELECT w.*, u.email FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY request_date DESC")->fetchAll();
$deposits = $pdo->query("SELECT d.*, u.email FROM deposits d JOIN users u ON d.user_id = u.id ORDER BY request_date DESC")->fetchAll();
$matches = $pdo->query("SELECT * FROM matches ORDER BY event_date DESC")->fetchAll();
$messages = $pdo->query("SELECT m.*, u.email FROM messages m JOIN users u ON m.user_id = u.id ORDER BY sent_at DESC")->fetchAll();

// Statistiques
$totalUsers = count($users);
$pendingWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
$pendingDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'dashboard' ? 'active' : '' ?>" href="?action=dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'users' ? 'active' : '' ?>" href="?action=users">
                                <i class="fas fa-users me-2"></i>
                                Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'matches' ? 'active' : '' ?>" href="?action=matches">
                                <i class="fas fa-futbol me-2"></i>
                                Matchs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'withdrawals' ? 'active' : '' ?>" href="?action=withdrawals">
                                <i class="fas fa-money-check-alt me-2"></i>
                                Retraits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'deposits' ? 'active' : '' ?>" href="?action=deposits">
                                <i class="fas fa-coins me-2"></i>
                                Dépôts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $action === 'messages' ? 'active' : '' ?>" href="?action=messages">
                                <i class="fas fa-envelope me-2"></i>
                                Messages
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php if($action === 'dashboard'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Tableau de bord</h1>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Utilisateurs</h5>
                                    <p class="card-text display-4"><?= $totalUsers ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Retraits en attente</h5>
                                    <p class="card-text display-4"><?= $pendingWithdrawals ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Dépôts en attente</h5>
                                    <p class="card-text display-4"><?= $pendingDeposits ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif($action === 'users'): ?>
                    <!-- Gestion des utilisateurs (existant) -->
                    <?php elseif($action === 'users'): ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestion des utilisateurs</h1>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= clean_input($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Solde</th>
                    <th>Dernière connexion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= clean_input($user['email']) ?></td>
                    <td><?= $user['points'] ?> €</td>
                    <td><?= $user['last_login'] ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_user.php?id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
                    <!-- Ajouter des boutons de modification/suppression -->

                <?php elseif($action === 'matches'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Gestion des matchs</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMatchModal">
                            <i class="fas fa-plus me-2"></i>Ajouter un match
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Équipe 1</th>
                                    <th>Équipe 2</th>
                                    <th>Date</th>
                                    <th>Cotes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($matches as $match): ?>
                                <tr>
                                    <td><?= htmlspecialchars($match['team1']) ?></td>
                                    <td><?= htmlspecialchars($match['team2']) ?></td>
                                    <td><?= $match['event_date'] ?></td>
                                    <td>
                                        <?= $match['odds_team1'] ?> / 
                                        <?= $match['odds_draw'] ?> / 
                                        <?= $match['odds_team2'] ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editMatchModal"
                                                data-match='<?= json_encode($match) ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="delete_match.php?id=<?= $match['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Supprimer ce match ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modals pour l'ajout/édition de matchs -->
                    <?php include 'includes/match_modals.php'; ?>

                <?php elseif($action === 'deposits'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Demandes de dépôt</h1>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($deposits as $deposit): ?>
                                <tr>
                                    <td><?= htmlspecialchars($deposit['email']) ?></td>
                                    <td><?= $deposit['amount'] ?> €</td>
                                    <td><?= ucfirst($deposit['method']) ?></td>
                                    <td><?= $deposit['request_date'] ?></td>
                                    <td><?= $deposit['status'] ?></td>
                                    <td>
                                        <?php if($deposit['status'] === 'pending'): ?>
                                            <a href="process_deposit.php?id=<?= $deposit['id'] ?>&action=approve" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="process_deposit.php?id=<?= $deposit['id'] ?>&action=reject" 
                                               class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php elseif($action === 'messages'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Messagerie</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer un message
                        </button>
                    </div>

                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Destinataire</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Expéditeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($messages as $msg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($msg['email']) ?></td>
                                    <td><?= htmlspecialchars($msg['message']) ?></td>
                                    <td><?= $msg['sent_at'] ?></td>
                                    <td><?= ucfirst($msg['sender']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal d'envoi de message -->
                    <div class="modal fade" id="sendMessageModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="send_admin_message.php" method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Nouveau message</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Destinataire</label>
                                            <select name="user_id" class="form-select" required>
                                                <?php foreach($users as $user): ?>
                                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['email']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Message</label>
                                            <textarea name="message" class="form-control" rows="5" required></textarea>
                                        </div>
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Envoyer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour l'édition des matchs
        const editMatchModal = document.getElementById('editMatchModal');
        if(editMatchModal) {
            editMatchModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const matchData = JSON.parse(button.dataset.match);
                
                document.getElementById('team1').value = matchData.team1;
                document.getElementById('team2').value = matchData.team2;
                document.getElementById('event_date').value = matchData.event_date.replace(' ', 'T');
                document.getElementById('odds_team1').value = matchData.odds_team1;
                document.getElementById('odds_draw').value = matchData.odds_draw;
                document.getElementById('odds_team2').value = matchData.odds_team2;
            });
        }
    </script>
</body>
</html>