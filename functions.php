<?php
// Fichiers communs : constantes, session, fonctions utilitaires
define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('DOSSIER_UPLOADS', __DIR__ . '/uploads/');
const FILIERES = ['GI', 'IDIA', 'GE', 'GC'];
const UTILISATEURS = ['admin' => 'admin123', 'prof' => 'prof2026'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Thème (bonus) : cookie clair / sombre
if (isset($_GET['theme']) && in_array($_GET['theme'], ['clair', 'sombre'], true)) {
    setcookie('theme', $_GET['theme'], time() + 30 * 24 * 3600, '/');
    $_COOKIE['theme'] = $_GET['theme'];
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function connecte(): bool
{
    return isset($_SESSION['login']);
}

function exiger_connexion(): void
{
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

function nettoyer(string $s): string
{
    $s = str_replace([';', "\r", "\n"], ' ', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
}

function lire_etudiants(): array
{
    $liste = [];
    if (!file_exists(FICHIER)) {
        return $liste;
    }
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        $champs = explode(';', $ligne);
        if (count($champs) < 6) {
            continue;
        }
        [$id, $nom, $prenom, $filiere, $note, $photo] = $champs;
        $liste[(int)$id] = [
            'id'      => (int)$id,
            'nom'     => $nom,
            'prenom'  => $prenom,
            'filiere' => $filiere,
            'note'    => (float)$note,
            'photo'   => $photo,
        ];
    }
    return $liste;
}

function sauver_etudiants(array $liste): void
{
    $lignes = [];
    foreach ($liste as $e) {
        $lignes[] = implode(';', [$e['id'], $e['nom'], $e['prenom'], $e['filiere'], $e['note'], $e['photo']]);
    }
    file_put_contents(FICHIER, implode("\n", $lignes) . (count($lignes) ? "\n" : ''));
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}
