<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🎌Otaku🎴Nexus🎌</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Roboto:wght@300;500&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="nav-glow" style="padding: 1rem; text-align: center; display: flex; justify-content: space-between; align-items: center">
        <h1 style="font-family: 'Orbitron', sans-serif; color: #ff4d4d">🎌Otaku🎴Nexus🎌</h1>
        
        <div style="display: flex; gap: 1rem; align-items: center">
            <form method="GET" style="display: flex">
                <input type="text" name="q" placeholder="🔍 Rechercher..." 
                       style="padding: 10px; background: #0a0a15; border: 1px solid #2d2d42; color: white">
                <button type="submit" class="cyber-button" style="margin-left: 5px">Scan</button>
            </form>
            
            <?php if(is_logged_in()): ?>
                <a href="profile.php" class="cyber-button" style="padding: 10px 15px">Profil</a>
                <a href="logout.php" class="cyber-button" style="padding: 10px 15px">Déconnexion</a>
                <?php if(is_admin()): ?>
                    <a href="admin/dashboard.php" class="cyber-button" style="padding: 10px 15px">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="cyber-button" style="padding: 10px 15px">Connexion</a>
                <a href="register.php" class="cyber-button" style="padding: 10px 15px">Inscription</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="anime-grid">
        <?php
        $search = $_GET['q'] ?? '';
        $query = "SELECT s.*, 
                  (SELECT COUNT(*) FROM episodes WHERE series_id = s.id) AS episodes 
                  FROM series s 
                  WHERE s.titre LIKE ? 
                  ORDER BY s.created_at DESC";
        
        $stmt = $bdd->prepare($query);
        $stmt->execute(["%$search%"]);
        
        while($serie = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <a href="series.php?id=<?= $serie['id'] ?>" class="anime-card">
                <img src="thumbs/<?= $serie['miniature'] ?>" 
                     alt="<?= $serie['titre'] ?>" 
                     style="width: 100%; height: 350px; object-fit: cover">
                <div style="padding: 1rem">
                    <h3><?= htmlspecialchars($serie['titre']) ?></h3>
                    <div style="display: flex; justify-content: space-between">
                        <span>📺 <?= $serie['episodes'] ?> épisodes</span>
                        <span style="color: #ff4d4d">⚡</span>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</body>
</html>