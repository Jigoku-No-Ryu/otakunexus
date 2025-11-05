<?php require 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
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
        <h2 style="color: #ff4d4d">Créer un compte</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="register" class="cyber-button">S'inscrire</button>
        </form>
        <p style="margin-top: 1rem">Déjà inscrit? <a href="login.php" style="color: #ff4d4d">Connectez-vous</a></p>

        <?php
        if(isset($_POST['register'])) {
            $username = htmlspecialchars($_POST['username']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            try {
                $stmt = $bdd->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password]);
                echo '<p style="color:#4dff4d; margin-top:1rem">Compte créé avec succès!</p>';
            } catch(PDOException $e) {
                echo '<p style="color:#ff4d4d; margin-top:1rem">Erreur: ' . $e->getMessage() . '</p>';
            }
        }
        ?>
    </div>
</body>
</html>