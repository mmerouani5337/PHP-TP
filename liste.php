<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// Recherche
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($e) use ($q) {
        return stripos($e['nom'], $q) !== false || stripos($e['prenom'], $q) !== false;
    });
}

// Tri
$tris = ['ajout' => "Ordre d'ajout", 'nom' => 'Nom', 'filiere' => 'Filière', 'note' => 'Note (décroissante)'];
$tri = $_GET['tri'] ?? 'ajout';
if (!array_key_exists($tri, $tris)) {
    $tri = 'ajout';
}
switch ($tri) {
    case 'nom':
        usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
        break;
    case 'filiere':
        usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
        break;
    case 'note':
        usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']);
        break;
    default:
        usort($etudiants, fn($a, $b) => $a['id'] <=> $b['id']);
}

// Statistiques
$nb = count($etudiants);
$notes = array_column($etudiants, 'note');
$moyenne = $nb > 0 ? array_sum($notes) / $nb : 0;
$meilleure = $nb > 0 ? max($notes) : 0;

// Moyenne par filière (bonus)
$parFiliere = [];
foreach ($etudiants as $e) {
    $parFiliere[$e['filiere']][] = $e['note'];
}

// Message flash
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$titre = 'Liste des étudiants';
include 'header.php';
?>
<h2>Liste des étudiants</h2>
<?php if ($flash): ?><p class="msg succes"><?= h($flash) ?></p><?php endif; ?>

<form method="get" class="recherche">
    <input type="text" name="q" id="recherche" placeholder="Rechercher un nom ou prénom..." value="<?= h($q) ?>">
    <select name="tri">
        <?php foreach ($tris as $cle => $libelle): ?>
            <option value="<?= $cle ?>" <?= $tri === $cle ? 'selected' : '' ?>><?= h($libelle) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
</form>

<div class="stats">
    <span>Étudiants affichés : <strong><?= $nb ?></strong></span>
    <span>Moyenne : <strong><?= number_format($moyenne, 2, ',', ' ') ?></strong></span>
    <span>Meilleure note : <strong><?= number_format($meilleure, 2, ',', ' ') ?></strong></span>
</div>
<?php if ($parFiliere): ?>
<div class="stats">
    <?php foreach ($parFiliere as $f => $n): ?>
        <span><?= h($f) ?> : <strong><?= number_format(array_sum($n) / count($n), 2, ',', ' ') ?></strong></span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<table id="table-etudiants">
    <thead>
    <tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Note</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if ($nb === 0): ?>
        <tr><td colspan="6" class="vide">Aucun étudiant</td></tr>
    <?php else: ?>
        <?php foreach ($etudiants as $e): ?>
            <tr>
                <td>
                    <?php if ($e['photo'] !== '' && file_exists(DOSSIER_UPLOADS . $e['photo'])): ?>
                        <img class="photo" src="uploads/<?= h($e['photo']) ?>" alt="photo">
                    <?php else: ?>
                        <span class="photo sans"><?= h(strtoupper(substr($e['prenom'], 0, 1))) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= h(strtoupper($e['nom'])) ?></td>
                <td><?= h(ucfirst(strtolower($e['prenom']))) ?></td>
                <td><?= h($e['filiere']) ?></td>
                <td class="<?= $e['note'] >= 10 ? 'note-ok' : 'note-ko' ?>"><?= number_format($e['note'], 2, ',', ' ') ?></td>
                <td>
                    <a href="ajouter.php?id=<?= $e['id'] ?>">Modifier</a>
                    <a class="supprimer" href="supprimer.php?id=<?= $e['id'] ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>
