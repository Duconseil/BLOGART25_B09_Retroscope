<?php
// Inclure le fichier de connexion et les fonctions nécessaires
include '../../../header.php';

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs du formulaire
    if (!empty($_POST["pseudoMemb"]) && !empty($_POST["mot_de_passe"])) {
        $pseudoMemb = trim($_POST["pseudoMemb"]);
        $passMemb = trim($_POST["mot_de_passe"]);

        try {
            // Récupérer les informations du membre avec sql_select
            $user = sql_select("membre", "*", "pseudoMemb = ?", [$pseudoMemb]);

            if ($user && count($user) > 0) {
                $user = $user[0]; // Extraire le premier (et unique) résultat

                // Vérifier le mot de passe
                if (password_verify($passMemb, $user['passMemb'])) {
                    // Stocker les infos en session
                    $_SESSION['pseudoMemb'] = $user['pseudoMemb'];
                    $_SESSION['prenomMemb'] = $user['prenomMemb'];
                    $_SESSION['nomMemb'] = $user['nomMemb'];
                    $_SESSION['numStat'] = $user['numStat']; 

                    // Charger tous les statuts disponibles
                    $statuts = sql_select("STATUT", "*");

                    header("Location: http://localhost:8888?message=connexion_reussie");
                    exit;
                } else {
                    echo "<p style='color:red;'>Mot de passe incorrect.</p>";
                }
            } else {
                echo "<p style='color:red;'>Pseudo non trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Erreur de requête : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Veuillez remplir tous les champs.</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            input.type = (input.type === "password") ? "text" : "password";
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <form action="login.php" method="post" class="form-container">
            <div class="form-group">
                <label for="pseudoMemb">Pseudo</label>
                <input type="text" name="pseudoMemb" placeholder="Pseudonyme" minlength="6" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" minlength="8" maxlength="15" required>
                <p>(8-15 caractères, une majuscule, une minuscule, un chiffre, un caractère spécial)</p>
                <input type="checkbox" onclick="togglePassword('mot_de_passe')"> Afficher Mot de passe
            </div>

            <div class="form-group">
                <button type="submit">Se connecter</button>
            </div>
        </form>
    </div>

    <?php
    // Vérification de l'authentification et de l'affichage du bouton Admin
    if (isset($_SESSION['pseudoMemb']) && $_SESSION['numStat'] != 3) {
        echo '<button class="admin-button"><a href="admin_dashboard.php">Accéder à l\'Admin</a></button>';
    }
    ?>

</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 20px;
        background-color: #f4f4f4;
    }

    .form-container {
        width: 50%;
        margin: 0 auto;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
    }

    .form-group input, .form-group button {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .form-group button {
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #0056b3;
    }

    .admin-button {
        margin-top: 20px;
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 16px;
    }

    .admin-button:hover {
        background-color: #218838;
    }
</style>
