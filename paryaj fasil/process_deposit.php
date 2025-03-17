<?php
session_start();
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if (!is_superadmin()) die("Accès refusé");

$id = intval($_GET['id']);
$action = $_GET['action'] ?? '';

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE id = ?");
    $stmt->execute([$id]);
    $deposit = $stmt->fetch();
    $stmt = $pdo->prepare("UPDATE deposits SET 
    status = ?,
    processed_date = NOW()
    WHERE id = ?");

    if ($deposit && in_array($action, ['approve', 'reject'])) {
        $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
        
        $stmt = $pdo->prepare("UPDATE deposits SET status = ?, processed_date = NOW() WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $stmt->execute([$deposit['amount'], $deposit['user_id']]);
        }

        $pdo->commit();
        header("Location: superadmin.php?action=deposits&success=Action effectuée");
    }
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: superadmin.php?action=deposits&error=" . urlencode($e->getMessage()));
}
exit();