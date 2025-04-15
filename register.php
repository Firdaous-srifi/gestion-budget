<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            try {
                // Check if the email is already registered
                $stmt = $connection->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "Cet email est déjà enregistré.";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the new user into the database
                    $stmt = $connection->prepare("INSERT INTO users (nom, email, password) VALUES (:name, :email, :password)");
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->execute();

                    // Redirect to login page after successful registration
                    $_SESSION['success'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                    header('Location: login.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        } else {
            $error = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <h1>Inscription</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">Confirmer le mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>.</p>
</body>
</html>