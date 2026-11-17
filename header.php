<?php $theme = $_COOKIE['theme'] ?? 'clair'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titre ?? 'Gestion des étudiants') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?= h($theme) ?>">
<header class="entete">
    <h1>Gestion des étudiants</h1>
    <?php if (connecte()): ?>
        <nav>
            <a href="liste.php">Liste</a>
            <a href="ajouter.php">Ajouter</a>
            <a href="export.php">Export CSV</a>
            <a href="?theme=<?= $theme === 'sombre' ? 'clair' : 'sombre' ?>">Thème <?= $theme === 'sombre' ? 'clair' : 'sombre' ?></a>
            <span class="user"><?= h($_SESSION['login']) ?></span>
            <a href="logout.php">Déconnexion</a>
        </nav>
    <?php endif; ?>
</header>
<main class="contenu">
