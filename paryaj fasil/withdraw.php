<?php
// withdraw.php
session_start();
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Retrait</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h1>Demande de Retrait</h1>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php elseif(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <form action="withdraw_request.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="mb-3">
                <label for="method" class="form-label">Méthode de paiement :</label>
                <select name="method" id="method" class="form-select" required>
                    <option value="">Choisissez...</option>
                    <option value="moncash">Moncash</option>
                    <option value="paypal">PayPal</option>
                    <option value="wise">Wise</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="destination" id="destinationLabel" class="form-label">Coordonnée :</label>
                <input type="text" name="destination" id="destination" class="form-control" placeholder="Entrez votre info" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Montant à retirer :</label>
                <input type="number" name="amount" id="amount" class="form-control" step="0.01" placeholder="Ex : 50.00" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message / Commentaire (optionnel) :</label>
                <textarea name="message" id="message" rows="4" class="form-control" placeholder="Ajouter une remarque..."></textarea>
            </div>
            <!-- Vous pouvez garder ou retirer l'upload de preuve si nécessaire -->
            <!-- <div class="mb-3">
                <label for="proof" class="form-label">Preuve de paiement (optionnel) :</label>
                <input type="file" name="proof" id="proof" class="form-control" accept="image/*,application/pdf">
            </div> -->
            <button type="submit" class="btn btn-primary">Envoyer la demande</button>
        </form>
    </div>
    <script>
        // Mise à jour dynamique du label et du placeholder pour le champ "destination"
        document.getElementById('method').addEventListener('change', function() {
            var method = this.value;
            var destinationLabel = document.getElementById('destinationLabel');
            var destinationField = document.getElementById('destination');
            var instructions = "";
            switch(method) {
                case 'moncash':
                    destinationLabel.innerText = "Numéro Moncash :";
                    destinationField.placeholder = "Ex : +509XXXXXXXX";
                    break;
                case 'paypal':
                    destinationLabel.innerText = "Adresse email PayPal :";
                    destinationField.placeholder = "Ex : votreadresse@gmail.com";
                    break;
                case 'wise':
                    destinationLabel.innerText = "Adresse email Wise :";
                    destinationField.placeholder = "Ex : votreadresse@wise.com";
                    break;
                default:
                    destinationLabel.innerText = "Coordonnée :";
                    destinationField.placeholder = "Entrez votre info";
            }
        });
    </script>
</body>
</html>
