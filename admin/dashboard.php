<?php 
require '../config.php';

if(!is_admin()) {
    redirect('../login.php');
}

$stats = [
    'series' => $bdd->query("SELECT COUNT(*) FROM series")->fetchColumn(),
    'episodes' => $bdd->query("SELECT COUNT(*) FROM episodes")->fetchColumn(),
    'users' => $bdd->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'views' => $bdd->query("SELECT SUM(views) FROM episodes")->fetchColumn()
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: #0a0a15;
            border-bottom: 2px solid #ff4d4d;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }
        .admin-card {
            background: linear-gradient(45deg, #111126, #1a1a1a);
            border: 1px solid #2d2d42;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s;
            cursor: pointer;
        }
        .admin-card:hover {
            transform: translateY(-5px);
            border-color: #ff4d4d;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .stat-card {
            background: #151525;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🛡️ Tableau de bord Admin</h1>
        <a href="../logout.php" class="cyber-button">Déconnexion</a>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <h3>📺 Séries</h3>
            <p><?= $stats['series'] ?></p>
        </div>
        <div class="stat-card">
            <h3>🎬 Épisodes</h3>
            <p><?= $stats['episodes'] ?></p>
        </div>
        <div class="stat-card">
            <h3>👥 Utilisateurs</h3>
            <p><?= $stats['users'] ?></p>
        </div>
        <div class="stat-card">
            <h3>👁️ Vues totales</h3>
            <p><?= $stats['views'] ?></p>
        </div>
    </div>

    <div class="admin-grid">
        <a href="upload.php" class="admin-card">
            <h2>📤 Upload de contenu</h2>
            <p>Gérer séries/épisodes</p>
        </a>
        
        <a href="users.php" class="admin-card">
            <h2>👥 Gestion utilisateurs</h2>
            <p>Voir/modifier les comptes</p>
        </a>
        
        <a href="stats.php" class="admin-card">
            <h2>📈 Statistiques détaillées</h2>
            <p>Analytique du site</p>
        </a>
    </div>
</body>
</html>