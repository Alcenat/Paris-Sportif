<?php
// register.php
session_start();
require 'includes/config.php';

// Gestion du lien de parrainage
if (isset($_GET['ref'])) {
    $referrer = $_GET['ref'];
    $stmt = $pdo->prepare("UPDATE users SET referrals = referrals + 1, points = points + 10 WHERE unique_id = ?");
    $stmt->execute([$referrer]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Vérifications basiques
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    }
    if (empty($password)) {
        $error = "Le mot de passe est requis.";
    }
    if (!isset($error)) {
        // Hachage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // Génération d'un identifiant unique
        $unique_id = uniqid('user_', true);
        // Insérer l'utilisateur en définissant par défaut un compte Bronze, 0 points et 0 parrainages
        $stmt = $pdo->prepare("INSERT INTO users (email, password, unique_id, account_type, points, last_login, referrals) VALUES (?, ?, ?, 'bronze', 0, NOW(), 0)");
        if ($stmt->execute([$email, $passwordHash, $unique_id])) {
            $_SESSION['user'] = $pdo->lastInsertId();
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1>Inscription</h1>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="register.php<?php echo isset($_GET['ref']) ? '?ref='.$_GET['ref'] : ''; ?>">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
            <br>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <br>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
