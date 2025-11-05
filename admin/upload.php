<?php
require '../config.php';

if(!is_admin()) {
    redirect('../login.php');
}

$error = $success = null;
$allowed_images = ['image/jpeg', 'image/png', 'image/webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if(isset($_POST['add_serie'])) {
            $file_type = $_FILES['miniature']['type'];
            if(!in_array($file_type, $allowed_images)) {
                throw new Exception("Type d'image non autorisé. Formats acceptés: JPEG, PNG, WebP");
            }
            
            $miniature = uniqid() . '_' . basename($_FILES['miniature']['name']);
            move_uploaded_file($_FILES['miniature']['tmp_name'], '../thumbs/' . $miniature);

            $bdd->prepare("INSERT INTO series (titre, description, miniature, category_id) VALUES (?,?,?,?)")
               ->execute([
                   $_POST['titre'],
                   $_POST['description'],
                   $miniature,
                   $_POST['category_id']
               ]);
            $success = "Série ajoutée avec succès!";
        }

        if(isset($_POST['add_episode'])) {
            $videoFile = $_FILES['video'];
            $video = uniqid() . '_' . basename($videoFile['name']);
            move_uploaded_file($videoFile['tmp_name'], '../videos/' . $video);

            $bdd->prepare("INSERT INTO episodes (series_id, numero, titre, fichier) VALUES (?,?,?,?)")
               ->execute([
                   $_POST['series_id'],
                   $_POST['numero'],
                   $_POST['titre'],
                   $video
               ]);
            $success = "Épisode ajouté avec succès!";
        }

    } catch(Exception $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}

$categories = $bdd->query("SELECT * FROM categories")->fetchAll();
$series = $bdd->query("SELECT * FROM series")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: block;
            padding: 10px;
            background: #1a1a2e;
            border: 1px dashed #ff4d4d;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📤 Upload de contenu</h1>
        <a href="dashboard.php" class="cyber-button">Retour</a>
    </div>

    <div style="max-width: 1200px; margin: 2rem auto; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem">
        <form method="post" enctype="multipart/form-data" style="background: #151525; padding: 2rem; border-radius: 12px">
            <h2 style="color: #ff4d4d; margin-bottom: 1.5rem">Nouvelle série</h2>
            
            <div style="margin-bottom: 1rem">
                <label>Titre</label>
                <input type="text" name="titre" required style="width: 100%">
            </div>
            
            <div style="margin-bottom: 1rem">
                <label>Description</label>
                <textarea name="description" rows="3" style="width: 100%"></textarea>
            </div>
            
            <div style="margin-bottom: 1rem">
                <label>Catégorie</label>
                <select name="category_id" required style="width: 100%">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem">
                <label>Miniature (300x450px)</label>
                <div class="file-input-container">
                    <input type="file" name="miniature" accept="image/*" required>
                    <div class="file-input-label">📷 Sélectionner une miniature</div>
                </div>
            </div>
            
            <button type="submit" name="add_serie" class="cyber-button">Créer</button>
        </form>

        <form method="post" enctype="multipart/form-data" style="background: #151525; padding: 2rem; border-radius: 12px">
            <h2 style="color: #ff4d4d; margin-bottom: 1.5rem">Nouvel épisode</h2>
            
            <div style="margin-bottom: 1rem">
                <label>Série</label>
                <select name="series_id" required style="width: 100%">
                    <?php foreach($series as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['titre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 1rem">
                <label>Numéro</label>
                <input type="number" name="numero" min="1" required style="width: 100%">
            </div>
            
            <div style="margin-bottom: 1rem">
                <label>Titre épisode</label>
                <input type="text" name="titre" required style="width: 100%">
            </div>
            
            <div style="margin-bottom: 1.5rem">
                <label>Fichier vidéo (Tous formats acceptés)</label>
                <div class="file-input-container">
                    <input type="file" name="video" required>
                    <div class="file-input-label">🎬 Sélectionner une vidéo (MKV, AVI, MP4, etc.)</div>
                </div>
            </div>
            
            <button type="submit" name="add_episode" class="cyber-button">Ajouter</button>
        </form>
    </div>

    <?php if($success): ?>
        <div style="background: #4dff4d20; color: #4dff4d; padding: 1rem; margin: 1rem auto; max-width: 1200px; border-radius: 8px">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div style="background: #ff4d4d20; color: #ff4d4d; padding: 1rem; margin: 1rem auto; max-width: 1200px; border-radius: 8px">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <script>
        // Mise à jour du nom du fichier sélectionné
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if(this.files.length > 0) {
                    label.textContent = this.files[0].name;
                } else {
                    label.textContent = this.previousElementSibling?.textContent || 'Sélectionner un fichier';
                }
            });
        });
    </script>
</body>
</html>