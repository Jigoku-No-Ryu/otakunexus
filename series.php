<?php 
require 'config.php';

try {
    if (!isset($_GET['id']) || !($serie_id = filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
        throw new Exception("ID de série invalide");
    }

    $stmt_serie = $bdd->prepare("
        SELECT s.*, c.nom AS categorie 
        FROM series s
        JOIN categories c ON s.category_id = c.id
        WHERE s.id = ?
    ");
    $stmt_serie->execute([$serie_id]);
    $serie = $stmt_serie->fetch(PDO::FETCH_ASSOC);

    if (!$serie) {
        throw new Exception("Série non trouvée");
    }

    $stmt_episodes = $bdd->prepare("
        SELECT * FROM episodes 
        WHERE series_id = ? 
        ORDER BY numero ASC
    ");
    $stmt_episodes->execute([$serie_id]);
    $episodes = $stmt_episodes->fetchAll(PDO::FETCH_ASSOC);

} catch(Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($serie['titre']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div style="max-width: 1200px; margin: 2rem auto">
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem">
            <img src="thumbs/<?= $serie['miniature'] ?>" 
                 style="border-radius: 12px; border: 2px solid #2d2d42">
            
            <div>
                <h1 style="font-family: 'Orbitron', sans-serif"><?= htmlspecialchars($serie['titre']) ?></h1>
                <div style="background: #151525; padding: 1rem; border-radius: 8px">
                    <p><?= nl2br(htmlspecialchars($serie['description'])) ?></p>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem">
                        <span style="color: #ff4d4d">Catégorie : <?= htmlspecialchars($serie['categorie']) ?></span>
                        <span>🗓️ <?= date('Y', strtotime($serie['created_at'])) ?></span>
                    </div>
                </div>

                <div style="margin-top: 2rem">
                    <h2>Épisodes :</h2>
                    <?php foreach($episodes as $ep): ?>
                        <a href="watch.php?id=<?= $ep['id'] ?>" 
                           style="display: block; 
                                  padding: 1rem; 
                                  margin-bottom: 0.5rem; 
                                  background: #151525; 
                                  border-radius: 8px;
                                  transition: transform 0.3s">
                            <div style="display: flex; justify-content: space-between">
                                <span>🎬 Épisode <?= $ep['numero'] ?> - <?= htmlspecialchars($ep['titre']) ?></span>
                                <span style="color: #ff4d4d">👁️ <?= $ep['views'] ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>