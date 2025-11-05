<?php require 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            background: #151525;
            border-radius: 12px;
            border: 1px solid #ff4d4d;
            text-align: center;
        }
        .auth-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #0a0a15;
            border: 1px solid #2d2d42;
            color: white;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2 style="color: #ff4d4d">Connexion</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="login" class="cyber-button">Se connecter</button>
        </form>
        <p style="margin-top: 1rem">Pas de compte? <a href="register.php" style="color: #ff4d4d">Inscrivez-vous</a></p>

        <?php
        if(isset($_POST['login'])) {
            $stmt = $bdd->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$_POST['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                if($user['is_admin']) {
                    redirect('admin/dashboard.php');
                } else {
                    redirect('index.php');
                }
            } else {
                echo '<p style="color:#ff4d4d; margin-top:1rem">Identifiants incorrects</p>';
            }
        }
        ?>
    </div>
</body>
</html>