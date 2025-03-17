<?php
// deposit.php
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
    <title>Dépôt</title>
    <!-- Inclusion de Bootstrap pour un design moderne -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h1>Dépôt</h1>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php elseif(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <form action="deposit_request.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="mb-3">
                <label for="amount" class="form-label">Montant à déposer :</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="Entrez le montant" required>
            </div>
            
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
                <label id="paymentDetailsLabel" class="form-label">Détails du paiement :</label>
                <p id="paymentDetails" class="form-text"></p>
            </div>
            
            <div class="mb-3">
                <label for="payment_id" class="form-label">ID du paiement :</label>
                <input type="text" name="payment_id" id="payment_id" class="form-control" placeholder="Entrez l'ID du paiement" required>
            </div>
            
            <div class="mb-3">
                <label for="proof" class="form-label">Preuve de paiement (photo) :</label>
                <input type="file" name="proof" id="proof" class="form-control" accept="image/*" required>
            </div>
            
            <div class="mb-3">
                <label for="comment" class="form-label">Commentaire (optionnel) :</label>
                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Ajouter une remarque"></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Envoyer le dépôt</button>
        </form>
    </div>
    <script>
        // Mise à jour dynamique des instructions en fonction de la méthode choisie
        document.getElementById('method').addEventListener('change', function() {
            var method = this.value;
            var detailsText = "";
            switch(method) {
                case "moncash":
                    detailsText = "Pour Moncash, envoyez votre paiement au numéro +50947326771.";
                    break;
                case "paypal":
                    detailsText = "Pour PayPal, envoyez votre paiement à l'adresse alcenat2001@gmail.com.";
                    break;
                case "wise":
                    detailsText = "Pour Wise, envoyez votre paiement à l'adresse casquettela@gmail.com.";
                    break;
                default:
                    detailsText = "";
            }
            document.getElementById("paymentDetails").innerText = detailsText;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
