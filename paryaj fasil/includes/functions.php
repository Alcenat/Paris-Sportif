<?php
// includes/functions.php

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

function clean_input($data) {
    return htmlspecialchars(trim($data));
}

function get_team_name($match_id, $team) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT $team FROM matches WHERE id = ?");
    $stmt->execute([$match_id]);
    return $stmt->fetchColumn();
}

function is_superadmin() {
    if (!isset($_SESSION['user'])) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']]);
    $user = $stmt->fetch();
    
    return ($user['email'] === 'superadmin@votresite.com');
}
?>