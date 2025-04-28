<?php
    function loginUser($pdo, $email, $password) {
        try {
            $queryLogin = "SELECT id, email, password FROM users WHERE email = :email";
            $stmt = $pdo->prepare($queryLogin);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['password'])) {
                return ['success' => true, 'user_id' => $user['id']];
            } else {
                return ['success' => false, 'error' => "Email ou mot de passe incorrect"];
            }
        } 
        catch (PDOException $e) {
            return ['success' => false, 'error' => "Erreur de connexion: " . $e->getMessage()];
        }
    }

    function checkEmailExists($pdo, $email, &$errors) {
        try {
            $queryCheckEmail = "SELECT COUNT(*) FROM `users` WHERE `email` = :email";
            $stmtCheck = $pdo->prepare($queryCheckEmail);
            $stmtCheck->bindParam(':email', $email);
            $stmtCheck->execute();
            $count = $stmtCheck->fetchColumn();
            if ($count > 0) {
                $errors['email'][] = "Cet email est déjà utilisé.";
                return false;
            }
            return true;
        } catch (PDOException $e) {
            $errors['email'][] = "Erreur lors de la vérification de l'email : " . $e->getMessage();
            return false;
        }
    }

    function registerUser($pdo, $nom, $email, $password, &$errors) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
  
            $queryRegister = "INSERT INTO `users` (`nom`, `email`, `password`) VALUES (:nom, :email, :password)";
            $stmt = $pdo->prepare($queryRegister);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
  
            if ($stmt->execute()) {
                return ['success' => true];
            } else {
                $errorInfo = $stmt->errorInfo();
                $errors['general'][] = "Erreur lors de l'inscription : " . $errorInfo[2];
                return ['success' => false, 'error' => "Erreur lors de l'inscription : " . $errorInfo[2]];
            }
        }
        catch (PDOException $e) {
          $errors['general'][] = "Erreur de base de données : " . $e->getMessage();
          return ['success' => false, 'error' => "Erreur de base de données : " . $e->getMessage()];
        }
    }



    