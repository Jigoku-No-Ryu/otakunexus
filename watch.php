<?php
require 'config.php';

try {
    if (!isset($_GET['id']) || !($episode_id = filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
        throw new Exception("ID d'épisode invalide");
    }

    $stmt = $bdd->prepare("
        SELECT e.*, s.titre AS serie_titre, s.miniature 
        FROM episodes e
        JOIN series s ON e.series_id = s.id 
        WHERE e.id = ?
    ");
    $stmt->execute([$episode_id]);
    $episode = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$episode) {
        throw new Exception("Épisode non trouvé");
    }

    // Mettre à jour le compteur de vues
    $update = $bdd->prepare("UPDATE episodes SET views = views + 1 WHERE id = ?");
    $update->execute([$episode_id]);
    
    // Gestion de l'historique avec vérification de l'utilisateur
    if(is_logged_in()) {
        try {
            // Vérifier d'abord que l'utilisateur existe dans la base
            $check_user = $bdd->prepare("SELECT id FROM users WHERE id = ?");
            $check_user->execute([$_SESSION['user_id']]);
            
            if ($check_user->rowCount() > 0) {
                // L'utilisateur existe, on peut ajouter à l'historique
                $insert = $bdd->prepare("
                    INSERT INTO history (user_id, episode_id, watched_at) 
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE watched_at = NOW()
                ");
                $insert->execute([$_SESSION['user_id'], $episode_id]);
            } else {
                // L'utilisateur n'existe pas dans la base - nettoyer la session
                error_log("Utilisateur non trouvé en base: " . $_SESSION['user_id']);
                session_destroy();
            }
            
        } catch (PDOException $e) {
            // Logger l'erreur mais ne pas bloquer la lecture
            error_log("Erreur historique: " . $e->getMessage());
        }
    }

} catch(Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($episode['serie_titre']) ?> - Épisode <?= $episode['numero'] ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.10/dist/hls.min.js"></script>
    <style>
        .video-player {
            background: #0a0a15;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(255, 77, 77, 0.2);
            margin-bottom: 2rem;
        }
        
        .episode-info {
            background: #151525;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #2d2d42;
        }
        
        .episode-title {
            color: #ff4d4d;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .episode-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            color: #aaaaaa;
        }
        
        .download-btn {
            background: linear-gradient(45deg, #ff4d4d, #ff6b6b);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.3s ease;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem">
        <!-- Bouton retour -->
        <a href="series.php?id=<?= $episode['series_id'] ?>" 
           class="cyber-button"
           style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 2rem; text-decoration: none">
           <span>←</span> Retour à la série
        </a>

        <!-- Lecteur vidéo -->
        <div class="video-player">
            <video id="animePlayer" 
                   controls 
                   style="width: 100%; height: auto; max-height: 70vh;"
                   poster="thumbs/<?= htmlspecialchars($episode['miniature']) ?>">
                Votre navigateur ne supporte pas la lecture de vidéos.
            </video>
        </div>

        <!-- Informations de l'épisode -->
        <div class="episode-info">
            <h1 class="episode-title">
                <?= htmlspecialchars($episode['serie_titre']) ?> - Épisode <?= $episode['numero'] ?>
            </h1>
            
            <?php if(!empty($episode['titre'])): ?>
                <h3 style="color: #ffffff; margin-bottom: 1rem">
                    <?= htmlspecialchars($episode['titre']) ?>
                </h3>
            <?php endif; ?>
            
            <div class="episode-meta">
                <span style="display: flex; align-items: center; gap: 5px">
                    👁️ <?= ($episode['views'] + 1) ?> vues
                </span>
                
                <?php if(!empty($episode['duree'])): ?>
                    <span style="display: flex; align-items: center; gap: 5px">
                        ⏱️ <?= htmlspecialchars($episode['duree']) ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if(!empty($episode['description'])): ?>
                <div style="color: #cccccc; line-height: 1.6; margin-bottom: 1.5rem">
                    <?= nl2br(htmlspecialchars($episode['description'])) ?>
                </div>
            <?php endif; ?>
            
            <!-- Lien de téléchargement -->
            <div style="margin-top: 1.5rem">
                <a href="videos/<?= htmlspecialchars($episode['fichier']) ?>" 
                   download="<?= htmlspecialchars($episode['serie_titre']) ?> - Episode <?= $episode['numero'] ?>.<?= pathinfo($episode['fichier'], PATHINFO_EXTENSION) ?>"
                   class="download-btn">
                   📥 Télécharger l'épisode
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('animePlayer');
            const videoSrc = "videos/<?= htmlspecialchars($episode['fichier']) ?>";
            const extension = videoSrc.split('.').pop().toLowerCase();
            
            // Formats supportés nativement
            const nativeFormats = ['mp4', 'webm', 'ogg'];
            
            function setupNativePlayer() {
                video.innerHTML = `
                    <source src="${videoSrc}" type="<?= getMimeType($episode['fichier']) ?>">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                `;
                video.load();
            }
            
            function setupHLSPlayer() {
                if (Hls.isSupported()) {
                    const hls = new Hls({
                        debug: false,
                        enableWorker: false // Désactiver le worker pour plus de compatibilité
                    });
                    
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);
                    
                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                        console.log('HLS manifest chargé');
                    });
                    
                    hls.on(Hls.Events.ERROR, function(event, data) {
                        console.error('HLS error:', data);
                        if (data.fatal) {
                            switch(data.type) {
                                case Hls.ErrorTypes.NETWORK_ERROR:
                                    hls.startLoad();
                                    break;
                                case Hls.ErrorTypes.MEDIA_ERROR:
                                    hls.recoverMediaError();
                                    break;
                                default:
                                    setupNativePlayer();
                                    break;
                            }
                        }
                    });
                    
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    // Support HLS natif (Safari)
                    video.src = videoSrc;
                } else {
                    // Fallback vers le player natif
                    setupNativePlayer();
                }
            }
            
            // Détection du format et initialisation du player
            if (extension === 'm3u8' || extension === 'm3u') {
                setupHLSPlayer();
            } else if (nativeFormats.includes(extension)) {
                setupNativePlayer();
            } else {
                // Format non reconnu - essayer le player natif
                setupNativePlayer();
            }
            
            // Gestion des erreurs générales du lecteur
            video.addEventListener('error', function(e) {
                console.error('Video error:', e);
                const errorMessage = document.createElement('div');
                errorMessage.style.cssText = `
                    padding: 2rem;
                    text-align: center;
                    color: #ff4d4d;
                    background: #151525;
                    border-radius: 8px;
                    margin: 1rem 0;
                `;
                errorMessage.innerHTML = `
                    <h3>❌ Erreur de lecture</h3>
                    <p>Impossible de lire la vidéo. Utilisez le lien de téléchargement ci-dessous.</p>
                `;
                video.parentNode.insertBefore(errorMessage, video.nextSibling);
            });
            
            // Lecture automatique si possible
            video.addEventListener('loadeddata', function() {
                video.play().catch(function(error) {
                    console.log('Lecture automatique bloquée:', error);
                });
            });
        });
    </script>
</body>
</html>