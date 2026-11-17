<?php
// Exercice 3 : test d'écriture / lecture avec trois étudiants
require_once 'functions.php';

$liste = [
    1 => ['id' => 1, 'nom' => 'benali', 'prenom' => 'sara', 'filiere' => 'GI', 'note' => 14.5, 'photo' => ''],
    2 => ['id' => 2, 'nom' => 'alaoui', 'prenom' => 'youssef', 'filiere' => 'IDIA', 'note' => 9, 'photo' => ''],
    3 => ['id' => 3, 'nom' => 'tazi', 'prenom' => 'imane', 'filiere' => 'GE', 'note' => 16.75, 'photo' => ''],
];
sauver_etudiants($liste);

echo '<h3>Contenu du fichier</h3><pre>' . h(file_get_contents(FICHIER)) . '</pre>';
echo '<h3>Tableau relu</h3><pre>';
print_r(lire_etudiants());
echo '</pre>';
