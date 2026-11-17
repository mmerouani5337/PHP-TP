<?php
require_once 'functions.php';
exiger_connexion();

$id = (int)($_GET['id'] ?? 0);
$etudiants = lire_etudiants();

if (isset($etudiants[$id])) {
    $photo = $etudiants[$id]['photo'];
    if ($photo !== '' && file_exists(DOSSIER_UPLOADS . $photo)) {
        unlink(DOSSIER_UPLOADS . $photo);
    }
    unset($etudiants[$id]);
    sauver_etudiants($etudiants);
    flash('Étudiant supprimé.');
}
header('Location: liste.php');
exit;
