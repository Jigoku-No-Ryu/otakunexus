<?php
require 'config.php';

// Supprimer les entrées d'historique avec des user_id qui n'existent pas
$clean_query = $bdd->prepare("
    DELETE h FROM history h
    LEFT JOIN users u ON h.user_id = u.id
    WHERE u.id IS NULL
");

$deleted_count = $clean_query->rowCount();
echo "Entrées d'historique nettoyées: " . $deleted_count;

// Vérifier les user_id dans la session
if (isset($_SESSION['user_id'])) {
    $check_session_user = $bdd->prepare("SELECT id FROM users WHERE id = ?");
    $check_session_user->execute([$_SESSION['user_id']]);
    
    if ($check_session_user->rowCount() === 0) {
        // L'utilisateur de la session n'existe plus
        session_destroy();
        echo "Session nettoyée - utilisateur inexistant";
    }
}
?>