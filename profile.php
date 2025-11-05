<?php 
require 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

$stmt = $bdd->prepare("
    SELECT e.*, s.titre AS serie_titre 
    FROM history h
    JOIN episodes e ON h.episode_id = e.id
    JOIN series s ON e.series_id = s.id
    WHERE h.user_id = ?
    ORDER BY h.watched_at DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mon Profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div style="max-width: 800px; margin: 2rem auto">
        <h1 style="color: #ff4d4d">👤 <?= htmlspecialchars($_SESSION['username']) ?></h1>
        
        <div class="user-info" style="background: #151525; padding: 1.5rem; border-radius: 12px; margin: 2rem 0">
            <h2>Historique de visionnage</h2>
            
            <?php if(!empty($history)): ?>
                <?php foreach($history as $item): ?>
                    <div style="padding: 1rem; border-bottom: 1px solid #2d2d42">
                        <a href="watch.php?id=<?= $item['id'] ?>">
                            <h3><?= htmlspecialchars($item['serie_titre']) ?></h3>
                            <p>Épisode <?= $item['numero'] ?> : <?= htmlspecialchars($item['titre']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun historique de visionnage</p>
            <?php endif; ?>
        </div>
        
        <a href="logout.php" class="cyber-button">Déconnexion</a>
    </div>
</body>
</html>