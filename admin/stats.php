<?php
require '../config.php';

if(!is_admin()) {
    redirect('../login.php');
}

$popular_series = $bdd->query("
    SELECT s.id, s.titre, COUNT(h.id) AS views 
    FROM history h
    JOIN episodes e ON h.episode_id = e.id
    JOIN series s ON e.series_id = s.id
    GROUP BY s.id
    ORDER BY views DESC
    LIMIT 5
")->fetchAll();

$active_users = $bdd->query("
    SELECT u.username, COUNT(h.id) AS views 
    FROM history h
    JOIN users u ON h.user_id = u.id
    GROUP BY u.id
    ORDER BY views DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistiques</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-header">
        <h1>📈 Statistiques</h1>
        <a href="dashboard.php" class="cyber-button">Retour</a>
    </div>

    <div style="max-width: 1200px; margin: 2rem auto; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem">
        <div style="background: #151525; padding: 1.5rem; border-radius: 12px">
            <h2 style="color: #ff4d4d; margin-bottom: 1rem">Séries populaires</h2>
            <ul style="list-style-type: none; padding: 0">
                <?php foreach($popular_series as $serie): ?>
                    <li style="padding: 0.5rem; border-bottom: 1px solid #2d2d42">
                        <?= htmlspecialchars($serie['titre']) ?> 
                        <span style="color: #ff4d4d; float: right"><?= $serie['views'] ?> vues</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div style="background: #151525; padding: 1.5rem; border-radius: 12px">
            <h2 style="color: #ff4d4d; margin-bottom: 1rem">Utilisateurs actifs</h2>
            <ul style="list-style-type: none; padding: 0">
                <?php foreach($active_users as $user): ?>
                    <li style="padding: 0.5rem; border-bottom: 1px solid #2d2d42">
                        <?= htmlspecialchars($user['username']) ?> 
                        <span style="color: #ff4d4d; float: right"><?= $user['views'] ?> vues</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>