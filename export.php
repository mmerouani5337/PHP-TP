<?php
require_once 'functions.php';
exiger_connexion();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="etudiants.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['id', 'nom', 'prenom', 'filiere', 'note', 'photo'], ';');
foreach (lire_etudiants() as $e) {
    fputcsv($out, $e, ';');
}
fclose($out);
