<?php
function addToHistory($bdd, $user_id, $episode_id) {
    try {
        // Vérifier que l'utilisateur existe
        $check_user = $bdd->prepare("SELECT id FROM users WHERE id = ?");
        $check_user->execute([$user_id]);
        
        if ($check_user->rowCount() === 0) {
            error_log("Tentative d'ajout historique avec user_id inexistant: " . $user_id);
            return false;
        }
        
        // Vérifier que l'épisode existe
        $check_episode = $bdd->prepare("SELECT id FROM episodes WHERE id = ?");
        $check_episode->execute([$episode_id]);
        
        if ($check_episode->rowCount() === 0) {
            error_log("Tentative d'ajout historique avec episode_id inexistant: " . $episode_id);
            return false;
        }
        
        // Insérer ou mettre à jour l'historique
        $query = $bdd->prepare("
            INSERT INTO history (user_id, episode_id, watched_at) 
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE watched_at = NOW()
        ");
        
        return $query->execute([$user_id, $episode_id]);
        
    } catch (PDOException $e) {
        error_log("Erreur ajout historique: " . $e->getMessage());
        return false;
    }
}
?>