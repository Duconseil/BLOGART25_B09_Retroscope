<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';
include '../../header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $numMemb    = isset($_POST['numMemb'])    ? ctrlSaisies($_POST['numMemb'])    : null;
    $prenomMemb = isset($_POST['prenomMemb']) ? ctrlSaisies($_POST['prenomMemb']) : null;
    $nomMemb    = isset($_POST['nomMemb'])    ? ctrlSaisies($_POST['nomMemb'])    : null;
    $passMemb   = isset($_POST['passMemb'])   ? ctrlSaisies($_POST['passMemb'])   : null;
    $passMemb2  = isset($_POST['passMemb2'])  ? ctrlSaisies($_POST['passMemb2'])  : null;
    $eMailMemb  = isset($_POST['eMailMemb'])  ? ctrlSaisies($_POST['eMailMemb'])  : null;
    $eMailMemb2 = isset($_POST['eMailMemb2']) ? ctrlSaisies($_POST['eMailMemb2']) : null;
    $numStat    = isset($_POST['numStat'])    ? ctrlSaisies($_POST['numStat'])    : null;

    $errors = [];

    if (!$numMemb) {
        $errors[] = "ID du membre manquant.";
    } else {
        // Vérifier que le membre existe bien
        $current = sql_select('MEMBRE', 'numMemb, numStat', "numMemb = '$numMemb'");
        if (empty($current)) {
            $errors[] = "Le membre spécifié n'existe pas.";
        } else {
            $currentStat = $current[0]['numStat'];
        }
    }

    if (!empty($passMemb) || !empty($passMemb2)) {
        if (!preg_match('/[A-Z]/', $passMemb) || !preg_match('/[a-z]/', $passMemb) || !preg_match('/[0-9]/', $passMemb)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.";
        }
        if ($passMemb !== $passMemb2) {
            $errors[] = "Les mots de passe doivent être identiques.";
        }
        if (empty($errors)) {
            $hash_password = password_hash($passMemb, PASSWORD_DEFAULT);
        }
    }

    if (!filter_var($eMailMemb, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "$eMailMemb n'est pas une adresse mail valide.";
    }
    if ($eMailMemb !== $eMailMemb2) {
        $errors[] = "Les adresses mail doivent être identiques.";
    }

    $admin_exist = sql_select('MEMBRE', 'numMemb', "numStat = 1");

    if (!empty($admin_exist) && $numStat == 1 && $currentStat != 1) { 
        $errors[] = "Il y a déjà un administrateur, vous ne pouvez pas en créer un autre.";
        $numStat = $currentStat; // On garde l'ancien statut
    }

    if (empty($errors) && isset($numMemb, $prenomMemb, $nomMemb, $eMailMemb, $numStat)) {

        if (isset($hash_password)) {
            $updateFields = "prenomMemb = '$prenomMemb', nomMemb = '$nomMemb', passMemb = '$hash_password', eMailMemb = '$eMailMemb', numStat = '$numStat'";
        } else {
            $updateFields = "prenomMemb = '$prenomMemb', nomMemb = '$nomMemb', eMailMemb = '$eMailMemb', numStat = '$numStat'";
        }

        sql_update('MEMBRE', $updateFields, "numMemb = '$numMemb'");
        header('Location: ../../views/backend/members/list.php');
        exit();
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>