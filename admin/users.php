<?php
require '../config.php';

if(!is_admin()) {
    redirect('../login.php');
}

$users = $bdd->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$success = $error = null;

if(isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if($id && $id !== $_SESSION['user_id']) {
        $bdd->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $success = "Utilisateur supprimé";
    } else {
        $error = "Action interdite";
    }
}

if(isset($_GET['promote'])) {
    $id = filter_var($_GET['promote'], FILTER_VALIDATE_INT);
    if($id && $id !== $_SESSION['user_id']) {
        $bdd->prepare("UPDATE users SET is_admin = 1 WHERE id = ?")->execute([$id]);
        $success = "Utilisateur promu administrateur";
    } else {
        $error = "Action interdite";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion Utilisateurs</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-header">
        <h1>👥 Gestion Utilisateurs</h1>
        <a href="dashboard.php" class="cyber-button">Retour</a>
    </div>

    <div style="max-width: 1200px; margin: 2rem auto">
        <table style="width: 100%; border-collapse: collapse">
            <thead>
                <tr style="background: #0a0a15">
                    <th style="padding: 1rem; text-align: left">ID</th>
                    <th style="padding: 1rem; text-align: left">Nom</th>
                    <th style="padding: 1rem; text-align: left">Email</th>
                    <th style="padding: 1rem; text-align: left">Admin</th>
                    <th style="padding: 1rem; text-align: left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr style="border-bottom: 1px solid #2d2d42">
                        <td style="padding: 1rem"><?= $user['id'] ?></td>
                        <td style="padding: 1rem"><?= htmlspecialchars($user['username']) ?></td>
                        <td style="padding: 1rem"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 1rem"><?= $user['is_admin'] ? '✅' : '❌' ?></td>
                        <td style="padding: 1rem">
                            <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $user['id'] ?>" style="color: #ff4d4d">Supprimer</a>
                                <?php if(!$user['is_admin']): ?>
                                    | <a href="?promote=<?= $user['id'] ?>" style="color: #4dff4d">Promouvoir</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <em>Compte actuel</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
</body>
</html>